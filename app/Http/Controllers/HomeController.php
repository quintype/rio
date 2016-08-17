<?php

namespace App\Http\Controllers;

use Log;
use Illuminate\Http\Request;
use App\Http\Controllers\QuintypeController;
use App\Api\Bulk;
use App\Api\Config;
use App\Api\StoriesRequest;

class HomeController extends QuintypeController {

    public function index() {
        $bulk = new Bulk();
        // $config = $this->client->config();
        $fields = "id,headline,slug,url,hero-image-s3-key,hero-image-metadata,first-published-at,last-published-at,alternative,published-at,author-name,author-id,sections,story-template,summary,metadata,hero-image-attribution,cards,subheadline";
        $bulk->addRequest('top_stories', (new StoriesRequest('top'))->addParams(["limit" => 8, "fields" => $fields]));
        $bulk->addRequest('weatherstories', (new StoriesRequest('top'))->addParams(["section" => "Weather", "limit" => 3, "fields" => $fields]));
        $bulk->addRequest('videosstories', (new StoriesRequest('top'))->addParams(["section" => "Video", "limit" => 3, "fields" => $fields]));

            //   $config = $this->client->config();
            // $sections = $config['sections'];

            // $stacks= $config['stack'];
            // echo "<pre>";
            // print_r($stacks);

    // @config.stacks.select { |stack| stack["show-on-all-sections?"] || stack["show-on-locations"].try(:include?, location) }


        $bulk->addRequest('breaking_news', (new StoriesRequest('stack-115'))->addParams(["limit" => 3, "fields" => $fields]));

        $bulk->addRequest('foodhealth', (new StoriesRequest('top'))->addParams(["section" => "Food & Health", "limit" => 4, "fields" => $fields]));

        $bulk->execute($this->client);

        $a = $bulk->getResponse("top_stories");
// echo sizeof($a);
        // echo "<pre>"; print_r($a);

        return view('home', $this->toView([
                    "stories" => $bulk->getResponse("top_stories"),
                    "page" => ["type" => "home"],
                    "videos_stories" => $bulk->getResponse("videosstories"),
                    "weather_stories" => $bulk->getResponse("weatherstories"),
                    "food_storiess" => $bulk->getResponse("foodhealth"),
                    "breaking_news" => $bulk->getResponse("breaking_news")]));
    }

    public function storyview($category, $y, $m, $d, $slug) {
        $bulk = new Bulk();
        $fields = "id,headline,slug,url,hero-image-s3-key,hero-image-metadata,first-published-at,last-published-at,alternative,published-at,author-name,author-id,sections,story-template,summary,metadata,subheadline";
        $story = $this->client->storyData(array('slug' => $slug))['story'];

         // echo "<pre>";  print_r($story);
        $author_data = $this->client->author($story['author-id']);
          // echo "<pre>";print_r($author_data);
        $authorbio=strip_tags($author_data['bio']); 

        $bulk->addRequest('related_stories', (new StoriesRequest('top'))->addParams(["section" => $story["sections"][0]["name"], "limit" => 4, "fields" => $fields]));
        $bulk->execute($this->client);
        $abcd=$bulk->getResponse("related_stories");

        $pos=array_search($story['id'],$abcd);

        return view('story', $this->toView(["storyData" => $story, "page" => ["type" => "story"], "relatedstories" => $bulk->getResponse("related_stories"),
            "authordata"=>$author_data,"authorbio"=>$authorbio]));

    }

    public function sectionview($section) {

 $fields = "id,headline,slug,url,hero-image-s3-key,hero-image-metadata,first-published-at,last-published-at,alternative,published-at,author-name,author-id,sections,story-template,summary,metadata,hero-image-attribution,cards,subheadline";

        $config = $this->client->config();
        $sections = $config['sections'];
        $cur_section = $sections[array_search($section, array_column($sections, 'slug'), true)];
        $params = array('story-group' => 'top', 'section' => $cur_section['name'], 'limit' => 8, "fields" => $fields);
        $stories = $this->getStories($params);
        // echo"<pre>";   print_r($stories);    
         // echo $cur_section['name'];
        if ($cur_section['name'] != 'Inquiring Minds')
            return view('section', $this->toView(["section" => $cur_section, "page" => ["type" => "section"], "section_stories" => $stories, "params" => $params]));
        else
            return view('podcasts', $this->toView(["section" => $cur_section, "page" => ["type" => "section"], "section_stories" => $stories, "params" => $params]));
    }

    public function searchview(Request $request) {
        //  return view('search', $this->toView([]));
          $fields = "id,headline,slug,url,hero-image-s3-key,hero-image-metadata,first-published-at,last-published-at,alternative,published-at,author-name,author-id,sections,story-template,summary,metadata,hero-image-attribution,cards,subheadline";
        $query = $request->q;
        $searchedstories = $this->searchStories(array('q' => $query, 'size' => 7, "fields" => $fields));
        $searchsize=sizeof($searchedstories);


        if ($searchsize < 1)
        return view('noresults');
        else
            return view('search', $this->toView(["searchresults" => $searchedstories, "page" => ["type" => "search"], "term" => $query]));



    }

    public function tagsview(Request $request) {
        //$tag = $request->tag;
        //  print_r($request->topic);
         $fields = "id,headline,slug,url,hero-image-s3-key,hero-image-metadata,first-published-at,last-published-at,alternative,published-at,author-name,author-id,sections,story-template,summary,metadata,hero-image-attribution,cards,subheadline";
        $a = explode("/", $_SERVER['REQUEST_URI']);
        $tag = $a[sizeof($a) - 1];
        $tagStories = $this->getStories(array('story-group' => 'top', 'tag' => $tag, 'limit' => 7,"fields" => $fields));
        return view('tags', $this->toView(["tagresults" => $tagStories, "page" => ["type" => "topic"], "tag" => $tag]));
    }

    public function aboutview() {
        return view('about', $this->toView(["page" => ["type" => "about"]]));
    }

    public function privacyview() {
        return view('privacy', $this->toView(["page" => ["type" => "privacy"]]));
    }

    public function termsview() {
        return view('terms', $this->toView(["page" => ["type" => "terms"]]));
    }

      public function errorview() {
        return view('404');
    }

}

?>
