<?php
/**
 * @package Joomla
 * @subpackage Abook
 * @copyright (C) 2010 Ugolotti Federica
 * @license GNU/GPL, see LICENSE.php
 * Abook is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License 2
 * as published by the Free Software Foundation.

 * Abook is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with Abook; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.modellist');
	
class AbookModelAuthor extends JModelList
{
	protected $_item = null;
	protected $_authorname = null;

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
		$this->setState('params', $mergedParams);

                // List state information
                $limit = $app->getUserStateFromRequest('abook.author.list.limit', 'limit', $params->get('bookpagination'));
                $this->setState('list.limit', $limit);

                $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
                $this->setState('list.start', $limitstart);

		$orderCol       = JRequest::getCmd('filter_order', $params->get('display_order','ordering'));
                $this->setState('list.ordering', $orderCol);

                $listOrder      =  JRequest::getCmd('filter_order_Dir', $params->get('books_display_order_dir','ASC'));
                $this->setState('list.direction', $listOrder);

                $id = JRequest::getVar('id', 0, '', 'int');
                $this->setState('author.id', $id);

                $this->setState('filter.published',     1);

                $this->setState('filter.language',$app->getLanguageFilter());
        }


        public function getListQuery()
        {
                $user   = JFactory::getUser();
                $groups = implode(',', $user->getAuthorisedViewLevels());

                // Create a new query object.
                $db             = $this->getDbo();
                $query  = $db->getQuery(true);
                                $query->select($this->getState('item.select', 'book.*'));
                                $query->from('#__abbookauth AS authbook');
				$query->join('LEFT', '#__abbook AS book ON authbook.idbook=book.id');
                                // Join on category table.
                                $query->select('c.title AS category_title, c.alias AS category_alias, c.access AS category_access');
                                $query->join('LEFT', '#__abcategories AS c on c.id = book.catid');
				
				$query->join('LEFT', '#__abauthor AS author on author.id = authbook.idauth');
                                // Join over the categories to get parent category titles
                                $query->select('parent.title as parent_title, parent.id as parent_id, parent.path as parent_route, parent.alias as parent_alias');
                                $query->join('LEFT', '#__abcategories as parent ON parent.id = c.parent_id');

                                $query->where('author.id = ' . (int) $this->getState('author.id'));
				                                // Filter by published state.
                                $published = $this->getState('filter.published');
                                if (is_numeric($published)) {
                                        $query->where('book.state = ' . (int) $published);
                                }
			$query->order($db->escape($this->getState('list.ordering', 'a.ordering')).' '.$db->escape($this->getState('list.direction', 'ASC')));
			return $query;
        }


	protected function _Authorname($idbook)
        {
                $db             = $this->getDbo();
                $query  = $db->getQuery(true);
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
                // Lets load the content if it doesn't already exist
		$db             = $this->getDbo();
                $query  = $db->getQuery(true);
                $query->select('a.name AS title, a.id AS tag_id, 1 AS access, "" AS params, a.alias');
                $query->from('#__abbooktag AS b');
                $query->innerjoin('#__abtag AS a ON a.id = b.idtag');
                $query->where('b.idbook = '. (int) $idbook);
		$query->where('a.language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')');
                $db->setQuery($query);
                $this->_tagname = $db->loadObjectList();
                return $this->_tagname;
        }

	public function &getAuthor()
        {
                $pk = (int) $this->getState('author.id');
                if ($this->_item === null) {
                        $this->_item = array();
                }
                if (empty($this->_item)) {
                        try {
                                $db = $this->getDbo();
                                $query = $db->getQuery(true);

                                $query->select($this->getState('item.select', 'ab.*'));
                                $query->from('#__abbookauth AS ab');

                                // Join on authors table.
				if ($this->state->params->get('name_display', 0) == 1){
                        		$query->select('CONCAT(a.name, " ", a.lastname) AS author_name');
		                }else{
                		        $query->select('CONCAT(a.lastname, " ", a.name) AS author_name');
                		}
                                $query->select('a.id AS author_id, a.description, a.image, a.metakey, a.metadesc');
                                $query->rightjoin('#__abauthor AS a on a.id = ab.idauth');

                                $query->where('a.id = ' . (int) $pk);

                                $db->setQuery($query);

                                $data = $db->loadObject();
                                if (empty($data)) {
                                        return JError::raiseError(404, JText::_('COM_CONTENT_ERROR_ARTICLE_NOT_FOUND'));
                                }
                                $this->_item = $data;
                        }
                        catch (Exception $e)
                        {
                                $this->setError($e);
                                $this->_item = false;
                        }
                }
                return $this->_item;
	}
	
	function _Voting($idbook)
        {
		$db = $this->getDbo();
                $query = $db->getQuery(true);
                $query->select('ROUND( v.rating_sum / v.rating_count ) AS rating, v.rating_count');
                $query->from('#__abbook AS a');
                $query->leftjoin('#__abrating AS v ON a.id = v.book_id');
                $query->where('a.id='. (int) $idbook);
                $db->setQuery($query);
                $this->_voting = $db->loadAssoc();
                return $this->_voting;
        }
}
