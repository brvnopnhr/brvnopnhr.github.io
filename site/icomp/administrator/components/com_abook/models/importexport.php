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

jimport('joomla.application.component.modeladmin');
jimport( 'joomla.filesystem.file' );

class AbookModelImportexport extends JModelAdmin
{
	var $_categories = null;
	function __construct()
	{
		parent::__construct();

	}

	public function getTable($type = 'Category', $prefix = 'AbookTable', $config = array())
        {
                return JTable::getInstance($type, $prefix, $config);
        }

	public function getForm($data = array(), $loadData = true)
        {
                $form = $this->loadForm('com_abook.importexport', 'importexport', array('control' => 'jform', 'load_data' => $loadData));
                if (empty($form)) {
                        return false;
                }

                return $form;
        }

	function setExportType($export){
		$session = JFactory::getSession();
                $session->set('export', $export, 'Abook');
	}

	function getExportType(){
                $session = JFactory::getSession();
                $export=$session->get('export', array(), 'Abook');
		switch ($export['type']){
                        case 1:
				$ext=".sql";
			break;
			case 2:
				$ext=".csv";
			break;
		}
		return $ext;
        }

	function getExport()
	{
		$session = JFactory::getSession();
                $export=$session->get('export', array(), 'Abook');
//error_log(print_r($export, true), 3, '/tmp/debug.log');
		switch ($export['type']){
			case 1:
				$tables= array('#__abauthor', '#__abbook', '#__abbookauth', '#__abcategories', '#__abeditor', '#__ablibrary', '#__ablocations', '#__abrating', '#__abbooktag', '#__abtag', '#__abtag_groups', '#__ablend');
				foreach ($tables as $table){
					$file=$this->datadumpSql($table);
				}
			break;
			case 2:
				$file=$this->datadumpCsv($export['category']);
			break;
		}

		return $file;
	}

	function datadumpCsv ($category) {
                $result = '';
                $resrt = '';
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('title, subtitle, alias, ideditor, price, pag, user_id, created_by_alias, description, image, file, year, idlocation, idlibrary, vote, numvote, hits, state, qty, isbn, approved, userid, url, url_label, dateinsert, catalogo, checked_out, checked_out_time, access, metakey, metadesc, note, language, editedby, GROUP_CONCAT(DISTINCT ba.idauth SEPARATOR ",") AS autori, GROUP_CONCAT(DISTINCT bt.idtag SEPARATOR ",") AS tags');
		$query->from('#__abbook AS b');
		$query->leftjoin('#__abbookauth AS ba ON ba.idbook=b.id');
		$query->leftjoin('#__abbooktag AS bt ON bt.idbook=b.id');
		if($category==0){
		}else{
			$query->where('catid='.$category);
		}
		$query->group('b.id');
                $db->setQuery($query);
                $rows = $db->loadAssocList();
                foreach($rows as $row){
                        $resrt = '"';
                        while (list($key, $value) = each($row)) {
                                $resrt .= $db->escape($value).'";"';
                        }
                        $resrt = substr($resrt,0,-2);
                        $resrt .= "\r\n";
                        $result .= $resrt;
                }
                echo $result;
                return;
        }

	function datadumpSql ($table) {
	        $result = '';
        	$resrt = '';
	        $reslt = "";
		$db = $this->getDbo();
                $query = $db->getQuery(true);
	        $query->select('*');
		$query->from($table);
		if ($table=="#__abcategories"){
			$query->where('title != "ROOT"');
		}
		$db->setQuery($query);
		$rows = $db->loadAssocList();
	        foreach($rows as $row){
	                $reslt .= "INSERT INTO ".$table." (";
        	        $resrt .= ") VALUES ('";
                	while (list($key, $value) = each($row)) {
	                        $reslt .= $key.",";
        	                $resrt .= $this->_db->escape($value)."','";
                	}
                	$reslt = substr($reslt,0,-1);
                	$resrt = substr($resrt,0,-2);
	                $resrt .= ");\n";
        	        $result .= ($reslt.$resrt);
                	$reslt ='';
	                $resrt ='';
        	}
        	echo $result . "\n\n\n";
		return;
        }
	
	function importSql($filename, $user, $database=0) {
		$db = JFactory::getDbo();
		$buffer = '';
		if($database){ 
			$buffer = Jfile::read(JPATH_COMPONENT_ADMINISTRATOR.DS.'uninstall.sql');
			$buffer .= Jfile::read(JPATH_COMPONENT_ADMINISTRATOR.DS.'install.sql');
		}
		$buffer .= Jfile::read(JPATH_COMPONENT_ADMINISTRATOR.DS."uploads".DS.$filename);
                $queries = $db->splitSql($buffer);
		if (count($queries) == 0) {
                	// No queries to process
                        return 0;
                }
		$query = $db->getQuery(true);
		foreach ($queries as $qry){
			$qry = trim($qry);
                        if ($qry != '' && $qry{0} != '#'){
				//from abook 2.0.4
				$qry = preg_replace('/,hits,published,catid,/', ',hits,state,catid,', $qry);
				$qry = preg_replace('/INSERT INTO #__abauthor \(id,name,/', 'INSERT INTO #__abauthor (id,lastname,', $qry);
				//from abook 1.1.2 cancellare se non riesci a finire
				//$qry = preg_replace('/,hits,published,ordering,/', ',hits,state,ordering,', $qry);
				//$qry = preg_replace('/,created_by_alias,html,image,/', ',created_by_alias,description,image,', $qry);
				//$qry = preg_replace('/,image,html,checked_out,/', ',image,description,checked_out,', $qry);
				//$qry = preg_replace('/aid,idbook,idauth/', 'id,idbook,idauth', $qry);

				$query->clear();
                        	$db->setQuery($qry);	
                                if (!$db->execute()) {
                                	JError::raiseWarning(1, 'JDatabase::query: '.JText::_('SQL Error')." ".$db->stderr(true));
                                	return false;
                                }
			}
		}
				//update book owner
                if ($user!=''){
			$query = $db->getQuery(true);
			$query->update('#__abbook');
                        $query->set('user_id='.$user);
                        $db->setQuery($query);
                        $db->execute();
			//update category owner
			$query = $db->getQuery(true);
                        $query->update("#__abcategories");
                        $query->set("created_user_id=".$user.", modified_user_id=".$user);
			$query->where("title!='ROOT'");
                        $db->setQuery($query);
                        $db->execute();
                }

		Jfile::delete(JPATH_COMPONENT_ADMINISTRATOR.DS."uploads".DS.$filename);
		//delete category asset_id
		$query = $db->getQuery(true);
                $query->update('#__abcategories');
		$query->set('asset_id=0');
                $db->setQuery($query);
		$db->execute();
		//delete book asset_id
		$query = $db->getQuery(true);
		$query->update('#__abbook');
		$query->set('asset_id=0');
                $db->setQuery($query);
		$db->execute();		
		//update category asset_id
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__abcategories');
		$query->where('title != "ROOT"');
		$db->setQuery($query);
		$categories = $db->loadAssocList();
		//error_log(print_r($data, true), 3, '/var/log/php.log');	
		foreach ($categories as $cat){
			// Getting the asset table
                        $category = JTable::getInstance('Category', 'AbookTable', array());

                        // Bind data to save content
                        if (!$category->bind($cat)) {
                                echo JError::raiseError(500, $category->getError());
                        }

                        // Check the content
                        if (!$category->check()) {
                                echo JError::raiseError(500, $category->getError());
                        }

                        // Insert the content
                        if (!$category->store()) {
                                echo JError::raiseError(500, $category->getError());
                        }
		}
                //update book asset_id
		$query = $db->getQuery(true);
                $query->select('*');
		$query->from('#__abbook');
                $db->setQuery($query);
                $books = $db->loadAssocList();
                //error_log(print_r($data, true), 3, '/var/log/php.log');       
                foreach ($books as $item){
                        // Getting the asset table
                        $book = JTable::getInstance('Book', 'AbookTable', array());

                        // Bind data to save content
                        if (!$book->bind($item)) {
                                echo JError::raiseError(500, $book->getError());
                        }

                        // Check the content
                        if (!$book->check()) {
                                echo JError::raiseError(500, $book->getError());
                        }

                        // Insert the content
                        if (!$book->store()) {
                                echo JError::raiseError(500, $book->getError());
                        }
                }
		//rebuild categories
                $table = $this->getTable();
                $model = JModelLegacy::getInstance('Category', 'AbookModel', array('ignore_request' => true));
                $model->setState('params', $this->getState('params'));
                $model->setState('category.component', 'com_abook');
                $model->rebuild();
		return count($queries);
	}

	function importCsv($csvfile, $category, $user, $version='') {
		$buffer = file($csvfile);
		$insertrows=0;
		for($i=0; $i<sizeof ($buffer); $i++) {
			$id=null;
			$authorsLine=array();
			$line = trim($buffer[$i]);
			//list($title, $subtitle, $alias, $gid, $gecos, $home, $shell) = explode(":", $data);
			if ($version==''){
				//cerca l'ultimo ";" della stringa e taglia la stringa fino all'ultimo punto e virgola
				$pos=strripos($line, ';');
				//il "+ 1" serve per togliere il ";" all'inizio, poi tolgo gli apici e lo faccio diventare un array
				$tagsLine = explode(',', preg_replace('/"/', '', substr($line, $pos + 1)));
				$line=substr($line, 0, $pos);
			}
			//cerca di nuovo l'ultimo ";" della stringa e sega la stringa fino all'ultimo punt e virgola
			$pos=strripos($line, ';');
			$bookLine = preg_replace('/";"/', '","', substr($line, 0, $pos));
//error_log($bookLine);
			//il "+ 1" serve per togliere il ";" all'inizio, poi tolgo gli apici e lo faccio diventare un array
			if(preg_replace('/"/', '', substr($line, $pos + 1))!=''){
				$authorsLine = explode(',', preg_replace('/"/', '', substr($line, $pos + 1)));
			}
//error_log(print_r($authorsLine, true), 3, '/tmp/debug.log');
			$lang=$version==''?'language, ':'';
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->insert("#__abbook ");
			$query->columns("title, subtitle, alias, ideditor, price, pag, user_id, created_by_alias, description, image, file, year, idlocation, idlibrary, vote, numvote, hits, state, qty, isbn, approved, userid, url, url_label, dateinsert, catalogo, checked_out, checked_out_time, access, metakey, metadesc, note, ".$lang."editedby, catid");
			$query->values($bookLine.",".$category);
			$db->setQuery( $query );
			if (!$db->execute()) {
 	                       JError::raiseWarning(1, 'JDatabase::query: '.JText::_('SQL Error')." ".$db->stderr(true));
			}else{
				$id=$db->insertid();
				$insertrows=($id > 0) ? $insertrows + 1 : $insertrows;
				if (count($authorsLine)>0 && isset($id)){
					foreach($authorsLine as $authorID){
						if($authorID != ''){
							$query = $db->getQuery(true);
							$query->insert("#__abbookauth");
							$query->columns("idbook, idauth");
							$query->values($id.",".$authorID);
							$db->setQuery( $query );
							$db->execute();
						}
					}
				}
				if (count($tagsLine) > 0 && isset($id)){
                                        foreach($tagsLine as $tagID){
						if($tagID != ''){
							$query = $db->getQuery(true);
                                                	$query->insert("#__abbooktag");
                                                        $query->columns('idbook, idtag');
	                                                $query->values($id.",".$tagID);
        	                                        $db->setQuery( $query );
                	                                $db->execute();
						}
                                        }
                                }
				//update number of copies
                                $query = $db->getQuery(true);
                                $query->update("#__abbook");
                                $query->set("qty=1");
                                $query->where("qty=0 AND id=".$id);
                                $db->setQuery($query);
                                $db->execute();
				//update book owner
				if ($user!=''){
					$query = $db->getQuery(true);
					$query->update("#__abbook");
                                        $query->set("user_id=".$user);
                                	$query->where("id=".$id);
                                	$db->setQuery($query);
                                	$db->execute();
				}
		                //update book asset_id
				$query = $db->getQuery(true);
                		$query->select('*');
				$query->from('#__abbook');
				$query->where('id='.$id);
		                $db->setQuery($query);
		                $item = $db->loadAssoc();
		                //error_log(print_r($data, true), 3, '/var/log/php.log');       
		                // Getting the asset table
	                        $book = JTable::getInstance('Book', 'AbookTable', array());

	                        // Bind data to save content
	                        if (!$book->bind($item)) {
                   	             echo JError::raiseError(500, $book->getError());
          	        	}

                        	// Check the content
	                        if (!$book->check()) {
	                                echo JError::raiseError(500, $book->getError());
	                        }

	                        // Insert the content
	                        if (!$book->store()) {
	                                echo JError::raiseError(500, $book->getError());
	                        }
				
				//sistema i permessi, joomla15 access 0,1,2 joomla16 access 1,2,3
                                if ($version=='j15'){
                                        $query = $db->getQuery(true);
                                        $query->update("#__abbook");
                                        $query->set("access=(access + 1), language='*'");
                                        $query->where("id=".$id);
                                        $db->setQuery($query);
                                        $db->execute();
                                }
			}
		}
		/*$query	= "LOAD DATA LOCAL INFILE '" . $csvfile . "' INTO TABLE #__abbook"
			. " FIELDS TERMINATED BY ';' ENCLOSED BY '\"' LINES TERMINATED BY "."'\\n'"
			. " (title, subtitle, alias, ideditor, price, pag, user_id, created_by_alias, description, image, file, "
			. " year, idlocation, idlibrary, vote, numvote, hits, published, qty, isbn, approved, userid, url, "
			. " url_label, dateinsert, catalogo, checked_out, checked_out_time, access, metakey, metadesc)"
			. " SET catid = ".$category;
		$this->_db->setQuery( $query );	
		$this->_db->query();
		$rows=$this->_db->getAffectedRows();*/
		Jfile::delete($csvfile);
		return $insertrows;
	}
}
