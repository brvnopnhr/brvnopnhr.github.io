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
<script type="text/javascript">
        Joomla.submitbutton = function(pressbutton) {
                if (pressbutton == 'lend.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
                        Joomla.submitform(pressbutton, document.getElementById('item-form')); 
                }
        }
</script>
<form action="<?php JRoute::_('index.php?option=com_abook'); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

                <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_ABOOK_GENERAL_OPTIONS', true)); ?>
		<fieldset class="adminform">
			<legend><?php echo empty($this->item->id) ? JText::_('COM_ABOOK_ADD_NEW_LEND') : JText::sprintf('COM_ABOOK_EDIT_LEND', $this->item->id); ?></legend>
			<div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('book_id'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('book_id'); ?></div>
                        </div>
			<hr />
			<div class="controls-row">
				<div class="control-group span5">
	                                <div class="control-label"><?php echo $this->form->getLabel('user_id'); ?></div>
        	                        <div class="controls"><?php echo $this->form->getInput('user_id'); ?></div>
				</div>
				<div class="span1 center">
					<div class="control-label"><?php echo JText::_('COM_ABOOK_OR');?></div>
				</div>
				<div class="span5">
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('custom_user_name'); ?></div>
        		        	        <div class="controls"><?php echo $this->form->getInput('custom_user_name'); ?></div>
					</div>
					<div class="control-group">
		                                <div class="control-label"><?php echo $this->form->getLabel('custom_user_email'); ?></div>
                	        	        <div class="controls"><?php echo $this->form->getInput('custom_user_email'); ?></div>
					</div>
				</div>
                        </div>
<hr />
			<div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('lend_out'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('lend_out'); ?></div>
                        </div>
			<div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('lend_in'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('lend_in'); ?></div>
                        </div>
			<div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('state'); ?></div>
                        </div>
                </fieldset>
		<?php echo $this->form->getInput('id'); ?>
		<?php echo $this->form->getInput('admin_id'); ?>
		<input type="hidden" name="task" value="" />
	        <input type="hidden" name="return" value="<?php echo JRequest::getCmd('return');?>" />
	        <?php echo JHtml::_('form.token'); ?>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
	</div>
</form>

<div class="clearfix clr"></div>

<!--

                <?php if (empty($this->items)) : ?>
                        <div class="alert alert-no-items">
                                <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                        </div>
                <?php else : ?>
                <table class="table table-striped" id="bookList">
                        <thead>
                                <tr>
                                        <th width="1%" style="min-width:55px" class="nowrap center">
                                                <?php echo JText::_('JSTATUS');?>
                                        </th>
                                        <th>
                                                <?php echo JText::_('COM_ABOOK_TITLE');?>
                                        </th>
                                        <th class="hidden-phone">
                                                <?php echo JText::_('COM_ABOOK_LIBRARY');?>
                                        </th>
                                        <th width="1%" class="hidden-phone">
                                                <?php echo JText::_('COM_ABOOK_CATALOG');?>
                                        </th>
                                        <th width="10%" class="nowrap hidden-phone">
                                                <?php echo JText::_('JGLOBAL_USERNAME');?>
                                        </th>
                                        <th width="10%" class="nowrap hidden-phone">
                                                <?php echo JText::_('COM_ABOOK_LEND_OUT');?>
                                        </th>
                                        <th width="10%" class="nowrap hidden-phone">
                                                <?php echo JText::_('COM_ABOOK_LEND_IN');?>
                                        </th>
                                        <th width="1%" class="nowrap hidden-phone">
                                                <?php echo JText::_('JGRID_HEADING_ID');?>
                                        </th>
                                </tr>
                        </thead>
		                        <tbody>
                        <?php
				$user   = JFactory::getUser();
				$userId         = $user->get('id');
                                foreach ($this->items as $i => $item) :
                                $canCreate  = $user->authorise('core.create', 'com_abook.category.'.$item->catid);
                                $canEdit    = $user->authorise('core.edit', 'com_abook.:.'.$item->id);
                                $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out==$userId || $item->checked_out==0;
                                $canEditOwn = $user->authorise('core.edit.own', 'com_abook.lend.'.$item->id) && $item->user_id == $userId;
                                $canChange  = $user->authorise('core.edit.state', 'com_abook.lend.'.$item->id) && $canCheckin;
                        ?>
                                <?php switch($item->state){
                                        case 0:
                                                $state="label-important";
                                                $late=date('Y-m-d') > $item->lend_in?"<i class='icon-info'></i>":'';
                                                $state_label=JText::_('COM_ABOOK_LEND');
                                                break;
                                        case 1:
                                                $state="label-success";
                                                $state_label=JText::_('COM_ABOOK_RETURN');
                                                break;
                                        case 2:
                                                $state="label-warning";
                                                $state_label=JText::_('COM_ABOOK_REQUEST');
                                                break;
                                }
                                ?>
                                <tr class="<?php echo $css_class;?>" sortable-group-id="<?php echo $item->catid; ?>">
                                        <td>
                                                <span class="label <?php echo $state;?>"><?php echo $state_label;?></span>
                                        </td>
                                        <td class="has-context nowrap">
                                                <div class="pull-left">
                                                <?php if ($item->checked_out) : ?>
                                                        <?php echo JHtml::_('jgrid.checkedout', $i, $item->owner, $item->checked_out_time, 'lends.', $canCheckin); ?>
                                                <?php endif; ?>
                                                <?php if ($canEdit || $canEditOwn) : ?>
                                                        <a href="<?php echo JRoute::_('index.php?option=com_abook&view=lend&task=lend.edit&id='.$item->id);?>" title="<?php echo JText::_('JACTION_EDIT'); ?>"><?php echo $this->escape($item->title); ?></a>
                                                <?php else: ?>
                                                        <span><?php echo $this->escape($item->title); ?></span>
                                                <?php endif; ?>
                                                <p class="small"><?php echo JText::_('COM_ABOOK_ISBN');?>: <?php echo $item->isbn; ?></p>
                                                </div>
                                        </td>
                                        <td class="small hidden-phone">
                                                <?php echo $item->library; ?>
                                        </td>
                                        <td class="small hidden-phone">
                                                <?php echo $item->catalogo; ?>
                                        </td>
                                        <td class="small hidden-phone">
                                                <?php echo $item->user_name; ?>
					                                        </td>
                                        <td class="nowrap small hidden-phone">
                                                <?php echo JHtml::_('date', $item->lend_out, JText::_('DATE_FORMAT_LC4')); ?>
                                        </td>
                                        <td class="nowrap small hidden-phone">
                                                <?php echo JHtml::_('date', $item->lend_in, JText::_('DATE_FORMAT_LC4')); ?> <?echo $late;?>
                                        </td>
                                        <td class="center hidden-phone">
                                                <?php echo $item->id; ?>
                                        </td>
                                </tr>
                                <?php endforeach;?>
                        </tbody>
                </table>
                <?php endif; ?>
-->
<p><?php echo JHTML::_('credit.credit');?></p>
