function photoStorySlider() {
  $('.js-photo-story-slider:not(.js-slick-init)')
    .each(function(index, el){
      var $el = $(el);
      $el.slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: true,
        autoplay: true
      });
      $el.addClass('js-slick-init');
    });
}

function imageGallerySlider() {
  $('.js-slideshow-element:not(.js-slick-init)')
  .each(function(index, el){
    var $el = $(el);
    $el.slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      arrows: true
    });
    $el.addClass('js-slick-init');
  });
}

module.exports = {
  photoStory: photoStorySlider,
  imageGallery: imageGallerySlider
}
