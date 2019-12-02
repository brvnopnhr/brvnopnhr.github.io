<?php
/**
 * @package     Joomla.Platform
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Utility class for categories
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 */
//tendina delle categorie nella vista della ricerca del frontend
class JHtmlAbeditor
{
	/**
	 * @var    array  Cached array of the category items.
	 */
	protected static $items = array();

	/**
	 * Returns an array of categories for the given extension.
	 *
	 * @param   string  The extension option.
	 * @param   array   An array of configuration options. By default, only published and unpulbished categories are returned.
	 *
	 * @return  array
	 */
	public static function options($categoryId, $includeSubcategories=false, $levels=2)
	{
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);

			$query->select('DISTINCT e.id AS value, e.name AS text');
			$query->from('#__abcategories AS c');
			$query->leftjoin('#__abbook AS b ON b.catid=c.id');
			$query->leftjoin('#__abeditor AS e ON e.id=b.ideditor');
			$query->where('b.ideditor > 0');
			// Filter by category.
                        $categoryEquals = 'b.catid ='.(int) $categoryId;

                        if ($includeSubcategories) {
                                // Create a subquery for the subcategory list
                                $subQuery = $db->getQuery(true);
                                $subQuery->select('sub.id');
                                $subQuery->from('#__abcategories as sub');
                                $subQuery->join('INNER', '#__abcategories as this ON sub.lft > this.lft AND sub.rgt < this.rgt');
                                $subQuery->where('this.id = '.(int) $categoryId);
                                if ($levels >= 0) {
                                        $subQuery->where('sub.level <= this.level + '.$levels);
                                }
                                // Add the subquery to the main query
                                $query->where('('.$categoryEquals.' OR b.catid IN ('.$subQuery->__toString().'))');
                        }else {
                                $query->where($categoryEquals);
                        }
			$query->group('e.id');
			$query->order('e.name');

			$db->setQuery($query);
			$items = $db->loadObjectList();

			// Assemble the list options.
			self::$items = array();
			self::$items[] = JHtml::_('select.option', '0', JText::_('JLIB_FORM_BUTTON_SELECT'));
			foreach ($items as &$item) {
				self::$items[] = JHtml::_('select.option', $item->value, $item->text);
			}
		return self::$items;
	}
}
