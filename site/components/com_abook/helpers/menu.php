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

class JHTMLMenu
{
	function treerecurse( $id, $indent, $img, $list, &$children, $maxlevel=9999, $level=0, $type=1 )
        {
                if (@$children[$id] && $level <= $maxlevel)
                {
                        foreach ($children[$id] as $v)
                        {
                                $id = $v->id;

                                if ( $type ) {
                                        $pre    = '<sup>|_</sup>&nbsp;';
                                        $spacer = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                                } else {
                                        $pre    = '- ';
                                        $spacer = '&nbsp;&nbsp;';
                                }

                                if ( $v->parent_id == 0 ) {
                                        $txt    = $img . $v->name;
                                } else {
                                        $txt    = $pre . $img . $v->name;
                                }
                                $pt = $v->parent_id;
                                $list[$id] = $v;
                                $list[$id]->treename = "$indent$txt";
                                $list[$id]->children = count( @$children[$id] );
				$list = JHTMLMenu::treerecurse($id, $indent . $spacer, $img, $list, $children, $maxlevel, $level+1, $type);
                        }
                }
                return $list;
        }
}
