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
class AbookViewCategory extends JViewLegacy
{
        protected $form;
        protected $item;
        protected $state;

        public function display($tpl = null)
        {
                $this->form             = $this->get('Form');
                $this->item             = $this->get('Item');
                $this->state    = $this->get('State');
		$this->canDo    = AbookHelper::getActions($this->state->get('category.component'));

                // Check for errors.
                if (count($errors = $this->get('Errors'))) {
                        JError::raiseError(500, implode("\n", $errors));
                        return false;
                }

                JRequest::setVar('hidemainmenu', true);
                $this->addToolbar();
		parent::display($tpl);
        }

        protected function addToolbar()
        {
                // Initialise variables.
                $user           = JFactory::getUser();
                $userId         = $user->get('id');

                $isNew          = ($this->item->id == 0);
                $checkedOut     = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);

                // Load the category helper.
                require_once JPATH_COMPONENT.'/helpers/categories.php';

                // Get the results for each action.
                $canDo = AbookHelper::getActions('com_abook', $this->item->id);

                $document       = JFactory::getDocument();
                $document->addStyleSheet('components/com_abook/assets/css/com_abook.css');

                $title = JText::_('COM_ABOOK_CATEGORY_'.($isNew?'ADD':'EDIT'));

                // Prepare the toolbar.
		$icon = $isNew ? 'categoryadd' : 'categoryedit';
                JToolBarHelper::title(JText::_('COM_ABOOK_PAGE_'.($checkedOut ? 'VIEW_CATEGORY' : ($isNew ? 'ADD_CATEGORY' : 'EDIT_CATEGORY'))), $icon);

                // For new records, check the create permission.
                if ($isNew && $canDo->get('core.create')) {
                        JToolBarHelper::apply('category.apply');
                        JToolBarHelper::save('category.save');
                        JToolBarHelper::save2new('category.save2new');
                }

                // If not checked out, can save the item.
                else if (!$checkedOut && ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_user_id == $userId))) {
                        JToolBarHelper::apply('category.apply');
                        JToolBarHelper::save('category.save');
                        if ($canDo->get('core.create')) {
                                JToolBarHelper::save2new('category.save2new');
                        }
                }

                // If an existing item, can save to a copy.
                if (!$isNew && $canDo->get('core.create')) {
                        JToolBarHelper::save2copy('category.save2copy');
                }

                if (empty($this->item->id))  {
                        JToolBarHelper::cancel('category.cancel');
                }
                else {
                        JToolBarHelper::cancel('category.cancel', 'JTOOLBAR_CLOSE');
                }

                JToolBarHelper::divider();
        }
}
