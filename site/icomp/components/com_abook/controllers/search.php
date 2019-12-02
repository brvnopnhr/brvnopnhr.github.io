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

jimport('joomla.application.component.controller');

class AbookControllerSearch extends JControllerLegacy
{

        function display($cachable = false, $urlparams = false)
        {
                parent::display($cachable = false, $urlparams = false);
                return $this;
        }
/*	function search()
	{
		$search=JRequest::getVar('filter', null, 'post', "array");
		$app            = JFactory::getApplication();
		 //$model = $this->getModel('Search');
		//$model->populateState();
		$session =& JFactory::getSession();
//print_r($session);
		//$app->setUserState('com_abook.search.filter.search', 'ciccio');
//$searchterms = $this->state->get('filter.search');
$session->set('com_abook.search', $search);
$app->setUserState( 'com_abook.search.filter.search', $search);
error_log(print_r($session, true), 3, "tmp/log.ini");
                $this->setRedirect(JRoute::_('index.php?&option=com_abook&view=search'));
	}*/

	/*function search()
        {
		$search=JRequest::getVar('filter', null, 'post', "array");
		print_r($search);
		foreach ($search as $key=>$value){
			$url.="&".$key.'='.$value;
		}
		$this->setRedirect(AbookHelperRoute::getSearchRoute().$url);
	}*/
}
