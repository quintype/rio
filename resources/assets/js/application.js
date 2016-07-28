var Twig = require("twig");
global.app = {
    initQtReady: require("./qt_ready"),
    startHomePreview: require("./home_preview"),
    startStoryPreview: require("./story_preview"),
    loadMoreStories: require("./load_more")
};

require("./jquery.bxSlider");

// require ("./slick.min");

// $(document).ready(function(){
  
//   $('.your-class').slick({
//   dots: true,
//   infinite: true,
//   speed: 300,
//   slidesToShow: 1,
//   adaptiveHeight: true
// });

// });