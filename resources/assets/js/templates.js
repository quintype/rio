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

require("../../../resources/views/story/elements/socialshare.twig");
require("../../../resources/views/story/story_elements.twig");
require("../../../resources/views/story/story_tags.twig");
require("../../../resources/views/story/elements/text.twig");
require("../../../resources/views/story/elements/youtube.twig");
require("../../../resources/views/story/elements/location.twig");
require("../../../resources/views/story/elements/bigfact.twig");
require("../../../resources/views/story/elements/images.twig");
require("../../../resources/views/story/elements/questionandanswer.twig");


var TEMPLATES = {
    "home_body": require("../../../resources/views/home/body.twig"),
    "story": require("../../../resources/views/home/story.twig"),
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
        return summary.substring(0, 112);
    }
});


Twig.extendFunction("get_logo", function(key) {
   var data =  {"Atlantic":
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
   };

    if (data[key]) {
        return data[key][0]['Logo'];
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
