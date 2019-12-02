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

class JHTMLCredit
{
        public static function credit()
        {
		$xmldata = JApplicationHelper::parseXMLInstallFile(JPATH_ADMINISTRATOR .DS. 'components'.DS.'com_abook'.DS.'abook.xml');
		$credit='<div style="text-align:center;"><a href="http://www.alexandriabooklibrary.org" target="_blank">Alexandria Book Library '.$xmldata['version'].'</a></div><br />';
                return $credit;
        }
}
