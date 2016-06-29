<?php

namespace App\Http\Controllers;

use Log;
use App\Http\Controllers\QuintypeController;

use App\Api\Bulk;
use App\Api\StoriesRequest;

class HomeController extends QuintypeController
{
    public function index()
    {
        $bulk = new Bulk();
        $bulk->addRequest('top_stories', (new StoriesRequest('top')));
        $bulk->addRequest('politics', (new StoriesRequest('top'))->addParams(["section" => "Politics"]));
        $bulk->execute($this->client);
        return view('home', $this->toView(["stories" => $bulk->getResponse("top_stories")]));
    }
}