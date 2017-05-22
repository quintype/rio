var template = require("./templates").story;

function postStoryPageRender() {
  global.app.slickSlider.photoStory();
  global.app.slickSlider.imageGallery();
  global.app.videos.load();
  global.app.videos.init();
  $('.js-table-element').each(function() {
    var $element = $(this);
    global.app.handleFileSelect($element);
  })
}

module.exports = function () {
  window.addEventListener("message", function (event) {
    var story = event.data['story'];
    if (story) {
      document.getElementById("story-container").innerHTML = template.render({
        storyData: story,
        food_stories: Array(20).fill(story),
        preview: true,
        config: qtConfig
      });
      postStoryPageRender();
    }
  });
}
