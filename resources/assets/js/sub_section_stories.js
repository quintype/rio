var _ = require("lodash");

var storiesTemplate = require("../../views/stories_popup.twig");

function makeRequest(params, start, callback) {
  $.getJSON("/api/v1/stories", _.merge(params, {
      offset: start
  }),
  function(response){
    callback(response["stories"]);
  })
  .done(function() {
    //console.log( "success" );
  })
  .fail(function() {
    //console.log( "error" );
  })
  .always(function() {
    //console.log( "complete" );
  });
}

function renderStories(stories) {
    return storiesTemplate.render({
        stories: stories
    },function(){
      console.log('da');
    });
}

function loadStories(params, targetElement) {
  var limit = params.limit || 20;
  var storiesLoaded = params.offset || 0;

  makeRequest(params, storiesLoaded, function(stories) {
    storiesLoaded += limit
    if (_.size(stories) > 0) {
      targetElement.empty();
      targetElement.append(renderStories(stories));
    } else {
      targetElement.empty();
      targetElement.append('<h6 style="color:#fff;">There are no stories in this section.</h6>'); //todo: this msg needs to be templatized
    }
  });

  //animate stories while loading first time
  setTimeout(function () {
    targetElement.addClass('loaded');
  },1000);
}

function subSectionStories(parentElement, triggerElement, targetElement, params) {

  //keep first sub tab loaded initially for every main tab
  setTimeout(function () {
    $(parentElement).find(triggerElement).filter(':nth-child(1)').trigger('mouseenter');
  },500);

  $(triggerElement).on("mouseenter", function() {
    var storiesContainer = $(this).parents(parentElement).find(targetElement);
    var sectionId = $(this).attr("data-section-id");
    var strSecLoader;

    //check if this section story container is exist(check if already Fetched Sections)
    if(storiesContainer.find('[data-section-container="'+sectionId+'"]').length > 0) {
      //show only this section story contatiner
      storiesContainer.find('[data-section-container]').removeClass('active');
      storiesContainer.find('[data-section-container="'+sectionId+'"]').addClass('active');

    } else {
      //merge tjos 'section-id' property to params object
      params = _.merge(params, {
          "section-id": sectionId
      });

      //create wrapper container and loader for each section and append it in realative section
      $('<div>', {'class': 'section-container', 'data-section-container': sectionId})
      .append($('<div>', {'class': 'loader', 'data-section-loader': sectionId}))
      .appendTo(storiesContainer);
      strSecLoader = storiesContainer.find('[data-section-container="'+sectionId+'"]');

      //load stories in this section container
      loadStories(params, strSecLoader);

      //show only this section story container
      storiesContainer.find('[data-section-container]').removeClass('active');
      storiesContainer.find('[data-section-container="'+sectionId+'"]').addClass('active');
    }

    //keep last mouse over sub tab active
    $(this).parents(parentElement).find(triggerElement).removeClass('active');
    $(this).addClass('active');

  });
}

module.exports = subSectionStories;
