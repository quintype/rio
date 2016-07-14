<?php

use App\Api\FocusedImage;

function assetPath($file)
{
    $cdn = config("quintype.asset-host");
    return $cdn . elixir($file, config("quintype.publisher-name") . "/assets");
}

function focusedImageUrl($slug, $aspectRatio, $metadata, $opts) {
    $cdn = config("quintype.image-cdn");
    $image = new FocusedImage($slug, $metadata);
    return $cdn . "/" . $image->path($aspectRatio, $opts);
}


function get_logo($key) {
 
  $data =' {"Atlantic":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/Atlantic-Logo.png",
  "URL": "http://www.theatlantic.com"}],
"CIR":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/CIR-Logo.png",
  "URL": ""}],
"CityLab":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/CityLab-Logo.png",
  "URL": "http://www.citylab.com"}],
"ClimateDesk":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/ClimateDesk_logo.png",
  "URL": "http://climatedesk.org"}],
"Fusion":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/Fusion-Logo-Horiztonal.png",
  "URL": "http://fusion.net"}],
"Grist":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/Grist-Logo.png",
  "URL": "https://grist.org"}],
"Guardian":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/Guardian-Logo.png",
  "URL": "https://www.theguardian.com"}],
"HuffPost":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/HuffPost-US-4xLogos.png",
  "URL": "http://www.huffingtonpost.in"}],
"Medium":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/Medium-Logo.png",
  "URL": "https://medium.com"}],
"MotherJones":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/MotherJones-Logo-Horiztonal.png",
  "URL": "http://www.motherjones.com"}],
"NewRepublic":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/NewRepublic-Logo.png",
  "URL": "https://newrepublic.com"}],
"NewsWeek":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/Newsweek-Logo.png",
  "URL": "http://www.newsweek.com"}],
"Reveal":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/Reveal-Logo-1.png",
  "URL": "https://www.revealnews.org"}],
"Slate":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/Slate-Logo.png",
  "URL": "http://www.slate.com"}],
"Wired":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/Wired-Logo.png",
  "URL": "http://www.wired.com"}]
}';
 

$s =  json_decode($data, true);
//echo "<pre>";
//print_r($s);

if($key!="")
echo $s["$key"][0]['Logo'];
else
echo "";
}