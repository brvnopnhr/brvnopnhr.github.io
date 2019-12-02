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

class AbookModelCategories extends JModelList
{
	public function __construct($config = array())
        {
                if (empty($config['filter_fields'])) {
                        $config['filter_fields'] = array(
                                'id', 'a.id',
                                'title', 'a.title',
                                'alias', 'a.alias',
                                'published', 'a.published',
                                'access', 'a.access', 'access_level',
                                'language', 'a.language',
                                'checked_out', 'a.checked_out',
                                'checked_out_time', 'a.checked_out_time',
                                'created_time', 'a.created_time',
                                'created_user_id', 'a.created_user_id',
                                'lft', 'a.lft',
                                'rgt', 'a.rgt',
                                'level', 'a.level',
                                'path', 'a.path',
                        );
                }

                parent::__construct($config);
        }

	protected function populateState($ordering = null, $direction = null)
        {
                // Initialise variables.
                $app            = JFactory::getApplication();
		if ($layout = JRequest::getVar('layout', 'default')) {
                        $this->context .= '.'.$layout;
                }

		$extension="com_abook";
		$this->setState('filter.extension', $extension);

                $search = $app->getUserStateFromRequest($this->context.'.search', 'filter_search');
                $this->setState('filter.search', $search);

		$level = $this->getUserStateFromRequest($this->context.'.filter.level', 'filter_level', 0, 'int');
                $this->setState('filter.level', $level);

		$access = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', 0, 'int');
                $this->setState('filter.access', $access);

                $published = $app->getUserStateFromRequest($this->context.'.published', 'filter_published', '');
                $this->setState('filter.published', $published);

		$language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
                $this->setState('filter.language', $language);

                // List state information.
                parent::populateState('a.lft', 'asc');
        }

	protected function getStoreId($id = '')
        {
                // Compile the store id.
                $id     .= ':'.$this->getState('filter.search');
                $id     .= ':'.$this->getState('filter.published');
		$id     .= ':'.$this->getState('filter.language');

                return parent::getStoreId($id);
        }

	function getListQuery($resolveFKs = true)
        {
                // Create a new query object.
                $db = $this->getDbo();
                $query = $db->getQuery(true);

                // Select the required fields from the table.
                $query->select(
                        $this->getState(
                                'list.select',
                                'a.id, a.title, a.alias, a.published, a.access, a.path' .
                                ', a.checked_out, a.checked_out_time' .
                                ', a.parent_id, a.level, a.lft, a.rgt '.
				', a.language, a.created_user_id'
                        )
                );
                $query->from('#__abcategories AS a');

		$query->select('l.title AS language_title');
                $query->join('LEFT', '`#__languages` AS l ON l.lang_code = a.language');

		$query->select('u.name AS owner');
                $query->join('LEFT', '#__users AS u ON u.id = a.checked_out');

		$query->select('ag.title AS access_level');
                $query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		// Filter by extension
                $query->where("a.extension = 'com_abook'");

		$published = $this->getState('filter.published');
                if (is_numeric($published)) {
                        $query->where('a.published = ' . (int) $published);
                } else if ($published === '') {
                        $query->where('(a.published IN (0, 1))');
                }

		// Filter on the level.
                if ($level = $this->getState('filter.level')) {
                        $query->where('a.level <= '.(int) $level);
                }

		// Filter by access level.
                if ($access = $this->getState('filter.access')) {
                        $query->where('a.access = ' . (int) $access);
                }

		$search = $this->getState('filter.search');
                if (!empty($search)) {
                        $search = $db->Quote('%'.$db->escape($search, true).'%');
                        $query->where('(a.title LIKE '.$search.')');
                }

		// Filter on the language.
                if ($language = $this->getState('filter.language')) {
                        $query->where('a.language = '.$db->quote($language));
                }

		$query->order($db->escape($this->getState('list.ordering', 'a.title')).' '.$db->escape($this->getState('list.direction', 'ASC')));

		return $query;
        }

}
