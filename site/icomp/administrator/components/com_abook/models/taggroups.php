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

jimport( 'joomla.application.component.modellist' );

class AbookModelTaggroups extends JModelList
{
        public function __construct($config = array())
        {
                if (empty($config['filter_fields'])) {
                        $config['filter_fields'] = array(
                                'id', 'a.id',
                                'name', 'a.name',
                                'alias', 'a.alias',
                                'checked_out', 'a.checked_out',
                                'checked_out_time', 'a.checked_out_time',
                                'published', 'a.published',
                                //'access', 'a.access', 'access_level',
                                'ordering', 'a.ordering',
                                'language', 'a.language'
                        );
                }
                parent::__construct($config);
        }

	protected function populateState($ordering = null, $direction = null)
        {
		// Initialise variables.
                $app = JFactory::getApplication('administrator');

                $search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
                $this->setState('filter.search', $search);

                $language = $app->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
                $this->setState('filter.language', $language);

		// Load the parameters.
                $params = JComponentHelper::getParams('com_abook');
                $this->setState('params', $params);

                parent::populateState('a.name', 'asc');
        }

	protected function getStoreId($id = '')
        {
                // Compile the store id.
                $id.= ':' . $this->getState('filter.search');
		$id.= ':' . $this->getState('filter.language');

                return parent::getStoreId($id);
        }

	protected function getListQuery()
        {
                // Create a new query object.
                $db             = $this->getDbo();
                $query  = $db->getQuery(true);

                // Select the required fields from the table.
                $query->select(
                        $this->getState(
                                'list.select',
                                'a.*'
                        )
                );
                $query->from('`#__abtag_groups` AS a');
	
		// Join over the language
                $query->select('l.title AS language_title');
                $query->join('LEFT', '`#__languages` AS l ON l.lang_code = a.language');
	
		// Join over the users for the checked out user.
                $query->select('u.name AS owner');
                $query->join('LEFT', '#__users AS u ON u.id=a.checked_out');


		// Filter by search in title
                $search = $this->getState('filter.search');
                if (!empty($search)) {
                        if (stripos($search, 'id:') === 0) {
                                $query->where('a.id = '.(int) substr($search, 3));
                        } else {
                                $search = $db->Quote('%'.$db->escape($search, true).'%');
                                $query->where('(a.name LIKE '.$search.')');
                        }
                }

		// Filter on the language.
                if ($language = $this->getState('filter.language')) {
                        $query->where('a.language = ' . $db->quote($language));
                }

		// Add the list ordering clause.
                $orderCol       = $this->state->get('list.ordering');
                $orderDirn      = $this->state->get('list.direction');
                if ($orderCol == 'a.ordering' || $orderCol == 'category_title') {
                        $orderCol = 'category_title '.$orderDirn.', a.ordering';
                }
                $query->order($db->escape($orderCol.' '.$orderDirn));
                return $query;
        }
}	
