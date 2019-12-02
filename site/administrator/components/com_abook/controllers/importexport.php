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
jimport('joomla.application.component.controller');
jimport('joomla.filesystem.file');
class AbookControllerImportexport extends JControllerAdmin 
{
        function export()
        {
		JRequest::checkToken() or die( 'Invalid Token' );
                $post   = JRequest::getVar('jform', array(), 'post', 'array');
//error_log(print_r($post, true), 3, '/tmp/debug.log');
		$link = 'index.php?option=com_abook&view=importexport&format=raw';
		$model = $this->getModel('importexport');
		$model->setExportType($post);
                $this->setRedirect($link);
        }

	function import() {
		$msgtype = '';
		$post   = JRequest::getVar('jform', array(), 'post', 'array');
		$model = $this->getModel('importexport');
	        $importfile = JRequest::getVar('jform', null, 'files', 'array' );
		//$importfile = $_FILES['jform'];
//error_log(print_r($post, true), 3, '/tmp/debug.log');
		$link = 'index.php?option=com_abook&view=importexport';
		$filename = JFile::makeSafe($importfile['name']['import_file']);
		$src = $importfile['tmp_name']['import_file'];
		$dest = JPATH_COMPONENT_ADMINISTRATOR . DS . "uploads" . DS . $filename;
		JFile::upload($src, $dest);


		/*if (JFile::getExt($filename)=="zip"){
		$package = JInstallerHelper::unpack($tmp_dest, true);
		}*/
		switch ($post['type2']){
                        case 1:
				if (JFile::getExt($filename)=="sql"){
					if ($returncount = $model->importSql($filename, $post['user'], $post['db'])) {
                                		$msg = JText::sprintf( 'COM_ABOOK_SUCCESSFULLY_MYSQL_IMPORT', $returncount );
					}else{
						$msg = JText::sprintf( 'COM_ABOOK_AN_ERROR_HAS_OCCURRED', $returncount );
					}
				}else{
					$msg = JText::_( 'COM_ABOOK_ERROR_SQL_EXT' );
					$msgtype = 'error';
				}
                                break;
                        case 2:
				if (JFile::getExt($filename)=="csv"){
					//$post['category2']    = $post['jform']['category2'];
					$returncount = $model->importCsv($dest, $post['category2'], $post['user']);
                                	if ($returncount > 0) {
                                        	$msg = JText::sprintf( 'COM_ABOOK_SUCCESSFULLY_CSV_IMPORT', $returncount);
						$msgtype = 'message';
        	                        }else{
                	                        $msg = JText::_( 'COM_ABOOK_AN_ERROR_HAS_OCCURRED');
						$msgtype = 'error';
                                	}
				}else{
                                        $msg = JText::_( 'COM_ABOOK_ERROR_CSV_EXT' );
					$msgtype = 'error';
                                }
                                break;
			case 3:
                                if (JFile::getExt($filename)=="csv"){
                                        //$post['category2']    = $post['jform']['category2'];
                                        $returncount = $model->importCsv($dest, $post['category2'], $post['user'], 'j15');
                                        if ($returncount > 0) {
                                                $msg = JText::sprintf( 'COM_ABOOK_SUCCESSFULLY_CSV_IMPORT', $returncount);
                                                $msgtype = 'message';
                                        }else{
                                                $msg = JText::_( 'COM_ABOOK_AN_ERROR_HAS_OCCURRED');
                                                $msgtype = 'error';
                                        }
                                }else{
                                        $msg = JText::_( 'COM_ABOOK_ERROR_CSV_EXT' );
                                        $msgtype = 'error';
                                }
                                break;
                        default:
                                $msg = JText::_( 'COM_ABOOK_SELECT_A_IMPORT_TYPE' );
				$msgtype = 'error';
                                break;
		}
		$this->setRedirect($link, $msg, $msgtype);
	}

}
