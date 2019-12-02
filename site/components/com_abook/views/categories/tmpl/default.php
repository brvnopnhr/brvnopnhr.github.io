<?php
/**
 * @version		$Id: default.php 17130 2010-05-17 05:52:36Z eddieajau $
 * @package		Joomla.Site
 * @subpackage	com_newsfeeds
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
// If the page class is defined, wrap the whole output in a div.
$pageClass = $this->params->get('pageclass_sfx');
?>
<div class="categories-list<?php echo $pageClass;?> blog">
<?php if ($this->params->def('show_page_heading', 1)) : ?>
	<h1><?php echo $this->params->get( 'page_heading', $this->params->def('comp_name'));?></h1>
<?php endif; ?>
<?php if ($this->params->get( 'search', 1 )== 1) { ?>
                <?php echo JHTML::_('icon.search');?>
                <div class="clr clearfix"></div>
        <?php } ?>
<?php if($this->params->get('show_category_title', 1)) : ?>
<h2>
        <?php echo $this->params->def('comp_name'); ?>
</h2>
<?php endif; ?>
	<?php if ($this->params->get('show_base_description')) : ?>
	<?php 	//If there is a description in the menu parameters use that; ?>
		<?php if($this->params->get('categories_description')) : ?>
			<?php echo  JHtml::_('content.prepare',$this->params->get('categories_description')); ?>
		<?php  else: ?>
			<?php //Otherwise get one from the database if it exists. ?>
			<?php  if ($this->params->get('categories_description')) : ?>
				<div class="category-desc">
					<?php  echo JHtml::_('content.prepare', $this->parent->description); ?>
				</div>
			<?php  endif; ?>
		<?php  endif; ?>
	<?php endif; ?>
<?php
echo $this->loadTemplate('items');
?>
</div>
