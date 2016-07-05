<?php

namespace App\Http\Controllers;

use App\Http\Controllers\QuintypeController;

class PreviewController extends QuintypeController {

    public function home() {
        return view('preview_home', $this->toView([]));
    }

    public function storyview() {
    	$a=explode("/",$_SERVER['REQUEST_URI']);
    	//echo sizeof($a);
    	$slug=$a[sizeof($a)-1];
    	echo $slug;
        
         //$story_data = new QuintypeClient();
         
        return view('story', $this->toView([]));
    }

}
