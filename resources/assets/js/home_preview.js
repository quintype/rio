var template = require("./templates").home_body;

module.exports = function () {
    window.addEventListener("message", function (event) {
        var story = event.data['story'];
        if (story) {
          var defaultSection = {'id' : '0', 'name' : 'Section Name'};
          var html = template.render({
              stories: Array(20).fill(story),
              videos: {"stories" : Array(20).fill(story), "section" : defaultSection},
              entertainment: {"stories" : Array(20).fill(story), "section" : defaultSection},
              most_popular: Array(20).fill(story),
              international: {"stories" : Array(20).fill(story), "section" : defaultSection},
              weather_stories: Array(20).fill(story),
              breaking_news: Array(20).fill(story),
              food_stories: Array(20).fill(story),
              banners: Array(20).fill(story),
              preview: true
          })
          $("#home-container").html(html);
        }
    });
}
