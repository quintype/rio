var Twig = require("twig");
    require("jquery.marquee");
    require("jquery");

global.app = {
    initQtReady: require("./qt_ready"),
    startHomePreview: require("./home_preview"),
    startStoryPreview: require("./story_preview"),
    loadMoreStories: require("./load_more"),
    subSectionStories: require("./sub_section_stories"),
    analytics: require("./analytics.js"),
    videos: require("./videos.js")
};

// require("./jquery.min");
require("./responsiveslides.min");

$(document).ready(function(){

  $(".js-menu").click(function(event){
   event.stopPropagation();
    $(".menuitems").slideToggle("slow");
  });

  $('#search_init,#search_init1').click(function (event) {
    event.stopPropagation();
    $(".nav-search").toggle();
  });

  $(document).click( function(){
    $(".menuitems").hide();
  });

  $('.js-photo-story-slider').slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      arrows: true,
      autoplay: true
  });

  $('.js-story-slider').slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      arrows: true
  });

  $('.js-breakingnews-marquee').marquee({
      duration: 19000,
      startVisible: true,
      duplicated: true,
      pauseOnHover: true
  });

});
