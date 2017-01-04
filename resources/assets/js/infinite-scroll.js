var _ = require("lodash");
var template = require("./templates").story;

var $doc = $(document);
var $win = $(window);
var firstStoryId;
var excludeStoryIds =[];

function nextStoryLoader(params,start,callback,excludeStoryIds) {
  params['limit'] = 1;
  var notStoryContentIds = _.toString(excludeStoryIds);
  var fields = 'id,headline,slug,hero-image-s3-key,hero-image-metadata,first-published-at,last-published-at,alternative,published-at,author-name,author-id,sections,story-template,summary,metadata,hero-image-attribution,cards,subheadline,authors,tags';
  $.getJSON("/api/v1/stories", _.merge({}, params, {offset: start, fields: fields,'not-story-content-ids': notStoryContentIds}),
    (response) => callback(_.first(response["stories"])))
}

function renderStory(story) {
  var html = template.render({storyData: story});
  excludeStoryIds.push(story.id);
  $('.js-stories-container').append(html);
}

var storiesLoaded = 0;

var scrollFn = function(e) {
  if ($doc.height() - $win.height() == $win.scrollTop()) {
    storiesLoaded += 1
    nextStoryLoader({}, storiesLoaded, renderStory,excludeStoryIds);
  }
}

var scrollHandler = _.throttle(scrollFn, 300);


$win.scroll(scrollHandler);

function infiniteScroll() {
  firstStoryId = $('.js-story-header-content').data("storyId");
  excludeStoryIds.push(firstStoryId);
  $('.js-story-header-content:last').bind('inview', function(e, isInView) {
    if (!isInView) {
      return;
    }

    var $el = $(this);
    var headline = $el.find('.js-story-headline').text();
    var url = '/' + $el.data("storySlug");
    if (history.replaceState) {
      history.replaceState({}, headline, url);
      $(document).prop('title', headline);
    }
  });
}


module.exports  = {
  infiniteScroll: infiniteScroll
}
