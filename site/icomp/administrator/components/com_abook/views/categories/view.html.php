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
class AbookViewCategories extends JViewLegacy
{
	protected $items;
        protected $pagination;
        protected $state;

	public function display($tpl = null)
        {
		if ($this->getLayout() !== 'modal')
                {
                        AbookHelper::addSubmenu('categories');
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

                // Preprocess the list of items to find ordering divisions.
                foreach ($this->items as &$item) {
                        $this->ordering[$item->parent_id][] = $item->id;
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
		$categoryId     = $this->state->get('filter.category_id');
		$component= "com_abook";
		$canDo = AbookHelper::getActions($component, $categoryId);
		JToolBarHelper::title(JText::_( 'COM_ABOOK' ).': '.JText::_( 'COM_ABOOK_CATEGORIES'), 'folder categories' );
		if ($canDo->get('core.create')) {
                        JToolBarHelper::addNew('category.add');
                }
		if ($canDo->get('core.edit' ) || $canDo->get('core.edit.own')) {
                        JToolBarHelper::editList('category.edit');
                }
		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::publishList('categories.publish', 'JTOOLBAR_PUBLISH', true);
                	JToolBarHelper::unpublishList('categories.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		}
                //JToolBarHelper::deleteList('category.delete');
		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete', $component)) {
                        JToolBarHelper::deleteList('', 'categories.delete', 'JTOOLBAR_EMPTY_TRASH');
                }
                else if ($canDo->get('core.edit.state')) {
                        JToolBarHelper::trash('categories.trash');
                        JToolBarHelper::divider();
                }

		if ($canDo->get('core.admin')) {		
                        JToolBarHelper::custom('categories.rebuild', 'refresh', '', 'JTOOLBAR_REBUILD', false);
                        JToolBarHelper::preferences('com_abook', '550');
                }
	}

	protected function getSortFields()
        {
                return array(
                        'a.lft' => JText::_('JGRID_HEADING_ORDERING'),
                        'a.published' => JText::_('JSTATUS'),
                        'a.title' => JText::_('JGLOBAL_TITLE'),
                        'access_level' => JText::_('JGRID_HEADING_ACCESS'),
                        'language' => JText::_('JGRID_HEADING_LANGUAGE'),
                        'a.id' => JText::_('JGRID_HEADING_ID')
                );
        }
}
