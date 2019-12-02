<?php
/**
 * @version             $Id: default_items.php 16651 2010-05-02 08:50:07Z infograf768 $
 * @package             Joomla.Site
 * @subpackage  com_newsfeeds
 * @copyright   Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license             GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
$class = 'first';
if (count($this->items) > 0 && $this->maxLevel != 0) :
?>
<table class="books">
<tbody>
<?php foreach($this->items as $id => $item) : ?>
        <?php
        if($this->params->get('show_empty_categories', 1) || $item->numitems || count($item->getChildren())) :
        if(!isset($this->items[$id + 1]))
        {
                $class = 'last';
        }
        ?>
        <tr>
        <td class="<?php echo $class; ?> folder">
	<h3 class="item-title">
        <?php echo str_repeat('<span class="indent"></span>', $item->level) ?>
        <?php $class = '';?>
        <?php if ($this->params->get('catimage')!="-1"){
		if($this->params->get('catimage')=='icon-folder'){?>
			<i class="icon-folder"></i>
		<?php }else{?>
                <?php $catimage=$this->params->get('catimage')== "cat_custom_image"?$item->params->get('image','components/com_abook/assets/images/no_img_cat.png') : "components/com_abook/assets/images/folder/".$this->params->get('catimage', "folder_blue.png"); ?>
                <img class="cat-img-folder" src="<?php echo $catimage; ?>" style="vertical-align:middle;" alt="<?php echo JText::_('COM_ABOOK_CATEGORY').' '.$this->escape($item->title); ?>" />
        	<?php }
	}?>
                        <a href="<?php echo JRoute::_(AbookHelperRoute::getCategoryRoute($item->slug));?>">
                        <?php echo $this->escape($item->title); ?></a>
                        <?php if ($this->params->get('show_item_count', 0) == 1) :?>
				<span class="badge tip" title="<?php echo JHtml::tooltipText('COM_ABOOK_NUM_ITEMS')?>">
                                 <?php echo $item->numitems; ?>
				</span>
                        <?php endif; ?>
           </h3>
                <?php if ($item->description && $this->params->get('show_list_description', 0) == 1): ?>
                        <div class="category-desc">
                                <?php echo JHtml::_('content.prepare', $item->description); ?>
                        </div>
                <?php endif; ?>
                <?php /*if(count($item->getChildren()) > 0) :
                        $this->items[$item->id] = $item->getChildren();
                        $this->parent = $item;
                        $this->maxLevel--;
                        echo $this->loadTemplate('items');
                        $this->parent = $item->getParent();
                        $this->maxLevel++;
                endif; */?>
        </td>
        </tr>
        <?php endif; ?>
<?php endforeach; ?>
</tbody>
</table>
<?php else:?>
<p><?php echo JText::_('COM_ABOOK_NO_CATEGORIES'); ?></p>
<?php endif; ?>
<div class="pull-right"><?php echo JHTML::_('credit.credit') ?></div>
<div class="clearfix"></div>
<?php if (!empty($this->items)) : ?>
        <?php if ($this->params->get('showpagination', 1)) : ?>
                <div class="pagination">
                        <?php if ($this->params->def('show_pagination_results', 1)) : ?>
                                <p class="counter"><?php echo $this->pagination->getPagesCounter(); ?></p>
                        <?php endif; ?>
                        <?php echo $this->pagination->getPagesLinks();?>
                        <p class="pagnum"><?php echo $this->pagination->getResultsCounter();?></p>
                </div>
        <?php endif; ?>
<?php endif; ?>
