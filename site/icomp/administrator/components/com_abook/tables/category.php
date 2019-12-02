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
jimport('joomla.database.tablenested');

class AbookTableCategory extends JTableNested 
{
	public function __construct(&$db)
        {
                parent::__construct('#__abcategories', 'id', $db);
		$this->access   = (int) JFactory::getConfig()->get('access');
        }

	protected function _getAssetName()
        {
                $k = $this->_tbl_key;
                return $this->extension.'.category.'.(int) $this->$k;
        }

	protected function _getAssetTitle()
        {
                return $this->title;
        }

        protected function _getAssetParentId(JTable $table = null, $id = null)
        {
                // Initialise variables.
                $assetId = null;
                $db             = $this->getDbo();

                // This is a category under a category.
                if ($this->parent_id > 1) {
                        // Build the query to get the asset id for the parent category.
                        $query  = $db->getQuery(true);
                        $query->select('asset_id');
                        $query->from('#__abcategories');
                        $query->where('id = '.(int) $this->parent_id);

                        // Get the asset id from the database.
                        $db->setQuery($query);
                        if ($result = $db->loadResult()) {
                                $assetId = (int) $result;
                        }
                }
                // This is a category that needs to parent with the extension.
                elseif ($assetId === null) {
                        // Build the query to get the asset id for the parent category.
                        $query  = $db->getQuery(true);
                        $query->select('id');
                        $query->from('#__assets');
                        $query->where('name = '.$db->quote($this->extension));

                        // Get the asset id from the database.
                        $db->setQuery($query);
                        if ($result = $db->loadResult()) {
                                $assetId = (int) $result;
                        }
                }
                // Return the asset id.
                if ($assetId) {
                        return $assetId;
                } else {
                        return parent::_getAssetParentId($table, $id);
                }
        }
	//forse si può cancellare perchè la funzione parent non cerca direttamente #__catgoies però non fa il check dell'alias
        public function check()
        {
                // Check for a title.
                if (trim($this->title) == '') {
                        $this->setError(JText::_('JLIB_DATABASE_ERROR_MUSTCONTAIN_A_TITLE_CATEGORY'));
                        return false;
                }
                $this->alias = trim($this->alias);
                if (empty($this->alias)) {
                        $this->alias = $this->title;
                }

                $this->alias = JApplication::stringURLSafe($this->alias);
                if (trim(str_replace('-','',$this->alias)) == '') {
                        $this->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
                }

                return true;
        }

	public function bind($array, $ignore = '')
        {
                if (isset($array['params']) && is_array($array['params'])) {
                        $registry = new JRegistry;
                        $registry->loadArray($array['params']);
                        $array['params'] = (string)$registry;
                }

                if (isset($array['metadata']) && is_array($array['metadata'])) {
                        $registry = new JRegistry;
                        $registry->loadArray($array['metadata']);
                        $array['metadata'] = (string)$registry;
                }

                // Bind the rules.
                if (isset($array['rules']) && is_array($array['rules'])) {
                        $rules = new JRules($array['rules']);
                        $this->setRules($rules);
                }

                return parent::bind($array, $ignore);
        }
        public function store($updateNulls = false)
        {
                $date   = JFactory::getDate();
                $user   = JFactory::getUser();

                if ($this->id) {
                        // Existing category
                        $this->modified_time    = $date->toSql();
                        $this->modified_user_id = $user->get('id');
                } else {
                        // New category
                        $this->created_time             = $date->toSql();
                        $this->created_user_id  = $user->get('id');
                }
        	// Verify that the alias is unique
                $table = JTable::getInstance('Category','AbookTable');
                if ($table->load(array('alias'=>$this->alias,'parent_id'=> (int)$this->parent_id,'extension'=>$this->extension)) && ($table->id != $this->id || $this->id==0)) {

                        $this->setError(JText::_('JLIB_DATABASE_ERROR_CATEGORY_UNIQUE_ALIAS'));
                        return false;
                }
                return parent::store($updateNulls);
        }

	public function delete($pk = null, $children = true)
        {
                //check per impedire la cancellazione di un autore già utilizzato
                $query = 'SELECT * FROM #__abbook WHERE catid = '.$pk;

                $this->_db->setQuery($query);

                $xid = $this->_db->loadResult();
                if (count($xid) > 0) {
                        JError::raiseWarning('SOME_ERROR_CODE', JText::_('COM_ABOOK_DELETE_NOT_ALLOWED_ITEM_ALREADY_USED_IN_A_BOOK'));
                        return false;
                }
		//check per impedire la cancellazione di una categoria già utilizzata come parent
		$query = 'SELECT * FROM #__abcategories WHERE parent_id	= '.$pk;
		$this->_db->setQuery($query);

                $xid = $this->_db->loadResult();
                if (count($xid) > 0) {
                        JError::raiseWarning('SOME_ERROR_CODE', JText::_('COM_ABOOK_DELETE_NOT_ALLOWED_ITEM_ALREADY_USED_IN_A_CATEGORY_PARENT'));
                        return false;
                }


                return parent::delete($pk);
        }


}
