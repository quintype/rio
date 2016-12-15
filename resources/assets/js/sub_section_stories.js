var _ = require("lodash");

var storiesTemplate = require("../../views/stories_popup.twig");
var alreadyFetchedSections = [];
var xhr;

function makeRequest(params, start, callback) {
    xhr = $.getJSON("/api/v1/stories", _.merge(params, {
            offset: start
        }), (response) => callback(response["stories"]))
        .done(function(response) {
            /*if (alreadyFetchedSections.indexOf(params['section-id']) == -1) {
                alreadyFetchedSections[params['section-id']] = response["stories"];
            }*/
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

   makeRequest(params, storiesLoaded, function(stories) {
     storiesLoaded += limit
     if (_.size(stories) > 0) {
         targetElement.append(renderStories(stories));
     }
   });
}

function subSectionStories(parentElement, triggerElement, targetElement, params) {

  //keep first sub tab loaded initially for every main tab
  setTimeout(function () {
      $(parentElement).find(triggerElement).filter(':nth-child(1)').trigger('mouseenter');
  },500);

  $(triggerElement).on("mouseenter", function() {
    var storiesContainer = $(this).parents(parentElement).find(targetElement);
    var sectionId = $(this).attr("data-section-id");

    //check if this section story container is exist(check if already Fetched Sections)
    if(storiesContainer.find('[data-section-container="'+sectionId+'"]').length > 0) {
      //show only this section story contatiner
      storiesContainer.find('[data-section-container]').hide();
      storiesContainer.find('[data-section-container="'+sectionId+'"]').show();

    } else {
      //merge tjos 'section-id' property to params object
      params = _.merge(params, {
          "section-id": sectionId
      });

      //create wrapper container for each section and append
      storiesContainer.append($('<div>', {'data-section-container': sectionId}));
      var strSecLoader = storiesContainer.find('[data-section-container="'+sectionId+'"]');

      //load stories in this section container
      loadStories(params, strSecLoader);

      //show only this section story container
      storiesContainer.find('[data-section-container]').hide();
      storiesContainer.find('[data-section-container="'+sectionId+'"]').show();
    }

    //keep last mouse over sub tab active
    $(this).parents(parentElement).find(triggerElement).removeClass('active');
    $(this).addClass('active');

  });
}

module.exports = subSectionStories;
