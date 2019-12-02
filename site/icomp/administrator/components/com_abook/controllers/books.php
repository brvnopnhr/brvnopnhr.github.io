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

class AbookControllerBooks extends JControllerAdmin
{

	public function getModel($name = 'Book', $prefix = 'AbookModel', $config = array())
        {
                $model = parent::getModel($name, $prefix, array('ignore_request' => true));
                return $model;
        }

        function resethits()
        {
                // Check for request forgeries
                JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

                // Initialise variables.
                $user   = JFactory::getUser();
                $ids    = JRequest::getVar('cid', array(), '', 'array');
                $task   = $this->getTask();

                // Access checks.
                foreach ($ids as $i => $id)
                {
                        if (!$user->authorise('core.edit.state', 'com_abook.book.'.(int) $id)) {
                                // Prune items that you can't change.
                                unset($ids[$i]);
                                JError::raiseNotice(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
                        }
                }

                if (empty($ids)) {
                        JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
                }else {
                        // Get the model.
                        $model = $this->getModel();

                        // Publish the items.
                        if (!$model->resethits($ids)) {
                                JError::raiseWarning(500, $model->getError());
                        }else{
                               	$ntext = $this->text_prefix.'_N_ITEMS_RESET_HITS';
                                $this->setMessage(JText::plural($ntext, count($ids)));
                	}
                }

                $this->setRedirect('index.php?option=com_abook&view=books');
        }

}
