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
class AbookViewTaggroup extends JViewLegacy
{

	protected $form;
        protected $item;
        protected $state;
	protected $config;

        /**
         * Display the view
         */
        public function display($tpl = null)
        {
                // Initialise variables.
                $this->form     = $this->get('Form');
                $this->item     = $this->get('Item');
                $this->state    = $this->get('State');
		
		$this->config = JComponentHelper::getParams( 'com_abook' );
                $this->session= JFactory::getSession();

                // Check for errors.
                if (count($errors = $this->get('Errors'))) {
                        JError::raiseError(500, implode("\n", $errors));
                        return false;
                }

                $this->addToolbar();
                parent::display($tpl);
        }

	protected function addToolbar()
        {
                JRequest::setVar('hidemainmenu', true);

                $user           = JFactory::getUser();
                $isNew          = ($this->item->id == 0);
                $checkedOut     = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
                $canDo          = AbookHelper::getActions();

		$document       = JFactory::getDocument();
                $document->addStyleSheet('components/com_abook/assets/css/com_abook.css');

		$icon = $isNew ? 'taggroupadd' : 'taggroupedit';
		JToolBarHelper::title(JText::_('COM_ABOOK_PAGE_'.($checkedOut ? 'VIEW_TAGGROUP' : ($isNew ? 'ADD_TAGGROUP' : 'EDIT_TAGGROUP'))), $icon);

                // If not checked out, can save the item.
                if (!$checkedOut && $canDo->get('core.edit')) {
                        JToolBarHelper::apply('taggroup.apply', 'JTOOLBAR_APPLY');
                        JToolBarHelper::save('taggroup.save', 'JTOOLBAR_SAVE');
                }

                if (empty($this->item->id))  {
                        JToolBarHelper::cancel('taggroup.cancel');
                } else {
                        JToolBarHelper::cancel('taggroup.cancel', 'JTOOLBAR_CLOSE');
                }
        }
}
