<?php
/**
 * @package     Joomla.Cms
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

JLoader::register('AbookHelperRoute', JPATH_BASE . '/components/com_abook/helpers/route.php');

?>
<?php if (!empty($displayData)) : ?>
	<div class="tags">
		<span class="icon-tags"></span> <?php echo JText::_('COM_ABOOK_TAGS') .": "; ?> 
		<?php foreach ($displayData as $i => $tag) : ?>
			<?php if (in_array($tag->access, JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id')))) : ?>
				<?php $tagParams = new JRegistry($tag->params); ?>
				<?php $link_class = $tagParams->get('tag_link_class', 'label label-info'); ?>
				<?php $link = JRoute::_(AbookHelperRoute::getTagRoute($tag->tag_id . ':' . $tag->alias)); ?>
				<span class="tag-<?php echo $tag->tag_id; ?> tag-list<?php echo $i ?>">
					<a href="<?php echo $link;?>" class="<?php echo $link_class; ?>">
						<?php echo $this->escape($tag->title); ?>
					</a>
				</span>&nbsp;
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
