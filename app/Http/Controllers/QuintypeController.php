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
      "config" => $this->config()
    ], $args);
  }

  public function getStories($args = null){
    return $this->client->stories($args);
  }

  public function config($args = null) {
    return array_merge($this->client->config(), config("quintype"));
  }

  public function bulkStories($args = null){
    $bulk = new Bulk();
    $bulk->addRequest('stories', (new StoriesRequest('top'))->addParams($args));
    $bulk->execute($this->client);
    return $bulk->getResponse("stories");
  }

  public function searchStories($args = null){
    return $this->client->search($args);
  }

}
