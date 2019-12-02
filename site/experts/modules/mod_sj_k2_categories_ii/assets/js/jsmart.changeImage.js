;
(function($) {	
	
	$.fn.changeImage = function(options){
		
		return this.each(function() {
			var $liRoot		= $(this).find(".theme3_li_root");
			var $divImage	= $(this).find(".theme3_main_image");
			var $divContent	= $(this).find(".theme3_content_detail");
			
			
			$liRoot.each(function(){
				$(this).mousemove(function(){
					var myString = $(this).attr("rel");

					var mySplitResult = myString.split("|");
					
					$divImage.css("background","url("+mySplitResult[0]+") no-repeat center center");
					
					$divContent.text(mySplitResult[1]);
				});
			});
			
		});
	}
	
})(jQuery);
