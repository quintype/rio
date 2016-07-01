<?php

namespace App\Http\Controllers;

use App\Http\Controllers\QuintypeController;

class PreviewController extends QuintypeController
{
    public function home()
    {
        return view('preview_home', $this->toView([]));
    }
    
     public function storyview()
    {
        return view('story', $this->toView([]));
    }
    
    
}
