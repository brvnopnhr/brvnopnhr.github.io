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

class AbookModelCategory extends JModelAdmin
{
	protected $text_prefix = 'COM_ABOOK';

	protected function canDelete($record)
        {
                $user = JFactory::getUser();

                return $user->authorise('core.delete', $record->extension.'.category.'.(int) $record->id);
        }

	protected function canEditState($record)
        {
                $user = JFactory::getUser();

                return $user->authorise('core.edit.state', $record->extension.'.category.'.(int) $record->id);
        }
	
	public function getTable($type = 'Category', $prefix = 'AbookTable', $config = array())
        {
                return JTable::getInstance($type, $prefix, $config);
        }

	protected function populateState()
        {
		$app = JFactory::getApplication('administrator');

                $parentId = JRequest::getInt('parent_id');
                $this->setState('category.parent_id', $parentId);

                // Load the User state.
                $pk = (int) JRequest::getInt('id');
                $this->setState($this->getName().'.id', $pk);

                $extension = JRequest::getCmd('extension', 'com_abook');
                $this->setState('category.extension', $extension);
                $parts = explode('.',$extension);

                // Extract the component name
                $this->setState('category.component', $parts[0]);

                // Extract the optional section name
                $this->setState('category.section', (count($parts)>1)?$parts[1]:null);

                // Load the parameters.
                $params = JComponentHelper::getParams('com_abook');
                $this->setState('params', $params);
        }

	public function getItem($pk = null)
        {
                if ($result = parent::getItem($pk)) {
                        // Prime required properties.
                        if (empty($result->id)) {
                                $result->parent_id      = $this->getState('category.parent_id');
                                $result->extension      = $this->getState('category.extension');
                        }

                        // Convert the metadata field to an array.
                        $registry = new JRegistry();
                        $registry->loadString($result->metadata);
                        $result->metadata = $registry->toArray();

                        // Convert the created and modified dates to local user time for display in the form.
                        jimport('joomla.utilities.date');
                        $tz     = new DateTimeZone(JFactory::getApplication()->getCfg('offset'));

                        if (intval($result->created_time)) {
                                $date = new JDate($result->created_time);
                                $date->setTimezone($tz);
                                $result->created_time = $date->toSql(true);
                        } else {
                                $result->created_time = null;
                        }

                        if (intval($result->modified_time)) {
                                $date = new JDate($result->modified_time);
                                $date->setTimezone($tz);
                                $result->modified_time = $date->toSql(true);
                        } else {
                                $result->modified_time = null;
                        }
                }

                return $result;
        }

	public function getForm($data = array(), $loadData = true)
        {
                // Initialise variables.
                $extension      = $this->getState('category.extension');

                // Get the form.
                $form = $this->loadForm('com_abook.category'.$extension, 'category', array('control' => 'jform', 'load_data' => $loadData));
                if (empty($form)) {
                        return false;
                }

                return $form;
        }

	protected function loadFormData()
        {
                // Check the session for previously entered form data.
                $data = JFactory::getApplication()->getUserState('com_abook.edit.category.data', array());

                if (empty($data)) {
                        $data = $this->getItem();
                }

                return $data;
        }

	protected function preprocessForm(JForm $form, $data, $group = 'abook')
        {
                jimport('joomla.filesystem.path');

                // Initialise variables.
                $lang           = JFactory::getLanguage();
                $extension      = $this->getState('category.extension');
                $component      = $this->getState('category.component');
                $section        = $this->getState('category.section');

                // Get the component form if it exists
                jimport('joomla.filesystem.path');
                $name = 'category'.($section ? ('.'.$section):'');

		// Looking first in the component models/forms folder
                //$path = JPath::clean(JPATH_ADMINISTRATOR."/components/$component/abook.xml");
		$path = JPath::clean(JPATH_ADMINISTRATOR."/components/$component/models/forms/$name.xml");

		// Old way: looking in the component folder
                if (!file_exists($path)) {
			$path = JPath::clean(JPATH_ADMINISTRATOR."/components/$component/abook.xml");
                }

		if (file_exists($path)) {
                        $lang->load($component, JPATH_BASE, null, false, false);
                        $lang->load($component, JPATH_BASE, $lang->getDefault(), false, false);

                        if (!$form->loadFile($path, false)) {
                                throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
                        }
                }

                // Try to find the component helper.
                $eName  = str_replace('com_', '', $component);
                $path   = JPath::clean(JPATH_ADMINISTRATOR."/components/$component/helpers/abook.php");

                if (file_exists($path)) {
                        require_once $path;
                        $cName  = ucfirst($eName).ucfirst($section).'HelperCategory';

                        if (class_exists($cName) && is_callable(array($cName, 'onPrepareForm'))) {
                                        $lang->load($component, JPATH_BASE, null, false, false)
                                ||      $lang->load($component, JPATH_BASE . '/components/' . $component, null, false, false)
                                ||      $lang->load($component, JPATH_BASE, $lang->getDefault(), false, false)
                                ||      $lang->load($component, JPATH_BASE . '/components/' . $component, $lang->getDefault(), false, false);
                                call_user_func_array(array($cName, 'onPrepareForm'), array(&$form));

                                // Check for an error.
                                if (JError::isError($form)) {
                                        $this->setError($form->getMessage());
                                        return false;
                                }
                        }
                }

                // Set the access control rules field component value.
                $form->setFieldAttribute('rules', 'component',  $component);
                $form->setFieldAttribute('rules', 'section',    $name);

                // Trigger the default form events.
                parent::preprocessForm($form, $data);
        }

/*	public function save($data)
        {
                $pk             = (!empty($data['id'])) ? $data['id'] : (int)$this->getState('category.id');
                $isNew  = true;

                // Get a row instance.
                $table = $this->getTable();
                // Load the row if saving an existing category.
                if ($pk > 0) {
                        $table->load($pk);
                        $isNew = false;
                }

                // Set the new parent id if parent id not matched OR while New/Save as Copy .
                if ($table->parent_id != $data['parent_id'] || $data['id'] == 0) {
                        $table->setLocation($data['parent_id'], 'last-child');
                }

                // Alter the title for save as copy
                if (!$isNew && $data['id'] == 0 && $table->parent_id == $data['parent_id']) {
                        $m = null;
                        $data['alias'] = '';
                        if (preg_match('#\((\d+)\)$#', $table->title, $m)) {
                                $data['title'] = preg_replace('#\(\d+\)$#', '('.($m[1] + 1).')', $table->title);
                        } else {
                                $data['title'] .= ' (2)';
                        }
                }

                // Bind the data.
                if (!$table->bind($data)) {
                        $this->setError($table->getError());
                        return false;
                }

                // Bind the rules.
                if (isset($data['rules'])) {
                        $rules = new JRules($data['rules']);
                        $table->setRules($rules);
                }

                // Check the data.
                if (!$table->check()) {
                        $this->setError($table->getError());
                        return false;
                }

                // Store the data.
                if (!$table->store()) {
                        $this->setError($table->getError());
                        return false;
                }

                // Rebuild the tree path.
                if (!$table->rebuildPath($table->id)) {
                        $this->setError($table->getError());
                        return false;
                }

                $this->setState('category.id', $table->id);

                return true;
        }
*/
        public function save($data)
        {
                // Initialise variables;
                $dispatcher = JDispatcher::getInstance();
                $table          = $this->getTable();
                $pk                     = (!empty($data['id'])) ? $data['id'] : (int)$this->getState($this->getName().'.id');
                $isNew          = true;

                // Include the content plugins for the on save events.
                JPluginHelper::importPlugin('content');

                // Load the row if saving an existing category.
                if ($pk > 0) {
                        $table->load($pk);
                        $isNew = false;
                }

                // Set the new parent id if parent id not matched OR while New/Save as Copy .
                if ($table->parent_id != $data['parent_id'] || $data['id'] == 0) {
                        $table->setLocation($data['parent_id'], 'last-child');
                }

                // Alter the title for save as copy
                if (JRequest::getVar('task') == 'save2copy') {
                        list($title,$alias) = $this->generateNewTitle($data['parent_id'], $data['alias'], $data['title']);
                        $data['title']  = $title;
                        $data['alias']  = $alias;
                }

                // Bind the data.
                if (!$table->bind($data)) {
                        $this->setError($table->getError());
                        return false;
                }

                // Bind the rules.
                if (isset($data['rules'])) {
                        $rules = new JRules($data['rules']);
                        $table->setRules($rules);
                }

                // Check the data.
                if (!$table->check()) {
                        $this->setError($table->getError());
                        return false;
                }

                // Trigger the onContentBeforeSave event.
                $result = $dispatcher->trigger($this->event_before_save, array($this->option.'.'.$this->name, &$table, $isNew));
                if (in_array(false, $result, true)) {
                        $this->setError($table->getError());
                        return false;
                }

                // Store the data.
                if (!$table->store()) {
                        $this->setError($table->getError());
                        return false;
                }

                // Trigger the onContentAfterSave event.
                $dispatcher->trigger($this->event_after_save, array($this->option.'.'.$this->name, &$table, $isNew));

                // Rebuild the path for the category:
                if (!$table->rebuildPath($table->id)) {
                        $this->setError($table->getError());
                        return false;
                }

                // Rebuild the paths of the category's children:
                if (!$table->rebuild($table->id, $table->lft, $table->level, $table->path)) {
                        $this->setError($table->getError());
                        return false;
                }

                $this->setState($this->getName().'.id', $table->id);

                // Clear the cache
                $this->cleanCache();

                return true;
        }
        public function rebuild()
        {
                // Get an instance of the table obejct.
                $table = $this->getTable();

                if (!$table->rebuild()) {
                        $this->setError($table->getError());
                        return false;
                }

                // Clear the cache
                $this->cleanCache();

                return true;
        }
	/*public function rebuild()
        {
                // Get an instance of the table obejct.
                $table = $this->getTable();

                if (!$table->rebuild()) {
                        $this->setError($table->getError());
                        return false;
                }

                return true;
        }*/

	/*public function saveorder($idArray = null, $lft_array = null)
        {
                // Get an instance of the table object.
                $table = $this->getTable();

                if (!$table->saveorder($idArray, $lft_array)) {
                        $this->setError($table->getError());
                        return false;
                }

                return true;

        }*/
        public function saveorder($idArray = null, $lft_array = null)
        {
                // Get an instance of the table object.
                $table = $this->getTable();

                if (!$table->saveorder($idArray, $lft_array)) {
                        $this->setError($table->getError());
                        return false;
                }

                // Clear the cache
                $this->cleanCache();

                return true;

        }

	protected function generateNewTitle($parent_id, $alias, $title)
        {
                // Alter the title & alias
                $table = $this->getTable();
                while ($table->load(array('alias' => $alias, 'parent_id' => $parent_id)))
                {
                        $title = JString::increment($title);
                        $alias = JString::increment($alias, 'dash');
                }

                return array($title, $alias);
        }
}
