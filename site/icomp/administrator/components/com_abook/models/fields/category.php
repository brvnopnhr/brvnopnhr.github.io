<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_BASE') or die;
jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
//sarebbe più giusto mettere JPATH_COMPONENT_ADMINISTRATOR, però non funziona perchè risulta essere com_menu, è come se non prendesse com_abook, nella versione com_abook16_1_1_0_install_6.tgz funziona, poi non so quale modifica ha smesso di farlo funzionare
//JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers');
JHtml::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_abook'.DS.'helpers'.DS.'html');

JFormHelper::loadFieldClass('list');
/**
 * Supports an HTML select list of categories
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldCategory extends JFormFieldList
{
	/**
	 * @var    string	The form field type.
	 * @since  11.1
	 */
	public $type = 'Category';

	/**
	 * Method to get the field options.
	 *
	 * @return  array    The field option objects.
	 * @since   11.1
	 */
	protected function getOptions()
	{
		// Initialise variables.
		$options	= array();
		$extension	= $this->element['extension'] ? (string) $this->element['extension'] : (string) $this->element['scope'];
		$published	= (string) $this->element['published'];

		// Load the category options for a given extension.
		if (!empty($extension)) {

			// Filter over published state or not depending upon if it is present.
			if ($published) {
				$options = JHtml::_('category.options', $extension, array('filter.published' => explode(',', $published)));
			}
			else {
				$options = JHtml::_('category.options', $extension);
			}

			// Verify permissions.  If the action attribute is set, then we scan the options.
			if ($action	= (string) $this->element['action']) {

				// Get the current user object.
				$user = JFactory::getUser();

				foreach($options as $i => $option)
				{
					// To take save or create in a category you need to have create rights for that category
					// unless the item is already in that category.
					// Unset the option if the user isn't authorised for it. In this field assets are always categories.
					if ($user->authorise('core.create', $extension.'.category.'.$option->value) != true ) {
						unset($options[$i]);
					}
				}

			}

			if (isset($this->element['show_root'])) {
				array_unshift($options, JHtml::_('select.option', '0', JText::_('JGLOBAL_ROOT')));
			}
		}
		else {
			JError::raiseWarning(500, JText::_('JLIB_FORM_ERROR_FIELDS_CATEGORY_ERROR_EXTENSION_EMPTY'));
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
