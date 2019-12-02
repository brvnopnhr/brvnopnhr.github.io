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

jimport( 'joomla.application.component.view');

class AbookViewImportexport extends JViewLegacy
{
	function display($tpl = null)
	{
		global $mainframe;
                $file           = $this->get('Export');
		$ext= $this->get('ExportType');

		$doc = JFactory::getDocument();
		$doc->setMimeEncoding('text/plain');
		$xmldata = JApplicationHelper::parseXMLInstallFile(JPATH_ADMINISTRATOR .DS. 'components'.DS.'com_abook'.DS.'abook.xml');
		$xmldata['version']=str_replace(".", "_", $xmldata['version']);


		$filename="abook".$xmldata['version']."-export-".date('d-m-Y-H-i').$ext;
		$ext=null;
		JResponse::setHeader('Content-disposition', 'attachment'.'; filename='.$filename);
	}
}
?>
