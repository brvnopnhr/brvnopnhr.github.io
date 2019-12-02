<?php
// no direct access
defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers');
$pageClass = $this->params->get('pageclass_sfx');
?>
<div class="item-page<?php echo $pageClass;?>">
	<?php if ($this->params->def('show_page_heading', 1)) : ?>
		<h1><?php echo $this->params->get( 'page_heading', $this->params->def('comp_name'));?></h1>
	<?php endif; ?>
	<?php if ($this->params->get( 'search', 1 )== 1) { ?>
                <?php echo JHTML::_('icon.search');?>
                <div class="clr"></div>
        <?php } ?>
        <?php if ($this->params->get( 'breadcrumb', 2 )== 2) { ?>
                <div class="abook-path">
                        <?php echo JHTML::_('icon.breadcrumb', null);?>
                </div>
        <div class="clr"></div>
        <?php } ?>
        <h2><?php echo $this->author->author_name; ?></h2>
	<div class="abook_category_desc">
	<?php if ($this->author->image) : ?>
		<div class="item-image">
			<img src="<?php echo $this->author->image; ?>" class="img-polaroid cat-image" alt="<?php echo $this->author->author_name; ?>"/>
		</div>
	<?php endif; ?>
	<?php if ($this->author->description) : ?>
		<?php echo $this->author->description; ?>
	<?php endif; ?>
		<div class="clr"></div>
	        <hr class="separator<?php echo $pageClass;?>" />
	</div>
	<?php if (!empty($this->items)){ ?>
		<h3 class="<?php echo $pageClass; ?>"><?php echo JText::_('COM_ABOOK_PUBLICATIONS_BY_AUTHOR');?></h3>
		<?php echo $this->loadTemplate('items'); ?>
	<?php } ?>
</div>
