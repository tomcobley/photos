import Component from '@ember/component';
import $ from 'jquery';

export default Component.extend({
  didInsertElement() {
    this._super(...arguments);

    function positionInfoBar(imageContainer) {
      // set class of info bar to adjust its postion based on relative sizes of
      //    image and window

      // find image info bar div
      var infoBar = $( imageContainer.find('.image-info')[0] );

      // all heights/widths are in px
      var image = $( imageContainer.find('img')[0] );
      var imageHeight = image.height();
      var imageWidth = image.width();

      var windowHeight = $(window).height();
      var windowWidth = $(window).width();

      // outerHeight includes border and padding
      var imageInfoHeight = infoBar.outerHeight();

      // set right position and max width of info bar
      infoBar.css("right", (windowWidth - imageWidth)/2 );
      infoBar.css("max-width", imageWidth);
      infoBar.css("max-height", imageHeight);

      if (imageHeight < windowHeight - 2*imageInfoHeight) {
        // place info bar below image
        infoBar.addClass('beneath-image');
        infoBar.removeClass('overlaying-image');
        infoBar.css("bottom", (windowHeight - imageHeight)/2 - imageInfoHeight );

      } else {
        // place info bar overlaying image in bottom right corner
        infoBar.addClass('overlaying-image');
        infoBar.removeClass('beneath-image');
        infoBar.css("bottom", (windowHeight - imageHeight)/2 );
      }
    }

    var imageContainer = $( this.element.querySelector('#image-show') );

    // add event listener so info bar is repositoned when window is resized
    $(window).on('resize', function(){
      positionInfoBar(imageContainer);
    });

    // set positon of infobar once image has laoded (and hide spinner)
    // imageContainer.imagesLoaded( function() {
    //
    //   imageContainer.find('.image-loading').hide();
    //
    //   imageContainer.find('.hidden').show();
    //
    //   positionInfoBar(imageContainer);
    //
    // });
    // TODO: remove
    imageContainer.find('.hidden').show();



  }
});
