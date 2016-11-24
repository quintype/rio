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
        $this->client->addBulkRequest('weather_stories', 'top', ['section' => 'Weather & Climate', 'fields' => $this->fields, 'limit' => 4]);
        $this->client->addBulkRequest('videos_stories', 'top', ['section' => 'Videos', 'fields' => $this->fields, 'limit' => 3]);
        $this->client->addBulkRequest('food_health', 'top', ['section' => 'campaign2016', 'fields' => $this->fields, 'limit' => 3]);
        $this->client->executeBulk();

        $top_stories = $this->client->getBulkResponse('top_stories');
        $weather_stories = $this->client->getBulkResponse('weather_stories');
        $videos_stories = $this->client->getBulkResponse('videos_stories');
        $food_health = $this->client->getBulkResponse('food_health');

        $page = ['type' => 'home'];
        $home = new Seo\Home(array_merge($this->config, config('quintype')), $page['type']);
        $this->meta->set($home->tags());

        $alternativePage = 'home';

        return view('home', $this->toView([
        'stories' => $this->client->prepareAlternateDetails($top_stories, $alternativePage),
        'weather_stories' => $this->client->prepareAlternateDetails($weather_stories, $alternativePage),
        'videos_stories' => $this->client->prepareAlternateDetails($videos_stories, $alternativePage),
        'food_storiess' => $this->client->prepareAlternateDetails($food_health, $alternativePage),
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

    public function sectionview($sectionSlug)
    {
        $allSections = $this->config['sections'];
        $section = $this->client->getSectionDetails($sectionSlug, $allSections);

        $params = [
          'story-group' => 'top',
          'section' => $section['name'],
          'limit' => 8,
          'fields' => $this->fields,
        ];
        $stories = $this->client->stories($params);

        $page = ['type' => 'section'];
        $sectionMeta = new Seo\Section(array_merge($this->config, config('quintype')), $page['type'], $sectionSlug);
        $this->meta->set($sectionMeta->tags());

        if ($section['name'] == 'Inquiring Minds') {
            return view('podcasts', $this->toView([
            'section' => $section,
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
        $tagStories = $this->client->stories($params);
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
}
