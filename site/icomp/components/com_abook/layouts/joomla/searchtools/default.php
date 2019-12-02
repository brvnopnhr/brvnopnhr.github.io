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
$params=$data['view']->params;
// Receive overridable options
$data['options'] = !empty($data['options']) ? $data['options'] : array();

// Set some basic options
$customOptions = array(
	'filtersHidden'       => isset($data['options']['filtersHidden']) ? $data['options']['filtersHidden'] : empty($data['view']->activeFilters),
	'defaultLimit'        => isset($data['options']['defaultLimit']) ? $data['options']['defaultLimit'] : $params->get('bookpagination', JFactory::getApplication()->getCfg('list_limit', 20)),
	'searchFieldSelector' => '#filter_search',
	'orderFieldSelector'  => '#list_fullordering'
);

$data['options'] = array_unique(array_merge($customOptions, $data['options']));
$formSelector = !empty($data['options']['formSelector']) ? $data['options']['formSelector'] : '#adminForm';

// Load search tools
JHtml::_('searchtools.form', $formSelector, $data['options']);

?>
<div class="js-stools clearfix">
	<div class="clearfix">
		<div class="js-stools-container-bar">
			<?php echo JLayoutHelper::render('joomla.searchtools.default.bar', $data); ?>
		</div>
		<div class="js-stools-container-list hidden-phone hidden-tablet">
			<?php echo JLayoutHelper::render('joomla.searchtools.default.list', $data); ?>
		</div>
	</div>
	<?php if ($params->get('show_filters_search', 1)!=0){ ?>
	<!-- Filters div -->
	<div class="js-stools-container-filters hidden-phone clearfix">
		<?php echo JLayoutHelper::render('joomla.searchtools.default.filters', $data); ?>
	</div>
	<?php } ?>
</div>
