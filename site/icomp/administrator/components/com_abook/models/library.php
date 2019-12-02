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

jimport('joomla.application.component.modeladmin');

class AbookModelLibrary extends JModelAdmin
{
	protected $text_prefix = 'COM_ABOOK';


	protected function canDelete($record)
        {
                $user = JFactory::getUser();

		if ($record->catid) {
			return $user->authorise('core.delete', 'com_abook.category.'.(int) $record->id);
		} else {
                        return parent::canDelete($record);
                }
        }



	protected function canEditState($record)
        {
                $user = JFactory::getUser();

		return $user->authorise('core.edit.state', 'com_abook.category.'.(int) $record->id);
        }



	public function getTable($type = 'Library', $prefix = 'AbookTable', $config = array())
        {
                return JTable::getInstance($type, $prefix, $config);
        }



	public function getForm($data = array(), $loadData = true)
        {
		$form = $this->loadForm('com_abook.library', 'library', array('control' => 'jform', 'load_data' => $loadData));
                if (empty($form)) {
                        return false;
                }

		return $form;
        }



	protected function loadFormData()
        {
                // Check the session for previously entered form data.
                $data = JFactory::getApplication()->getUserState('com_abook.edit.library.data', array());

                if (empty($data)) {
                        $data = $this->getItem();
                }

                return $data;
        }


	/*protected function prepareTable(&$table)
        {
                $table->name = htmlspecialchars_decode($table->name, ENT_QUOTES);
		$table->alias           = JApplication::stringURLSafe($table->name);
		
        }*/
	
	

}
