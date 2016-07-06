<?php

namespace App\Http\Controllers;

use App\Api\QuintypeClient;
use App\Http\Controllers\Controller;
use App\Api\Bulk;
use App\Api\Config;
use App\Api\StoriesRequest;


class QuintypeController extends Controller
{
    public function __construct()
    {
        $this->client = new QuintypeClient(config("quintype.api-host"));
    }

    public function toView($args) {
        return array_merge([
            "config" => $this->client->config()
        ], $args);
    }
        
 public function getStories($args = null){
        return $this->client->stories($args);
    }
    
}