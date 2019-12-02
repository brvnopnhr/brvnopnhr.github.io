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
jimport('joomla.database.tableasset');

class AbookTableBook extends JTable
{
	public function __construct(&$db)
        {
        	parent::__construct('#__abbook', 'id', $db);
        }

	public function bind($array, $ignore = '') 
        {
                if (isset($array['params']) && is_array($array['params'])) 
                {
                        // Convert the params field to a string.
                        $parameter = new JRegistry;
                        $parameter->loadArray($array['params']);
                        $array['params'] = (string)$parameter;
                }
                if (isset($array['metadata']) && is_array($array['metadata'])) {
                        $registry = new JRegistry;
                        $registry->loadArray($array['metadata']);
                        $array['metadata'] = (string)$registry;
                }
		if (isset($array['rules']) && is_array($array['rules'])) {
                        $rules = new JRules($array['rules']);
                        $this->setRules($rules);
                }
                return parent::bind($array, $ignore);
        }

	public function load($pk = null, $reset = true) 
	{
		if (parent::load($pk, $reset)) 
		{
			// Convert the params field to a registry.
			$params = new JRegistry;
			$params->loadString($this->params);
			$this->params = $params;
			return true;
		}
		else
		{
			return false;
		}
	}

	public function store($updateNulls = false)
        {
                $date   = JFactory::getDate();
                $user   = JFactory::getUser();
                if (!intval($this->dateinsert)) {
                	$this->dateinsert = $date->toSql();
                }
                if (empty($this->user_id)) {
                	$this->user_id = $user->get('id');
                }
		// Verify that the alias is unique
                $table = JTable::getInstance('Book', 'AbookTable');
                if ($table->load(array('alias' => $this->alias, 'catid' => $this->catid)) && ($table->id != $this->id || $this->id == 0))
                {
                        $this->setError(JText::_('COM_ABOOK_ERROR_BOOK_UNIQUE_ALIAS').' '.$this->alias);
                        return false;
                }
                // Attempt to store the user data.
                return parent::store($updateNulls);
        }

	protected function _getAssetName()
        {
                $k = $this->_tbl_key;
                return 'com_abook.book.'.(int) $this->$k;
        }

	protected function _getAssetTitle()
        {
                return $this->title;
        }

	protected function _getAssetParentId(JTable $table = null, $id = null)
        {
                // Initialise variables.
                $assetId = null;
                $db = $this->getDbo();

                // This is a article under a category.
                if ($this->catid) {
                        // Build the query to get the asset id for the parent category.
                        $query  = $db->getQuery(true);
                        $query->select('asset_id');
                        $query->from('#__abcategories');
                        $query->where('id = '.(int) $this->catid);

                        // Get the asset id from the database.
                        $this->_db->setQuery($query);
                        if ($result = $this->_db->loadResult()) {
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

	public function delete($pk=null)
        {
		$db = $this->getDbo();
                //cancellazione autori e tags relatii a questo libro
		$query = $db->getQuery(true);
                $query->delete()->from('#__abbookauth')->where('idbook = '. $pk);
                $this->_db->setQuery($query);
                $this->_db->execute();
		$query = $db->getQuery(true);
                $query->delete()->from('#__abbooktag')->where('idbook = '. $pk);
                $this->_db->setQuery($query);
                $this->_db->execute();
//$this->_db->query();
                return parent::delete($pk);
        }

	public function check() 
        {       
                if (trim($this->title) == '')
                {
                        $this->setError(JText::_('COM_CONTENT_WARNING_PROVIDE_VALID_NAME'));
                        return false;
                }
        
                if (trim($this->alias) == '')
                {
                        $this->alias = $this->title;
                }
                
                $this->alias = JApplication::stringURLSafe($this->alias);
                
                if (trim(str_replace('-', '', $this->alias)) == '')
                {
                        $this->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
                }
                
                return true;
	}

	public function publish($pks = null, $state = 1, $userId = 0)
        {
                $k = $this->_tbl_keys;

                if (!is_null($pks))
                {
                        foreach ($pks AS $key => $pk)
                        {
                                if (!is_array($pk))
                                {
                                        $pks[$key] = array($this->_tbl_key => $pk);
                                }
                        }
                }

                $userId = (int) $userId;
                $state  = (int) $state;

                // If there are no primary keys set check to see if the instance key is set.
                if (empty($pks))
                {
                        $pk = array();

                        foreach ($this->_tbl_keys AS $key)
                        {
                                if ($this->$key)
                                {
                                        $pk[$this->$key] = $this->$key;
                                }
                                // We don't have a full primary key - return false
                                else
                                {
                                        return false;
                                }
                        }

                        $pks = array($pk);
                }
		foreach ($pks AS $pk)
                {
                        // Update the publishing state for rows with the given primary keys.
                        $query = $this->_db->getQuery(true)
                                ->update($this->_tbl)
                                ->set('state = ' . (int) $state);

                        // Determine if there is checkin support for the table.
                        if (property_exists($this, 'checked_out') || property_exists($this, 'checked_out_time'))
                        {
                                $query->where('(checked_out = 0 OR checked_out = ' . (int) $userId . ')');
                                $checkin = true;
                        }
                        else
                        {
                                $checkin = false;
                        }

                        // Build the WHERE clause for the primary keys.
                        $this->appendPrimaryKeys($query, $pk);

                        $this->_db->setQuery($query);
                        $this->_db->execute();

                        // If checkin is supported and all rows were adjusted, check them in.
                        if ($checkin && (count($pks) == $this->_db->getAffectedRows()))
                        {
                                $this->checkin($pk);
                        }

                        $ours = true;

                        foreach ($this->_tbl_keys AS $key)
                        {
                                if ($this->$key != $pk[$key])
                                {
                                        $ours = false;
                                }
                        }

                        if ($ours)
                        {
                                $this->published = $state;
                        }
                }

                $this->setError('');

                return true;
        }
}
