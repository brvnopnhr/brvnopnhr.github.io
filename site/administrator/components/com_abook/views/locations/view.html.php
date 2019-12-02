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

jimport( 'joomla.application.component.view' );
JHTML::addIncludePath(JPATH_COMPONENT.DS.'helpers');
class AbookViewLocations extends JViewLegacy
{
	protected $items;
        protected $pagination;
        protected $state;

        /**
         * Display the view
         */
        public function display($tpl = null)
        {
		if ($this->getLayout() !== 'modal')
                {
                        AbookHelper::addSubmenu('locations');
                }
                $this->state            = $this->get('State');
                $this->items            = $this->get('Items');
                $this->pagination       = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
                $this->activeFilters = $this->get('ActiveFilters');

                // Check for errors.
                if (count($errors = $this->get('Errors'))) {
                        JError::raiseError(500, implode("\n", $errors));
                        return false;
                }

                if ($this->getLayout() !== 'modal')
                {
                        $this->addToolbar();
                        $this->sidebar = JHtmlSidebar::render();
                }
                parent::display($tpl);
        }

	        protected function addToolbar()
        {
               	$canDo  = JHelperContent::getActions('com_abook'); 

                JToolBarHelper::title(JText::_( 'COM_ABOOK' ).': '.JText::_( 'COM_ABOOK_LOCATION_MANAGER' ), 'flag locations' );
                if ($canDo->get('core.create')) {
                        JToolBarHelper::addNew('location.add','JTOOLBAR_NEW');
                }
                if ($canDo->get('core.edit')) {
                        JToolBarHelper::editList('location.edit','JTOOLBAR_EDIT');
                }

		if ($canDo->get('core.delete')) {
                        JToolBarHelper::deleteList('', 'locations.delete','JTOOLBAR_DELETE');
		}

		if ($canDo->get('core.admin')) {
                        JToolBarHelper::divider();
                        JToolBarHelper::preferences('com_abook');
                }
        }
	protected function getSortFields()
        {
                return array(
                        'a.name'        => JText::_('JGLOBAL_TITLE'),
                        'language'       => JText::_('JGRID_HEADING_LANGUAGE'),
                        'a.id'           => JText::_('JGRID_HEADING_ID'),
                );
        }
}
