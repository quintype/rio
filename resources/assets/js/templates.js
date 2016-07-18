var _ = require("lodash");
var Twig = require("twig");

var FocusedImage = require("quintype-js").FocusedImage;

global.transformTemplates = function(x) {
  return _.extend(x, {
    id: x.id.replace(/resources\/views\//, "").replace(/.twig/, ''),
    path: x.path.replace(/resources\/views\//, "").replace(/.twig/, '')
  })
};


var TEMPLATES = {
  "home_body": require("../../../resources/views/home/body.twig"),
  "story": require("../../../resources/views/home/story.twig")
};

Twig.extendFunction("focusedImageUrl", function(slug, config, aspectRatio, metadata, options) {
  var cdn = global.qtConfig["image-cdn"];
  var image = new FocusedImage(slug, metadata);
  return cdn + "/" + image.path(aspectRatio, options);
});

Twig.extendFunction("assetPath", function(file, config) {
  return global.qtConfig["image-cdn"] + "/" + global.qtConfig["publisher-name"] +  "/" + "assets" + file
});

module.exports = window.ooga = TEMPLATES;
