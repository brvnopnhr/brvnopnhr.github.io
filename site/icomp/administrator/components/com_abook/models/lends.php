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


class AbookModelLends extends JModelList
{
        public function __construct($config = array())
        {
                if (empty($config['filter_fields'])) {
                        $config['filter_fields'] = array(
                                'id', 'a.id',
                                'user_id', 'a.user_id',
                                'checked_out', 'a.checked_out',
                                'checked_out_time', 'a.checked_out_time',
                                'admin_id', 'a.admin_id',
                                'state', 'a.state',
                                'ordering', 'a.ordering',
				"library_id", "b.idlibrary",
				"lend_out", "a.lend_out",
				"lend_in", "a.lend_in",
				"title", "b.title",
				"catalogo", "b.catalogo",
				"name", "u.name"
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

		$state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state');
                $this->setState('filter.state', $state);

		$book_id = $this->getUserStateFromRequest($this->context.'.filter.book_id', 'filter_book_id');
                $this->setState('filter.book_id', $book_id);

		parent::populateState('a.state', 'DESC');
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
		$id .= ':' . $this->getState('filter.state');
                $id.= ':' . $this->getState('filter.library_id');

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
		$query->from('#__ablend AS a');

		$query->select('b.title, b.catalogo, b.catid, b.ideditor, b.isbn, b.qty');
                $query->join('LEFT', '`#__abbook` AS b ON b.id = a.book_id');
		// Join over the users for the checked out user.
                $query->select('COALESCE(u.name,a.custom_user_name) AS user_name, COALESCE(u.email, a.custom_user_email) AS user_email')
                        ->join('LEFT', '#__users AS u ON u.id=a.user_id');

		$query->select('uc.name AS owner')
                        ->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		$query->select('lb.name AS library');
                $query->join('LEFT', '#__ablibrary AS lb ON lb.id = b.idlibrary');

		// Filter by published state
           //     $query->where('b.state IN (0,1,2)');

		// Filter by search in title.
		$search = $this->getState('filter.search');
                if (!empty($search)) {
			$search = $db->Quote('%'.$db->escape($search, true).'%');
                        $query->where('(b.title LIKE '.$search.' OR b.alias LIKE '.$search.' OR b.isbn LIKE '.$search.' OR b.catalogo LIKE '.$search.')');
                }

		// Filter by published state
                $book_id = $this->getState('filter.book_id');
                if (!empty($book_id)) {
                        $query->where('book_id='.$book_id);
                }

		$state = $this->getState('filter.state', '');
                if (is_numeric($state))
                {
                        $query->where('a.state = ' . (int) $state);
                }elseif ($state === '')
                {
                        $query->where('(a.state = 0 OR a.state = 2)');
                }
		// Add the list ordering clause.
                $orderCol       = $this->state->get('list.ordering', 'a.state');
                $orderDirn      = $this->state->get('list.direction', 'DESC');
                if ($orderCol == 'a.ordering' || $orderCol == 'category') {
                        $orderCol = 'category '.$orderDirn.', a.ordering';
                }

                $query->order($db->escape($orderCol.' '.$orderDirn));
                return $query;
        }

}
