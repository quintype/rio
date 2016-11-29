<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Meta;
use Quintype\Seo;

class HomeController extends QuintypeController
{
    public function __construct()
    {
        parent::__construct();
        $this->meta = new Meta();
        $this->fields = 'id,headline,slug,url,hero-image-s3-key,hero-image-metadata,first-published-at,last-published-at,alternative,published-at,author-name,author-id,sections,story-template,summary,metadata,hero-image-attribution,cards,subheadline,authors';
    }

    public function index()
    {
        $this->client->addBulkRequest('top_stories', 'top', ['fields' => $this->fields, 'limit' => 8]);
        $this->client->addBulkRequest('entertainment', 'top', ['section' => 'Entertainment', 'fields' => $this->fields, 'limit' => 4]);
        $this->client->addBulkRequest('videos', 'top', ['section' => 'Videos', 'fields' => $this->fields, 'limit' => 3]);
        $this->client->addBulkRequest('international', 'top', ['section' => 'International', 'fields' => $this->fields, 'limit' => 3]);
        $this->client->addBulkRequest('banners', 'top', ['section' => 'Banners', 'fields' => $this->fields, 'limit' => 1]);

        $this->client->buildStacksRequest($this->config['layout']['stacks'], $this->fields);

        $this->client->executeBulk();

        $showAltInPage = 'home';
        $top_stories = $this->client->getBulkResponse('top_stories', $showAltInPage);
        $entertainment = $this->client->getBulkResponse('entertainment', $showAltInPage);
        $videos = $this->client->getBulkResponse('videos', $showAltInPage);
        $international = $this->client->getBulkResponse('international', $showAltInPage);
        $banners = $this->client->getBulkResponse('banners', $showAltInPage);

        $stacks = $this->client->buildStacks($this->config['layout']['stacks']);
        $most_popular = $this->client->getStoriesByStackName('Most Shared', $stacks);

        $page = ['type' => 'home'];
        $home = new Seo\Home(array_merge($this->config, config('quintype')), $page['type']);
        $this->meta->set($home->tags());

        return view('home', $this->toView([
        'stories' => $top_stories,
        'entertainment' => $entertainment,
        'videos' => $videos,
        'international' => $international,
        'most_popular' => $most_popular,
        'banners' => $banners,
        'page' => $page,
        'meta' => $this->meta,
      ]));
    }

    public function storyview($category, $y, $m, $d, $slug)
    {
        $story = $this->client->storyBySlug(['slug' => $slug]);

        $this->client->addBulkRequest('related_stories', 'top', ['section' => $story['sections'][0]['name'], 'fields' => $this->fields, 'limit' => 4]);
        $this->client->executeBulk();
        $related_stories = $this->client->getBulkResponse('related_stories');

        $finalauthor = array();
        for ($kk = 0; $kk < sizeof($story['authors']); ++$kk) {
            $author_data = $this->client->getAuthor($story['authors'][$kk]['id']);
            $authorbio = strip_tags($author_data['bio']);
            array_push($finalauthor, $author_data);
        }

        $page = ['type' => 'story'];
        $stories = new Seo\Story(array_merge($this->config, config('quintype')), $page['type'], $story);
        $this->meta->set($stories->tags());

        return view('story', $this->toView([
          'storyData' => $story,
          'relatedstories' => $related_stories,
          'authordata' => $finalauthor,
          'page' => $page,
          'meta' => $this->meta,
        ]));
    }

    public function sectionview($sectionSlug, $subSectionSlug = '')
    {
        $allSections = $this->config['sections'];
        $section = $this->client->getSectionDetails($sectionSlug, $allSections);
        if(sizeof($section) > 0){
            $sectionId = $section['id'];
            $sectionName = $section['slug'];
        } else {
          return response()->view('errors/404', $this->toView([]), 404);
        }

        if ($subSectionSlug !== '') {
            $subSection = $this->client->getSectionDetails($subSectionSlug, $allSections);
            if (sizeof($subSection) > 0) {
                if ($subSection['parent-id'] == $section['id']) {
                    $sectionId = $subSection['id'];
                    $sectionName = $subSection['slug'];
                } else {
                    return response()->view('errors/404', $this->toView([]), 404);
                }
            } else {
                return response()->view('errors/404', $this->toView([]), 404);
            }
        }

        $params = [
          'story-group' => 'top',
          'section-id' => $sectionId,
          'limit' => 8,
          'fields' => $this->fields,
        ];

        $showAltInPage = 'home';
        $stories = $this->client->stories($params, $showAltInPage);

        $page = ['type' => 'section'];
        $sectionMeta = new Seo\Section(array_merge($this->config, config('quintype')), $page['type'], $sectionName);
        $this->meta->set($sectionMeta->tags());

        if ($subSectionSlug !== '') {
            return view('sub_section', $this->toView([
            'subSection' => $subSection,
            'page' => $page,
            'meta' => $this->meta,
            'section_stories' => $stories,
            'params' => $params,
          ]));
        } else {
            return view('section', $this->toView([
            'section' => $section,
            'page' => $page,
            'meta' => $this->meta,
            'section_stories' => $stories,
            'params' => $params,
          ]));
        }
    }

    public function searchview(Request $request)
    {
        $query = $request->q;
        $params = [
          'q' => $query,
          'limit' => 7,
          'fields' => $this->fields,
        ];
        $searchedstories = $this->client->search($params);

        $page = ['type' => 'search'];
        $search = new Seo\Search(array_merge($this->config, config('quintype')), $page['type'], $query);
        $this->meta->set($search->tags());

        if (sizeof($searchedstories) < 1) {
            return view('noresults');
        } else {
            return view('search', $this->toView([
            'searchresults' => $searchedstories,
            'page' => $page,
            'meta' => $this->meta,
            'term' => $query,
            'params' => $params,
          ]));
        }
    }

    public function tagsview(Request $request)
    {
        $tag = $request->topic;
        $params = [
          'story-group' => 'top',
          'tag' => $tag,
          'limit' => 7,
        ];
        $showAltInPage = 'home';
        $tagStories = $this->client->stories($params, $showAltInPage);
        $tag = urldecode($tag);

        $page = ['type' => 'tag'];
        $tags = new Seo\Tag(array_merge($this->config, config('quintype')), $page['type'], $tag);
        $this->meta->set($tags->tags());

        return view('tags', $this->toView([
        'tagresults' => $tagStories,
        'page' => $page,
        'meta' => $this->meta,
        'tag' => $tag,
        'params' => $params,
      ]));
    }

    public function aboutview()
    {
        $page = ['type' => 'about'];
        $about = new Seo\StaticPage('About Us');
        $this->meta->set($about->tags());

        return view('about', $this->toView([
        'page' => $page,
        'meta' => $this->meta,
      ]));
    }

    public function privacyview()
    {
        $page = ['type' => 'privacy'];
        $privacy = new Seo\StaticPage('Privacy Policy');
        $this->meta->set($privacy->tags());

        return view('privacy', $this->toView([
        'page' => $page,
        'meta' => $this->meta,
      ]));
    }

    public function termsview()
    {
        $page = ['type' => 'terms'];
        $terms = new Seo\StaticPage('Terms of use');
        $this->meta->set($terms->tags());

        return view('terms', $this->toView([
        'page' => $page,
        'meta' => $this->meta,
      ]));
    }

    public function authorview($authorId)
    {
        $authorDetails = $this->client->getAuthor($authorId);
        $params = [
            'author-id' => $authorId,
            'sort' => 'latest-published',
            'limit' => 3,
            'fields' => $this->fields,
        ];
        $authorStories = $this->client->search($params);

        return view('author', $this->toView([
          'authorDetails' => $authorDetails,
          'authorStories' => $authorStories,
          'params' => $params,
        ])
      );
    }
}
