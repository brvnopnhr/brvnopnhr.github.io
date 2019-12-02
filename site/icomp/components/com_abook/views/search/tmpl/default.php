<?php
// no direct access
defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers');
JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers/html');
$pageClass = $this->params->get('pageclass_sfx');
//echo $this->params->get( 'show_filters');
?>
<div class="blog<?php echo $pageClass;?>">
	<?php if ($this->params->def('show_page_heading', 1)) : ?>
	<h1>
		<?php echo $this->params->get( 'page_heading', $this->params->def('comp_name'));?>	
	</h1>
	<?php endif; ?>
	<?php echo $this->loadTemplate('items'); ?>
	<div class="clearfix"></div>
</div>
