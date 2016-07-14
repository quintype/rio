global.app = {
  initQtReady: require("./qt_ready"),
  startHomePreview: require("./home_preview"),
};

require ("./jquery.bxSlider");
// require ("./slick.min");

function assetPath(file, config) {
  return config['image-cdn'] + "/" + config['publisher-name'] +  "/" + "assets" + file
}

//function focusedImageUrl($slug, $aspectRatio, $metadata, $opts) {
//    $cdn = config("quintype.image-cdn");
//    $image = new FocusedImage($slug, $metadata);
//    return $cdn . "/" . $image->path($aspectRatio, $opts);
//}
