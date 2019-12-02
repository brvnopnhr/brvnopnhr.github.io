<?php
/**
 * ------------------------------------------------------------------------
 * JA Extenstion Manager Component
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
 

class jaCompareTool
{
	//version is installed and modify by users
	var $_vLocal;
	//save version with local, but this is original version in server
	var $_vServer;
	//newer version is wanted to upgrade to
	var $_vUpgrade;
	//older version is wanted to downgrade
	var $_vDowngrade;
	
	//store version is result of compare
	var $_vCompare;
	/**
	 * store status of each file in compared version (diff object must be have exactly same struce of compared version)
	 * avaiable status
	 * - new (new file in new version) #0000FF
	 * - updated (updated file in new version) #FF0000
	 * - removed (removed file in new version) #9696BF
	 * - ucreated (created by user) #19EA22
	 * - umodified (modified by user) #EABB08
	 * - nochange (nochange) #000000
	 */
	var $_diff;
	
	//files which generated by jaupdater system
	var $exceptions = array("jaupdater.checksum.json", "jaupdater.comment.txt"); //"jaupdater.info.json"


	function jaCompareTool()
	{
	
	}


	/**
	 * checkToUpgrade
	 * @desc build diff view between three versions to support for upgrade purpose
	 *
	 * @param (object) $vLocal
	 * @param (object) $vServer
	 * @param (object) $vUpgrade
	 * @return (mixed) diff object or false
	 */
	function checkToUpgrade($vLocal, $vServer, $vUpgrade)
	{
		if (!is_object($vLocal) || !is_object($vServer) || !is_object($vUpgrade)) {
			return false;
		}
		$diff = new stdClass();
		$vCompare = new stdClass();
		
		$this->_vLocal = $vLocal;
		$this->_vServer = $vServer;
		$this->_vUpgrade = $vUpgrade;
		
		$crcLocal = is_object($vLocal->crc) ? $vLocal->crc : json_decode($vLocal->crc);
		$crcServer = is_object($vServer->crc) ? $vServer->crc : json_decode($vServer->crc);
		$crcUpgrade = is_object($vUpgrade->crc) ? $vUpgrade->crc : json_decode($vUpgrade->crc);
		
		$this->_diff = $this->_checkUpgrade($crcLocal, $crcServer, $crcUpgrade, $diff);
		return $this->_diff;
	}


	/**
	 * Rule to compare element
	 * - first: between upgrade version and server version (to get new files, updated files, removed files in new versions)
	 * - second: between local version and server version (to get created files and modified files by user)
	 *
	 * @param unknown_type $vLocal
	 * @param unknown_type $vServer
	 * @param unknown_type $vUpgrade
	 * @param unknown_type $diff
	 */
	function _checkUpgrade($vLocal, $vServer, $vUpgrade, &$diff)
	{
		$aFilesServer = $this->_getFilesOnly($vServer);
		
		if (count($vUpgrade) > 0) {
			foreach ($vUpgrade as $key => $value) {
				if (in_array($key, $this->exceptions)) {
					continue;
				}
				if (!$this->_isFolder($value)) {
					//compare files
					if (!$this->_isExistedInVersion($vServer, $key)) {
						//$vCompare->$key = $value;
						$diff->$key = 'new';
					} elseif ($value != $vServer->$key) {
						$diff->$key = 'updated';
					} else {
						$diff->$key = 'nochange';
					}
				} else {
					//compare folder
					$diff->$key = new stdClass();
					if (!$this->_isExistedInVersion($vServer, $key)) {
						$this->_copyFolder($value, $diff->$key, 'new');
					} else {
						if (!isset($vLocal->$key)) {
							$vLocal->$key = new stdClass();
						}
						$this->_checkUpgrade($vLocal->$key, $vServer->$key, $vUpgrade->$key, $diff->$key);
					}
				}
			}
		}
		
		if ($this->_isFolder($vServer)) {
			if (count($vServer) > 0) {
				foreach ($vServer as $key => $value) {
					if (in_array($key, $this->exceptions)) {
						continue;
					}
					if (!$this->_isFolder($value)) {
						if (!$this->_isExistedInVersion($diff, $key)) {
							$diff->$key = 'removed';
						}
					} else {
						if (!$this->_isExistedInVersion($diff, $key)) {
							$diff->$key = new stdClass();
							$this->_copyFolder($value, $diff->$key, 'removed');
						}
					}
				}
			}
		}
		
		if ($this->_isFolder($vLocal)) {
			if (count($vLocal) > 0) {
				foreach ($vLocal as $key => $value) {
					if (in_array($key, $this->exceptions)) {
						continue;
					}
					if (!$this->_isFolder($value)) {
						if ($this->_isExistedInVersion($diff, $key)) {
							if (isset($vServer->$key) && $vServer->$key != $value) {
								if ($diff->$key == 'nochange') {
									$diff->$key = 'umodified';
								} elseif ($diff->$key == 'updated') {
									$diff->$key = 'bmodified';
								}
							}
						} else {
							$diff->$key = 'ucreated';
						}
					} else {
						if (!$this->_isExistedInVersion($diff, $key)) {
							$diff->$key = new stdClass();
							$this->_copyFolder($value, $diff->$key, 'ucreated');
						}
					}
				}
			}
		}
		
		//need user removed check?
		

		return $diff;
	}


	/**
	 * compare between 2 version to build upgrade package
	 *
	 * @param (product object) $vServer (current product version (that user is currently using))
	 * @param (product object) $vUpgrade (new version need to upgarde to)
	 * @return unknown
	 */
	function checkToBuildUpgradePackage($vServer, $vUpgrade)
	{
		if (!is_object($vServer) || !is_object($vUpgrade)) {
			return false;
		}
		$diff = new stdClass();
		$vCompare = new stdClass();
		
		$this->_vServer = $vServer;
		$this->_vUpgrade = $vUpgrade;
		
		$crcServer = is_object($vServer->crc) ? $vServer->crc : json_decode($vServer->crc);
		$crcUpgrade = is_object($vUpgrade->crc) ? $vUpgrade->crc : json_decode($vUpgrade->crc);
		
		$this->_diff = $this->_checkToBuildUpgradePackage($crcServer, $crcUpgrade, $diff);
		return $this->_diff;
	}


	function _checkToBuildUpgradePackage($vServer, $vUpgrade, &$diff)
	{
		$aFilesServer = $this->_getFilesOnly($vServer);
		
		if (count($vUpgrade) > 0) {
			foreach ($vUpgrade as $key => $value) {
				if (in_array($key, $this->exceptions)) {
					continue;
				}
				if (!$this->_isFolder($value)) {
					//compare files
					if (!$this->_isExistedInVersion($vServer, $key)) {
						//$vCompare->$key = $value;
						$diff->$key = 'new';
					} elseif ($value != $vServer->$key) {
						$diff->$key = 'updated';
					} else {
						$diff->$key = 'nochange';
					}
				} else {
					//compare folder
					$diff->$key = new stdClass();
					if (!$this->_isExistedInVersion($vServer, $key)) {
						$this->_copyFolder($value, $diff->$key, 'new');
					} else {
						$this->_checkToBuildUpgradePackage($vServer->$key, $vUpgrade->$key, $diff->$key);
					}
				}
			}
		}
		
		if ($this->_isFolder($vServer)) {
			if (count($vServer) > 0) {
				foreach ($vServer as $key => $value) {
					if (in_array($key, $this->exceptions)) {
						continue;
					}
					if (!$this->_isFolder($value)) {
						if (!$this->_isExistedInVersion($diff, $key)) {
							$diff->$key = 'removed';
						}
					} else {
						if (!$this->_isExistedInVersion($diff, $key)) {
							$diff->$key = new stdClass();
							$this->_copyFolder($value, $diff->$key, 'removed');
						}
					}
				}
			}
		}
		return $diff;
	}


	function _copyFolder($from, &$to, $status)
	{
		foreach ($from as $key => $value) {
			if (!$this->_isFolder($value))
				$to->$key = $status;
			else
				$this->_copyFolder($value, $to->$key, $status);
		}
	}


	function _getFilesOnly($obj)
	{
		$aFiles = array();
		if ($this->_isFolder($obj)) {
			foreach ($obj as $key => $value) {
				if (!$this->_isFolder($value)) {
					$aFiles[$key] = $value;
				}
			}
		}
		return $aFiles;
	}


	function _isFolder($obj)
	{
		return (is_object($obj)) ? 1 : 0;
	}


	function _isExistedInVersion($version, $element)
	{
		return (isset($version->$element)) ? 1 : 0;
	}
}
?>