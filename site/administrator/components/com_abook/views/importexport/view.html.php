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
class AbookViewImportexport extends JViewLegacy
{
	protected $state;
	protected $form;
	function display($tpl = null)
	{
		$this->state            = $this->get('State');
		$this->form     = $this->get('Form');
		AbookHelper::addSubmenu('Importexport');
		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	protected function addToolbar()
        {
		$canDo  = JHelperContent::getActions('com_abook');
		JHTML::_('behavior.tooltip');
                $document       = JFactory::getDocument();
                $document->addStyleSheet('components/com_abook/assets/css/com_abook.css');
                JToolBarHelper::title(JText::_( 'COM_ABOOK' ).': '.JText::_( 'COM_ABOOK_IMPORTEXPORT_MANAGER' ), 'refresh importexport' );
                JHTML::addIncludePath(JPATH_COMPONENT.DS.'helpers');
                //$document->addScript(DS.'includes'.DS.'js'.DS.'overlib_mini.js');

                if ($canDo->get('core.admin')){
                        JToolBarHelper::preferences('com_abook');
                }
        }
}
