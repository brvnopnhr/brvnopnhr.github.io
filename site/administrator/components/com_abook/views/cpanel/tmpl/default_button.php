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
defined('_JEXEC') or die;
$user=JFactory::getUser();
?>
<ul class="j-links-group nav nav-list">
<?php foreach ($this->buttons as $button){ 
//	if ($user->authorise($button['access'][0], $button['access'][1])){
?>
	<li>
               	<a href="<?php echo $button['link']; ?>">
        	        <i class="<?php echo $button['image'];?>"></i>
                       	<span class="j-links-link"><?php echo $button['text']; ?></span>
		</a>
        </li>
<?php 
//	} 
}?>
</ul>
