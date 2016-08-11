var _ = require("lodash");
require('./jquery.inview');

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

    if (event.target.dataset['storyElementAction']) {
      attributes['story-element-action'] = event.target.dataset['storyElementAction'];
    }

    qlitics('track', 'story-element-view', attributes);
  }
}

module.exports = setup;
