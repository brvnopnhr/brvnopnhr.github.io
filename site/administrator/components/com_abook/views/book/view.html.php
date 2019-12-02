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
jimport('joomla.application.component.helper');
jimport('joomla.filesystem.folder');
JHTML::addIncludePath(JPATH_COMPONENT.DS.'helpers');
class AbookViewBook extends JViewLegacy
{
	protected $item;
	protected $form;
        protected $state;
	protected $author;
	//protected $bookauthor;
	protected $tag;
	protected $booktag;
	protected $config;

	public function display($tpl = null)
        {
		$this->params = JComponentHelper::getParams('com_abook');
		$this->item             = $this->get('Item');
		$this->form             = $this->get('Form');
                $this->state            = $this->get('State');
		$this->author           = $this->get('Authorslist');
		//$this->bookauthor       = $this->get('BooksAuthorslist');
		$this->tag	        = $this->get('Tagslist');
                //$this->booktag       	= $this->get('BooksTagslist');

		$this->folderlist	= $this->get('FolderList');
		if ($this->item->docsfolder != ''){
			$this->docslist		= JFolder::files(JPATH_ROOT.DS.$this->item->docsfolder.DS, '', '', '', array('.svn', 'CVS','.DS_Store','__MACOSX', 'index.html'));
		}
//$document = &JFactory::getDocument();
//$document->addScript( '/administrator/components/com_abook/assets/javascript/functions.js' );
JHTML::script(Juri::base() . 'components/com_abook/assets/javascript/functions.js');
//JHTML::_('script','components/com_abook/assets/javascript/functions.js');

		$this->config = JComponentHelper::getParams( 'com_abook' );
                $this->imageconfig= JComponentHelper::getParams('com_media');
		$this->session= JFactory::getSession();
		$this->canDo    = AbookHelper::getActions($this->state->get('filter.category_id'));

		if (count($errors = $this->get('Errors'))) {
                        JError::raiseError(500, implode("\n", $errors));
                        return false;
                }

                $this->addToolbar();
                parent::display($tpl);
        }

	protected function addToolbar()
        {
               	JFactory::getApplication()->input->set('hidemainmenu', true);

                $user           = JFactory::getUser();
                $isNew          = ($this->item->id == 0);
                $checkedOut     = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
                $canDo          = AbookHelper::getActions($this->state->get('filter.category_id'), $this->item->id);
		$icon = $isNew ? 'bookadd' : 'bookedit';

                JToolBarHelper::title(JText::_('COM_ABOOK_PAGE_'.($checkedOut ? 'VIEW_BOOK' : ($isNew ? 'ADD_BOOK' : 'EDIT_BOOK'))), $icon);

		$document       = JFactory::getDocument();
                //$document->addStyleSheet('components/com_abook/assets/css/com_abook.css');

                // If not checked out, can save the item.
                if (!$checkedOut && $canDo->get('core.edit')) {
                        JToolBarHelper::apply('book.apply', 'JTOOLBAR_APPLY');
                        JToolBarHelper::save('book.save', 'JTOOLBAR_SAVE');
			JToolbarHelper::save2new('book.save2new');
			JToolbarHelper::save2copy('book.save2copy');
                }

                if (empty($this->item->id))  {
                        JToolBarHelper::cancel('book.cancel', 'JTOOLBAR_CANCEL');
                } else {
                        JToolBarHelper::cancel('book.cancel', 'JTOOLBAR_CLOSE');
                }
        }
}
