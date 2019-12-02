<?php
/**
 * @package Joomla
 * @subpackage Jbook
 * @copyright (C) 2010 Ugolotti Federica
 * @license GNU/GPL, see LICENSE.php
 * Abook is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License 2
 * as published by the Free Software Foundation.

 * Jbook is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with Jbook; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view' );
JHTML::addIncludePath(JPATH_COMPONENT.DS.'helpers');
class AbookViewBooks extends JViewLegacy
{
	protected $items;
        protected $pagination;
        protected $state;
	public function display($tpl = null)
	{
		if ($this->getLayout() !== 'modal')
                {
                        AbookHelper::addSubmenu('books');
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
		
		// Levels filter.
                $options        = array();
                $options[]      = JHtml::_('select.option', '1', JText::_('J1'));
                $options[]      = JHtml::_('select.option', '2', JText::_('J2'));
                $options[]      = JHtml::_('select.option', '3', JText::_('J3'));
                $options[]      = JHtml::_('select.option', '4', JText::_('J4'));
                $options[]      = JHtml::_('select.option', '5', JText::_('J5'));
                $options[]      = JHtml::_('select.option', '6', JText::_('J6'));
                $options[]      = JHtml::_('select.option', '7', JText::_('J7'));
                $options[]      = JHtml::_('select.option', '8', JText::_('J8'));
                $options[]      = JHtml::_('select.option', '9', JText::_('J9'));
                $options[]      = JHtml::_('select.option', '10', JText::_('J10'));
                
                $this->f_levels = $options;


		if ($this->getLayout() !== 'modal')
                {
                        $this->addToolbar();
                        $this->sidebar = JHtmlSidebar::render();
                }
                parent::display($tpl);
        }
	protected function addToolbar()
	{
		$user           = JFactory::getUser();
		$canDo = JHelperContent::getActions('com_abook', 'category', $this->state->get('filter.category_id'));

		JToolBarHelper::title(JText::_( 'COM_ABOOK' ).': '.JText::_( 'COM_ABOOK_BOOK_MANAGER' ), 'book books' );
		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_abook', 'core.create'))) > 0) {
                        JToolBarHelper::addNew('book.add','JTOOLBAR_NEW');
                }
                if ($canDo->get('core.edit') || ($canDo->get('core.edit.own'))) {
                        JToolBarHelper::editList('book.edit','JTOOLBAR_EDIT');
                }
		if ($canDo->get('core.edit.state')) {
                        JToolBarHelper::divider();
                        JToolBarHelper::publish('books.publish','JTOOLBAR_PUBLISH', true);
                        JToolBarHelper::unpublish('books.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::archiveList('books.archive');
			JToolBarHelper::custom('books.resethits', 'refresh', '', 'COM_ABOOK_RESET_HITS', true);
                }

                if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
                        JToolBarHelper::deleteList('', 'books.delete','JTOOLBAR_EMPTY_TRASH');
                        JToolBarHelper::divider();
                }
                else if ($canDo->get('core.edit.state')) {
                        JToolBarHelper::trash('books.trash');
                        JToolBarHelper::divider();
                }

                if ($user->authorise('core.admin', 'com_abook')) {
                        JToolBarHelper::preferences('com_abook');
                }
	}

	protected function getSortFields()
        {
                return array(
                        'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
                        'a.state' => JText::_('JSTATUS'),
                        'a.title' => JText::_('JGLOBAL_TITLE'),
                        'a.catid' => JText::_('JCATEGORY'),
                        'access_level' => JText::_('JGRID_HEADING_ACCESS'),
                        'a.language' => JText::_('JGRID_HEADING_LANGUAGE'),
                        'a.year' => JText::_('COM_ABOOK_BOOK_YEAR'),
                        'a.id' => JText::_('JGRID_HEADING_ID'),
			'a.dateinsert' => JText::_('COM_ABOOK_DATEINSERT'),
			'a.catalogo' => JText::_('COM_ABOOK_CATALOG'),
			'a.idlibrary' => JText::_('COM_ABOOK_LIBRARY'),
			'a.idlocation' => JText::_('COM_ABOOK_LOCATION'),
			'a.ideditor' => JText::_('COM_ABOOK_EDITOR')
                );
        }
}
