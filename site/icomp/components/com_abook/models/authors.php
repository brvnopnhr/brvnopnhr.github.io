<?php

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
jimport('joomla.application.categories');

class AbookModelAuthors extends JModelList
{
	protected $_item = null;

	/**
	 * Method to get a list of items.
	 *
	 * @return	mixed	An array of objects on success, false on failure.
	 */

	protected function getListQuery()
	{
		$params = $this->getState()->get('params');

		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select required fields from the categories.
		$query->select($this->getState('list.select', 'a.*'));
		$query->from('`#__abauthor` AS a');

		if ($this->state->params->get('author_name_display', 0) == 1){
                        $query->select('CONCAT(a.name, " ", a.lastname) AS name');
                }else{
                        $query->select('CONCAT(a.lastname, " ", a.name) AS name');
                }
		
		// Filter by language
		if ($this->getState('filter.language')) {
			$query->where('a.language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')');
		}

		// Add the list ordering clause.
//		$query->order($db->escape($this->getState('list.ordering', 'a.ordering')).' '.$db->escape($this->getState('list.direction', 'ASC')));


		if ($this->getState('filter.letter')) {
			$query->where("a.lastname REGEXP '^".$this->getState('filter.letter')."'");
		}
		if ($this->getState('filter.keyword') !='') {
			$query->where('LOWER( a.name ) LIKE "%'.$this->getState('filter.keyword').'%"');
		}

		if ($this->getState('list.ordering') !='') {
                        $query->order($this->getState('list.ordering'). ' '.$this->getState('list.direction'));
                }
		return $query;
	}

	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app    = JFactory::getApplication('site');
		$itemid = JRequest::getInt('id', 0) . ':' . JRequest::getInt('Itemid', 0);
                // Load the parameters. Merge Global and Menu Item params into new object
                $params = $app->getParams();
                $menuParams = new JRegistry;

                if ($menu = $app->getMenu()->getActive()) {
                        $menuParams->loadString($menu->params);
                }
                $mergedParams = clone $menuParams;
                $mergedParams->merge($params);
		// Load the parameters.
                $this->setState('params', $mergedParams);

		// List state information
		$limit = $app->getUserStateFromRequest('abook.authors.list.limit', 'limit', $params->get('bookpagination'));
		$this->setState('list.limit', $limit);

		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		$this->setState('list.start', $limitstart);

		$orderCol	= JRequest::getCmd('filter_order', $params->get('author_display_order','lastname'));
		$this->setState('list.ordering', $orderCol);

		$listOrder	=  JRequest::getCmd('filter_order_Dir', $params->get('author_display_order_dir','ASC'));
		$this->setState('list.direction', $listOrder);

		$this->setState('filter.language',$app->getLanguageFilter());
		$showSubcategories = $params->get('show_subcategory_book', '1');

		$letter      =  JRequest::getVar('letter', '');
                $this->setState('filter.letter', $letter);

		$this->setState('filter.keyword', JRequest::getString('filter-keyword'));
		//$this->setState('filter.category_id', JRequest::getInt('filter-category_id'));
		$author=$app->getUserStateFromRequest('abook.category.list.' . $itemid . '.author', 'filter-author_id', JRequest::getInt('filter-author_id'));
	}
}
