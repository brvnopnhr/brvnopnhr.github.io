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
	<ul class="line items-row1" >
		
		<?php foreach ($data as $root): ?>
		
			<li class="icon-image " style="width: <?php echo $widthItem;?>px;">
				<?php if ($options->image_linkable ==1) { ?>
					<a href="<?php echo $root->url;?>" <?php echo YtUtils::getTargetAttr($options->category_link_target);?>>
				<?php } ?>
				<img style="width: <?php echo $widthImage;?>px; height: <?php echo $options->item_thumbnail_height;?>px;" src="<?php echo $root->image;?>" alt="image"/>
				<?php if ($options->image_linkable ==1) { ?>
				</a>
				<?php } ?>
				<div class="over-image"></div>
				
			</li>
			<li class="content">
				<h3 class="tilte">
				<a href="<?php echo $root->url;?>" <?php echo YtUtils::getTargetAttr($options->category_link_target);?>><?php echo $root->title;?></a></h3>
				<p class="des"><?php echo $root->description; ?></p>
				
			</li>
		<?php endforeach; ?>
	</ul>


