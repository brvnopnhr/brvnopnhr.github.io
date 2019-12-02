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
JHtml::_('formbehavior.chosen', 'select');
$script="
window.addEvent('domready', function(){
        jQuery('#category').hide();
        jQuery('#jform_category').removeClass('required');
        jQuery('#jform_category').attr('aria-required', 'false');
        jQuery('#jform_category').removeAttr('required');

        jQuery('#jform_type').change(function(){
                if (jQuery('#jform_type').val()=='2') {
                        jQuery('#category').show();
                        jQuery('#jform_category').addClass('required');
                        jQuery('#jform_category').attr('aria-required', 'true');
                        jQuery('#jform_category').attr('required', '');
                }else{
                        jQuery('#category').hide();
                        jQuery('#jform_category').removeClass('required');
                        jQuery('#jform_category').attr('aria-required', false);
                        jQuery('#jform_category').removeAttr('required');
                }
        });
        jQuery('#category2').hide();
        jQuery('#jform_category2').removeClass('required');
        jQuery('#jform_category2').attr('aria-required', 'false');
        jQuery('#jform_category2').removeAttr('required');
        jQuery('#db').show();

        jQuery('#jform_type2').change(function(){
                if (jQuery('#jform_type2').val()=='2' || jQuery('#jform_type2').val()=='3') {
                        jQuery('#category2').show();
                        jQuery('#jform_category2').addClass('required');
                        jQuery('#jform_category2').attr('aria-required', 'true');
                        jQuery('#jform_category2').attr('required', '');
                        jQuery('#db').hide();
                }else{
                        jQuery('#category2').hide();
                        jQuery('#jform_category2').removeClass('required');
                        jQuery('#jform_category2').attr('aria-required', false);
                        jQuery('#jform_category2').removeAttr('required');
                        jQuery('#db').show();
                }
        });
});";
JFactory::getDocument()->addScriptDeclaration($script);
?>
<script language="javascript" type="text/javascript">
        /*Joomla.expcat=function()
        {       
                var type = document.getElementById('jform_type_chzn').value;
                if(type == 2){
                	document.getElementById('jform_category_chzn').disabled=false;
                }else{
                	document.getElementById('jform_category_chzn').disabled=true;
		}
        }
        function impcat()
        {       
                var type = document.getElementById('jform_type2_chzn').value;
                if(type == 2 || type == 3){
                	document.getElementById('jform_category2_chzn').disabled=false;
                }else{
                	document.getElementById('jform_category2chzn').disabled=true;
		}
        }*/
</script>

<form action="<?php echo JRoute::_('index.php?option=com_abook&view=importexport'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">
	<?php if (!empty( $this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
                <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
        <?php else : ?>
        <div id="j-main-container">
        <?php endif;?>
		<div class="row-fluid">
			<fieldset class="form-horizontal">
                	<legend><?php echo JText::_( 'COM_ABOOK_EXPORT_BOOK' ); ?></legend>
			<div class="row-fluid">
                        	<div class="span6">
					<div class="control-group">
                                        	<div class="control-label"><?php echo $this->form->getLabel('type'); ?></div>
		                        	<div class="controls"><?php echo $this->form->getInput('type'); ?></div>
					</div>
					<div id="category" class="control-group">
                        			<div class="control-label"><?php echo $this->form->getLabel('category'); ?></div>
                	        		<div class="controls"><?php echo $this->form->getInput('category'); ?></div>
					</div>
					<button class="btn btn-primary" type="submit" onclick="document.id('task').value='importexport.export';this.form.submit()" /><i class="icon-download icon-white"></i> <?php echo JText::_('COM_ABOOK_EXPORT'); ?></button>
				</div>
                                <div class="span6">
                                	<div class="alert alert-info"><?php echo JText::_('COM_ABOOK_EXPORT_TYPE_DESC');?></div>
                                </div>
			</div>
			</fieldset>
		</div>
		<div class="row-fluid">
			<fieldset class="form-horizontal">
				<legend><?php echo JText::_( 'COM_ABOOK_IMPORT_BOOK' ); ?></legend>
				<div class="row-fluid">
					<div class="span6">
						<div class="control-group">
                        				<div class="control-label"><?php echo $this->form->getLabel('type2'); ?></div>
                                			<div class="controls"><?php echo $this->form->getInput('type2'); ?></div>
						</div>
						<div id="db" class="control-group">
                                                        <div class="control-label"><?php echo $this->form->getLabel('db'); ?></div>
                                                        <div class="controls"><?php echo $this->form->getInput('db'); ?></div>
                                                </div>
						<div id="category2" class="control-group">
                                			<div class="control-label"><?php echo $this->form->getLabel('category2'); ?></div>
                                			<div class="controls"><?php echo $this->form->getInput('category2'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('user'); ?></div>
			                                <div class="controls"><?php echo $this->form->getInput('user'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('import_file'); ?></div>
                        			        <div class="controls"><?php echo $this->form->getInput('import_file'); ?></div>
			                        </div>
						<button class="btn btn-primary" type="submit" onclick="document.id('task').value='importexport.import';this.form.submit()" /><i class="icon-upload icon-white"></i> <?php echo JText::_('COM_ABOOK_IMPORT'); ?></button>
					</div>
					<div class="span6">
						<div class="alert alert-info"><?php echo JText::_('COM_ABOOK_IMPORT_TYPE_DESC');?></div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_abook" />
	<input type="hidden" name="task" id="task" value="" />
	<input type="hidden" name="controller" value="importexport" />
</form>
<div class="clearfix"></div>
<p><?php echo JHTML::_('credit.credit');?></p>
