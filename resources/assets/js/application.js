var Twig = require("twig");
    require("jquery.marquee");
    require("jquery");
    require('./vendor/jquery.inview');
    require("slick");

global.app = {
    initQtReady: require("./qt_ready"),
    startHomePreview: require("./home_preview"),
    startStoryPreview: require("./story_preview"),
    loadMoreStories: require("./load_more"),
    subSectionStories: require("./sub_section_stories"),
    analytics: require("./analytics.js"),
    videos: require("./videos.js"),
    infiniteScroll: require("./infinite-scroll"),
    slickSlider: require("./photo-story-slider")
};


require("./responsiveslides.min");

$(document).ready(function(){
  global.app.slickSlider.photoStory();
  global.app.slickSlider.imageGallery();

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

  $('.snapshot-header').click( function(event){
    $(".snapshot-text").toggleClass("hide");
    $(".snapshot-icon-plus").toggleClass("hide");
  });

  $('.js-breakingnews-marquee').marquee({
      duration: 19000,
      startVisible: true,
      duplicated: true,
      pauseOnHover: true
  });

  $( '.story-element-text a[href^="http://"] ' ).attr( 'target','_blank' );
  $( '.story-element-text a[href^="https://"] ' ).attr( 'target','_blank' );

});
