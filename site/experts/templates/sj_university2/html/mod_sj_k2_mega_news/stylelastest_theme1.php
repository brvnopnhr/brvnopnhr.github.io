<?php
/**
 * @package SJ Mega News for K2
 * @version 3.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2014 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */

defined('_JEXEC') or die;
if(!function_exists('otherDiffDate')) {
		function otherDiffDate($end='2015-03-19 10:30:00'){
			$intervalo = date_diff(date_create(), date_create($end));
			if(!empty($intervalo)){
				$_date_time = array(); $i = 0;
				foreach($intervalo as $inter){ $i++;
					if($i <= 5){
						$_date_time[] = $inter;
					}
				}
				$str = '';
				if(!empty($_date_time)){
					$_labels = array('year','month','day','hour','minute', 'second');
					 for($i = 0; $i <= count($_date_time) ; $i++){
						 if($_date_time[$i] > 0){
							if($_date_time[$i] <= 1){
								$str .= $_date_time[$i].' '.$_labels[$i].' ago'; 
							}else{
								$str .= $_date_time[$i].' '.$_labels[$i].'s ago';  
							}
							 break;
						 }
					 }
				}
				return $str;
			
			}
		}
	}
?>
<?php
$item0 = array_shift($_items);
$other_items = $_items;
?>
	<div class="item-first">
		

		<?php $img = K2MegaNewsHelper::getK2Image($item0, $params);
		if ($img) {
			?>
			<div class="item-image">
				<a href="<?php echo $item0->link; ?>"
				   title="<?php echo $item0->name ?>" <?php echo K2MegaNewsHelper::parseTarget($params->get('link_target')); ?>  >
					<?php echo K2MegaNewsHelper::imageTag($img); ?>
				</a>
				<div class="over-image"></div>
			</div>
		<?php } ?>
		<div class="content-main">
			<?php if ($params->get('item_title_display') == 1) { ?>
				<div class="item-title">
					<a href="<?php echo $item0->link; ?>"
					   title="<?php echo $item0->name ?>" <?php echo K2MegaNewsHelper::parseTarget($params->get('link_target')); ?>  >
						<?php echo K2MegaNewsHelper::truncate($item0->name, $params->get('item_title_max_characs')); ?>
					</a>
				</div>
			<?php } ?>
			<ul class="by-date">
				<li class="athor"><span><?php echo JText::_('POST_BY'); ?></span>&nbsp;<?php echo $item0->author;?></li>
				<li>&ndash;</li>
				<!-- Date created -->
				<li class="time-ago">
					<?php 
					$_date = JHTML::_('date',$item0->created, 'Y-m-d H:m:s');
					echo otherDiffDate($_date); ?>
				</li>
			</ul>
			<?php if ($options->item_desc_display == 1 && $item0->displayIntrotext != '') { ?>
			<div class="item-description">
				<?php $introtext_after= str_replace('...', '', $item0->displayIntrotext);
				 echo $introtext_after; ?>
			</div>
			<?php } ?>
			
			<?php if ($item0->tags != '' && !empty($item0->tags)) { ?>
			<div class="item-tags">
				<div class="tags">
					<?php $hd = -1;
					foreach ($item0->tags as $tag): $hd++; ?>
						<span class="tag-<?php echo $tag->id . ' tag-list' . $hd; ?>">
							<a class="label label-info" href="<?php echo $tag->link; ?>"
							   title="<?php echo $tag->name; ?>" target="_blank">
								<?php echo $tag->name; ?>
							</a>
						</span>
					<?php endforeach; ?>
				</div>
			</div>
			<?php } ?>
	
			<?php if ($params->get('item_readmore_display') == 1) { ?>
				
			<div class="item-readmore">
				<a href="<?php echo $item0->link; ?>"
				   title="<?php echo $item0->title; ?>" <?php echo K2MegaNewsHelper::parseTarget($params->get('item_link_target')); ?> >							
					<i class="fa fa-check"></i><span><?php echo $params->get('item_readmore_text'); ?></span>
				</a>
			</div>
			<?php } ?>
		</div><!-- end content-main-->
	</div>
<?php if (!empty($other_items)) { ?>
	<div class="item-other">
		
		<ul class="otehr-link">
			<?php foreach ($other_items as $item) { ?>
				<li class="row">
					<a href="<?php echo $item->link; ?>" class="col-xs-7"
					   title="<?php echo $item->name ?>" <?php echo K2MegaNewsHelper::parseTarget($params->get('link_target')); ?>  >
						<?php echo $item->name; ?>
					</a>
					<ul class="by-date col-xs-5">
						<li class="athor"><span><?php echo JText::_('POST_BY'); ?></span>&nbsp;<?php echo $item0->author;?></li>
						<li>&ndash;</li>
						<!-- Date created -->
						<li class="time-ago">
							<?php 
							$_date = JHTML::_('date',$item0->created, 'Y-m-d H:m:s');
							echo otherDiffDate($_date); ?>
						</li>
					</ul>
				</li>
			<?php } ?>
		</ul>
	</div>
<?php
}
if ((int)$params->get('item_viewall_display', 1)) {
	?>
	<div class="meganew-viewall">
		<a href="<?php echo $items->link; ?>"
		   title="<?php echo $items->name; ?>" <?php echo K2MegaNewsHelper::parseTarget($params->get('link_target')); ?> >
			
			<i class="fa fa-folder-open"></i><span><?php echo $params->get('item_viewall_text', 'View') . ' ' . $items->name; ?></span>
		</a>
	</div>
<?php } ?>