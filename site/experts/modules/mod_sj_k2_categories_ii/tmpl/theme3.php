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

$heightModule		= 40 + $options->item_thumbnail_height + 30; //margin_top+heightImage+paddingBottom
$urlFirstImage		= '';
$firstContent		= '';

?>
<div class="sj_section_wrap sj_clearfix">
	<div class="theme3_wrap sj_clearfix" id="categories_theme3_<?php echo $instance;?>" style="height: <?php echo $heightModule;?>px;">
		<ul class="theme3_ul_root">
			<?php $intData	= 0; ?>
			<?php foreach($data as $root){ ?>
				<?php 
					$intData++;
					if ( $intData==1){
						$urlFirstImage	= $root->image;
						$firstContent	= $root->description;
					}
				?>
				<li class="theme3_li_root" rel="<?php echo $root->image.'|'.$root->description;?>">
					<a href="<?php echo $root->url;?>" <?php echo YtUtils::getTargetAttr($options->category_link_target);?>>
						<div class="theme3_root_title"><?php echo $root->title;?></div>
					</a>
					<?php if (count($root->childCat) >0 ) { ?>
						<ul class="theme3_ul_lev1">
						<?php $cntLev1	= 0;?>
						<?php foreach($root->childCat as $catLev1) { ?>
							<?php 
								$cntLev1++;
								if ($cntLev1>$options->max_item_lev1){
									break;
								}
							?>
							<li class="theme3_li_lev1">
								<a href="<?php echo $catLev1->url;?>" <?php echo YtUtils::getTargetAttr($options->category_link_target);?>>
									<div class="theme3_lev1_title">
										<?php echo $catLev1->title;?>
										<?php if (count($catLev1->childCat) > 0) { ?>
												<img class="theme3_img_arrow" src="<?php echo JURI::base().'modules/mod_sj_k2_categories_ii/assets/images/theme3_arrow.png';?>" />
										<?php } ?>
									</div>
								</a>
								<?php if (count($catLev1->childCat) > 0) { ?>
									<ul class="theme3_ul_lev2">
										<?php foreach($catLev1->childCat as $catLev2) { ?>
											<li class="theme3_li_lev2">
												<a href="<?php echo $catLev2->url;?>" <?php echo YtUtils::getTargetAttr($options->category_link_target);?>>
													<div class="theme3_title_lev3">
														<?php echo $catLev2->title;?>
													</div>
												</a>
											</li>
										<?php } ?>
									</ul>
								<?php } ?>
							</li>
						<?php } ?>
						</ul>
					<?php } ?>
				</li>
			<?php } ?>
		</ul>
		
		<?php 
		
		/* when resize mode as none */
		
		/* $urlFirstImage = str_replace("\\"," ",$urlFirstImage);
		$urlFirstImage = str_replace(" ","/",$urlFirstImage);  */
		
		?>
		<div class="theme3_content">
			<div class="theme3_main_image" style="width: <?php echo $options->item_thumbnail_width;?>px;height: <?php echo $options->item_thumbnail_height;?>px; background: url(<?php echo $urlFirstImage;?>) no-repeat center center;"></div>
			
			<div class="theme3_content_detail" style="height: <?php echo $options->item_thumbnail_height;?>px;">
				<?php echo $firstContent;?>
			</div>
		</div>
	</div>
</div>
