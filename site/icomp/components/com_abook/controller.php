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

class AbookController extends JControllerLegacy {

	function display($cachable = false, $urlparams = false)
	{
		$safeurlparams = array('catid' => 'INT', 'id' => 'INT', 'cid' => 'ARRAY', 'year' => 'INT', 'month' => 'INT', 'limit' => 'UINT', 'limitstart' => 'UINT',
                        'showall' => 'INT', 'return' => 'BASE64', 'filter' => 'STRING', 'filter_order' => 'CMD', 'filter_order_Dir' => 'CMD', 'filter-search' => 'STRING', 'print' => 'BOOLEAN', 'lang' => 'CMD', 'Itemid' => 'INT', 'letter' => 'STRING', 'filter-category_id' => 'INT', 'filter-tag_id' => 'INT', 'filter-library_id' => 'INT', 'filter-location_id' => 'INT', 'filter-year' => 'INT', 'filter-author_id' => 'INT', 'filter-editor_id' => 'INT');
		$cachable = true;
		$vName = $this->input->getCmd('view', 'categories');
		if ($vName == 'search'){
			$cachable = false;
		}
		parent::display($cachable, $safeurlparams);
		return $this;
	}

}
