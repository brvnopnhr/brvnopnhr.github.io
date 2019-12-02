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
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
$user   = JFactory::getUser();
$listOrder      = $this->escape($this->state->get('list.ordering'));
$listDirn       = $this->escape($this->state->get('list.direction'));
$canOrder       = $user->authorise('core.edit.state', 'com_abook.tag');
$saveOrder      = $listOrder == 'a.ordering';

if ($saveOrder)
{
        $saveOrderingUrl = 'index.php?option=com_abook&task=locations.saveOrderAjax&tmpl=component';
        JHtml::_('sortablelist.sortable', 'locationList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();
$assoc          = JLanguageAssociations::isEnabled();
?>
<script type="text/javascript">
        Joomla.orderTable = function()
        {
                table = document.getElementById("sortTable");
                direction = document.getElementById("directionTable");
                order = table.options[table.selectedIndex].value;
                if (order != '<?php echo $listOrder; ?>')
                {
                        dirn = 'asc';
                }
                else
                {
                        dirn = direction.options[direction.selectedIndex].value;
                }
                Joomla.tableOrdering(order, dirn, '');
        }
</script>
<form action="<?php echo JRoute::_('index.php?option=com_abook&view=tags'); ?>" method="post" id="adminForm" name="adminForm">
	<?php if (!empty( $this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
                <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
        <?php else : ?>
        <div id="j-main-container">
        <?php endif;?>
        <?php
                // Search tools bar
        echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
        ?>
        <?php if (empty($this->items)) : ?>
                <div class="alert alert-no-items">
                        <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                </div>
        <?php else : ?>
	<table class="table table-striped" id="locationList">
	<thead>
		<tr>
			<th width="1%" class="hidden-phone">
                                <?php echo JHtml::_('grid.checkall'); ?>
                        </th>			
			<th class="title">
                                <?php echo JHTML::_('grid.sort',  'COM_ABOOK_NAME', 'a.name', $listDirn, $listOrder );?>
			</th>
			<th width="10%">
			<?php echo JHTML::_('grid.sort',  'COM_ABOOK_TAGGROUPS', 'a.id_taggroup', $listDirn, $listOrder );?>
			</th>
			<th width="10%" class="nowrap hidden-phone">
                        	<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
                        </th>
			<th width="1%" class="nowrap hidden-phone">
                                <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                        </th>
		</tr>
	</thead>
	<tbody>
	<?php
	foreach ($this->items as $i => $item) :	
		$ordering       = ($listOrder == 'a.ordering');
		$canCreate      = $user->authorise('core.create',               'com_abook.tag.'.$item->id);
                $canEdit        = $user->authorise('core.edit',                 'com_abook.tag.'.$item->id);
                $canCheckin     = $user->authorise('core.manage',               'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
                $canChange      = $user->authorise('core.edit.state',   'com_abook.tag.'.$item->id) && $canCheckin;
		$link 		= JRoute::_( 'index.php?option=com_abook&task=tag.edit&id='. $item->id );
		?>
		<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->id_taggroup; ?>">
			<td class="center hidden-phone">
                                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                        </td>	
			<td>
                                        <?php if ($item->checked_out) : ?>
                                                <?php echo JHtml::_('jgrid.checkedout', $i, $item->owner, $item->checked_out_time, 'tags.', $canCheckin); ?>
                                        <?php endif; ?>
                                        <?php if ($canCreate || $canEdit) : ?>
                                        <a href="<?php echo $link;?>">
                                                <?php echo $this->escape($item->name); ?></a>
                                        <?php else : ?>
                                                <?php echo $this->escape($item->name); ?>
                                        <?php endif; ?>
					<p class="smallsub">
                                                <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?></p>
                        </td>
			<td class="center">
                                <?php echo $item->taggroup_name; ?>
                        </td>
			<td class="center nowrap hidden-phone">
                        	<?php if ($item->language=='*'):?>
                                	<?php echo JText::_('JALL'); ?>
                                <?php else:?>
                                        <?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
                                <?php endif;?>
                        </td>
			<td class="center hidden-phone">
                                <?php echo (int) $item->id; ?>
                        </td>
		</tr>
	<?php endforeach; ?>
	</tbody>
	</table>
	<?php endif; ?>
        <?php echo $this->pagination->getListFooter(); ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<div class="clearfix"></div>
<p><?php echo JHTML::_('credit.credit');?></p>
