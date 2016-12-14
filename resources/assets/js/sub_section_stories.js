var _ = require("lodash");

var storiesTemplate = require("../../views/stories_popup.twig");
var alreadyFetchedSections = [];
var xhr;

function makeRequest(params, start, callback) {
    xhr = $.getJSON("/api/v1/stories", _.merge(params, {
            offset: start
        }), (response) => callback(response["stories"]))
        .done(function(response) {
            if (alreadyFetchedSections.indexOf(params['section-id']) == -1) {
                alreadyFetchedSections[params['section-id']] = response["stories"];
            }
        });
}

function renderStories(stories) {
    return storiesTemplate.render({
        stories: stories
    });
}

function loadStories(params, targetElement) {
    var limit = params.limit || 20;
    var storiesLoaded = params.offset || 0;

    if (!alreadyFetchedSections[params['section-id']]) {
        makeRequest(params, storiesLoaded, function(stories) {
            storiesLoaded += limit
            if (_.size(stories) > 0) {
                targetElement.append(renderStories(stories));
            }
        });
    } else {
        targetElement.append(renderStories(alreadyFetchedSections[params['section-id']]));
    }
}

function subSectionStories(parentElement, triggerElement, targetElement, params) {
    var trigEle = $(triggerElement);
    var storyLoader;
    var timeoutId;

      //keep first sub tab loaded initially
      setTimeout(function () {
          $(parentElement).find(triggerElement+':first-child').trigger('mouseenter');
      },500);

    trigEle.on("mouseenter", function() {
      storyLoader = $(this).parents(parentElement).find(targetElement);
      storyLoader.html("");
      storyLoader.show();

      var sectionId = $("#" + this.id).attr("data-sectionId");

      if (sectionId) {
          params = _.merge(params, {
              "section-id": sectionId
          });

          timeoutId = setTimeout(function() {
              loadStories(params, storyLoader);
          }, 300);
      }

      //keep last mouse over sub tab active
      $(this).parents(parentElement).find(trigEle).removeClass('active');
      $(this).addClass('active');
    });
}

module.exports = subSectionStories;
