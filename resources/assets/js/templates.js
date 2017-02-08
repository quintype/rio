var _ = require("lodash");
var Twig = require("twig");
global.Twig = Twig;
var FocusedImage = require("quintype-js").FocusedImage;

global.transformTemplates = function (x) {
    return _.extend(x, {
        id: x.id.replace(/resources\/views\//, "").replace(/.twig/, ''),
        path: x.path.replace(/resources\/views\//, "").replace(/.twig/, '')
    })
};

//story
require("../../../resources/views/story/header-card.twig");
require("../../../resources/views/story/cards.twig");
require("../../../resources/views/story/card.twig");
require("../../../resources/views/story/tags.twig");
require("../../../resources/views/story/byline.twig");
require("../../../resources/views/story/ads.twig");

require("../../../resources/views/ads/Story_LB1_placeholder.twig");
require("../../../resources/views/ads/Story_Mrec1_placeholder.twig");

//templates
require("../../../resources/views/story/templates/photo.twig");
require("../../../resources/views/story/templates/video.twig");
require("../../../resources/views/story/templates/recipe.twig");
require("../../../resources/views/story/templates/default.twig");

//shared
require("../../../resources/views/shared/socialshare.twig");

//elements
require("../../../resources/views/story/elements/composite.twig");
require("../../../resources/views/story/elements/text.twig");
require("../../../resources/views/story/elements/youtube-video.twig");
require("../../../resources/views/story/elements/jsembed.twig");
require("../../../resources/views/story/elements/image.twig");
require("../../../resources/views/story/elements/slideshow.twig");
require("../../../resources/views/story/elements/gallery.twig");
require("../../../resources/views/story/elements/soundcloud-audio.twig");
require("../../../resources/views/story/elements/polltype.twig");
require("../../../resources/views/story/elements/references.twig");
require("../../../resources/views/story/elements/title.twig");
require("../../../resources/views/story/elements/external-file.twig");

//element subtypes
require("../../../resources/views/story/elements/elements_subtypes/bigfact.twig");
require("../../../resources/views/story/elements/elements_subtypes/blockquote.twig");
require("../../../resources/views/story/elements/elements_subtypes/blurb.twig");
require("../../../resources/views/story/elements/elements_subtypes/jwplayer.twig");
require("../../../resources/views/story/elements/elements_subtypes/q-and-a.twig");
require("../../../resources/views/story/elements/elements_subtypes/quote.twig");
require("../../../resources/views/story/elements/elements_subtypes/summary.twig");

var TEMPLATES = {
    "home_body": require("../../../resources/views/home/body.twig"),
    "story": require("../../../resources/views/story/story.twig"),
    "story_tag": require("../../../resources/views/home/story_tags.twig")
};

Twig.extendFunction("focusedImageUrl", function (slug, config, aspectRatio, metadata, options) {
    var cdn = global.qtConfig["image-cdn"];
    var image = new FocusedImage(slug, metadata);
    return cdn + "/" + image.path(aspectRatio, options);
});

Twig.extendFunction("assetPath", function (file, config) {
    return global.qtConfig["image-cdn"] + "/" + global.qtConfig["publisher-name"] + "/" + "assets" + file
});

Twig.extendFunction("shorthead", function (headline) {
    if (headline != null) {
        return headline.substring(0, 100);
    }
});

Twig.extendFunction("shortsummary", function (summary) {
    if (summary != null) {
      if (summary.length>100){
var description=summary.substring(0, 112)+'...';
}
else
var description= summary;

        return description;
    }
});

Twig.extendFunction("decode64", function(string) {
   return atob(string);
});

Twig.extendFunction("url", function(string) {
   return window.location.origiphotoStoryImagesn + string;
});

Twig.extendFunction("dateIsoFormat", function(date) {
   return new Date(date).toISOString();
});

Twig.extendFunction("getPhotoStoryImages", function(story) {
  console.log(story);
  var storyHeroImage = {
      'image-s3-key' : story['hero-image-s3-key'] ,
      'image-metadata' : story['hero-image-metadata'],
      'title' :  story['title']
    };

    var storyImages = [storyHeroImage];

    _.forEach(story['cards'], function(card) {
      _.forEach(card['story-elements'], function (storyElement) {
        if (storyElement['type'] === 'image') {
          storyImages.push(storyElement);
        }
      });
    });
    return storyImages;
});

Twig.extendFunction("get_logo", function(key,param) {
  var data =  {"Atlantic":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/atlantic.png",
  "URL": "http://www.theatlantic.com",
  "Partnername": "The Atlantic"
}],
"CIR":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/CIR-Logo.png",
  "URL": "",
"Partnername": ""}],
"CityLab":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/CityLab-Logo.png",
  "URL": "http://www.citylab.com",
"Partnername": "CityLab"}],
"ClimateDesk":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/ClimateDesk_logo.png",
  "URL": "http://climatedesk.org",
"Partnername": "Climate Desk"}],
"Fusion":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/Fusion-Logo-Horiztonal.png",
  "URL": "http://fusion.net",
"Partnername": "Fusion"}],
"Grist":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/Grist-Logo.png",
  "URL": "https://grist.org",
"Partnername": "Grist"}],
"Guardian":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/Guardian-Logo.png",
  "URL": "https://www.theguardian.com",
"Partnername": "The Guardian"}],
"HighCountryNews":
  [{"Logo" : "http://s3.amazonaws.com/third-party-logos/HighCountryNews.png",
  "URL": "https://www.hcn.org/",
"Partnername": "High Country News"}],
"HuffPost":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/HuffPost-US-4xLogos.png",
  "URL": "http://www.huffingtonpost.in",
"Partnername": "The Huffington Post"}],
"Medium":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/medium.png",
  "URL": "https://medium.com",
"Partnername": "Medium"}],
"MotherJones":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/MotherJones-Logo-Horiztonal.png",
  "URL": "http://www.motherjones.com",
"Partnername": "Mother Jones"}],
"NewRepublic":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/NewRepublic-Logo.png",
  "URL": "https://newrepublic.com",
"Partnername": "New Republic"}],
"NewsWeek":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/Newsweek-Logo.png",
  "URL": "http://www.newsweek.com",
"Partnername": "Newsweek"}],
"Reveal":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/Reveal-Logo-1.png",
  "URL": "https://www.revealnews.org",
"Partnername": "Reveal from the Center for Investigative Reporting"}],
"Slate":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/Slate-Logo.png",
  "URL": "http://www.slate.com",
"Partnername": "Slate"}],
"Wired":
  [{"Logo" : "https://s3.amazonaws.com/third-party-logos/Wired-Logo.png",
  "URL": "http://www.wired.com",
"Partnername": "Wired"}]
};

    if (data[key]) {
        return data[key][0][param];
    }
});

Twig.extendFunction("menuBase", function(menuType) {
    if (menuType == 'section') {
        return '/section/';
    } else {
        return '';
    }
});

Twig.extend(function (Twig) {
    Twig.Template.prototype.importFile = function (file) {
        var url, sub_template;
        if (!this.url && this.options.allowInlineIncludes) {
            sub_template = Twig.Templates.load("/" + file);

            if (!sub_template) {
                sub_template = Twig.Templates.loadRemote(url, {
                    id: file,
                    method: this.getLoaderMethod(),
                    async: false,
                    options: this.options
                });

                if (!sub_template) {
                    throw new Twig.Error("Unable to find the template " + file);
                }
            }

            sub_template.options = this.options;

            return sub_template;
        }

        url = Twig.path.parsePath(this, file);

        // Load blocks from an external file
        sub_template = Twig.Templates.loadRemote(url, {
            method: this.getLoaderMethod(),
            base: this.base,
            async: false,
            options: this.options,
            id: url
        });

        return sub_template;
    }
});

module.exports = window.ooga = TEMPLATES;
