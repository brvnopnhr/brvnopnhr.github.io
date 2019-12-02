<?php
// no direct access
defined('_JEXEC') or die;

$pageClass = $this->params->get('pageclass_sfx');
?>
<div class="contact-category<?php echo $pageClass;?>">
<?php if ($this->params->def('show_page_heading', 1)) : ?>
	<h1><?php echo $this->params->get( 'page_heading', $this->params->def('comp_name'));?>	</h1>
<?php endif; ?>
<?php if ($this->params->get( 'search', 1 )== 1) { ?>
	<?php echo JHTML::_('icon.search');?>
        <div class="clr"></div>
        <?php } ?>
<?php if ($this->params->get( 'breadcrumb', 2 )== 2) { ?>
	<div class="abook-path">
		<a href="<?php echo JRoute::_('index.php?option=com_abook&view=tags');?>"><?php echo JText::_('COM_ABOOK_BACK_TO_TOP'); ?></a> <?php echo '>';?>
		<?php echo $this->tag->name; ?>
	</div>
	<div class="clr"></div>
<?php } ?>
	<h2><?php echo $this->tag->name; ?></h2>
<?php if ($this->params->def('show_base_description', 1)) : ?>
	<div class="abook_category_desc">
	<?php if ($this->params->get('show_description') && $this->tag->description) : ?>
		<?php echo JHtml::_('content.prepare', $this->tag->description); ?>
	<?php endif; ?>
	<div class="clr"></div>
	</div>
<?php endif; ?>

<?php echo $this->loadTemplate('items'); ?>

</div>
