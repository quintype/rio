<?php

namespace App\Http\Controllers;

use Log;
use App\Http\Controllers\QuintypeController;
use App\Api\Bulk;
use App\Api\MenuItem;
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


    

    public function sectionview() {
        $a = explode("/", $_SERVER['REQUEST_URI']);
        //echo sizeof($a);
        $slug = $a[sizeof($a) - 1];
        echo $slug;

        //$story_data = new QuintypeClient();

        return view('section', $this->toView([]));
    }

    public function podcastview() {
        $a = explode("/", $_SERVER['REQUEST_URI']);
        //echo sizeof($a);
        $slug = $a[sizeof($a) - 1];
        echo $slug;

        //$story_data = new QuintypeClient();

        return view('podcasts', $this->toView([]));
    }

    public function searchview() {
        return view('search', $this->toView([]));
    }
        public function tagsview() {    
        return view('tags', $this->toView([]));
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