<?php

namespace App\Api;

use ArrayObject;
use GuzzleHttp\Client;
use Log;

class MenuItem extends ArrayObject
{
    public function __construct($hash, $config) {
        parent::__construct($hash);
        $this->config = $config;
    }

    public function title() {
        return $this["title"];
    }

    public function url() {
        switch($this["item-type"]) {
        case "section": return "/section/" . $this["section-slug"];
        default: return "#";
        }
    }
}

class Config extends ArrayObject
{
    public function menuItems() {
        return array_map(function($menu) {
            return new MenuItem($menu, $this);
        }, $this["layout"]["menu"]);
    }
}

class Story extends ArrayObject
{

}

class QuintypeClient
{
    public function __construct($apiHost) {
        $this->client = new Client([
            "base_uri" => $apiHost
        ]);
    }

    public function getResponse($query) {
    	return json_decode($this->client->get($query)->getBody(), true);
    }

    public function config() {
        return new Config($this->getResponse("/api/v1/config"));
    }

    public function stories() {
        return array_map(function ($s) {
            return new Story($s);
        }, $this->getResponse("/api/v1/stories")["stories"]);
    }
}