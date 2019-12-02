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

jimport( 'joomla.application.component.controllerform' );

class AbookControllerCategory extends JControllerForm
{
	protected $extension;

        public function __construct($config = array())
        {
                parent::__construct($config);

                $this->extension = 'com_abook';
        }

	protected function allowAdd($data = array())
        {
		return JFactory::getUser()->authorise('core.create', $this->extension);
        }

        protected function allowEdit($data = array(), $key = 'parent_id')
        {
                // Initialise variables.
                $recordId       = (int) isset($data[$key]) ? $data[$key] : 0;
                $user           = JFactory::getUser();
                $userId         = $user->get('id');

                // Check general edit permission first.
                if ($user->authorise('core.edit', $this->extension)) {
                        return true;
                }

                // Check specific edit permission.
                if ($user->authorise('core.edit', $this->extension.'.category.'.$recordId)) {
                        return true;
                }

                // Fallback on edit.own.
                // First test if the permission is available.
                if ($user->authorise('core.edit.own', $this->extension.'.category.'.$recordId) || $user->authorise('core.edit.own', $this->extension)) {
                        // Now test the owner is the user.
                        $ownerId        = (int) isset($data['created_user_id']) ? $data['created_user_id'] : 0;
                        if (empty($ownerId) && $recordId) {
                                // Need to do a lookup from the model.
                                $record         = $this->getModel()->getItem($recordId);

                                if (empty($record)) {
                                        return false;
                                }

                                $ownerId = $record->created_user_id;
                        }
                        // If the owner matches 'me' then do the test.
                        if ($ownerId == $userId) {
                                return true;
                        }
                }
                return false;
         }

	public function batch($model)
        {
                JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

                // Set the model
                $model  = $this->getModel('Category');

                $extension = 'com_abook';
                if ($extension) {
                        $extension = '&extension='.$extension;
                }

                // Preset the redirect
                $this->setRedirect('index.php?option=com_abook&view=categories'.$extension);

                return parent::batch($model);
        }

	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
        {
                $append = parent::getRedirectToItemAppend($recordId);

                return $append;
        }

        protected function getRedirectToListAppend()
        {
                $append = parent::getRedirectToListAppend();
                $append .= '&extension='.$this->extension;

                return $append;
        }
}
