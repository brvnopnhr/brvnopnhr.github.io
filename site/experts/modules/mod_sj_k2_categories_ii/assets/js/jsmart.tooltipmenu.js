;
(function($) {	
	$.fn.tooltipMenu = function(options){
		var defaults = {
			tooltipClass 		: '',
			subTooltipClass 	: '',
			marginTop			: -25,
			marginLeft			: 100,
			maxColunm			: 2,
			addMarginLeft		: 0
		};
		
		var options = $.extend(defaults, options);
		
		
		function callTooltip(obj,tooltipClass,marginTop,marginLeft){
			var className = $(obj).attr('class');
			
			$(obj).mouseover(function() {
				$(obj).addClass(className + '_selected');

				$(tooltipClass).css('margin-top', marginTop);
				$(tooltipClass).css('margin-left', ($('.theme1_menu_lev1_title',$(this)).width() + 15));
				if($(this).hasClass('theme1_level2_li')){
					$(tooltipClass).css('margin-left', marginLeft);
				}
				// $(tooltipClass).css('left',$(obj).position().left +options.marginLeft);
				
			})
			/*.mousemove(function(e) {

				//Keep changing the X and Y axis for the tooltip, thus, the tooltip move along with the mouse
				$('.tooltip').css('top', e.pageY + 10 );
				$('.tooltip').css('left', e.pageX + 20 );
				
			})*/
			;
			
			$(obj).mouseout(function() {
				
				$(obj).removeClass(className + '_selected');
				
			});
		}
		
		return this.each(function() {
			$items	= $(this).children().children('.theme1_level1_li');
			
			$items.each(function(){
				$childItems1			= $(this).children().children().children().children('.theme1_level2_li');
				$rightArrow1			= $(this).children().children('.arrow_right_lev1');
				heightLi1				= 35;
				paddingTop1				= 12;
				heightSubTooltip1		= (heightLi1 * (Math.round($childItems1.size()/options.maxColunm)-1))/2;
				$rightArrow1.css('top', heightSubTooltip1 + paddingTop1);
				
				var sBrowser = navigator.userAgent;
		
				if (sBrowser.toLowerCase().indexOf('msie 7.') > -1){
					//IE 7
					callTooltip($(this),options.tooltipClass,-(heightSubTooltip1+5),options.marginLeft-options.addMarginLeft);
				}
				else{
					callTooltip($(this),options.tooltipClass,-(heightSubTooltip1+5),options.marginLeft);
				}
				
				$subItems	= $(this).children().children().children().children('.theme1_level2_li');
				
				$subItems.each(function(){
					$childItems			= $(this).children().children().children().children('.theme1_level3_li');
					$rightArrow			= $(this).children().children('.arrow_right_lev2');
					heightLi			= 26;
					heightSubTooltip	= (heightLi * ($childItems.size()-1))/2;
					paddingTop			= 5;
					$rightArrow.css('top', heightSubTooltip + paddingTop);
					
					callTooltip($(this),options.subTooltipClass,-heightSubTooltip,options.marginLeft);
				});
				
			});
			
		});
	}
	
	$.fn.MoreClick = function(options){
		
		return this.each(function() {
			
			var $button	= $(this).children('.theme1_button');
			
			var $li		= $(this).children().children('.theme1_level1_li');
			
			$button.click(function() {
				
				$li.each(function(){
					if ($(this).attr('rel')=='1'){
						if ($(this).css('display')=='block'){
							$(this).css('display','none');
							$button.removeClass('clicked');
						}
						else{
							$(this).css('display','block');
							$button.addClass('clicked');
						}
					}
				});

			});
		});
	}
	
})(jQuery);
