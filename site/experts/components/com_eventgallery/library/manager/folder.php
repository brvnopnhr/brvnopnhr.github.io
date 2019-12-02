<?php

/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class EventgalleryLibraryManagerFolder extends  EventgalleryLibraryManagerManager
{

    public static $SYNC_STATUS_NOSYNC = 0;
    public static $SYNC_STATUS_SYNC = 1;
    public static $SYNC_STATUS_DELTED = 2;


    protected $_folders;
    protected $_commentCount;

    public function __construct() {
    }

    /**
     * Returns a folder
     *
     * @param $foldername string|object
     * @return EventgalleryLibraryFolder
     */
    public function getFolder($foldername) {

        $currentFolder = "";

        if (is_object($foldername)) {
            $currentFolder = $foldername->folder;
        } else {            
            $currentFolder = $foldername;
        }


        if (!isset($this->_folders[$currentFolder])) {


            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select('f.*, ft.folderhandlerclassname');
            $query->from('#__eventgallery_folder f, #__eventgallery_foldertype ft');
            $query->where('f.foldertypeid=ft.id');
            $query->where('folder='.$db->quote($currentFolder));

            $db->setQuery($query);
            $databaseFolder = $db->loadObject();

            if (isset($databaseFolder->folderhandlerclassname)) {
	            $folderClass = $databaseFolder->folderhandlerclassname;
	            /**
	             * @var EventgalleryLibraryFolder $folderClass
	             * */
	            $this->_folders[$currentFolder] = new $folderClass($databaseFolder);
        	} else {
        		$this->_folders[$currentFolder] = null;
        	}

        }

        return $this->_folders[$currentFolder];
    }


    public function getCommentCount($foldername)
    {
        if (!$this->_commentCount)
        {
            $db = JFactory::getDBO();
            $query = $db->getQuery(true)
                ->select('folder, count(1) AS '.$db->quoteName('commentCount'))
                ->from($db->quoteName('#__eventgallery_comment'))
                ->where('published=1')
                ->group('folder');
            $db->setQuery($query);
            $comments = $db->loadObjectList();
            $this->_commentCount = array();
            foreach($comments as $comment)
            {
                $this->_commentCount[$comment->folder] = $comment->commentCount;
            }
        }

        if (isset($this->_commentCount[$foldername])) {
            return $this->_commentCount[$foldername];
        }

        return 0;
    }

    /**
     * scans the main dir and adds new folders to the database
     * Does not add Files!
     *
     * @return array with error strings
     */
    public function addNewFolders() {

        $errors = Array();

        $folderMgr = EventgalleryLibraryManagerFoldertype::getInstance();

        foreach($folderMgr->getFolderTypes(true) as $folderType) {
            $folderClass = $folderType->getFolderHandlerClassname();
            /**
             * @var EventgalleryLibraryFolder $folderClass
             * */
            $errors = array_merge($errors, $folderClass::addNewFolders());
        }

        return $errors;
    }


}
