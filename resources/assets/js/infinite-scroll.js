var _ = require("lodash");
var $doc = $(document);
var $win = $(window);
var template = require("../../views/story/story.twig");

function nextStoryLoader(params, start, callback) {
  params['limit'] = 1;
  $.getJSON("/api/v1/stories", _.merge({}, params, {offset: start}),
    (response) => callback(_.first(response["stories"])))
}

function renderStory(story) {
  var html = template.render({storyData: story});
  console.log('rendering story: ' + story.id);
  $('.stories-container').append(html);
}

var storiesLoaded = 0;
var bottomOffset = 250;

var scrollFn = function(e) {
  console.log("scroll: ", e.scrollY);

  console.log($doc.height(),$win.height(),$win.scrollTop()+'ggg');
  if ($doc.height() - $win.height() >= $win.scrollTop() + bottomOffset) {
    storiesLoaded += 1
    nextStoryLoader({}, storiesLoaded, renderStory);
  }
}

var scrollHandler = _.debounce(scrollFn, 150);

$win.scroll(scrollHandler);


function infiniteScroll() {

}


module.exports  = {
  infiniteScroll: infiniteScroll
}
