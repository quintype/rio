<?php

use App\Api\FocusedImage;

function assetPath($file, $config = null)
{
    $cdn = config("quintype.asset-host");
    return $cdn . elixir($file, config("quintype.publisher-name") . "/assets");
}

function focusedImageUrl($slug, $cdn, $aspectRatio, $metadata, $opts) {
  $image = new FocusedImage($slug, $metadata);
  return $cdn . "/" . $image->path($aspectRatio, $opts);
}

