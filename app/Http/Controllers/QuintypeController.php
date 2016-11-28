<?php

namespace App\Http\Controllers;
use Api;

class QuintypeController extends Controller
{
    public function __construct()
    {
        $this->client = new Api(config('quintype.api-host'));
        $this->config = array_merge($this->client->config(), config('quintype'));
    }

    public function toView($args)
    {
        return array_merge([
        "config" => $this->config,
        "client" => $this->client,
        "nestedMenuItems" => $this->client->prepareNestedMenu($this->config["layout"]["menu"]),
        "breaking_news" => $this->client->getBreakingNews(['limit' => 5, 'fields' => 'headline,metadata']),
      ], $args);
    }
}
