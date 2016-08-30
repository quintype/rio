<?php

namespace App\Http\Controllers;

use Log;
use Illuminate\Http\Request;
use App\Http\Controllers\QuintypeController;
use App\Api\Bulk;
use App\Api\Config;
use App\Api\StoriesRequest;
use Meta;
use Quintype\Seo;

class HomeController extends QuintypeController {

    public function __construct(){
        parent::__construct();
        $this->meta = new Meta;
        $this->config = $this->client->config();

    }

    public function index() {

        $bulk = new Bulk();
        // Setting Seo meta tags

        $page = ["type" => "home"];
        $home = new Seo\Home(array_merge($this->config, config('quintype')), $page["type"]);
        $this->meta->set($home->tags());

        $fields = "id,headline,slug,url,hero-image-s3-key,hero-image-metadata,first-published-at,last-published-at,alternative,published-at,author-name,author-id,sections,story-template,summary,metadata,hero-image-attribution,cards,subheadline";

        $bulk->addRequest('top_stories', (new StoriesRequest('top'))->addParams(["limit" => 8, "fields" => $fields]));
        $bulk->addRequest('weatherstories', (new StoriesRequest('top'))->addParams(["section" => "Weather & Climate", "limit" => 3, "fields" => $fields]));
        $bulk->addRequest('videosstories', (new StoriesRequest('top'))->addParams(["section" => "Videos", "limit" => 3, "fields" => $fields]));
        $bulk->addRequest('breaking_news', (new StoriesRequest('stack-115'))->addParams(["limit" => 3, "fields" => $fields]));
        $bulk->addRequest('foodhealth', (new StoriesRequest('top'))->addParams(["section" => "Food & Health", "limit" => 4, "fields" => $fields]));

        $bulk->execute($this->client);

        $a = $bulk->getResponse("top_stories");

        return view('home', $this->toView([
            "stories" => $bulk->getResponse("top_stories"),
            "page" => $page,
            "meta" => $this->meta,
            "videos_stories" => $bulk->getResponse("videosstories"),
            "weather_stories" => $bulk->getResponse("weatherstories"),
            "food_storiess" => $bulk->getResponse("foodhealth"),
            "breaking_news" => $bulk->getResponse("breaking_news")]));

    }

    public function storyview($category, $y, $m, $d, $slug) {

        $bulk = new Bulk();
        $fields = "id,headline,slug,url,hero-image-s3-key,hero-image-metadata,first-published-at,last-published-at,alternative,published-at,author-name,author-id,sections,story-template,summary,metadata,subheadline";
        $story = $this->client->storyData(array('slug' => $slug))['story'];
        $author_data = $this->client->author($story['author-id']);
        $authorbio=strip_tags($author_data['bio']);

        $bulk->addRequest('related_stories', (new StoriesRequest('top'))->addParams(["section" => $story["sections"][0]["name"], "limit" => 4, "fields" => $fields]));
        $bulk->execute($this->client);
        $abcd=$bulk->getResponse("related_stories");

        $pos=array_search($story['id'],$abcd);

        // Setting Seo meta tags
        $page = ["type" => "story"];
        $stories = new Seo\Story(array_merge($this->config, config('quintype')), $page["type"], $story);
        $this->meta->set($stories->tags());

        return view('story', $this->toView([
            "storyData" => $story,
            "page" => $page,
            "meta" => $this->meta,
            "relatedstories" => $bulk->getResponse("related_stories"),
            "authordata"=>$author_data,
            "authorbio"=>$authorbio]));

    }

    public function sectionview($section) {

        // Setting Seo meta tags
        $page = ["type" => "section"];
        $section = new Seo\Section(array_merge($this->config, config('quintype')), $page["type"], $section);
        $this->meta->set($section->tags());

        $fields = "id,headline,slug,url,hero-image-s3-key,hero-image-metadata,first-published-at,last-published-at,alternative,published-at,author-name,author-id,sections,story-template,summary,metadata,hero-image-attribution,cards,subheadline";
        $sections = $this->config['sections'];
        $cur_section = $sections[array_search($section, array_column($sections, 'slug'), true)];
        $params = array('story-group' => 'top', 'section' => $cur_section['name'], 'limit' => 8, "fields" => $fields);
        $stories = $this->getStories($params);

        if ($cur_section['name'] != 'Inquiring Minds')
            return view('section', $this->toView([
                "section" => $cur_section,
                "page" => $page,
                "meta" => $this->meta,
                "section_stories" => $stories,
                "params" => $params]));
        else
            return view('podcasts', $this->toView([
                "section" => $cur_section,
                "page" => $page,
                "meta" => $this->meta,
                "section_stories" => $stories,
                "params" => $params]));

    }

    public function searchview(Request $request) {

        // Setting Seo meta tags
        $page = ["type" => "search"];
        $query = $request->q;
        $search = new Seo\Search(array_merge($this->config, config('quintype')), $page["type"], $query);
        $this->meta->set($search->tags());

        $fields = "id,headline,slug,url,hero-image-s3-key,hero-image-metadata,first-published-at,last-published-at,alternative,published-at,author-name,author-id,sections,story-template,summary,metadata,hero-image-attribution,cards,subheadline";

        $searchedstories = $this->searchStories(array('q' => $query, 'size' => 7, "fields" => $fields));
        $searchsize=sizeof($searchedstories);
        $params=(array('q' => $query, 'limit' => 7, "fields" => $fields));


        if ($searchsize < 1)
            return view('noresults');
        else
            return view('search', $this->toView([
                "searchresults" => $searchedstories,
                "page" => $page,
                "meta" => $this->meta,
                "term" => $query,
                "params" => $params]));

    }

    public function tagsview(Request $request) {


        $fields = "id,headline,slug,url,hero-image-s3-key,hero-image-metadata,first-published-at,last-published-at,alternative,published-at,author-name,author-id,sections,story-template,summary,metadata,hero-image-attribution,cards,subheadline";
        $a = explode("/", $_SERVER['REQUEST_URI']);
        $tag = $a[sizeof($a) - 1];
        $tagStories = $this->getStories(array('story-group' => 'top', 'tag' => $tag, 'limit' => 7));
        $params = array('story-group' => 'top', 'tag' => $tag, 'limit' => 7);
        $tag = urldecode($tag);

        // Setting Seo meta tags
        $page = ["type" => "tag"];
        $tags = new Seo\Tag(array_merge($this->config, config('quintype')), $page["type"], $tag);
        $this->meta->set($tags->tags());

        return view('tags', $this->toView([
            "tagresults" => $tagStories,
            "page" => $page,
            "meta" => $this->meta,
            "tag" => $tag,
            "params" => $params]));

    }

    public function aboutview() {

        // Setting Seo meta tags
        $about = new Seo\StaticPage("About Us");
        $this->meta->set($about->tags());

        return view('about', $this->toView([
            "meta" => $this->meta
            ])
        );

    }

    public function privacyview() {

        // Setting Seo meta tags
        $privacy = new Seo\StaticPage("Privacy Policy");
        $this->meta->set($privacy->tags());
        return view('privacy', $this->toView([
            "meta" => $this->meta
            ])
        );

    }

    public function termsview() {
        // Setting Seo meta tags
        $terms = new Seo\StaticPage("Terms of use");
        $this->meta->set($terms->tags());
        return view('terms', $this->toView([
            "meta" => $this->meta
            ])
        );

    }

    //   public function errorview() {
    //     return view('404');
    // }

}
