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

class AbookTableLocation extends JTable
{
	public function __construct(& $db) {
		parent::__construct('#__ablocations', 'id', $db);
	}

	function check()
        {
                /** check for valid name */
                if (trim($this->name) == '') {
                        $this->setError(JText::_('LOCATION ITEM MUST HAVE A NAME'));
                        return false;
                }
                if (empty($this->alias)) {
                        $this->alias = $this->name;
                }
                $this->alias = JApplication::stringURLSafe($this->alias);
                if (trim(str_replace('-','',$this->alias)) == '') {
                        $this->alias = JFactory::getDate()->format("Y-m-d-H-i-s");
               	} 
                return true;
        }
        function delete($pk=null)
        {
                //check per impedire la cancellazione di una categoria piena
                $query = 'SELECT * FROM #__abbook WHERE idlocation = '.$pk;
                $this->_db->setQuery($query);
                $xid = $this->_db->loadResult();
                if (count($xid) > 0) {
                        JError::raiseWarning('SOME_ERROR_CODE', JText::sprintf('COM_ABOOK_DELETE_NOT_ALLOWED_ITEM_ALREADY_USED_IN_A_BOOK', $this->catname));
                        return false;
                }

               	return parent::delete($pk); 
        }
}
