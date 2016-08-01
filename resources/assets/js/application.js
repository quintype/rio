var Twig = require("twig");
global.app = {
    initQtReady: require("./qt_ready"),
    startHomePreview: require("./home_preview"),
    startStoryPreview: require("./story_preview"),
    loadMoreStories: require("./load_more")
};


require("./responsiveslides.min");

$(document).ready(function(){
$(".menubar").click(function(event){
 event.stopPropagation();
  $(".menuitems").toggle();
});

        $('#search_init,#search_init1').click(function ()
        {
            
 event.stopPropagation();
            $(".nav-search").toggle();
        });

  $(document).click( function(){
         $(".menuitems").hide();
          
    });

          
            $(function() {
    $(".rslides").responsiveSlides(

    	{
  auto: false,             // Boolean: Animate automatically, true or false
  pager:true,            // Integer: Speed of the transition, in milliseconds
  nav: true,      
}
);
  });

    });
