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
        $bulk->addRequest('top_stories', (new StoriesRequest('top'))->addParams(["limit" => 8]));
        $bulk->addRequest('weatherstories', (new StoriesRequest('top'))->addParams(["section" => "Weather", "limit" => 3]));
        $bulk->addRequest('videosstories', (new StoriesRequest('top'))->addParams(["section" => "Video", "limit" => 3]));
        $bulk->addRequest('breaking_news', (new StoriesRequest('breaking-news'))->addParams(["section" => "Video", "limit" => 3]));

        $bulk->addRequest('foodhealth', (new StoriesRequest('top'))->addParams(["section" => "Food & Health", "limit" => 3]));
        $bulk->execute($this->client);
        return view('home', $this->toView(["stories" => $bulk->getResponse("top_stories"), "videos_stories" => $bulk->getResponse("videosstories"), "weather_stories" => $bulk->getResponse("weatherstories"),
                    "food_stories" => $bulk->getResponse("foodhealth"), "breaking_news" => $bulk->getResponse("breaking_news")]));
    }

    public function storyview($category, $y, $m, $d, $slug) {
        $bulk = new Bulk();
        $story = $this->client->storyData(array('slug' => $slug))['story'];

       // echo "<pre>";
       // print_r($story);
        $bulk->addRequest('foodhealth', (new StoriesRequest('top'))->addParams(["section" => "Food & Health", "limit" => 3]));
        $bulk->execute($this->client);


        return view('story', $this->toView(["storyData" => $story, "food_stories" => $bulk->getResponse("foodhealth")]));

        // return view('story', $this->toView([]));
    }

    public function sectionview($section) {
        $config = $this->client->config();
        $sections = $config->sections();
        $cur_section = $sections[array_search($section, array_column($sections, 'slug'), true)];
        $stories = $this->getStories(array('story-group' => 'top', 'section' => $cur_section['name'], 'limit' => 8));
   //  echo"<pre>";   print_r($stories);
        if($cur_section['name']!='Inquiring Minds')
        return view('section', $this->toView(["section" => $cur_section, "section_stories" => $stories]));
        else
        return view('podcasts', $this->toView(["section" => $cur_section, "section_stories" => $stories]));
    }

 

    public function searchview() {
        return view('search', $this->toView([]));
    }

    public function tagsview(Request $request) {
        //$tag = $request->tag;
      //  print_r($request->topic);
         $a = explode("/", $_SERVER['REQUEST_URI']);
         $tag = $a[sizeof($a) - 1];
        $tagStories = $this->getStories(array('story-group' => 'top', 'tag' => $tag, 'limit' => 7));
        return view('tags', $this->toView(["tagresults" => $tagStories, "tag" => $tag]));
    }

    public function aboutview() {
        return view('about', $this->toView([]));
    }

    public function privacyview() {
        return view('privacy', $this->toView([]));
    }

    public function termsview() {
        return view('terms', $this->toView([]));
    }

}

?>