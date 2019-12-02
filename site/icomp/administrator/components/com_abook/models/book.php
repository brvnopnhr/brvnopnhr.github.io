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

class AbookModelBook extends JModelAdmin
{
	protected $text_prefix = 'COM_ABOOK';

	protected function canDelete($record)
        {
		if (!empty($record->id)) {
                        if ($record->state != -2) {
                                return ;
                        }
                        $user = JFactory::getUser();

                        if ($record->catid) {
                                return $user->authorise('core.delete', 'com_abook.category.'.(int) $record->catid);
                        }
                        else {
                                return parent::canDelete($record);
                        }
                }
        }

	protected function canEditState($record)
        {
		$user = JFactory::getUser();

                // Check for existing article.
                if (!empty($record->id)) {
                        return $user->authorise('core.edit.state', 'com_abook.book.'.(int) $record->id);
                }
                // New article, so check against the category.
                else if (!empty($record->catid)) {
                        return $user->authorise('core.edit.state', 'com_abook.category.'.(int) $record->catid);
                }
                // Default to component settings if neither article nor category known.
                else {
                        return parent::canEditState($record);
                }
        }

	protected function prepareTable($table)
        {
		jimport('joomla.filter.output');
                $date = JFactory::getDate();
                $user = JFactory::getUser();
                $table->title            = htmlspecialchars_decode($table->title, ENT_QUOTES);
                $table->alias           = JApplication::stringURLSafe($table->alias);

                if (empty($table->alias)) {
                        $table->alias = JApplication::stringURLSafe($table->title);
                }

                if (empty($table->id)) {
                       $table->reorder('catid = ' . (int) $table->catid . ' AND state >= 0'); 
                }
        }

	public function getTable($type = 'Book', $prefix = 'AbookTable', $config = array())
        {
                return JTable::getInstance($type, $prefix, $config);
        }

        public function getItem($pk = null)
        {
                if ($item = parent::getItem($pk))
                {
                        // Convert the metadata field to an array.
                        $registry = new JRegistry;
                        $registry->loadString($item->metadata);
                        $item->metadata = $registry->toArray();
		}
		return $item;
	}

	public function getForm($data = array(), $loadData = true)
        {
                // Get the form.
                $form = $this->loadForm('com_abook.book', 'book', array('control' => 'jform', 'load_data' => $loadData));
                if (empty($form)) {
                        return false;
                }

                // Determine correct permissions to check.
                if ($this->getState('book.id')) {
                        // Existing record. Can only edit in selected categories.
                        $form->setFieldAttribute('catid', 'action', 'core.edit');
                }
                else {
                        // New record. Can only create in selected categories.
                        $form->setFieldAttribute('catid', 'action', 'core.create');
                }

		// Modify the form based on Edit State access controls.
                if (!$this->canEditState((object) $data)) {
                        // Disable fields for display.
                        $form->setFieldAttribute('ordering', 'disabled', 'true');
                        $form->setFieldAttribute('state', 'disabled', 'true');

                        // Disable fields while saving.
                        // The controller has already verified this is an article you can edit.
                        $form->setFieldAttribute('ordering', 'filter', 'unset');
                        $form->setFieldAttribute('state', 'filter', 'unset');
                }

                return $form;
        }

	protected function loadFormData()
        {
                // Check the session for previously entered form data.
                $data = JFactory::getApplication()->getUserState('com_abook.edit.book.data', array());
                if (empty($data)) {
                        $data = $this->getItem();
                }
		$data->idauth=$this->getBooksAuthorslist();
		$data->idtag=$this->getBooksTagslist();
                return $data;
        }

	public function save($data)
        {
		$app = JFactory::getApplication();
		// Alter the title for save as copy
                if ($app->input->get('task') == 'save2copy')
                {
                        list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
                        $data['title'] = $title;
                        $data['alias'] = $alias;
                        $data['state'] = 0;
                }
		//fixed by le5, many thanks!
                if (parent::save($data)) {
			$this->storeauthor($this->getState('book.id'), $data['idauth']);
			$this->storetag($this->getState('book.id'), $data['idtag']);
                        return true;
                }

                return false;
        }

	function storeauthor($id, $idauthors)
        {
                $booksauthorslist['id'] = 0;
                $booksauthorslist['idbook'] = $id;

                $row1 =& $this->getTable('bookauth2');
                if (!$row1->delete($id)) {
                        $this->setError($this->_db->getErrorMsg());
                        return false;
                }
                $row2 =& $this->getTable('bookauth');
                //uno per volta
                foreach ($idauthors as $auth) {
                        $booksauthorslist['idauth']=$auth;
                        if (!$row2->bind($booksauthorslist)) {
                                $this->setError($this->_db->getErrorMsg());
                                return false;
                        }

                        // Make sure the record is valid
                        if (!$row2->check()) {
                                $this->setError($this->_db->getErrorMsg());
                                return false;
                        }

                        // Store the web link table to the database
                        if (!$row2->store($booksauthorslist, $booksauthorslist['id'])) {
                                $this->setError( $row2->getErrorMsg() );
                                return false;
                        }
                }
        }

        function storetag($id, $idtags)
        {
                $bookstagslist['id'] = 0;
                $bookstagslist['idbook'] = $id;

                $row1 =& $this->getTable('booktag2');
                if (!$row1->delete($id)) {
                        $this->setError($this->_db->getErrorMsg());
                        return false;
                }
                $row2 =& $this->getTable('booktag');
                //uno per volta
                foreach ($idtags as $tag) {
                        $bookstagslist['idtag']=$tag;
                        if (!$row2->bind($bookstagslist)) {
                                $this->setError($this->_db->getErrorMsg());
                                return false;
                        }

                        // Make sure the record is valid
                        if (!$row2->check()) {
                                $this->setError($this->_db->getErrorMsg());
                                return false;
                        }

                        // Store the web link table to the database
                        if (!$row2->store($bookstagslist, $bookstagslist['id'])) {
                                $this->setError( $row2->getErrorMsg() );
                                return false;
                        }
                }
        }

	/*public function getAuthorslist() {
                if (empty( $this->_authorslist )) {
                        $query = ' SELECT concat(lastname, " ", name) AS name, id'
                        . ' FROM #__abauthor ORDER BY lastname, name'
                        ;
                }
                if (empty($this->_authorslist)) {
                        $this->_db->setQuery( $query );
                        $this->_authorslist = $this->_getList( $query );
                }

                return $this->_authorslist;
        }*/

        public function getBooksAuthorslist() {
                if (empty( $this->_booksauthorslist )) {
			$db = $this->getDbo();
			$query = $db->getQuery(true);
                        $query->select('a.id');
			$query->from('#__abbookauth AS ba');
                        $query->innerjoin('#__abauthor AS a ON ba.idauth = a.id');
                        $query->where('ba.idbook = '.(int) $this->getItem()->id);
			$query->order('a.lastname, a.name');
                        $db->setQuery($query);
                        $this->_booksauthorslist = $db->loadColumn();
                }

                return $this->_booksauthorslist;
        }
	
	/*public function getTagslist() {
                if (empty( $this->_tagslist )) {
                        $query = ' SELECT name, id '
                        . ' FROM #__abtag ORDER BY name'
                        ;
                }
                if (empty($this->_tagslist)) {
                        $this->_db->setQuery( $query );
                        $this->_tagslist = $this->_getList( $query );
                }

                return $this->_tagslist;
        }*/

        public function getBooksTagslist() {
                if (empty( $this->_booksTagslist )) {
                //      $bookId       = (int) $this->form->getValue('id');
			$db = $this->getDbo();
			$query = $db->getQuery(true);
                        $query->select('a.id');
                        $query->from('#__abbooktag AS bt');
			$query->innerjoin('#__abtag AS a ON bt.idtag = a.id');
                        $query->where('bt.idbook = '.(int) $this->getItem()->id);
			$query->order('a.name');
			$db->setQuery( $query );
                        $this->_bookstagslist = $db->loadColumn();
                }

                return $this->_bookstagslist;
        }

        public function resethits($pks)
        {
                // Sanitize the ids.
                $pks = (array) $pks;
                JArrayHelper::toInteger($pks);

                if (empty($pks)) {
                        $this->setError(JText::_('JGLOBAL_NO_ITEM_SELECTED'));
                        return false;
                }

                $table = $this->getTable();

                try {
                        $db = $this->getDbo();
			$query = $db->getQuery(true);
                        $query->update('#__abbook AS a');
                        $query->set('a.hits = 0');
                        $query->where('a.id IN ('.implode(',', $pks).')');
			$db->setQuery($query);
                        if (!$db->execute()) {
                                throw new Exception($db->getErrorMsg());
                        }

                } catch (Exception $e) {
                        $this->setError($e->getMessage());
                        return false;
                }

                return true;
        }

	function getFolderList($base = null)
        {
                // Get some paths from the request
                if (empty($base)) {
                        $base = JPATH_ROOT.DS.'images';
                }
                // Get the list of folders
                jimport('joomla.filesystem.folder');
                $folders = JFolder::listFolderTree($base, '.');
		array_unshift($folders, array('id' => '1','parent' => 0,'name' => '','fullname' => '','relname' => ''));

                return $folders;
        }
}
