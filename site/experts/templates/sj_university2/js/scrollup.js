// Hide yt_top on on scroll down

jQuery(document).ready(function($){
	
	
	

	

});
var didScroll;
	var lastScrollTop = 0;
	var delta = 5;
	var navbarHeight = jQuery('#yt_header').height();
setInterval(function() {
		if (didScroll) {
			hasScrolled();
			didScroll = false;
		}
	}, 250);
	
jQuery(window).scroll(function(event){
		didScroll = true;
	
	});


function hasScrolled() {
    var st = jQuery(this).scrollTop();
    
    // Make sure they scroll more than delta
    if(Math.abs(lastScrollTop - st) <= delta)
        return;
    
    // If they scrolled down and are past the navbar, add class .nav-up.
    // This is necessary so you never see what is "behind" the navbar.
    if (st > lastScrollTop ){
        // Scroll Down
		
		//jQuery('#yt_header').animate({scrollTop:55}, 1200);
       // jQuery('#yt_top').slideUp();
		
    } else {
        // Scroll Up
        if(st == 0) {
           // jQuery('#yt_top').slideDown();
			
        }
    }
    
    lastScrollTop = st;
}