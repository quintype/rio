var _ = require("lodash");

var storiesTemplate = require("../../views/shared/stories_list.twig");
var storiesTemplateHorizontal = require("../../views/shared/stories_list_horizontal.twig");

var foo = false;
function loadStories(params, start, callback, api) {
  if(api === 'search'){
    $.getJSON("/api/v1/search", _.merge({}, params, {offset: start}), (response) => callback(response['results']["stories"]))
  } else {
    $.getJSON("/api/v1/stories", _.merge({}, params, {offset: start}), (response) => callback(response["stories"]))
  }
}

function renderStories(stories, api) {
  if(api === 'search'){
    return storiesTemplateHorizontal.render({stories: stories});
  } else {
    return storiesTemplate.render({stories: stories});
  }
}

function loadMore(button, target, params, api = '') {
  var limit = params.limit || 20;
  var storiesLoaded = limit;

  target = $(target);
  $(button).click(function() {
    loadStories(params, storiesLoaded, function(stories) {
      storiesLoaded += limit
      if(_.size(stories) == 0) {
        $(button).hide();
      } else {
        target.append(renderStories(stories, api));
      }
    }, api);
  });
}

module.exports = loadMore;
