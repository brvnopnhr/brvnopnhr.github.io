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
$widthImage			= $options->module_width - (180+8+4);
$urlFirstImage		= "";
$max_image_width	= $options->item_thumbnail_width;
if ( $max_image_width> ($options->module_width - 190))
	$max_image_width	= $options->module_width - 190;
?>
<div class="sj_section_wrap sj_clearfix">
	<div class="theme2_menu_left" id="theme2_menu_category_<?php echo $instance;?>" style="height: <?php echo ($options->item_thumbnail_height);?>px; ">
		<ul>
			<?php $intData	= 0;?>
			<?php foreach ($data as $root): ?>
				<?php 
					$intData++;
					if ( $intData==1){
						$urlFirstImage	= $root->image;
					} 
				?>
				<li class="theme2_root_li" >
					<a class="theme2_title_root" href="<?php echo $root->url;?>" rel="<?php echo $root->image;?>" <?php echo YtUtils::getTargetAttr($options->category_link_target);?>>
						<div class="theme2_menu_root">
							<img src="<?php echo JURI::base().'modules/mod_sj_k2_categories_ii/assets/images/li_level1.png';?>" class="theme2_img_li_root" alt="image" />
							<img src="<?php echo JURI::base().'modules/mod_sj_k2_categories_ii/assets/images/li_level1_hover.png';?>" class="theme2_img_li_root_active" alt="image" />
							<div class="theme2_menu_root_title" ><?php echo $root->title;?></div>
						</div>
					</a>
					<?php if ( count($root->childCat)>0){ ?>
						<div class="theme2_wrap_menu_lev1">
							<ul >
								<?php $cntChild	=0;?>
								<?php foreach($root->childCat as $catLev1){?>
									<?php 
										$cntChild++;
										if ($cntChild > $options->max_item_lev1){
											break;
										}
									?>
									<li class="theme2_menu_li_lev1">
										<?php if ( count($catLev1->childCat)>0 ){ ?>
											<div class="theme2_wrap_menu_lev2">
												<ul>
													<?php foreach($catLev1->childCat as $catLev2){ ?>
														<li class="theme2_level2_li">
															<a href="<?php echo $catLev2->url;?>" <?php echo YtUtils::getTargetAttr($options->category_link_target);?>>
																<div class="theme2_menu_lev2_bg"></div>
																<div class="theme2_menu_lev2">
																	<img src="<?php echo JURI::base().'modules/mod_sj_k2_categories_ii/assets/images/theme2_li_level2.png';?>" class="theme2_img_li_lev2" alt="image" />
																	<div class="theme2_menu_lev2_title">
																		<?php echo $catLev2->title;?>
																	</div>
																</div>
															</a>
														</li>
													<?php } ?>
												</ul>
											</div>
										<?php } ?>
										
										<a href="<?php echo $catLev1->url;?>" <?php echo YtUtils::getTargetAttr($options->category_link_target);?>>
											<div class="theme2_menu_lev1">
												<img src="<?php echo JURI::base().'modules/mod_sj_k2_categories_ii/assets/images/theme2_li_lev1.png';?>" class="theme2_img_li_lev1" alt="image" />
												<img src="<?php echo JURI::base().'modules/mod_sj_k2_categories_ii/assets/images/theme2_li_lev1_hover.png';?>" class="theme2_img_li_lev1_active" alt="image" />
												<div class="theme2_menu_lev1_title" ><?php echo $catLev1->title;?></div>
												
											</div>
											
										</a>
										
									</li>
								<?php } ?>
							</ul> 
						</div>
					<?php } ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	
	<?php 
		/* when resize mode as none */
		
		/* $urlFirstImage = str_replace("\\"," ",$urlFirstImage);
		$urlFirstImage = str_replace(" ","/",$urlFirstImage);  */
	
	?>
	
	<div class="theme2_image_categories_wrap" style="width: <?php echo ($max_image_width);?>px; height: <?php echo ($options->item_thumbnail_height);?>px;">
		<div class="theme2_image_categories_section" style="background: url(<?php echo $urlFirstImage;?>) no-repeat center center; ">
			
		</div>
	</div>
</div>