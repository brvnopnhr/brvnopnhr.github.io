<?php
// no direct access
defined('_JEXEC') or die;

$pageClass = $this->params->get('pageclass_sfx');
?>
<div class="contact-category<?php echo $pageClass;?>">
<?php if ($this->params->def('show_page_heading', 1)) : ?>
<h1>
	<?php echo $this->params->get( 'page_heading', $this->params->def('comp_name'));?>	
</h1>
<?php endif; ?>
<?php if($this->params->get('show_category_title', 1) && $this->params->get('page_subheading')) : ?>
<h2>
	<?php echo $this->escape($this->params->get('page_subheading')); ?>
</h2>
<?php endif; ?>
<?php echo $this->loadTemplate('items'); ?>
</div>
<div class="clearfix"></div>
