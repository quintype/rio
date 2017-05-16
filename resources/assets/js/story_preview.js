var template = require("./templates").story;

function postStoryPageRender() {
  global.app.slickSlider.photoStory();
  global.app.slickSlider.imageGallery();
  global.app.videos.load();
  global.app.videos.init();
  var tableData = $('#data-table').attr('content');
  if (tableData) {
    global.app.handleFileSelect(tableData);
  }
}

module.exports = function () {
  window.addEventListener("message", function (event) {
    console.log('hi');
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
