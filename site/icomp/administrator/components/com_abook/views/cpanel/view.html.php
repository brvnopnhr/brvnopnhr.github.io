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
class AbookViewCpanel extends JViewLegacy
{
        protected $buttons;
	protected $books;
	protected $categories;
	protected $authors;
	protected $editors;
	protected $libraries;
	protected $locations;
	protected $mostrated;
	protected $mostread;
        /**
         * Display the view
         */
        public function display($tpl = null)
        {
                $this->buttons       = $this->get('Buttons');
		$this->books		= $this->get('Books');
		$this->categories	= $this->get('Categories');
		$this->authors            = $this->get('Authors');
		$this->editors            = $this->get('Editors');
		$this->libraries            = $this->get('Libraries');
		$this->locations            = $this->get('Locations');
		$this->mostrated            = $this->get('Mostrated');
		$this->mostread            = $this->get('Mostread');
		$this->mostlent            = $this->get('Mostlent');

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
                $document       = JFactory::getDocument();
                $document->addStyleSheet('components/com_abook/assets/css/com_abook.css');
                //$document->addScript(DS.'includes'.DS.'js'.DS.'overlib_mini.js');

		JToolBarHelper::title(JText::_( 'COM_ABOOK' ), 'book abook-main' );
                require_once JPATH_COMPONENT.DS.'helpers'.DS.'abook.php';
                $state  = $this->get('State');
                $canDo  = AbookHelper::getActions($state->get('filter.category_id'));

		if ($canDo->get('core.admin')) {
                        JToolBarHelper::preferences('com_abook');
                }
        }
}
