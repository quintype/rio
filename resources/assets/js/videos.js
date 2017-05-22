var analytics = require('./analytics.js');
function handleYoutubePlayerStateChange(event) {
  analytics.trackYouTubeStoryElement(event);
}

function loadEagerPlayers($storyEl) {
  $('[data-youtube-id].eager', $storyEl).each(function(i, el) {
    var player = $(el).data('player');
    new window.YT.Player(player.id, player.params);
  });
}

function initYoutubeLibrary() {
  if (window.YT) {
      loadEagerPlayers();
    }
  else {
    window.onYouTubeIframeAPIReady = loadEagerPlayers;
    $("#youtube-iframe-api").append("<script src='https://www.youtube.com/iframe_api'></script>");
  }
}

function loadYoutubeVideos() {
  $('[data-youtube-id]').each(function(i, el) {
    var $el = $(el),
        videoId = $el.data('youtubeId'),
        player = {
          id: el,
          params: {
            videoId: videoId,
            playerVars: {
              autoplay: false
            },
            events: {
              onStateChange: handleYoutubePlayerStateChange
            }
          }
        };
    $el
      .data('player', player)
      .attr('data-loader', 'youtube')
      .addClass('eager');
  });

  if (window.YT) {
    loadEagerPlayers();
  }
}

module.exports = {
  load: loadYoutubeVideos,
  init: initYoutubeLibrary
};
