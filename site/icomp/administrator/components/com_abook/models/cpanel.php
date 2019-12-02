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

class AbookModelCpanel extends JModelList
{

	public function &getButtons()
        {
                if (empty($this->_buttons)) {
                        $this->_buttons = array(
                                array(
                                        'link' => JRoute::_('index.php?option=com_abook&task=book.add'),
                                        'image' => 'icon-book',
                                        'text' => JText::_('COM_ABOOK_ADD_NEW_BOOK'),
                                        'access' => array('core.manage', 'com_abook')
                                ),
				array(
                                        'link' => JRoute::_('index.php?option=com_abook&view=books'),
                                        'image' => 'icon-stack',
                                        'text' => JText::_('COM_ABOOK_BOOKS'),
                                        'access' => array('core.manage', 'com_abook')
                                ),
				array(
                                        'link' => JRoute::_('index.php?option=com_abook&task=category.add'),
                                        'image' => 'icon-folder-close',
                                        'text' => JText::_('COM_ABOOK_ADD_NEW_CATEGORY'),
                                        'access' => array('core.admin', 'com_abook')
                                ),
				array(
                                        'link' => JRoute::_('index.php?option=com_abook&view=categories'),
                                        'image' => 'icon-folder-open',
                                        'text' => JText::_('COM_ABOOK_CATEGORIES'),
                                        'access' => array('core.manage', 'com_abook')
                                ),
				array(
                                        'link' => JRoute::_('index.php?option=com_abook&task=author.add'),
                                        'image' => 'icon-user',
                                        'text' => JText::_('COM_ABOOK_ADD_NEW_AUTHOR'),
                                        'access' => array('core.admin', 'com_abook')
                                ),
                                array(
                                        'link' => JRoute::_('index.php?option=com_abook&view=authors'),
                                        'image' => 'icon-users',
                                        'text' => JText::_('COM_ABOOK_AUTHORS'),
                                        'access' => array('core.admin', 'com_abook')
                                ),
				array(
                                        'link' => JRoute::_('index.php?option=com_abook&task=editor.add'),
                                        'image' => 'icon-briefcase',
                                        'text' => JText::_('COM_ABOOK_ADD_NEW_EDITOR'),
                                        'access' => array('core.admin', 'com_abook')
                                ),
				array(
                                        'link' => JRoute::_('index.php?option=com_abook&task=library.add'),
                                        'image' => 'icon-home',
                                        'text' => JText::_('COM_ABOOK_ADD_NEW_LIBRARY'),
                                        'access' => array('core.admin', 'com_abook')
                                ),
				array(
                                        'link' => JRoute::_('index.php?option=com_abook&task=location.add'),
                                        'image' => 'icon-flag',
                                        'text' => JText::_('COM_ABOOK_ADD_NEW_LOCATION'),
                                        'access' => array('core.admin', 'com_abook')
                                ),
				array(
                                        'link' => JRoute::_('index.php?option=com_abook&task=tag.add'),
                                        'image' => 'icon-tag',
                                        'text' => JText::_('COM_ABOOK_ADD_NEW_TAG'),
                                        'access' => array('core.admin', 'com_abook')
                                ),
                                array(
                                        'link' => JRoute::_('index.php?option=com_abook&view=tags'),
                                        'image' => 'icon-tags',
                                        'text' => JText::_('COM_ABOOK_TAGS'),
                                        'access' => array('core.admin', 'com_abook')
				),
				array(
                                        'link' => JRoute::_('index.php?option=com_abook&view=lends'),
                                        'image' => 'icon-share',
                                        'text' => JText::_('COM_ABOOK_LEND'),
                                        'access' => array('core.admin', 'com_abook')
                                ),
				array(
                                        'link' => JRoute::_('index.php?option=com_abook&view=importexport'),
                                        'image' => 'icon-refresh',
                                        'text' => JText::_('COM_ABOOK_IMPORT').'/'.JText::_('COM_ABOOK_EXPORT'),
                                        'access' => array('core.admin', 'com_abook')
                                )

                        );
                }

                return $this->_buttons;
        }

	public function getBooks()
        {
		$_books = array();
		$db = $this->getDbo();
		$query =$db->getQuery(true);
                $query->select('id');
                $query->from('#__abbook');
                $query->where('state = 1');
                $db->setQuery($query);
		$db->execute();
                $_books['published'] = $db->getNumRows();

                $query =$db->getQuery(true);
                $query->select('id');
                $query->from('#__abbook');
                $query->where('state = 0');
                $db->setQuery($query);
		$db->execute();
                $_books['unpublished'] = $db->getNumRows();

                $_books['total'] = array_sum($_books);

                return $_books;
        }
	
	function getCategories()
        {
                $_categories = array();
		$db = $this->getDbo();

		$query =$db->getQuery(true);
                $query->select('id');
                $query->from('#__abcategories');
                $query->where('published = 1 AND extension="com_abook"');
                $db->setQuery($query);
                $db->execute();
                $_categories['published'] = $db->getNumRows();

                $query =$db->getQuery(true);
                $query->select('id');
                $query->from('#__abcategories');
                $query->where('published = 0 AND extension="com_abook"');
                $db->setQuery($query);  
                $db->execute();
                $_categories['unpublished'] = $db->getNumRows();

                $_categories['total'] = array_sum($_categories);

                return $_categories;
        }
	
	function getAuthors()
        {
                $_authors = array();
		$db = $this->getDbo();
		$query =$db->getQuery(true);
                $query->select('id');
		$query->from('#__abauthor');
                $db->setQuery($query);
		$db->execute();
                $_authors['total'] = $db->getNumRows();

                return $_authors;
        }

	function getEditors()
        {
                $_editors = array();
		$db = $this->getDbo();
		$query =$db->getQuery(true);
                $query->select('id');
		$query->from('#__abeditor');
                $db->setQuery($query);
		$db->execute();
                $_editors['total'] = $db->getNumRows();

                return $_editors;
        }

        function getLibraries()
        {
                $_libraries = array();
		$db = $this->getDbo();
                $query =$db->getQuery(true);
                $query->select('id');
		$query->from('#__ablibrary');
                $db->setQuery($query);
		$db->execute();
                $_libraries['total'] = $db->getNumRows();

                return $_libraries;
        }

        function getLocations()
        {
                $_locations = array();
		$db = $this->getDbo();
                $query =$db->getQuery(true);
                $query->select('id');
                $query->from('#__ablocations');
                $db->setQuery($query);
		$db->execute();
                $_locations['total'] = $db->getNumRows();

                return $_locations;
        }

	function getMostrated()
        {
                // Lets load the data if it doesn't already exist
                if (empty( $this->_mostrated ))
                {
			$db = $this->getDbo();
                	$query =$db->getQuery(true);
			$query->select('a.title, a.id, ROUND( v.rating_sum / v.rating_count ) AS rating, v.rating_count');
                        $query->from("#__abbook AS a");
                        $query->innerjoin('#__abrating AS v ON a.id = v.book_id');
                        $query->group('a.id');
                        $query->order('v.rating_count DESC');
                        $this->_mostrated = $this->_getList( $query, 0, 10 );
                }
                return $this->_mostrated;
        }

        function getMostread()
        {
                // Lets load the data if it doesn't already exist
                if (empty( $this->_mostread ))
                {
			$db = $this->getDbo();
                        $query =$db->getQuery(true);
			$query->select('title, id, hits');
			$query->from('#__abbook');
			$query->order('hits DESC');
                        $this->_mostread = $this->_getList( $query, 0, 10 );
                }
                return $this->_mostread;
        }

	function getMostlent()
        {
                // Lets load the data if it doesn't already exist
                if (empty( $this->_mostlent ))
                {
                        $db = $this->getDbo();
                        $query =$db->getQuery(true);
                        $query->select('b.title, COUNT(*) AS amount');
                        $query->from('#__ablend AS l');
			$query->leftjoin('#__abbook AS b ON b.id = l.book_id');
			$query->group('l.book_id');
			
                        $query->order('amount DESC');
                        $this->_mostlent = $this->_getList( $query, 0, 10 );
                }
                return $this->_mostlent;
        }
}	
