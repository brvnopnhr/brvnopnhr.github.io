<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

$data = $displayData;

// Load the form filters
$filters = $data['view']->filterForm->getGroup('filter');
?>
<?php if ($filters) : ?>
	<?php foreach ($filters as $fieldName => $field) : ?>
		<?php if ($fieldName != 'filter_search') : ?>

			<?php 
			if (($fieldName == 'filter_category_id' && $data['view']->params->get('show_search_cat')==0) ||
				($fieldName == 'filter_author_id' && $data['view']->params->get('show_search_auth')==0) ||
				($fieldName == 'filter_editor_id' && $data['view']->params->get('show_search_editor')==0) ||
				($fieldName == 'filter_location_id' && $data['view']->params->get('show_search_loc')==0) ||
				($fieldName == 'filter_library_id' && $data['view']->params->get('show_search_lib')==0) ||
				($fieldName == 'filter_tag_id' && $data['view']->params->get('show_search_tag')==0) ||
				($fieldName == 'filter_year' && $data['view']->params->get('show_search_year')==0)
			){
				continue;
			}?>
			<div class="js-stools-field-filter">
				<?php echo $field->input; ?>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>
