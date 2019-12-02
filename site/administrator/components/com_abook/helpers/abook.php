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

class AbookHelper extends JHelperContent
{
	public static $extension = 'com_abook';
        public static function addSubmenu($vName)
        {
		JHtmlSidebar::addEntry(
                        JText::_('COM_ABOOK_SUBMENU_CPANEL'),
                        'index.php?option=com_abook&view=cpanel',
                        $vName == 'cpanel'
                );
                JHtmlSidebar::addEntry(
                        JText::_('COM_ABOOK_SUBMENU_BOOKS'),
                        'index.php?option=com_abook&view=books',
                        $vName == 'books'
                );
		JHtmlSidebar::addEntry(
        		JText::_('COM_ABOOK_SUBMENU_CATEGORIES'),
                	'index.php?option=com_abook&view=categories',
	                $vName == 'categories'
        	);
		JHtmlSidebar::addEntry(
                        JText::_('COM_ABOOK_SUBMENU_AUTHORS'),
                        'index.php?option=com_abook&view=authors',
                        $vName == 'authors'
                );
		JHtmlSidebar::addEntry(
                        JText::_('COM_ABOOK_SUBMENU_EDITORS'),
                        'index.php?option=com_abook&view=editors',
                        $vName == 'editors'
                );
		JHtmlSidebar::addEntry(
                        JText::_('COM_ABOOK_SUBMENU_LIBRARIES'),
                        'index.php?option=com_abook&view=libraries',
                        $vName == 'libraries'
                );
		JHtmlSidebar::addEntry(
                        JText::_('COM_ABOOK_SUBMENU_LOCATIONS'),
                        'index.php?option=com_abook&view=locations',
                        $vName == 'locations'
                );
		JHtmlSidebar::addEntry(
                        JText::_('COM_ABOOK_SUBMENU_TAGS'),
                        'index.php?option=com_abook&view=tags',
                        $vName == 'tags'
                );
		JHtmlSidebar::addEntry(
                        JText::_('COM_ABOOK_SUBMENU_TAGGROUPS'),
                        'index.php?option=com_abook&view=taggroups',
                        $vName == 'taggroups'
                );
		JHtmlSidebar::addEntry(
                        JText::_('COM_ABOOK_SUBMENU_LENDS'),
                        'index.php?option=com_abook&view=lends',
                        $vName == 'lends'
                );
		JHtmlSidebar::addEntry(
                        JText::_('COM_ABOOK_SUBMENU_IMPORTEXPORT'),
                        'index.php?option=com_abook&view=importexport',
                        $vName == 'importexport'
                );
        }

	public static function getActions($categoryId = 0, $bookId = 0, $assetName = '')
        {
                $user   = JFactory::getUser();
                $result = new JObject;

                if (empty($bookId) && empty($categoryId)) {
                        $assetName = 'com_abook';
                }
                else if (empty($bookId)) {
                        $assetName = 'com_abook.category.'.(int) $categoryId;
                }
                else {
                        $assetName = 'com_abook.book.'.(int) $bookId;
                }

                $actions = array(
                        'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete'
                );

                foreach ($actions as $action) {
                        $result->set($action,   $user->authorise($action, $assetName));
                }

                return $result;
        }

	public static function getCategoryActions($extension, $categoryId = 0)
        {
                $user           = JFactory::getUser();
                $result         = new JObject;
                $parts          = explode('.',$extension);
                $component      = $parts[0];

                if (empty($categoryId)) {
                        $assetName = $component;
                }
                else {
                        $assetName = $component.'.category.'.(int) $categoryId;
                }

                $actions = array(
                        'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
                );

                foreach ($actions as $action) {
                        $result->set($action, $user->authorise($action, $assetName));
                }

                return $result;
        }

	public static function filterText($text)
        {
                JLog::add('AbookHelper::filterText() is deprecated. Use JComponentHelper::filterText() instead.', JLog::WARNING, 'deprecated');

                return JComponentHelper::filterText($text);
        }

}
