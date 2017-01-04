var _ = require("lodash");

function setup() {
  $('.story-view').bind('inview', trackStory);
  $('.story-element-view').bind('inview', trackStoryElement);
}

function trackStory(event, visible) {
  if (visible == true) {
    var storyContentId = event.target.dataset['storyContentId'];
    qlitics('track', 'story-view', {
      'story-content-id': storyContentId,
    });
    $('.story-view').unbind('inview');
  }
}

function trackStoryElement(event, visible) {
  if (visible) {
    var attributes = {
      'story-content-id': $('.story-view').data('story-content-id'),
      'story-version-id': $('.story-view').data('story-version-id'),
      'card-content-id': event.target.dataset['cardContentId'],
      'card-version-id': event.target.dataset['cardVersionId'],
      'story-element-id': event.target.dataset['storyElementId'],
      'story-element-type': event.target.dataset['storyElementType']
    }
    qlitics('track', 'story-element-view', attributes);
    $('div.author').bind('inview', unbindStoryElementEvent);
  }
}

function trackYouTubeStoryElement(event) {
  var iframe = event.target.getIframe(),
      action;

  var storyElement = $(iframe).closest('.story-element-view');
  var attributes = {
    'story-content-id': $('.story-view').data('story-content-id'),
    'story-version-id': $('.story-view').data('story-version-id'),
    'card-content-id': storyElement.data('card-content-id'),
    'card-version-id': storyElement.data('card-version-id'),
    'story-element-id': storyElement.data('story-element-id'),
    'story-element-type': storyElement.data('story-element-type')
  }

  switch(event.data) {
  case YT.PlayerState.PLAYING: action = 'play';     break;
  case YT.PlayerState.PAUSED:  action = 'pause';    break;
  case YT.PlayerState.ENDED:   action = 'complete'; break;
  }

  if (action) {
    attributes['story-element-action'] = action;
    qlitics('track', 'story-element-view', attributes);
  }
}

function unbindStoryElementEvent(event, visible) {
  if (visible) {
    $('.story-element-view').unbind('inview');
  }
}

module.exports = {
  setup: setup,
  trackYouTubeStoryElement: trackYouTubeStoryElement
};
