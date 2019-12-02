<?php

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class AbookModelTag extends JModelList
{
	protected $_item = null;
	protected $_tag = null;
//questa funzione contiene i libri che risultano dalla query più in basso, si può anche non mettere, la lascio perchè se voglio fare una variazione alla funzione di default basta che scrivo cose dopo $items = &parent::getItems(); facendo un ciclo for per tutti gli items
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
		$user   = JFactory::getUser();
                $groups = implode(',', $user->getAuthorisedViewLevels());

                // Create a new query object.
                $db             = $this->getDbo();
                $query  = $db->getQuery(true);

                // Select required fields from the categories.
                $query->select($this->getState('list.select', 'a.*'));
                $query->from('`#__abbook` AS a');
                $query->where('a.access IN ('.$groups.')');

                // Filter by category.
			$query->select('c.title AS cattitle, c.alias AS catalias');
                        $query->join('LEFT', '#__abcategories AS c ON c.id = a.catid');
                        $query->where('c.access IN ('.$groups.')');
                $query->join('LEFT', '#__abbooktag AS aa ON aa.idbook = a.id');
		$query->where('aa.idtag ='.$this->getState('tag.id'));
                // Filter by state
                $query->where('a.state = 1');
                // Filter by start and end dates.
                $nullDate = $db->Quote($db->getNullDate());
                $nowDate = $db->Quote(JFactory::getDate()->toSql());

                // Filter by language
                if ($this->getState('filter.language')) {
                        $query->where('a.language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')');
		}

		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'a.ordering')).' '.$db->escape($this->getState('list.direction', 'ASC')));

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
                $this->_db->setQuery($query);
                $this->_authorname = $this->_db->loadObjectList();
                return $this->_authorname;
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
		$limit = $app->getUserStateFromRequest('abook.tag.list.limit', 'limit', $params->get('bookpagination'));
		$this->setState('list.limit', $limit);

		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		$this->setState('list.start', $limitstart);

		$orderCol	= JRequest::getCmd('filter_order', $params->get('display_order','ordering'));
		$this->setState('list.ordering', $orderCol);

		$listOrder	=  JRequest::getCmd('filter_order_Dir', $params->get('books_display_order_dir','ASC'));
		$this->setState('list.direction', $listOrder);

		$id = JRequest::getVar('id', 0, '', 'int');
		$this->setState('tag.id', $id);

		$this->setState('filter.published',	1);

		$this->setState('filter.language',$app->getLanguageFilter());
	}

	/**
	 * Method to get tag data for the current tag
	 *
	 * @param	int		An optional ID
	 *
	 * @return	object
	 * @since	1.5
	 */
	public function getTag()
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
		$db             = $this->getDbo();
                $query  = $db->getQuery(true);
                $query->select('*');
                $query->from('#__abtag');
                $query->where('id = '. (int) $this->getState('tag.id'));
                $db->setQuery($query);
                $this->_item = $db->loadObject();


                }
                return $this->_item;
	}

	protected function _Tagname($idbook)
        {
		$db             = $this->getDbo();
                $query  = $db->getQuery(true);
		$query->select('a.name AS title, a.id AS tag_id, 1 AS access, "" AS params, a.alias');
		$query->from('#__abbooktag AS b');
		$query->innerjoin('#__abtag AS a ON a.id = b.idtag');
		$query->where('b.idbook = '. (int) $idbook);
		$query->order('a.name');
                $db->setQuery($query);
                $this->_tagname = $db->loadObjectList();
                return $this->_tagname;
        }

	function _Voting($idbook)
        {
		$db             = $this->getDbo();
                $query  = $db->getQuery(true);
                $query->select('ROUND( v.rating_sum / v.rating_count ) AS rating, v.rating_count');
                $query->from('#__abbook AS a');
                $query->leftjoin('#__abrating AS v ON a.id = v.book_id');
                $query->where('a.id='. (int) $idbook);
                $db->setQuery($query);
                $this->_voting = $db->loadAssoc();
                return $this->_voting;
        }
}
