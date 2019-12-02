<?php 
/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;



jimport( 'joomla.application.component.view');
jimport( 'joomla.html.pagination');
jimport( 'joomla.html.html');

/** @noinspection PhpUndefinedClassInspection */
class EventgalleryViewFile extends EventgalleryLibraryCommonView
{
	protected $state;

	protected $item;

	protected $form;

    protected $file;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{

		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');

        /**
         * @var EventgalleryLibraryManagerFile $fm
         */
        $fm = EventgalleryLibraryManagerFile::getInstance();
        $this->file = $fm->getFile($this->item);

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		EventgalleryHelpersEventgallery::addSubmenu('file');      
        $this->sidebar = JHtmlSidebar::render();
		return parent::display($tpl);
	}

	private function addToolbar() {
		$text = JText::_( 'Edit' ) . ' ' . $this->item->file;
		$bar = JToolbar::getInstance('toolbar');
		
		JToolbarHelper::title(   JText::_( 'COM_EVENTGALLERY_FILES' ).': <small>[ ' . $text.' ]</small>' );
			
		JToolbarHelper::apply('file.apply');			
		JToolbarHelper::save('file.save');
		JToolbarHelper::cancel( 'file.cancel', JText::_( 'JTOOLBAR_CLOSE' ) );
	}

}