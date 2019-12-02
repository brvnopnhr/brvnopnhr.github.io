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
jimport('joomla.application.component.helper');
jimport('joomla.html.html');
class JHtmlFiles
{
        public static function files( $name, $active = NULL, $javascript = NULL, $directory, $extensions =  "pdf|doc|odt|txt|epub|xls" )
        {
                jimport( 'joomla.filesystem.folder' );
                $listFiles = JFolder::files( JPATH_SITE.DS.$directory );
                $files         = array(  JHTML::_('select.option',  '', '- '. JText::_( 'COM_ABOOK_SELECT_FILE' ) .' -' ) );
                foreach ( $listFiles as $file ) {
                   if ( preg_match( '#('.$extensions.')$#', $file ) ) {
                                $files[] = JHTML::_('select.option',  $file );
                        }
                }
                $files = JHTML::_('select.genericlist',  $files, $name,  array(
                                'list.attr' => 'class="inputbox" size="1" '. $javascript,
                                'list.select' => $active
                        ));

                return $files;
        }
}
