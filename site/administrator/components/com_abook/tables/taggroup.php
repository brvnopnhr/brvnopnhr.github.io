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

class AbookTableTaggroup extends JTable
{
	public function __construct(& $db) {
		parent::__construct('#__abtag_groups', 'id', $db);
	}

	function check()
        {
                /** check for valid name */
                if (trim($this->name) == '') {
                        $this->setError(JText::_('TAG GROUP ITEM MUST HAVE A NAME'));
                        return false;
                }

                $alias = JFilterOutput::stringURLSafe($this->name);

                if(empty($this->alias) || $this->alias === $alias ) {
                        $this->alias = $alias;
                }
                return true;
        }
        function delete($pk=null)
        {
                //check per impedire la cancellazione di una categoria piena
                $query = 'SELECT * FROM #__abtag WHERE id_taggroup = '.$pk;
                $this->_db->setQuery($query);
                $xid = $this->_db->loadResult();
                if (count($xid) > 0) {
                        JError::raiseWarning('SOME_ERROR_CODE', JText::sprintf('YOU CAN NOT TO DELETE AN ITEM ALREADY USED IN A BOOK', $this->catname));
                        return false;
                }

             	return parent::delete($pk); 
        }
}
