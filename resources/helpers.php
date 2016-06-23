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
