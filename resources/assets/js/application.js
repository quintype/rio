var Twig = require("twig");
    require("jquery.marquee");

global.app = {
    initQtReady: require("./qt_ready"),
    startHomePreview: require("./home_preview"),
    startStoryPreview: require("./story_preview"),
    loadMoreStories: require("./load_more"),
    subSectionStories: require("./sub_section_stories"),
    analytics: require("./analytics.js"),
    videos: require("./videos.js"),
    infiniteScroll: require("./infinite-scroll")
};

// require("./jquery.min");
require("./responsiveslides.min");


$(document).ready(function(){

  global.app.infiniteScroll.infiniteScroll();

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

  $(function() {
    $(".js-image-slide-show").responsiveSlides({
      auto: false,
      // pager: true,
      nav: true,
      speed: 500,
      maxwidth: 800,
      namespace: "centered-btns"
  });


  $("#slider").responsiveSlides({
    auto: true,
    // pager: true,
    // nav: true,
    speed: 500
    // namespace: "centered-btns"
    });
  });

  $('.js-breakingnews-marquee').marquee({
    duration: 19000,
    startVisible: true,
    duplicated: true,
    pauseOnHover: true
  });

});
