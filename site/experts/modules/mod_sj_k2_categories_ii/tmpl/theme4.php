<?php
/*
 * @package Sj K2 Categories II
 * @version 3.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2013 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

$paddingLeftRight	= 10;
$widthLi			= 165 + $paddingLeftRight + 10;

$width_tooltip		= $options->columns_max_level2*$widthLi;

$widthItem			= (int)($options->module_width/$options->columns_max) - 20;// paddingLeftRight=20

$widthImage			= $options->item_thumbnail_width;
if ( $widthImage > $widthItem )
	$widthImage	= $widthItem;

$marginLeft			=  $widthImage +(int)(($widthItem-$widthImage)/2) -10;
?>
<div class="sj_section_wrap sj_clearfix">
	<div class="theme4_wrap sj_clearfix">
		<ul>
			<?php foreach ($data as $root): ?>
				<li class="theme4_item_li" style="width: <?php echo $widthItem;?>px;">
					<?php if (count($root->childCat)>0){ ?>
						<div class="theme4_tooltip" style="margin-left: <?php echo $marginLeft;?>px;">
							<div class="theme4_left_arrow"></div>
							<div class="theme4_tooltip_content" style="width: <?php echo $width_tooltip;?>px;">
								<ul class="theme4_ul_tooltip">
									<?php 
										$cntChild	= 0;
										$attr		= '';
									?>
									<?php foreach($root->childCat as $catLev1){ ?>
											<?php 
												$cntChild++;
												if ($cntChild > $options->columns_max_level2)
													$attr	= 'style="display: none;" rel="hide"';
											?>
											<li class="theme4_tooltip_item_li" <?php echo $attr;?>>
												<a href="<?php echo $catLev1->url;?>" <?php echo YtUtils::getTargetAttr($options->category_link_target);?>>
													<div class="theme4_tooltip_item_title">
														<?php echo $catLev1->title;?>
													</div>
												</a>
												<hr class="theme4_hr">
												<div class="theme4_tooltip_subitem">
													<ul>
														<?php foreach($catLev1->childCat as $catLev2) { ?>
															<li class="theme4_tooltip_subitem_li">
																<?php if (count($catLev2->childCat)>0) { ?>
																	<div class="theme4_tooltip_lev2_bg">
																		<ul>
																			<?php foreach($catLev2->childCat as $catLev3){ ?>
																				<li class="theme4_tooltip_lev2_li">
																					<a href="<?php echo $catLev3->url;?>" <?php echo YtUtils::getTargetAttr($options->category_link_target);?>>
																						<div class="theme4_tooltip_lev2_title">
																							<?php echo $catLev3->title;?>
																						</div>
																					</a>
																				</li>
																			<?php } ?>
																		</ul>
																	</div>
																<?php } ?>
																<a href="<?php echo $catLev2->url;?>" <?php echo YtUtils::getTargetAttr($options->category_link_target);?>>
																	<div class="theme4_subitem_li_content">
																		<div class="theme4_title_lev2">
																			<?php echo $catLev2->title;?>
																		</div>
																		<div class="theme4_total_child">
																			(<?php echo count($catLev2->childCat);?>)
																		</div>
																	</div>
																</a>
															</li>
														<?php } ?>
													</ul>
												</div>
											</li>
									<?php } ?>
								</ul>
								<div class="theme4_see_all">
									<?php $button_text = explode("/", $options->more_hide);?>
									<div class="expand">
										<?php echo $button_text[0];?>
									</div>
									<div class="narrow">
										<?php echo $button_text[1];?>
									</div>
								</div>
							</div>
						</div>
					<?php } ?>
					<div class="theme4_item">
							<?php if ($options->image_linkable ==1) { ?>
									<a href="<?php echo $root->url;?>" <?php echo YtUtils::getTargetAttr($options->category_link_target);?>>
							<?php } ?>
							<img class="theme4_item_image" style="width: <?php echo $widthImage;?>px; height: <?php echo $options->item_thumbnail_height;?>px;" src="<?php echo $root->image;?>" />
							<?php if ($options->image_linkable ==1) { ?>
									</a>
							<?php } ?>
							<a href="<?php echo $root->url;?>" <?php echo YtUtils::getTargetAttr($options->category_link_target);?>>
								<div class="theme4_item_title">
									<?php echo $root->title;?>
								</div>
							</a>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<!--<div class="theme4_button_more">
		<div class="theme4_button_right"></div>
		<div class="theme4_button_center">
			More
		</div>
		<div class="theme4_button_left"></div>
	</div>-->
</div>
<script>
	jQuery(function($) {
			$('.theme4_see_all').click(function() {
				var $li		= $(this).parent().find('.theme4_tooltip_item_li');
				var $button	= $(this);
				
				$li.each(function(){
					if ($(this).attr('rel')=='hide'){
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
</script>