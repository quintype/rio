var _ = require("lodash");

var storiesTemplate = require("../../views/story/elements/stories_list.twig");

var foo = false;
function loadStories(params, start, callback) {
  $.getJSON("/api/v1/stories", _.merge({}, params, {offset: start}), (response) => callback(response["stories"]))  
}

function renderStories(stories) {
 return storiesTemplate.render({stories: stories}); 
}

function loadMore(button, target, params) {  
  var limit = params.limit || 20;
  var storiesLoaded = limit;

  target = $(target);
  $(button).click(function() {
    loadStories(params, storiesLoaded, function(stories) {
      storiesLoaded += limit
      if(_.size(stories) == 0) {
        $(button).hide();
      } else {
        target.append(renderStories(stories));
      }      
    });    
  });
}

module.exports = loadMore;