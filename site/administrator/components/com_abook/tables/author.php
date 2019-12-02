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

class AbookTableAuthor extends JTable
{
	public function __construct(&$db)
        {
                parent::__construct('#__abauthor', 'id', $db);
        }

	function check()
        {
	/** check for valid name */
                if (trim($this->lastname) == '') {
                        $this->setError(JText::_('COM_ABOOK_WARNING_PROVIDE_VALID_NAME'));
                        return false;
                }

		if (empty($this->alias)) {
                        $this->alias = $this->name.'-'.$this->lastname;
                }
                $this->alias = JApplication::stringURLSafe($this->alias);
                if (trim(str_replace('-','',$this->alias)) == '') {
                        $this->alias = JFactory::getDate()->format("Y-m-d-H-i-s");
                }

		if (!empty($this->metakey)) {
                        // only process if not empty
                        $bad_characters = array("\n", "\r", "\"", "<", ">"); // array of characters to remove
                        $after_clean = JString::str_ireplace($bad_characters, "", $this->metakey); // remove bad characters
                        $keys = explode(',', $after_clean); // create array using commas as delimiter
                        $clean_keys = array();
                        foreach($keys as $key) {
                                if (trim($key)) {  // ignore blank keywords
                                        $clean_keys[] = trim($key);
                                }
                        }
                        $this->metakey = implode(", ", $clean_keys); // put array back together delimited by ", "
                }

                // clean up description -- eliminate quotes and <> brackets
                if (!empty($this->metadesc)) {
                        // only process if not empty
                        $bad_characters = array("\"", "<", ">");
                        $this->metadesc = JString::str_ireplace($bad_characters, "", $this->metadesc);
                }
                return true;
        }

	public function delete($pk=null)
        {
                //check per impedire la cancellazione di un autore già utilizzato
                $query = 'SELECT * FROM #__abbookauth WHERE idauth = '.$pk;

                $this->_db->setQuery($query);

                $xid = $this->_db->loadResult();
                if (count($xid) > 0) {
                        JError::raiseWarning('SOME_ERROR_CODE', JText::_('COM_ABOOK_DELETE_NOT_ALLOWED_ITEM_ALREADY_USED_IN_A_BOOK'));
                        return false;
                }

                return parent::delete($pk);
        }
}
