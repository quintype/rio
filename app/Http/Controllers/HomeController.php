<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends QuintypeController
{
    public function __construct()
    {
        parent::__construct();
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
        $setSeo = $this->seo->home($page['type']);
        $this->meta->set($setSeo->prepareTags());

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
        $this->client->executeBulk();

        $sectionNames = $this->getSectionNames($story);
        $otherAuthor = array();
        for ($kk = 0; $kk < sizeof($story['authors']); ++$kk) {
            $author_data = $this->client->getAuthor($story['authors'][$kk]['id']);
            $authorbio = strip_tags($author_data['bio']);
            array_push($otherAuthor, $author_data);
        }
        $imageSizeArray = $story['hero-image-metadata'];
        if (array_key_exists("height", $imageSizeArray) == false || array_key_exists("width", $imageSizeArray) == false ) {
            $story['hero-image-metadata'] = $this->imageSizeCalc($story);
        }
        $getRatingValues = $this->getAverageRating($story);
        $authorDetails = $this->client->getAuthor($story['author-id']);
        $cardAttribute = function ($card) {
            if (array_key_exists('metadata', $card) &&
                array_key_exists('attributes', $card['metadata']) &&
                array_key_exists('alignment', $card['metadata']['attributes'])) {
                if (sizeof($card['story-elements']) <= 2) {
                    if ($card['story-elements'][0]['type'] != 'text') {
                        return $card;
                    } else {
                        $card['story-elements'] = array_reverse($card['story-elements']);

                        return $card;
                    }
                }
            } else {
                return $card;
            }
        };

        $story['cards'] = array_map($cardAttribute, $story['cards']);

        $page = ['type' => 'story'];
        $setSeo = $this->seo->story($page['type'], $story);
        $this->meta->set($setSeo->prepareTags());

        return view('story', $this->toView([
          'storyData' => $story,
          'otherAuthor' => $otherAuthor,
          'authorDetails' => $authorDetails,
          'getRatingValues' => $getRatingValues,
          'page' => $page,
          'meta' => $this->meta,
          'sectionNames' => $sectionNames
        ]));

     }

    public function sectionview($sectionSlug, $subSectionSlug = '')
     {
        $allSections = $this->config['sections'];
        $section = $this->client->getSectionDetails($sectionSlug, $allSections);
        if (sizeof($section) > 0) {
            $sectionId = $section['id'];
            $sectionName = $section['slug'];
            $sectionId = $section['id'];
        } else {
            return response()->view('errors/404', $this->toView([]), 404);
        }
        if ($subSectionSlug !== '') {
            $subSection = $this->client->getSectionDetails($subSectionSlug, $allSections);
            if (sizeof($subSection) > 0) {
                if ($subSection['parent-id'] == $section['id']) {
                    $sectionId = $subSection['id'];
                    $sectionName = $subSection['slug'];
                    $sectionId = $subSection['id'];
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
          'limit' => 17,
          'fields' => $this->fields,
        ];

        $showAltInPage = 'home';
        $stories = $this->client->stories($params, $showAltInPage);

        $page = ['type' => 'section'];
        $setSeo = $this->seo->section($page['type'], $sectionName, $sectionId);
        $this->meta->set($setSeo->prepareTags());

        if(sizeof($stories)<1){
          return view('noresults',$this->toView([]));
        }
        if ($subSectionSlug != '') {
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
        $setSeo = $this->seo->search($query);
        $this->meta->set($setSeo->prepareTags());

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
        $tag = $request->tag;
        $params = [
          'story-group' => 'top',
          'tag' => $tag,
          'limit' => 7,
        ];
        $showAltInPage = 'home';
        $tagStories = $this->client->stories($params, $showAltInPage);
        $tag = urldecode($tag);

        $page = ['type' => 'tag'];
        $setSeo = $this->seo->tag($tag);
        $this->meta->set($setSeo->prepareTags());

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
        $setSeo = $this->seo->staticPage('About Us');
        $this->meta->set($setSeo->prepareTags());

        return view('about', $this->toView([
        'page' => $page,
        'meta' => $this->meta,
      ]));
    }

    public function privacyview()
    {
        $page = ['type' => 'privacy'];
        $setSeo = $this->seo->staticPage('Privacy Policy');
        $this->meta->set($setSeo->prepareTags());

        return view('privacy', $this->toView([
        'page' => $page,
        'meta' => $this->meta,
      ]));
    }

    public function termsview()
    {
        $page = ['type' => 'terms'];
        $setSeo = $this->seo->staticPage('Terms of use');
        $this->meta->set($setSeo->prepareTags());

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

        $page = ['type' => 'author'];
        $setSeo = $this->seo->staticPage($authorDetails['name']);
        $this->meta->set($setSeo->prepareTags());
        return view('author', $this->toView([
          'authorDetails' => $authorDetails,
          'authorStories' => $authorStories,
          'params' => $params,
          'page' => $page,
          'meta' => $this->meta,
        ])
      );
    }

    public function getSectionNames($story)
    {
        $sectionNameArray = array();
        $sectionNameLastArray = array();
          if ($story['story-template'] == 'recipe') {
            foreach ($story['sections'] as $key => $section) {
              array_push($sectionNameArray, $section['display-name']);
            }
            if (sizeof($story['sections']) == 1) {
              $sectionNames = $sectionNameArray[0];
            } elseif (sizeof($story['sections']) == 2) {
              $sectionNames = implode(" and ", $sectionNameArray);
            } else {
              $sectionNameLastArray = array_pop($sectionNameArray);
              $sectionNames = implode(", ", $sectionNameArray). " and ". $sectionNameLastArray;
            }
         return $sectionNames;
        }
     }
}
