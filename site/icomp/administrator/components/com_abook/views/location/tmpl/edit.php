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
defined('_JEXEC') or die('Restricted access');
JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
?>
<script language="javascript" type="text/javascript">
                Joomla.submitbutton = function(task)
        {
                if (task == 'editor.cancel' || document.formvalidator.isValid(document.id('item-form')))
                {
                        Joomla.submitform(task, document.getElementById('item-form'));
                }
        }
</script>
<form action="<?php JRoute::_('index.php?option=com_abook&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="form-horizontal">
                <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>
                <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', empty($this->item->id) ? JText::_('COM_ABOOK_ADD_NEW_LOCATION') : JText::sprintf('COM_ABOOK_EDIT_LOCATION', true)); ?>
                <div class="row-fluid">
                        <div class="span9">
				<fieldset class="adminform">
				<div class="control-group">
                                	<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
			                <div class="controls"><?php echo $this->form->getInput('name'); ?></div>
				</div>
				<div class="control-group">
                                        <div class="control-label"><?php echo $this->form->getLabel('alias'); ?></div>
                        		<div class="controls"><?php echo $this->form->getInput('alias'); ?></div>
				</div>
				</fieldset>
			</div>
			<div class="span3">
				<div class="control-group">
					<?php echo $this->form->getLabel('id'); ?>
					<?php echo $this->form->getInput('id'); ?>
				</div>
				<div class="control-group">
					<?php echo $this->form->getLabel('language'); ?>
		                        <?php echo $this->form->getInput('language'); ?>
				</div>
                        </div>
        	</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
                <?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
</form>
<div class="clearfix"></div>
<p><?php echo JHTML::_('credit.credit');?></p>
