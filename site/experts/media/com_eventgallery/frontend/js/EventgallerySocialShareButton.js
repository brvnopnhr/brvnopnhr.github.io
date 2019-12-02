(function(jQuery){
	jQuery( document ).ready(function() {

		jQuery(document).on('click', 'a.social-share-button-close', function(e) {
			e.preventDefault();
			var myDivs = jQuery(e.target).parents('.social-sharing-toolbox');
			myDivs.fadeOut(300, function() {
				jQuery(this).remove();
			});
		});	
				
		jQuery(document).on('click','a.social-share-button-open', function(e) {

			e.preventDefault();

			var link = jQuery(e.target);
			if (!link.data('link')) {
				link = link.parent('a');	
			}
	
			var id = 'id_' + Math.ceil(Math.random()*10000000);
			
			var targetPos = link.offset();
			var myDiv = jQuery('<div>Loading...</div>');
			myDiv.attr('id', id);
			myDiv.addClass('social-sharing-toolbox');
			myDiv.css( {
		    	'opacity': '1 !important',
		    	'position': 'absolute',    	
		    	'top': targetPos.top-10,
		    	'left': targetPos.left-10
		    });
		
			jQuery('body').append(myDiv);
			
			myDiv.fadeIn();
			myDiv.load(link.data('link'));

			var timer = null;
						
			var closeFunction = function(){						
				myDiv.fadeOut(300, function() {
					jQuery(this).remove();
				});
				jQuery(document).off('click', closeFunction2);
			};
			
			myDiv.mouseleave( function(){
				timer = window.setTimeout(closeFunction, 1000);
			});
			
			myDiv.mouseenter( function() {
				window.clearTimeout(timer);			
			});
					
			// this method is used to close the sharing windows if we click somewhere else.
			var closeFunction2 = function(e) {
				if (e.target.id != id && jQuery(e.target).parents('#' + id).length === 0) {
					closeFunction();
				}
			};
			
			jQuery(document).on('click', closeFunction2);			
			
		}); 
	});
})(Eventgallery.jQuery);
