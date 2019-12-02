<?php
// no direct access
defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers');
JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers/html');
$pageClass = $this->params->get('pageclass_sfx');
?>
<div class="category blog<?php echo $pageClass;?>">
	<?php if ($this->params->def('show_page_heading', 1)) : ?>
	<h1>
		<?php echo $this->params->get( 'page_heading', $this->params->def('comp_name'));?>	
	</h1>
	<?php endif; ?>
        <?php if ($this->params->get( 'search', 1 )== 1) { ?>
                <?php echo JHTML::_('icon.search');?>
                <div class="clr clearfix"></div>
        <?php } ?>
        <?php if ($this->params->get( 'breadcrumb', 2 )== 2) { ?>
                <div class="abook-path">
                        <?php echo JHTML::_('icon.breadcrumb', $this->path);?>
                </div>
        <div class="clr"></div>
        <?php } ?>
<?php if($this->params->get('show_category_title', 1)) : ?>
<h2>
	<?php echo $this->category->title; ?>
</h2>
<?php endif; ?>
<?php if ($this->params->def('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
	<div class="abook_category_desc">
	<?php if ($this->params->get('show_description_image', 1) && $this->category->getParams()->get('image')) : ?>
		<a href="<?php echo $this->category->getParams()->get('image'); ?>" class="modal" title="<?php echo $this->category->title; ?>">
			<img src="<?php echo $this->category->getParams()->get('image'); ?>" class="cat-image" alt="<?php echo $this->category->title; ?>" />
		</a>
	<?php endif; ?>
	<?php if ($this->params->get('show_description') && $this->category->description) : ?>
		<?php echo JHtml::_('content.prepare', $this->category->description); ?>
	<?php endif; ?>
	<div class="clr"></div>
	</div>
<?php endif; ?>

<?php if (!empty($this->children[$this->category->id]) && $this->maxLevel != 0) : ?>
	<div class="cat-children">
        	<h3><?php echo JText::_('COM_ABOOK_SUBCATEGORIES') ; ?>
		<!--<a href="#category" data-toggle="collapse" data-toggle="button" class="btn btn-mini pull-right collapsed"><span class="icon-plus"></span></a></h3>
        	<div class="collapse fade" id="category">--><?php echo $this->loadTemplate('children'); ?><!--</div>-->
	</div>
	<hr>
<?php endif; ?>

<?php echo $this->loadTemplate('items'); ?>
<div class="clr clearfix"></div>
</div>
