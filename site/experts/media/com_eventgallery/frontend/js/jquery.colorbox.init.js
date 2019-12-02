(function(jQuery){

jQuery(document).ready(function() {
	//legacy call to old lightbox 
	jQuery("a[rel^='lightbo2']").eventgallery_colorbox({maxWidth:"100%", maxHeight:"100%"});

	jQuery("a[data-eventgallery-lightbox='gallery']").eventgallery_colorbox({
        slideshow: EventGallerySlideShowConfiguration.slideshow,
        slideshowAuto: EventGallerySlideShowConfiguration.slideshowAuto,
        slideshowSpeed: EventGallerySlideShowConfiguration.slideshowSpeed,
        slideshowStart: EventGallerySlideShowConfiguration.slideshowStart,
        slideshowStop: EventGallerySlideShowConfiguration.slideshowStop,
        photo: true,
        maxWidth: '80%',
        maxHeight: '90%'
    });
	jQuery("a[data-eventgallery-lightbox='galleryfullscreen']").eventgallery_colorbox({
        slideshow: EventGallerySlideShowConfiguration.slideshow,
        slideshowAuto: EventGallerySlideShowConfiguration.slideshowAuto,
        slideshowSpeed: EventGallerySlideShowConfiguration.slideshowSpeed,
        slideshowStart: EventGallerySlideShowConfiguration.slideshowStart,
        slideshowStop: EventGallerySlideShowConfiguration.slideshowStop,
        className: 'eventgallery_colorbox_fullscreen',
        photo: true,
        maxWidth: '100%',
        maxHeight: '100%',
        opacity: 0
    });

	jQuery("a[data-eventgallery-lightbox='cart']").eventgallery_colorbox({photo: true, maxWidth: '90%', maxHeight: '90%', rel: 'cart'});
	jQuery("button[data-eventgallery-lightbox='content']").eventgallery_colorbox({inline:true, maxWidth: '80%'});
	jQuery("a[data-eventgallery-lightbox='content']").eventgallery_colorbox({inline:true, maxWidth: '80%'});

	
	
	jQuery(document).bind('eventgallery_cbox_complete', function(){
		var $cboxTitle = jQuery('#eventgallery_cboxTitle'),
			$cboxCurrent = jQuery('#eventgallery_cboxCurrent'),
			$cboxLoadedContent = jQuery('#eventgallery_cboxLoadedContent'),
			marginBottom = parseInt($cboxLoadedContent.css('margin-bottom'));
		//console.log(marginBottom);
		if(marginBottom>0 && $cboxTitle.outerHeight() > marginBottom ){ 
			$cboxTitle.hide();			
			$cboxCurrent.hide();
			$cboxLoadedContent.append('<span class="overlapping-content">' + $cboxTitle.html() + '<span class="eventgallery_current">' + $cboxCurrent.html() +'</span></span>').css({color: $cboxTitle.css('color')}); 			
			jQuery.fn.eventgallery_colorbox.resize();
			
		}
	});

    jQuery(document).bind('eventgallery_cbox_open', function(){
        Eventgallery.Touch.addTouch('body',
            jQuery.eventgallery_colorbox.prev,
            jQuery.eventgallery_colorbox.next
        );
    });

    jQuery(document).bind('eventgallery_cbox_closed', function(){
        Eventgallery.Touch.removeTouch('body');
    });
	
	jQuery('.singleimage-zoom').click(function(e){
		e.preventDefault();
		jQuery('#bigimagelink').click();
	});

    // the following code is from https://github.com/jackmoore/colorbox/issues/158#issuecomment-33762614
    // I commented out the addEventListener function to orientationchange
    jQuery(window).resize(function(){
        // Resize Colorbox when resizing window or changing mobile device orientation
        resizeColorBox();
        //window.addEventListener("orientationchange", resizeColorBox, false);
    });

    var resizeTimer;
    function resizeColorBox() {
        if (resizeTimer) {
            clearTimeout(resizeTimer);
        }
        resizeTimer = setTimeout(function() {
            if (jQuery('#eventgallery_cboxOverlay').is(':visible')) {
                jQuery.eventgallery_colorbox.reload();
            }
        }, 300);
    }

    if (EventGallerySlideShowConfiguration.slideshowRightClickProtection === true)
    {
        jQuery( document ).on( "contextmenu", "img.eventgallery_cboxPhoto", function(e) {
            e.preventDefault();
            return false;
        });
    }


});

})(Eventgallery.jQuery);   