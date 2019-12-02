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

class EventgalleryLibraryFoldertype extends EventgalleryLibraryDatabaseObject
{

    /**
     * @var int
     */
    protected $_foldertype_id = NULL;

    /**
     * @var TableFoldertype
     */
    protected $_foldertype = NULL;

    /**
     * @param int $foldertype_id
     */
    public function __construct($foldertype_id = -1)
    {
        $this->_foldertype_id = $foldertype_id;
        $this->_loadFolderType();
        parent::__construct();
    }

    /**
     *
     */
    protected function _loadFolderType()
    {

        if (null == $this->_foldertype_id) {
            return;
        }

        $this->_foldertype = NULL;

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('ft.*');
        $query->from('#__eventgallery_foldertype as ft');
        if ($this->_foldertype_id != -1) {
            $query->where('ft.id=' . $db->quote($this->_foldertype_id));
        }
        $query->order('ft.default DESC');
        $db->setQuery($query);
        $this->_foldertype = $db->loadObject();



        $this->_foldertype_id = $this->_foldertype->id;

    }


    /**
     * @return int
     */
    public function getId() {
        return $this->_foldertype->id;
    }

    /**
     * @return bool
     */
    public function isPublished() {
        return $this->_foldertype->published==1;
    }

    /**
     * @return bool
     */
    public function isDefault() {
        return $this->_foldertype->default==1;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->_foldertype->name;
    }

    /**
     * @return string
     */
    public function getDisplayName() {
        return $this->_foldertype->displayname;
    }

    /**
    * @return string the name of the class to handle this kind of folders
    */
    public function getFolderHandlerClassname() {
        return $this->_foldertype->folderhandlerclassname;
    }

}
