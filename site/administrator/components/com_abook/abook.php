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
JHtml::_('behavior.tabstate');
if(!defined('DS')){
   define('DS',DIRECTORY_SEPARATOR);
}

if (!JFactory::getUser()->authorise('core.manage', 'com_abook')) {
        return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}
JLoader::register('AbookHelper', __DIR__ . '/helpers/abook.php');
// Include dependencies
//jimport('joomla.application.component.controller');

$controller = JControllerLegacy::getInstance('Abook');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
