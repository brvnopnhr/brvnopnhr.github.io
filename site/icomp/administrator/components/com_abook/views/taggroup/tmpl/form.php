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
defined('_JEXEC') or die('Restricted access'); ?>
<script language="javascript" type="text/javascript">
        function submitbutton(pressbutton) {
                var form = document.adminForm;
                if (pressbutton == 'cancel') {
                        submitform( pressbutton );
                        return;
                }

                // do field validation
                if (form.location.value == ""){
                        alert( "<?php echo JText::_( 'TAGGROUP MUST HAVE A NAME', true ); ?>" );
                } else {
                        submitform( pressbutton );
                }
        }
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="col100">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'DETAILS' ); ?></legend>

		<table class="admintable">
		<tr>
			<td width="100" align="right" class="key">
				<label for="taggroup">
					<?php echo JText::_( 'TAGGROUP' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="taggroup" id="taggroup" size="32" maxlength="250" value="<?php echo $this->taggroup->taggroup;?>" />
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="key">
                                <label for="alias">
                                        <?php echo JText::_( 'ALIAS' ); ?>:
                                </label>
                        </td>
			<td>
				<input class="text_area" type="text" name="alias" id="alias" size="32" maxlength="250" value="<?php echo $this->taggroup->alias;?>" />
			</td>
		</tr>
	</table>
	</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="option" value="com_abook" />
<input type="hidden" name="cid" value="<?php echo $this->taggroup->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="taggroup" />
</form>
