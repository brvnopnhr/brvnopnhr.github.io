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
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(pressbutton) {
                if (pressbutton == 'book.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
                	<?php echo $this->form->getField('description')->save(); ?>
                        Joomla.submitform(pressbutton, document.getElementById('item-form')); 
        	}
        }
</script>
<form action="<?php echo JRoute::_('index.php?option=com_abook&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="form-inline form-inline-header">
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
                     	<div class="controls"><?php echo $this->form->getInput('title'); ?></div>
                </div>
		<div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('subtitle'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('subtitle'); ?></div>
                </div>
	</div>
	<div class="form-horizontal">
                <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>
                <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_ABOOK_GENERAL_OPTIONS', true)); ?>
		<div class="row-fluid">
                        <div class="span9">
                                <fieldset class="adminform">
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('catid'); ?></div>
                                			<div class="controls"><?php echo $this->form->getInput('catid'); ?></div>
						</div>
                                		<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('ideditor'); ?></div>
                                			<div class="controls"><?php echo $this->form->getInput('ideditor'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('editedby'); ?></div>
                                                	<div class="controls"><?php echo $this->form->getInput('editedby'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('idauth'); ?></div>
                                                	<div class="controls"><?php echo $this->form->getInput('idauth'); ?></div>
						</div>
						<div class="control-group">
                                        		<div class="control-label"><?php echo $this->form->getLabel('idtag'); ?></div>
                                        		<div class="controls"><?php echo $this->form->getInput('idtag'); ?></div>
                                		</div>
                                                <div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('note'); ?></div>
                                                	<div class="controls"><?php echo $this->form->getInput('note'); ?></div>
						</div>
                                </fieldset>
				<?php echo $this->form->getInput('description'); ?>
                        </div>
                        <div class="span3">
					<?php if($this->form->getValue('image')){?>
					<div class="control-group">
						<img class="img-polaroid hidden-phone" src="<?php echo JUri::root() . $this->form->getValue('image');?>" />
					</div>
					<?php }?>
					<div class="control-group">
                                                <?php echo $this->form->getLabel('image'); ?>
                                                        <?php echo $this->form->getInput('image'); ?>
                                        </div>
					<div class="control-group form-horizontal-desktop">
						<?php echo $this->form->getLabel('year'); ?>
                                        		<?php echo $this->form->getInput('year'); ?>
					</div>
                                        <div class="control-group">
						<?php echo $this->form->getLabel('idlocation'); ?>
                                        		<?php echo $this->form->getInput('idlocation'); ?>
                                        </div>
                                        <div class="control-group">
						<?php echo $this->form->getLabel('idlibrary'); ?>
                                        		<?php echo $this->form->getInput('idlibrary'); ?>
                                        </div>
	                                <div class="control-group">
						<?php echo $this->form->getLabel('catalogo'); ?>
                                        		<?php echo $this->form->getInput('catalogo'); ?>
                                        </div>
					<div class="control-group">
                                                <?php echo $this->form->getLabel('qty'); ?>
                                                <?php echo $this->form->getInput('qty'); ?>
                                        </div>
                                        <div class="control-group">
						<?php echo $this->form->getLabel('price'); ?>
                                        		<?php echo $this->form->getInput('price'); ?>
                                        </div>
                                        <div class="control-group">
						<?php echo $this->form->getLabel('pag'); ?>
                                        		<?php echo $this->form->getInput('pag'); ?>
                                        </div>
                                        <div class="control-group">
						<?php echo $this->form->getLabel('pag_index'); ?>
                                        		<?php echo $this->form->getInput('pag_index'); ?>
                                        </div>
                                        <div class="control-group">
						<?php echo $this->form->getLabel('isbn'); ?>
                                        		<?php echo $this->form->getInput('isbn'); ?>
                                        </div>
                                        <div class="control-group">
						<?php echo $this->form->getLabel('file');?>
                                        		<?php echo JHtml::_('files.files', 'jform[file]', $this->item->file, '', 'images'.DS.$this->params->get('file_path')); ?>
                                        </div>
                                        <div class="control-group">
						<?php echo $this->form->getLabel('url_label'); ?>
                                        		<?php echo $this->form->getInput('url_label'); ?>
                                        </div>
                                        <div class="control-group">
						<?php echo $this->form->getLabel('url'); ?>
                                        		<?php echo $this->form->getInput('url'); ?>
                                        </div>
                        </div>
                </div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('COM_ABOOK_FIELDSET_PUBLISHING', true)); ?>
                	<div class="row-fluid form-horizontal-desktop">
                                <div class="span6">
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
							<div class="controls">
                                				<?php echo $this->form->getInput('id'); ?>
							</div>
						</div>
                        			<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('alias'); ?></div>
							<div class="controls">
			                        		<?php echo $this->form->getInput('alias'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('dateinsert'); ?></div>
							<div class="controls">
			                        		<?php echo $this->form->getInput('dateinsert'); ?>
							</div>
						</div>
			                        <div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
							<div class="controls">
                        					<?php echo $this->form->getInput('state'); ?>
							</div>
						</div>
			                        <div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('access'); ?></div>
							<div class="controls">	
                        					<?php echo $this->form->getInput('access'); ?>
							</div>
						</div>
			                        <div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('user_id'); ?></div>
							<div class="controls">
                        					<?php echo $this->form->getInput('user_id'); ?>
							</div>
						</div>
			                        <div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('created_by_alias'); ?></div>
							<div class="controls">
                        					<?php echo $this->form->getInput('created_by_alias'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('language'); ?></div>
							<div class="controls">
                                				<?php echo $this->form->getInput('language'); ?>
							 </div>
                                                </div>
				</div>
				<div class="span6">
					<?php echo JLayoutHelper::render('joomla.edit.metadata', $this); ?>
				</div>
			</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php echo JLayoutHelper::render('joomla.edit.params', $this); ?>
		<?php if ($this->canDo->get('core.admin')) : ?>
                        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'access-rules', JText::_('COM_ABOOK_FIELDSET_RULES', true)); ?>
				<div class="row-fluid form-horizontal-desktop">
					<div class="span12">
                                		<?php echo $this->form->getInput('rules'); ?>
					</div>
				</div>
                        <?php echo JHtml::_('bootstrap.endTab'); ?>
                <?php endif; ?>
                <?php echo JHtml::_('bootstrap.endTabSet'); ?>		
	</div>
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
</form>
<div class="clearfix"></div>
<p><?php echo JHTML::_('credit.credit');?></p>
