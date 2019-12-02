<?php

// no direct access
defined('_JEXEC') or die;

//JHtml::core();

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>
<?php if (empty($this->items)) : ?>
	<p> <?php echo JText::_('COM_ABOOK_NO_TAGS'); ?>	 </p>
<?php else : ?>
	<?php 
		$n=1;
		foreach($this->items as $i => $item) { ?>
		<div class="taggroup">
			<h3><?php echo $item->name; ?></h3>
			<div>
				<dl>
				<?php foreach($item->tags as $i => $tag){
					$link=JRoute::_(AbookHelperRoute::getTagRoute($tag->slug));
					//$link=JRoute::_('index.php?option=com_abook&view=tag&id='.$tag->slug);?>
					<dd><a class="label label-info" href="<?php echo $link; ?>"><?php echo $tag->name; ?></a>
					<?php if ($this->params->get('show_item_count')){
						echo '('.$tag->num.')';
					}?>
					</dd>
				<?php } ?>
				</dl>
			</div>
		</div>
		<?php if ($n==$this->params->get('tagnum')){ ?>
			<div class="clearfix"></div>
		<?php $n=0;
			} 
		$n++
		?>
	<?php } ?>
<?php endif; ?>
<div class="clearfix"></div>
<div class="pull-right"><?php echo JHTML::_('credit.credit') ?></div>
<div class="clearfix"></div>
