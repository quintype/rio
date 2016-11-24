var Twig = require("twig");
    require("jquery.marquee");

global.app = {
    initQtReady: require("./qt_ready"),
    startHomePreview: require("./home_preview"),
    startStoryPreview: require("./story_preview"),
    loadMoreStories: require("./load_more"),
    analytics: require("./analytics.js"),
    videos: require("./videos.js")
};

// require("./jquery.min");
require("./responsiveslides.min");


$(document).ready(function(){

  $(".js-menu").click(function(event){
   event.stopPropagation();
    $(".menuitems").toggle();
  });

  $('#search_init,#search_init1').click(function (event) {
    event.stopPropagation();
    $(".nav-search").toggle();
  });

  $(document).click( function(){
    $(".menuitems").hide();
  });

  $(function() {
    $(".rslides").responsiveSlides({
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
