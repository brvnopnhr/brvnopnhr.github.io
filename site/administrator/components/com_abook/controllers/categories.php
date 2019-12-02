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

jimport('joomla.application.component.controlleradmin' );

class AbookControllerCategories extends JControllerAdmin
{
	public function getModel($name = 'Category', $prefix = 'AbookModel', $config = array())
        {
                $model = parent::getModel($name, $prefix, array('ignore_request' => true));
                return $model;
        }	

	public function saveorder()
        {
                JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

                // Get the arrays from the Request
                $order  = JRequest::getVar('order',     null, 'post', 'array');
                $originalOrder = explode(',', JRequest::getString('original_order_values'));

                // Make sure something has changed
                if (!($order === $originalOrder)) {
                        parent::saveorder();
                } else {
                        // Nothing to reorder
                        $this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
                        return true;
                }
        }
	public function rebuild()
        {
                JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

                $extension = JRequest::getCmd('extension');
                $this->setRedirect(JRoute::_('index.php?option=com_abook&view=categories&extension='.$extension, false));

                // Initialise variables.
                $model = $this->getModel();

                if ($model->rebuild()) {
                        // Rebuild succeeded.
                        $this->setMessage(JText::_('COM_ABOOK_REBUILD_SUCCESS'));
                        return true;
                } else {
                        // Rebuild failed.
                        $this->setMessage(JText::_('COM_ABOOK_REBUILD_FAILURE'));
                        return false;
                }
        }
}
