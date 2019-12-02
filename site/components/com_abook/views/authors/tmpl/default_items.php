<?php

// no direct access
defined('_JEXEC') or die;
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$letter       = $this->state->get('filter.letter');
?>
<form action="<?php echo JRoute::_('index.php?option=com_abook&view=authors'); ?>" method="post" name="adminForm" id="adminForm">
<?php if ($this->params->get( 'alphabet_bar', 1 )== 1) { ?>
        <fieldset class="filters"><?php echo JHTML::_('icon.alphabet');?></fieldset>
        <?php if ($letter!=''){ ?>
                <div class="thisletter"><h2><?php echo $letter;?></h2></div>
        <?php }

} ?>
<?php if (empty($this->items)) : ?>
        <p><?php echo JText::_('COM_ABOOK_NO_BOOKS'); ?></p>
<?php else:?>
	<table class="table table-hover books">
		<tbody>
			<?php foreach($this->items as $i => $item) : ?>
				<?php $linkbook=JRoute::_(AbookHelperRoute::getAuthorRoute($item->slug));?>
				<tr>
					<?php if ($this->params->get( 'show_item_image', 1) == 1){ ?>
					<td width="100px">
					<div class="item-image cover">
                        			<?php if ($item->image){ ?>
                                			<a href="<?php echo $linkbook; ?>" title="<?php echo JText::_('COM_ABOOK_COVEROF').' '.$item->title; ?>"><img class="img-polaroid" src="<?php echo $item->image ?>" alt="<?php echo JText::_('COM_ABOOK_COVEROF').' '.$item->title ;?>"/></a>
                              			<?php }else{ ?>
                                			<a href="<?php echo $linkbook; ?>" title="<?php echo JText::_('COM_ABOOK_NOCOVEROF').' '.$item->title; ?>"><img class="img-polaroid" src="components/com_abook/assets/images/nocover.png" alt="<?php echo JText::_('COM_ABOOK_NOCOVEROF').' '.$item->title; ?>" /></a>
                              			<?php }?>
                        		</div>
					</td>
				<?php }?>
					<td>
						<div class="book-title">
							<?php if ($this->params->get( 'link_titles', 1) == 1){ ?>
							<a href="<?php echo $linkbook; ?>"><?php echo $item->name; ?></a>
							<?php }else{ ?>
								<?php echo $item->name; ?>
							<?php }?>
						</div>
						<?php
						if ($this->params->get( 'show_item_desc', 1 )==1){ ?>
							<div><?php echo substr(strip_tags($item->description), 0, 100);?></div>
						<?php } ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>
<div class="pull-right"><?php echo JHTML::_('credit.credit') ?></div>
<div class="clearfix"></div>
<?php // Add pagination links ?>
<?php if (!empty($this->items)) : ?>
        <?php if ($this->params->def('showpagination', 1)) : ?>
        <div class="pagination">
                <?php if ($this->params->def('show_pagination_results', 1)) : ?>
                        <p class="counter pull-right">
                                <?php echo $this->pagination->getPagesCounter(); ?>
                        </p>
                <?php endif; ?>
                <?php echo $this->pagination->getPagesLinks(); ?>
        </div>
	<?php endif; ?>
<?php endif; ?>
	<div>
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<input type="hidden" name="letter" value="<?php echo $letter; ?>" />
	</div>
</form>
