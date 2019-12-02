<?php
/*
 * @package Sj K2 Categories II
 * @version 3.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2013 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$widthLi			= 170;
$rigthArrowWidth	= 11;
$paddingLeftRight	= 30;

$itemWidth			= (int) (($options->module_width/$options->columns_max)-$paddingLeftRight);

$max_width_tooltip		= $options->columns_max_level2*$widthLi;

$addingItemWidth	= $options->module_width - ($itemWidth+$paddingLeftRight)*$options->columns_max;
?>
<div class="sj_section_wrap sj_clearfix">
	<?php $intOrder	= 0;?>
	<?php foreach ($data as $root): ?>
		<?php $intOrder	++;?>
		<?php if (($intOrder % $options->columns_max ==1) || $options->columns_max==1){?>
			<div style="width: 100%; height: 100%; float: left;">
		<?php } ?>
		<div class="theme1_root_item" style="width: <?php echo ($intOrder % $options->columns_max ==1 ?  ($itemWidth+$addingItemWidth) :  $itemWidth);?>px;">
			<a href="<?php echo $root->url;?>" <?php echo YtUtils::getTargetAttr($options->category_link_target);?> >
				<div class="theme1_root_title">
					<?php echo $root->title;?>
				</div>
			</a>
			<div class="theme1_root_image">
				<?php if ($options->image_linkable==1){
					echo '<a href="'.$root->url.'" '.YtUtils::getTargetAttr($options->category_link_target).'>';
				}
				?>
				<img class="theme1_root_img" src="<?php echo $root->image;?>" style="width: <?php echo $options->item_thumbnail_width;?>px; height: <?php echo $options->item_thumbnail_height;?>px;" alt="images" />
				<?php if ($options->image_linkable==1){
					echo '</a>';
				}
				?>
			</div>
			<div class="theme1_root_list_child">
				<ul id="subMenu">
					<?php $intLiLev1	=0;?>
					<?php foreach($root->childCat as $catLev1){?>
						<?php 
							$intLiLev1++; 
							if ($intLiLev1>$options->max_item_lev1)
								$attr	= 'style="display:none;" rel="1"';
							else
								$attr	= ' rel="0"';
						?>
						<li class="theme1_level1_li" <?php echo $attr;?> >
							<?php if ( count($catLev1->childCat)>0){ ?>
								<?php 
									if ( count($catLev1->childCat)<$options->columns_max_level2){
										$width_tooltip		= count($catLev1->childCat)*$widthLi;
									} else{
										$width_tooltip		= $max_width_tooltip;
									}
								?>
								<div class="tooltip_lev1" style="width: <?php echo ($rigthArrowWidth+$width_tooltip);?>px;">
									<div class="arrow_right_lev1" >
										
									</div>
									<div class="level2_list" style="width: <?php echo $width_tooltip;?>px;">
										<ul class="level2_ul" >
											<?php foreach($catLev1->childCat as $catLev2){?>
												<li class="theme1_level2_li">
													<?php if ( count($catLev2->childCat)>0){ ?>
														<div class="tooltip_lev2" >
															<div class="arrow_right_lev2">
															
															</div>
															
															<div class="level3_list">
																<ul class="level3_ul">
																	<?php foreach($catLev2->childCat as $catLev3){ ?>
																		<li class="theme1_level3_li">
																			<a href="<?php echo $catLev3->url;?>" <?php echo YtUtils::getTargetAttr($options->category_link_target);?>>
																				<div class="theme1_menu_lev3_bg"></div>
																				<div class="theme1_menu_lev3">
																					<img src="<?php echo JURI::base().'modules/mod_sj_k2_categories_ii/assets/images/theme1_li_level3.png';?>" class="theme1_img_li_lev3" alt="image" />
																					<div class="theme1_menu_lev3_title">
																						<?php echo $catLev3->title;?>
																					</div>
																				</div>
																			</a>
																		</li>
																	<?php } ?>
																</ul>
															</div>
														</div>
													<?php } ?>
													
													<a href="<?php echo $catLev2->url;?>" <?php echo YtUtils::getTargetAttr($options->category_link_target);?>>
														<div class="theme1_wrap_li_lev2">
															<div class="theme1_bg_li_lev2"></div>
															<div class="theme1_menu_lev2">
																	<img src="<?php echo JURI::base().'modules/mod_sj_k2_categories_ii/assets/images/theme1_li_level2.png';?>" class="theme1_img_li_lev2" alt="image" />
																	<div class="theme1_menu_lev2_title">
																		<?php echo $catLev2->title;?>
																	</div>
															</div>
														</div>
													</a>
													
												</li>
											<?php } ?>
										</ul>
									</div>
								</div>
							<?php } ?>
							
							<a href="<?php echo $catLev1->url;?>" <?php echo YtUtils::getTargetAttr($options->category_link_target);?>>
								<div class="theme1_menu_lev1">
									<img src="<?php echo JURI::base().'modules/mod_sj_k2_categories_ii/assets/images/li_level1.png';?>" class="theme1_img_li_lev1" alt="image" />
									<img src="<?php echo JURI::base().'modules/mod_sj_k2_categories_ii/assets/images/li_level1_hover.png';?>" class="theme1_img_li_lev1_active" alt="image" />
									<div class="theme1_menu_lev1_title" ><?php echo $catLev1->title;?></div>
								</div>
							</a>
							
						</li>
					<?php } ?>
				</ul>
				<div class="theme1_button">
					<div class="theme1_button_left"></div>
					<?php $button_text = explode("/", $options->more_hide);?>
					<div class="theme1_button_center">
						<?php echo $button_text[0];?>
					</div>
					<div class="theme1_button_center_less">
						<?php echo $button_text[1];?>
					</div>
					<div class="theme1_button_right"></div>
				</div>
			</div>
		</div>
		<?php if ($intOrder % $options->columns_max ==0){?> 
			</div>
		<?php } ?>
	<?php endforeach; ?>
	<?php if ($intOrder % $options->columns_max !=0){?> 
			</div>
	<?php } ?>
</div>