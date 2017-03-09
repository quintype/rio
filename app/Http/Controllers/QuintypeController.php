<?php

namespace App\Http\Controllers;
use Api;
use Meta;
use Seo;

class QuintypeController extends Controller
{
    public function __construct()
    {
        $this->client = new Api(getQuintypeAPIHost(Request()->getHost()));
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
        "queryParams" => $_GET
      ], $args);

    }

    protected function getAverageRating($story) {
      if(sizeof($story['votes']) > 0) {
         $numerator = 0; $noOfVoters = 0;
         foreach ($story['votes'] as $key => $value) {
           $numerator += ($key * $value);
           $noOfVoters += $value;
         }
         $averageRatingValue = round(($numerator) / ($noOfVoters), 1);
         $ratingPercentValue = ($averageRatingValue * 100)/(5);
         $getRatingValues = array("average-rating"=> $averageRatingValue , "rating-percentage"=> $ratingPercentValue, "rater-count"=>$noOfVoters);
         return $getRatingValues;
       }
     }

     public function imageSizeCalc($story) {
       $imagePath = $this->config['cdn-image'].'/'.$story['hero-image-s3-key'];
       if (strpos($imagePath,'http:') === false) {
         $imagePath = 'http://'.$imagePath;
       }
       list($imageWidth, $imageHeight) = getimagesize($imagePath);
       $story['hero-image-metadata']['height'] = $imageHeight;
       $story['hero-image-metadata']['width'] = $imageWidth;
       return $story['hero-image-metadata'];
     } 
}
