<?php
/**
 * @version		$Id: categories.php
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
jimport('joomla.application.categories');
JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers/categories.php');

class AbookModelCategories extends JModelList
{
	public $_context = 'com_abook.categories';

	protected $_extension = 'com_abook';

	private $_parent = null;

	private $_items = null;

	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();
		$this->setState('filter.extension', $this->_extension);

		// Get the parent id if defined.
		$parentId = JRequest::getInt('id');
		$this->setState('id', $parentId);

		$params = $app->getParams();

                $menuParams = new JRegistry;

                if ($menu = $app->getMenu()->getActive()) {
                        $menuParams->loadString($menu->params);
                }
                $mergedParams = clone $menuParams;
                $mergedParams->merge($params);


		$this->setState('params', $mergedParams);
		// List state information
                $limit = $app->getUserStateFromRequest('abook.categories.list.limit', 'limit', $params->get('display_num'));
                $this->setState('list.limit', $limit);

                $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
                $this->setState('list.start', $limitstart);

		$this->setState('filter.published',	1);
		$this->setState('filter.access',	true);
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.extension');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.parentId');

		return parent::getStoreId($id);
	}

        protected function getListQuery()
        {
		$params = $this->getState()->get('params');
                $user   = JFactory::getUser();
                $groups = implode(',', $user->getAuthorisedViewLevels());
		$app = JFactory::getApplication();

                // Create a new query object.
                $db = $this->getDbo();
                $query = $db->getQuery(true);

                // Select the required fields from the table.
                $query->select('c.*');
		$query->select('CASE WHEN '.$query->charLength('c.alias', '!=', '0').' THEN '.$query->concatenate(array('c.id', 'c.alias'), ':'). ' ELSE c.id END as slug');
                $query->from('#__abcategories AS c');

                $query->select('u.name AS owner');
                $query->join('LEFT', '#__users AS u ON u.id = c.checked_out');

                $query->select('ag.title AS access_level');
                $query->leftjoin('#__viewlevels AS ag ON ag.id = c.access');


		$level = $params->get('maxLevel', '-1');
		if ($this->getState('id') != '0') {
			$query->where('s.id=' . (int) $this->getState('id'));
			if ($app->isSite() && $app->getLanguageFilter()){
				$query->leftJoin('#__abcategories AS s ON (s.lft < c.lft AND s.rgt >= c.rgt AND c.language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . '))');
			}else {
				$query->leftJoin('#__abcategories AS s ON (s.lft < c.lft AND s.rgt >= c.rgt)');
                        }
			if ($level > 0) {
                        	$query->where('c.level <= s.level + '.(int) $level);
                	}	
		}else{
//			$query->leftJoin('#__categories AS s ON (s.lft <= c.lft AND s.rgt >= c.rgt) OR (s.lft >= c.lft AND s.rgt <= c.rgt)');
			if ($app->isSite() && $app->getLanguageFilter())
                        {
                                $query->where('c.language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')');
                        }
			if ($level > 0) {
                        	$query->where('c.level <= '.(int) $level);	
                	}
		}

		if ($params->get('show_item_count', 0) == 1)
                {
                        $query->leftJoin(
                        	'#__abbook AS i ON i.catid = c.id AND i.state = 1 AND i.language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')'
                        );
                        $query->select('COUNT(i.id) AS numitems');
                }

/*		$subQuery = ' (SELECT cat.id as id FROM #__abcategories AS cat JOIN #__abcategories AS parent ' .
                        'ON cat.lft BETWEEN parent.lft AND parent.rgt WHERE parent.extension = ' . $db->quote($extension) .
                        ' AND parent.published != 1 GROUP BY cat.id) ';
                $query->leftJoin($subQuery . 'AS badcats ON badcats.id = c.id');
                $query->where('badcats.id is null');*/

                // Filter by extension
                $query->where("c.extension = 'com_abook'");

                $query->where('c.published =1');

                // Filter by access level.
		$query->where('c.access IN ('.$groups.')');

                if ($language = $this->getState('filter.language')) {
                        $query->where('c.language IN ( '.$db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*').')');
                }
		$order=$params->get('cat_display_order')=='ordering'?'lft':$params->get('cat_display_order', 'lft');
                //$query->order('c.lft, c.level ASC, c.parent_id ASC, c.'.$params->get('cat_display_order', 'title').' '.$params->get('cat_display_order_dir', 'ASC'));
		$query->order('c.'.$order.' '.$params->get('cat_display_order_dir', 'ASC').', c.lft, c.level');
//provare anche questo che ho messo da centroeinaudi
//$query->order($params->get('cat_display_order', 'title').' '.$params->get('cat_display_order_dir', 'ASC').', c.lft, c.level ASC, c.parent_id ASC');

                $query->group('c.id, c.language, c.level, c.lft, c.parent_id, c.path, c.rgt');
                return $query;
        }

	public function getParent()
	{
		if(!isset($this->_parent)){
			$db = $this->getDbo();
	                $query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__abcategories');
			$query->where('id ='.$this->getState('id'));
			$db->setQuery($query);
                        $this->_parent = $db->loadObject();
			
		}
		return $this->_parent;
	}

//ho messo questa funzione per cercare di sovrascrivere quella originale e aggiungere l'ordinamento ma non funziona. 14/08/2011
	/*function &getChildren()
        {
                if(!is_object($this->_items)){
                        $this->getCategory();
                }
                // Order subcategories
                if (sizeof($this->_children)) {
                        $params = $this->getState()->get('params');
                        if ($params->get('cat_display_order') == 'title' || $params->get('cat_display_order') == 'rtitle') {
                                jimport('joomla.utilities.arrayhelper');
                                JArrayHelper::sortObjects($this->_children, $params->get('cat_display_order'), ($params->get('cat_display_order') == 'title') ? 1 : -1);
                        }
                }
                return $this->_children;
        }*/
}
