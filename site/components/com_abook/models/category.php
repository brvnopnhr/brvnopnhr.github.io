<?php

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
jimport('joomla.application.categories');

class AbookModelCategory extends JModelList
{
	protected $_item = null;

	//protected $_articles = null;

	protected $_siblings = null;

	protected $_children = null;

	protected $_parent = null;

	/**
	 * The category that applies.
	 *
	 * @access	protected
	 * @var		object
	 */
	protected $_category = null;

	/**
	 * The list of other newfeed categories.
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $_categories = null;

	/**
	 * Method to get a list of items.
	 *
	 * @return	mixed	An array of objects on success, false on failure.
	 */
//questa funzione contiene i libri che risultano dalla query più in basso
	public function getItems()
	{
		// Invoke the parent getItems method to get the main list
		$items = parent::getItems();
		for ($i = 0, $n = count($items); $i < $n; $i++) {
                        $item = &$items[$i];
                        $this->_authorname($item->id);
                        $item->authors=$this->_authorname;
			$this->_tagname($item->id);
                        $item->tags=$this->_tagname;
			$this->_voting($item->id);
                        $item->vote=$this->_voting;
                }
		return $items;
	}

	//query che tira fuori i libri di una categoria che vengono messi nel risultato della funzione getItems
	protected function getListQuery()
	{
		$params = $this->getState()->get('params');
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());

		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select required fields from the categories.
		$query->select($this->getState('list.select', 'a.*'));
		$query->from('`#__abbook` AS a');
		$query->where('a.access IN ('.$groups.')');
		
		$query->select('loc.name AS location');
                $query->leftjoin('#__ablocations AS loc ON loc.id=a.idlocation');

		// Filter by category.
		$includeSubcategories = $this->getState('filter.subcategories', false);
		$categoryId = $this->getState('filter.category_id') !=""?$this->getState('filter.category_id'):$this->getState('category.id');	
		$categoryEquals = 'a.catid ='.(int) $categoryId;

		if ($includeSubcategories) {
                	$levels = (int) $this->getState('filter.max_category_levels', 2);
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
                        $query->where('('.$categoryEquals.' OR a.catid IN ('.$subQuery->__toString().'))');
                }else {
                        $query->where($categoryEquals);
                }


			//$query->where('a.catid = '.(int) $categoryId);
			$query->select('c.title AS cattitle, c.alias AS catalias');
			$query->join('LEFT', '#__abcategories AS c ON c.id = a.catid');
			$query->where('c.access IN ('.$groups.')');
		
		// Join on voting table
                $query->select('ROUND( v.rating_sum / v.rating_count ) AS rating, v.rating_count as rating_count');
                $query->join('LEFT', '#__abrating AS v ON a.id = v.book_id');

		// Filter by start and end dates.
		$nullDate = $db->Quote($db->getNullDate());
		$nowDate = $db->Quote(JFactory::getDate()->toSql());

		// Filter by language
		$query->where('a.language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')');

		// Filter by status
                //$query->where("a.published='".$params->get('published')."'");
		                // Filter by published state
                $published = $params->get('published');
                if ($published > 0) {
                        $query->where('a.state = ' . (int) $published);
                } else {
                        $query->where('(a.state = 1 OR a.state = 2)');
                }
		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'a.ordering')).' '.$db->escape($this->getState('list.direction', 'ASC')).', a.ordering');


		if ($this->getState('filter.letter')) {
			$query->where("a.title REGEXP '^".$this->getState('filter.letter')."'");
		}
		if ($this->getState('filter.keyword') !='') {
			$query->where('LOWER( a.title ) LIKE "%'.$this->getState('filter.keyword').'%"');
		}

		if ($this->getState('filter.year') > 0) {
                        $query->where('a.year = '.$this->getState('filter.year'));
                }

		if ($this->getState('filter.editor_id') > 0) {
                        $query->where('a.ideditor = '.$this->getState('filter.editor_id'));
                }

		if ($this->getState('filter.author_id') > 0) {
			$query->leftjoin('#__abbookauth AS ba ON ba.idbook = a.id');
                        $query->where('ba.idauth = '.$this->getState('filter.author_id'));
                }
		
		if ($this->getState('filter.tag_id') > 0) {
                        $query->leftjoin('#__abbooktag AS bt ON bt.idbook = a.id');
                        $query->where('bt.idtag = '.$this->getState('filter.tag_id'));
                }

		$query->group('a.id');
		return $query;
	}

	protected function _Authorname($idbook)
        {
		$db             = $this->getDbo();
                $query  = $db->getQuery(true);
                // Lets load the content if it doesn't already exist
		if ($this->state->params->get('name_display', 0) == 1){
			$query->select('CONCAT(a.name, " ", a.lastname) AS author, a.id AS idauthor, a.alias');
		}else{
			$query->select('CONCAT(a.lastname, " ", a.name) AS author, a.id AS idauthor, a.alias');
		}
                $query->from('#__abbookauth AS b');
		$query->innerjoin('#__abauthor AS a ON a.id = b.idauth');
		$query->where('b.idbook = '. (int) $idbook);
		$query->order('a.lastname');
                $db->setQuery($query);
                $this->_authorname = $db->loadObjectList();
                return $this->_authorname;
        }

	protected function _Tagname($idbook)
        {
		$db             = $this->getDbo();
                $query  = $db->getQuery(true);
                //$query->select('a.name AS tag, a.id AS idtag, a.alias');
		$query->select('a.name AS title, a.id AS tag_id, 1 AS access, "" AS params, a.alias');
                $query->from('#__abbooktag AS b');
                $query->innerjoin('#__abtag AS a ON a.id = b.idtag');
                $query->where('b.idbook = '. (int) $idbook);
		$query->where('a.language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')');
                $query->order('a.name');
                $db->setQuery($query);
                $this->_tagname = $db->loadObjectList();
                return $this->_tagname;
        }

	function _Voting($idbook)
        {
                $query='SELECT ROUND( v.rating_sum / v.rating_count ) AS rating, v.rating_count'
                	.' FROM #__abbook AS a'
                        .' LEFT JOIN #__abrating AS v ON a.id = v.book_id'
                        .' WHERE a.id='. (int) $idbook;
                $this->_db->setQuery($query);
                $this->_voting = $this->_db->loadAssoc();
                return $this->_voting;
        }
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app    = JFactory::getApplication('site');
		$itemid = $app->input->get('id', 0, 'int') . ':' . $app->input->get('Itemid', 0, 'int');	
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
		$limit = $app->getUserStateFromRequest('com_abook.category.list.' . $itemid . '.limit', 'limit', $params->get('bookpagination'), 'uint');
		$this->setState('list.limit', $limit);

		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		$this->setState('list.start', $limitstart);

		$orderCol	= JRequest::getCmd('filter_order', $params->get('display_order','ordering'));
		$this->setState('list.ordering', $orderCol);

		$listOrder	=  JRequest::getCmd('filter_order_Dir', $params->get('books_display_order_dir','ASC'));
		$this->setState('list.direction', $listOrder);

		//$id = JRequest::getVar('id', 0, '', 'int');
		//$category = $app->getUserStateFromRequest('abook.category.list.' . $itemid . '.category', 'filter-category_id', JRequest::getInt('filter-category_id', $params->get('id')));
		$category = $app->getUserStateFromRequest('abook.category.list.' . $itemid . '.category', 'filter-category_id', $app->input->getString('filter-category_id'), $params->get('id'));
		$this->setState('filter.category_id', $category);
		
		$pk = JRequest::getInt('id');
                $this->setState('category.id', $pk);

		$this->setState('filter.published',	1);

		$this->setState('filter.language',$app->getLanguageFilter());
		$showSubcategories = $params->get('show_subcategory_book', '1');

                if ($showSubcategories) {
                        $this->setState('filter.max_category_levels', $params->get('show_subcategory_book', '1'));
                        $this->setState('filter.subcategories', true);
                }

		$letter      =  JRequest::getvar('letter', '');
                $this->setState('filter.letter', $letter);
		$keyword=$app->getUserStateFromRequest('abook.category.list.' . $itemid . '.keyword', 'filter-keyword', $app->input->getString('filter-keyword'));
		$this->setState('filter.keyword', $keyword);
		//$this->setState('filter.category_id', JRequest::getInt('filter-category_id'));
		//$author=$app->getUserStateFromRequest('abook.category.list.' . $itemid . '.author', 'filter-author_id', JRequest::getInt('filter-author_id'));
		$author=$app->getUserStateFromRequest('abook.category.list.' . $itemid . '.author', 'filter-author_id', $app->input->getString('filter-author_id'));
                $this->setState('filter.author_id', $author);
//		$year=$app->getUserStateFromRequest('abook.category.list.' . $itemid . '.year', 'filter-year', JRequest::getInt('filter-year'));
		$year=$app->getUserStateFromRequest('abook.category.list.' . $itemid . '.year', 'filter-year', $app->input->getString('filter-year'));
		$this->setState('filter.year', $year);
		//$editor=$app->getUserStateFromRequest('abook.category.list.' . $itemid . '.editor_id', 'filter-editor_id', JRequest::getInt('filter-editor_id'));
		$editor=$app->getUserStateFromRequest('abook.category.list.' . $itemid . '.editor_id', 'filter-editor_id', $app->input->getString('filter-editor_id'));
		$this->setState('filter.editor_id', $editor);
		//$tag=$app->getUserStateFromRequest('abook.category.list.' . $itemid . '.tag_id', 'filter-tag_id', JRequest::getInt('filter-tag_id'));
		$tag=$app->getUserStateFromRequest('abook.category.list.' . $itemid . '.tag_id', 'filter-tag_id', $app->input->getString('filter-tag_id'));
                $this->setState('filter.tag_id', $tag);
	}

	/**
	 * Method to get category data for the current category
	 *
	 * @param	int		An optional ID
	 *
	 * @return	object
	 * @since	1.5
	 */
	public function getCategory()
	{
                if (!is_object($this->_item)) {
                        if( isset( $this->state->params ) ) {
                                $params = $this->state->params;
                                $options = array();
                                $options['countItems'] = $params->get('show_item_count', 0);
                        }
                        else {
                                $options['countItems'] = 0;
                        }
                        $categories = JCategories::getInstance('Abook', $options);
                        $this->_item = $categories->get($this->getState('category.id', 'root'));

                        if (is_object($this->_item)) {
                                $this->_children = $this->_item->getChildren();
                                $this->_parent = false;

                                if ($this->_item->getParent()) {
                                        $this->_parent = $this->_item->getParent();
                                }

                                $this->_rightsibling = $this->_item->getSibling();
                                $this->_leftsibling = $this->_item->getSibling(false);
                        } else {
                                $this->_children = false;
                                $this->_parent = false;
                        }
                }
                return $this->_item;
	}
	 
	public function getParent()
	{
		if(!is_object($this->_item))
		{
			$this->getCategory();
		}
		return $this->_parent;
	}

	function &getLeftSibling()
	{
		if(!is_object($this->_item))
		{
			$this->getCategory();
		}
		return $this->_leftsibling;
	}

	function &getRightSibling()
	{
		if(!is_object($this->_item))
		{
			$this->getCategory();
		}
		return $this->_rightsibling;
	}

	function &getChildren()
	{
		if(!is_object($this->_item))
		{
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
	}
}
