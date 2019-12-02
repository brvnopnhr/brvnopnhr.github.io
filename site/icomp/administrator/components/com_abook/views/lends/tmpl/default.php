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

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user   = JFactory::getUser();
$userId         = $user->get('id');
$listOrder      = $this->state->get('list.ordering');
$listDirn       = $this->state->get('list.direction');
$archived       = $this->state->get('filter.published') == 2 ? true : false;
$trashed        = $this->state->get('filter.published') == -2 ? true : false;
$saveOrder      = $listOrder == 'a.ordering';
$canOrder       = $user->authorise('core.edit.state', 'com_abook.lend');
$lend=0;

if ($saveOrder)
{
        $saveOrderingUrl = 'index.php?option=com_abook&task=lends.saveOrderAjax&tmpl=component';
        JHtml::_('sortablelist.sortable', 'bookList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
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
<form action="<?php echo JRoute::_('index.php?option=com_abook&view=lends'); ?>" method="post" name="adminForm" id="adminForm">
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
		<table class="table table-striped" id="bookList">	
			<thead>
				<tr>
					<th width="1%" class="hidden-phone">
		                        	<?php echo JHtml::_('grid.checkall'); ?>
                	        	</th>
					<th width="1%" style="min-width:55px" class="nowrap center">
                                                <?php echo JHTML::_('searchtools.sort',  'JSTATUS', 'a.state', $listDirn, $listOrder );?>
                                        </th>	
					<th>
						<?php echo JHTML::_('searchtools.sort',  'COM_ABOOK_TITLE', 'b.title', $listDirn, $listOrder );?>
					</th>
					<th class="hidden-phone">
                                		<?php echo JHTML::_('searchtools.sort',  'COM_ABOOK_LIBRARY', 'b.idlibrary', $listDirn, $listOrder );?>
		                        </th>
					<th width="1%" class="hidden-phone">
						<?php echo JHTML::_('searchtools.sort',  'COM_ABOOK_CATALOG', 'b.catalogo', $listDirn, $listOrder );?>
		                        </th>
					<th width="10%" class="nowrap hidden-phone">
                                                <?php echo JHTML::_('searchtools.sort',  'JGLOBAL_USERNAME', 'u.name', $listDirn, $listOrder );?>
                                        </th>
					<th width="10%" class="nowrap hidden-phone">
                                                <?php echo JHTML::_('searchtools.sort',  'COM_ABOOK_LEND_OUT', 'a.lend_out', $listDirn, $listOrder );?>
                                        </th>
					<th width="10%" class="nowrap hidden-phone">
                                                <?php echo JHTML::_('searchtools.sort',  'COM_ABOOK_LEND_IN', 'a.lend_in', $listDirn, $listOrder );?>
                                        </th>
					<th width="1%" class="nowrap hidden-phone">
                                                <?php echo JHTML::_('searchtools.sort',  'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder );?>
                                        </th>
				</tr>
			</thead>
			<tbody>
			<?php
				foreach ($this->items as $i => $item) :
				$ordering   = ($listOrder == 'a.ordering');
			        $canCreate  = $user->authorise('core.create', 'com_abook.category.'.$item->catid);
			        $canEdit    = $user->authorise('core.edit', 'com_abook.:.'.$item->id);
			        $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out==$userId || $item->checked_out==0;
				$canEditOwn = $user->authorise('core.edit.own', 'com_abook.lend.'.$item->id) && $item->user_id == $userId;
			        $canChange  = $user->authorise('core.edit.state', 'com_abook.lend.'.$item->id) && $canCheckin;

			?>
				<?php $late='';?>
				<?php switch($item->state){
					case 0:
						$state="label-important";
						$late=date('Y-m-d') > $item->lend_in?"<span class='text-error'><i class='icon-info'></i></span>":'';
						$state_label=JText::_('COM_ABOOK_LENT');
						break;
					case 1:
						$state="label-success";
						$state_label=JText::_('COM_ABOOK_RETURNED');
						break;
					case 2:
						$state="label-warning";
						$state_label=JText::_('COM_ABOOK_REQUESTED');
						break;
				}
				?>
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid; ?>">	
					<td class="center hidden-phone">
                                                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                		        </td>
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
						<p class="small">
							<a href="<?php echo JRoute::_('index.php?option=com_abook&view=book&task=book.edit&id='.$item->book_id);?>" title="<?php echo JText::_('JACTION_EDIT'); ?>"><i class="icon-edit"></i></a>
							<span><?php echo JText::_('COM_ABOOK_FIELD_QTY_LABEL');?>: <?php echo $item->qty; ?></span>
						</p>
						<?php if ($item->isbn!='') : ?>
                                                        <p class="small muted"><?php echo JText::_('COM_ABOOK_ISBN');?>: <?php echo $item->isbn; ?></p>
                                                <?php endif; ?>
						</div>
                        		</td>
					<td class="small hidden-phone">
                                		<?php echo $item->library; ?>
		                        </td>
					<td class="small hidden-phone">
                		                <?php echo $item->catalogo; ?>
		                        </td>
					<td class="small hidden-phone">
	                                        <strong><?php echo $item->user_name; ?></strong><?php if ($item->user_id!=0) : ?> <a href="http://joomla3new.neomediatec.eu/administrator/index.php?option=com_users&task=user.edit&id=<?php echo $item->user_id; ?>"><span class="icon-edit"></span></a><?php endif; ?>
						<?php if ($item->user_email!='') : ?>
                	                        	<p class="small"><?php echo $item->user_email; ?></p>
                        	                <?php endif; ?>
                                        </td>
					<td class="nowrap small hidden-phone">
						<?php echo JHtml::_('date', $item->lend_out, JText::_('DATE_FORMAT_LC4')); ?>
                                        </td>
					<td class="nowrap small hidden-phone">
						<?php if($item->lend_in=="0000-00-00 00:00:00"){?>
							-
						<?php } else {
							echo JHtml::_('date', $item->lend_in, JText::_('DATE_FORMAT_LC4')); ?> <?php echo $late;
						}?>
                                        </td>
					<td class="center hidden-phone">
                                                <?php echo $item->id; ?>
                                        </td>
				</tr>
				<?php endforeach;?>
			</tbody>
		</table>
		<?php endif; ?>
		<?php echo $this->pagination->getListFooter(); ?>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<div class="clearfix"></div>
<p><?php echo JHTML::_('credit.credit');?></p>
