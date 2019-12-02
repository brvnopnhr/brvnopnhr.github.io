<?php
/**
 * @version		$Id: categoryparent.php 17558 2010-06-08 22:02:10Z chdemko $
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @since		1.6
 */
//campo per la scelta della parent category nel form di inserimento delle categorie del backend
class JFormFieldCategoryParent extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'CategoryParent';
	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();
		$name = (string) $this->element['name'];

                // Let's get the id for the current item, either category or content item.
                $jinput = JFactory::getApplication()->input;
                // For categories the old category is the category id 0 for new category.
                if ($this->element['parent'])
                {
                        $oldCat = $jinput->get('id', 0);
                }
                else
                        // For items the old category is the category they are in when opened or 0 if new.
                {
                        $oldCat = $this->form->getValue($name);
                }

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('a.id AS value, a.title AS text, a.level');
		$query->from('#__abcategories AS a');
		$query->leftjoin('`#__abcategories` AS b ON a.lft > b.lft AND a.rgt < b.rgt');

$app = JFactory::getApplication();
$params = $app->getParams();
		if ($id=$params->get('id')){
			$query->where('b.id='.$params->get('id'));
		}
		if ($params->get('maxLevel') != -1){
			$query->where('a.level <= b.level + '.(int) $params->get('maxLevel'));
		}
		$query->where('a.published IN (0,1)');

		if ($this->element['show_root']=="false"){
			$query->where('a.level != 0');
		}

		$query->group('a.id');
		$query->order('a.lft ASC');
		// Get the options.
		$db->setQuery($query);
		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}

		// Pad the option text with spaces using depth level as a multiplier.
		for ($i = 0, $n = count($options); $i < $n; $i++) {
			// Translate ROOT
			if ($options[$i]->level == 0) {
				$options[$i]->text = JText::_('JGLOBAL_ROOT_PARENT');
			}
			$options[$i]->text = str_repeat('- ',$options[$i]->level).$options[$i]->text;
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
