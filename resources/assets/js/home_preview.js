var template = require("./templates").home_body;

module.exports = function () {
    window.addEventListener("message", function (event) {
        var story = event.data['story'];
        if (story) {
            document.getElementById("home-container").innerHTML = template.render({
                stories: Array(20).fill(story),
                videos: Array(20).fill(story),
                entertainment: Array(20).fill(story),
                most_popular: Array(20).fill(story),
                international: Array(20).fill(story),
                weather_stories: Array(20).fill(story),
                breaking_news: Array(20).fill(story),
                food_stories: Array(20).fill(story),
                banners: Array(20).fill(story),
                preview: true
            });
        }
    });
}
