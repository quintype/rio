<?php

namespace App\Http\Controllers;
use Api;
use Meta;
use Seo;

class QuintypeController extends Controller
{
    public function __construct()
    {
        $this->client = new Api(config('quintype.api-host'));
        $this->config = array_merge($this->client->config(), config('quintype'));
        $this->meta = new Meta();
        $this->seo = new Seo($this->config);
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

    protected function getAverageRating($story) {
      if(sizeof($story['votes']) > 0) {
         $numerator = 0; $denominator = 0;
         foreach ($story['votes'] as $key => $value) {
           $numerator += ($key * $value);
           $denominator += $value;
         }
         return round(($numerator) / ($denominator), 1);
       }
     }
}
