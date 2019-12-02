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

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controllerform');

class AbookControllerBook extends JControllerForm
{
	
        public function vote(){
		// Check for request forgeries.
                JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

                $user_rating = JRequest::getInt('user_rating', -1);

                if ( $user_rating > -1 ) {
                        $url = JRequest::getString('url', '');
                        $id = JRequest::getInt('id', 0);
                        $viewName = JRequest::getString('view', $this->default_view);
			$model = $this->getModel($viewName);

                        if ($model->storeVote($id, $user_rating)) {
                                $this->setRedirect($url, JText::_('COM_ABOOK_BOOK_VOTE_SUCCESS'));
                        } else {
                                $this->setRedirect($url, JText::_('COM_ABOOK_BOOK_VOTE_FAILURE'));
                        }
                }
        }

	function lendrequest(){
		$app = JFactory::getApplication();
                $url = JRequest::getString('url', '');
		$user = JFactory::getUser();
		if ($user->guest){
			// Check for request forgeries.
			$code= JRequest::get('dynamic_recaptcha_1');     
			JPluginHelper::importPlugin('captcha');
			$dispatcher = JDispatcher::getInstance();
			$res = $dispatcher->trigger('onCheckAnswer',$code);
			if(!$res[0]){
				$this->setRedirect($url, JText::sprintf('COM_ABOOK_BOOK_LEND_REQUEST_FAILURE', JText::_('COM_ABOOK_ERROR_CAPTCHA')), "error");
				return;
			}
		}
		$user = JFactory::getUser();
		$params = JComponentHelper::getParams('com_abook');
		if ($params->get( 'show_lend_request', 1 )==1 && in_array($params->get('allow_lend_request'), $user->getAuthorisedViewLevels())) {
                	JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
                        $id = JRequest::getInt('id', 0);
			$lend_out = JRequest::getVar('lend_out', '0000-00-00');
			$lend_in = JRequest::getVar('lend_in', '0000-00-00');
			$name = JRequest::getVar('custom_user_name', '');
			$email = JRequest::getVar('custom_user_email', '');
                        $viewName = JRequest::getString('view', $this->default_view);
                        $model = $this->getModel($viewName);
                        if ($model->lendRequest($id, $lend_out, $lend_in, $name, $email)) {
                                $this->setRedirect($url, JText::_('COM_ABOOK_BOOK_LEND_REQUEST_SUCCESS'), "info");
				JPluginHelper::importPlugin('abook');
		                $dispatcher = JEventDispatcher::getInstance();
				$results = $dispatcher->trigger('onSendEmail', array(&$from, &$to));
                        } else {
                                $this->setRedirect($url, JText::sprintf('COM_ABOOK_BOOK_LEND_REQUEST_FAILURE', $model->getError()), "error");
                        }
			return;
		}
		$this->setRedirect($url, JText::sprintf('COM_ABOOK_BOOK_LEND_REQUEST_FAILURE', JText::_('JGLOBAL_AUTH_ACCESS_DENIED')), "error");
	}
}
