<?php

namespace App\Http\Controllers;

use App\Http\Controllers\QuintypeController;

class PreviewController extends QuintypeController {

    public function home() {
        return view('preview_home', $this->toView([]));
    }

    public function storyview() {
    	$a=explode("/",$_SERVER['REQUEST_URI']);
    	$slug=$a[sizeof($a)-1];
        return view('story_preview', $this->toView([]));
    }

}
