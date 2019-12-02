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

class AbookModelLend extends JModelAdmin
{
	protected $text_prefix = 'COM_ABOOK';
	protected $_lends;

	public function getTable($type = 'Lend', $prefix = 'AbookTable', $config = array())
        
        {
                return JTable::getInstance($type, $prefix, $config);
        }



	public function getForm($data = array(), $loadData = true)
        {
		$form = $this->loadForm('com_abook.lend', 'lend', array('control' => 'jform', 'load_data' => $loadData));
                if (empty($form)) {
                        return false;
                }

		return $form;
        }

	public function save($data)
        {
		//se è una richiesta non fare controlli
		if ($data['state']==2){
			parent::save($data);
			return true;
		}else{
			$db = $this->getDbo();
			//se la quantità du copie è 0 non può essere prestato
			$query = $db->getQuery(true);
                        $query->select('*');
                        $query->from('#__abbook');
                        $query->where('id ='.$data['book_id']);
                        $db->setQuery($query);
                        $db->execute();
                        $book = $db->loadObject();
                        if ($book->qty==0){
                                $this->setError(JText::_('COM_ABOOK_AVAILABLE_COPIES_EXCEEDED'));
                                return false;
                        }

                	$query = $db->getQuery(true);
			$query->select('l.*, b.qty');
			$query->from('#__ablend AS l');
			$query->join('LEFT', '#__abbook AS b ON b.id=l.book_id');
			//if (isset($old_row) && $old_row->state == 2){
			//	$query->where('l.state IN (0,2)');
			//}else{
			$query->where('l.state = 0');
			//}
			$query->where('l.book_id ='.$data['book_id']);
			if (isset($data['id'])){
				$query->where('l.id !='.$data['id']);
			}
		/*	$query->where('((l.lend_out >= '.$db->escape($data['lend_out']). ' AND l.lend_in <= '.$db->escape($data['lend_in']).' ) OR 
			(l.lend_out < '.$db->escape($data['lend_out']). ' AND l.lend_in < '.$db->escape($data['lend_in']).' ) OR 
			(l.lend_out > '.$db->escape($data['lend_out']). ' AND l.lend_in > '.$db->escape($data['lend_in']).' ()');*/
			$db->setQuery($query);
			$db->execute();
        	        $rows = $db->getNumRows();
			$result = $db->loadObject();
			if (empty($result) || $rows < $result->qty){
				parent::save($data);
				return true;
			}else{
				$this->setError(JText::_('COM_ABOOK_AVAILABLE_COPIES_EXCEEDED'));
				return false;
			}
			return false;
		}
        }

	protected function loadFormData()
        {
                // Check the session for previously entered form data.
                $data = JFactory::getApplication()->getUserState('com_abook.edit.lend.data', array());

                if (empty($data)) {
                        $data = $this->getItem();
                }
                return $data;
        }

	public function getLends()
	{
                if (!isset($this->_lends )) {
			$item=$this->getItem();
			if ($item->get('book_id') > 0){
	                        //importo il model del calendars e gli passo i parametri che vuole
        	                $model = JModelLegacy::getInstance('Lends', 'AbookModel', array('ignore_request' => true));
                	        $model->setState('params', $this->getState('params'));
	                       	$model->setState('filter.book_id', $item->book_id);
        	                $model->getItems();
                	        $this->_lends = $model->getItems();
			}
                }
                return $this->_lends;
	}
}	
