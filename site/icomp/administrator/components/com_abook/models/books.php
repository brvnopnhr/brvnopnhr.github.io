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


class AbookModelBooks extends JModelList
{
        public function __construct($config = array())
        {
                if (empty($config['filter_fields'])) {
                        $config['filter_fields'] = array(
                                'id', 'a.id',
                                'title', 'a.title',
                                'alias', 'a.alias',
                                'checked_out', 'a.checked_out',
                                'checked_out_time', 'a.checked_out_time',
                                'catid', 'a.catid', 'category_title',
				'ideditor', 'a.ideditor',
                                'state', 'a.state',
                                'access', 'a.access', 'access_level',
                                'dateinsert', 'a.dateinsert',
                                'user_id', 'a.user_id',
                                'ordering', 'a.ordering',
				'catalogo', 'a.catalogo',
                                'language', 'a.language',
                                'hits', 'a.hits',
				'year', 'a.year',
				'idlocation', 'a.idlocation',
				'idlibrary', 'a.idlibrary',
				'published', 'a.published',
				'editor_id',
				'author_id',
				'category_id',
				'level',
                                'tag'
                        );
                }

                parent::__construct($config);
        }
        protected function populateState($ordering = null, $direction = null)
        {
		$app = JFactory::getApplication();
		if ($layout = JRequest::getVar('layout')) {
                        $this->context .= '.'.$layout;
                }
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
                $this->setState('filter.search', $search);

		$access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', 0, 'int');
                $this->setState('filter.access', $access);

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
                $this->setState('filter.published', $published);

		$categoryId = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id');
                $this->setState('filter.category_id', $categoryId);

		$level = $this->getUserStateFromRequest($this->context.'.filter.level', 'filter_level', 0, 'int');
                $this->setState('filter.level', $level);

		$language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
                $this->setState('filter.language', $language);

		$author = $this->getUserStateFromRequest($this->context.'.filter.author_id', 'filter_author_id', '');
                $this->setState('filter.author_id', $author);

		$editor = $this->getUserStateFromRequest($this->context.'.filter.editor_id', 'filter_editor_id', '');
                $this->setState('filter.editor_id', $editor);

		$tag = $this->getUserStateFromRequest($this->context.'.filter.tag_id', 'filter_tag_id', '');
                $this->setState('filter.tag_id', $tag);

		parent::populateState('a.title', 'asc');
		// Force a language
                $forcedLanguage = $app->input->get('forcedLanguage');

                if (!empty($forcedLanguage))
                {
                        $this->setState('filter.language', $forcedLanguage);
                        $this->setState('filter.forcedLanguage', $forcedLanguage);
                }
        }

	protected function getStoreId($id = '')
        {
                // Compile the store id.
                $id.= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.access');
                $id.= ':' . $this->getState('filter.category_id');
		$id.= ':' . $this->getState('filter.language');

                return parent::getStoreId($id);
        }

	protected function getListQuery()
        {
		$user   = JFactory::getUser();
		$db = $this->getDbo();
                $query = $db->getQuery(true);
		$query->select(
                        $this->getState(
                                'list.select',
                                'a.*')
                );
		$query->from('#__abbook AS a');

		// Join over the language
                $query->select('l.title AS language_title');
                $query->join('LEFT', '`#__languages` AS l ON l.lang_code = a.language');
		
		$query->select('c.title AS category');
                $query->join('LEFT', '#__abcategories AS c ON c.id = a.catid');

		$query->select('ee.name AS editor, ee.id AS editorid');
                $query->join('LEFT', '#__abeditor AS ee ON ee.id = a.ideditor');

                $query->join('LEFT', '#__abbookauth AS aa ON aa.idbook = a.id');

		$query->select('GROUP_CONCAT(bb.lastname, " ", bb.name SEPARATOR ", ") AS author');
                $query->join('LEFT', '#__abauthor AS bb ON bb.id=aa.idauth');

                //$query->join('LEFT', '#__users AS u ON u.id = a.checked_out');
		// Join over the users for the checked out user.
                $query->select('uc.name AS owner')
                        ->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		$query->select('lc.name AS location');
                $query->join('LEFT', '#__ablocations AS lc ON lc.id = a.idlocation');

		$query->select('lb.name AS library');
                $query->join('LEFT', '#__ablibrary AS lb ON lb.id = a.idlibrary');

		$query->select('g.title AS access_level');
                $query->join('LEFT', '#__viewlevels AS g ON g.id = a.access');
		$lend=0;
		if ($lend){
		$query->select('le.id AS lend_id');
                $query->join('LEFT', '#__ablend AS le ON le.bookid = a.id');
		}

		// Filter by published state
                $published = $this->getState('filter.published');
                if (is_numeric($published)) {
                        $query->where('a.state = ' . (int) $published);
                } else if ($published === '') {
                        $query->where('(a.state = 0 OR a.state = 1)');
                }

		// Filter by access level.
                if ($access = $this->getState('filter.access'))
                {
                        $query->where('a.access = ' . (int) $access);
                }

		// Filter by a single or group of categories.
		$baselevel = 1;
                $categoryId = $this->getState('filter.category_id');
                if (is_numeric($categoryId)) {
                        $cat_tbl = JTable::getInstance('Category', 'AbookTable');
                        $cat_tbl->load($categoryId);
                        $rgt = $cat_tbl->rgt;
                        $lft = $cat_tbl->lft;
                        $baselevel = (int) $cat_tbl->level;
                        $query->where('c.lft >= '.(int) $lft);
                        $query->where('c.rgt <= '.(int) $rgt);
                }
                elseif (is_array($categoryId)) {
                        JArrayHelper::toInteger($categoryId);
                        $categoryId = implode(',', $categoryId);
                        $query->where('a.catid IN ('.$categoryId.')');
                }

		// Filter on the author.
                if ($author = $this->getState('filter.author_id')) {
                        $query->where('aa.idauth ='.$author);
                }

		// Filter on the editor.
                if ($author = $this->getState('filter.editor_id')) {
                        $query->where('a.ideditor ='.$author);
                }

		// Filter on the tag.
                if ($tag = $this->getState('filter.tag_id')) {
			$query->join('LEFT', '#__abbooktag AS bt ON bt.idbook = a.id');

	                $query->select('t.name AS tag');
        	        $query->join('LEFT', '#__abtag AS t ON t.id=bt.idtag');	
                        $query->where('bt.idtag ='.$tag);
                }

                // Filter on the level.
                if ($level = $this->getState('filter.level')) {
                        $query->where('c.level <= '.((int) $level + (int) $baselevel - 1));
                }

		// Filter by search in title.
		$search = $this->getState('filter.search');
                if (!empty($search)) {
			$search = $db->Quote('%'.$db->escape($search, true).'%');
                        $query->where('(a.title LIKE '.$search.' OR a.alias LIKE '.$search.' OR a.isbn LIKE '.$search.' OR a.catalogo LIKE '.$search.')');
                }

		// Filter on the language.
                if ($language = $this->getState('filter.language')) {
                        $query->where('a.language = '.$db->quote($language));
                }
		
		// Implement View Level Access
                if (!$user->authorise('core.admin'))
                {
                    $groups     = implode(',', $user->getAuthorisedViewLevels());
                        $query->where('a.access IN ('.$groups.')');
                }

		// Add the list ordering clause.
                $orderCol       = $this->state->get('list.ordering', 'a.title');
                $orderDirn      = $this->state->get('list.direction', 'ASC');
                if ($orderCol == 'a.ordering' || $orderCol == 'category') {
                        $orderCol = 'category '.$orderDirn.', a.ordering';
                }
		// SQL server change
                if ($orderCol == 'language')
                {
                        $orderCol = 'l.title';
                }

                if ($orderCol == 'access_level')
                {
                        $orderCol = 'g.title';
                }

                $query->order($db->escape($orderCol.' '.$orderDirn));

		$query->group('a.id');
                return $query;
        }

	public function getItems()
        {
                $items  = parent::getItems();
                $app    = JFactory::getApplication();
                if ($app->isSite()) {
                        $user   = JFactory::getUser();
                        $groups = $user->getAuthorisedViewLevels();

                        for ($x = 0, $count = count($items); $x < $count; $x++) {
                                //Check the access level. Remove articles the user shouldn't see
                                if (!in_array($items[$x]->access, $groups)) {
                                        unset($items[$x]);
                                }
                        }
                }
                return $items;
        }
}
