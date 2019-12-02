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
$canOrder       = $user->authorise('core.edit.state', 'com_abook.book');
$lend=0;

if ($saveOrder)
{
        $saveOrderingUrl = 'index.php?option=com_abook&task=books.saveOrderAjax&tmpl=component';
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
<form action="<?php echo JRoute::_('index.php?option=com_abook&view=books'); ?>" method="post" name="adminForm" id="adminForm">
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
					<th width="1%" class="nowrap center hidden-phone">
                                                <?php echo JHtml::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                                        </th>
					<th width="1%" class="hidden-phone">
		                        	<?php echo JHtml::_('grid.checkall'); ?>
                	        	</th>
					<th width="1%" style="min-width:55px" class="nowrap center">
                                                <?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                                        </th>
					<th>
						<?php echo JText::_( 'COM_ABOOK_COVER' ); ?>
					</th>			
					<th>
						<?php echo JHTML::_('searchtools.sort',  'COM_ABOOK_TITLE', 'a.title', $listDirn, $listOrder );?>
					</th>
					<th class="hidden-phone">
						<?php echo JHTML::_('searchtools.sort',  'COM_ABOOK_EDITOR', 'a.ideditor', $listDirn, $listOrder );?>
		                        </th>
					<th class="hidden-phone">
		                                <?php echo JHTML::_('searchtools.sort',  'COM_ABOOK_YEAR', 'a.year', $listDirn, $listOrder );?>
                		        </th>
					<th class="hidden-phone">
                		                <?php echo JHTML::_('searchtools.sort',  'COM_ABOOK_LOCATION', 'a.idlocation', $listDirn, $listOrder );?>
		                        </th>
					<th class="hidden-phone">
                                		<?php echo JHTML::_('searchtools.sort',  'COM_ABOOK_LIBRARY', 'a.idlibrary', $listDirn, $listOrder );?>
		                        </th>
					<th>
                                		<?php echo JHTML::_('searchtools.sort',  'COM_ABOOK_CATEGORY', 'a.catid', $listDirn, $listOrder );?>
		                        </th>
					<th >
						<?php echo JHTML::_('searchtools.sort',  'COM_ABOOK_CATALOG', 'a.catalogo', $listDirn, $listOrder );?>
		                        </th>
					<th width="10%" class="nowrap hidden-phone">
						<?php echo JHTML::_('searchtools.sort',  'COM_ABOOK_DATEINSERT', 'a.dateinsert', $listDirn, $listOrder );?>
		                        </th>
					<th width="10%" class="nowrap hidden-phone">
                                		<?php echo JHTML::_('searchtools.sort',   'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder ); ?>
		                        </th>
					<th width="5%" class="nowrap hidden-phone">
		                                <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
                		        </th>
					<?php if ($lend) :?>
					<th width="5%">
                                		<?php echo JHtml::_('searchtools.sort', 'COM_ABOOK_LEND', 'le.lend', $listDirn, $listOrder); ?>
                        		</th>
					<?php endif; ?>
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
			        $canEdit    = $user->authorise('core.edit', 'com_abook.book.'.$item->id);
			        $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out==$userId || $item->checked_out==0;
				$canEditOwn = $user->authorise('core.edit.own', 'com_abook.book.'.$item->id) && $item->user_id == $userId;
			        $canChange  = $user->authorise('core.edit.state', 'com_abook.book.'.$item->id) && $canCheckin;

				$link2		= JRoute::_( 'index.php?option=com_abook&task=editor.edit&cid[]='. $item->editorid );
				$link3          = JRoute::_( 'index.php?option=com_abook&task=category.edit&cid[]='. $item->catid );
			?>
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid; ?>">	
					<td class="order nowrap center hidden-phone">
                                                <?php
                                                $iconClass = '';
                                                if (!$canChange)
                                                {
                                                        $iconClass = ' inactive';
                                                }
                                                elseif (!$saveOrder)
                                                {
                                                        $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
                                                }
                                                ?>
                                                <span class="sortable-handler<?php echo $iconClass ?>">
                                                        <i class="icon-menu"></i>
                                                </span>
                                                <?php if ($canChange && $saveOrder) : ?>
                                                        <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
                                                <?php endif; ?>
                        		</td>
					<td class="center hidden-phone">
                                                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                		        </td>
					<td class="center">
						<div class="btn-group">
						<?php echo JHtml::_('jgrid.published', $item->state, $i, 'books.', $canChange, 'cb'); ?>
		                        <?php if ($item->note!=""){
						//echo JHTML::_("tooltip", $item->note, JText::_( 'COM_ABOOK_NOTE' ) );?>
						<span class="btn btn-micro active hasTooltip" title="" data-original-title="<?php echo JText::_( 'COM_ABOOK_NOTE' ) .'<br />'. $item->note;?>">
						<i class="icon-info"></i>
						</span>
					<?php } else {?>
						<span class="btn btn-micro active" title=""><i class="icon-info" style="color:#fff;"></i></span>
					<?php } ?>
						</div>
                        		</td>
					<td>
						<img class="img-polaroid hasTip" title="" src="<?php echo $item->image!='' ? JURI::root() .$item->image : JURI::root()."components/com_abook/assets/images/nocover.png"; ?>" border="0" style="max-height:80px;min-width:24px;" alt="" />
						
					</td>
					<td class="has-context nowrap">
						<div class="pull-left">
                        			<?php if ($item->checked_out) : ?>
			                        	<?php echo JHtml::_('jgrid.checkedout', $i, $item->owner, $item->checked_out_time, 'books.', $canCheckin); ?>
                        			<?php endif; ?>
                        			<?php if ($canEdit || $canEditOwn) : ?>
                        				<a href="<?php echo JRoute::_('index.php?option=com_abook&view=book&task=book.edit&id='.$item->id);?>" title="<?php echo JText::_('JACTION_EDIT'); ?>"><?php echo $this->escape($item->title); ?></a>
                        			<?php else : ?>
                                			<span><?php echo $this->escape($item->title); ?></span>
                        			<?php endif; ?>
						<p class="small">
                                                	<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?></p>
						<p><?php echo JText::_( 'COM_ABOOK_AUTHOR' ); ?>: <?php echo $item->author; ?></p>
						</div>
                        		</td>
					<td class="hidden-phone">
		                                <a href="<?php echo $link2; ?>" title="<?php echo JText::_( 'EDIT EDITOR' );?>::<?php echo $item->editor; ?>"><?php echo $item->editor; ?></a>
                		        </td>
					<td class="hidden-phone">
                		                <?php echo $item->year; ?>
		                        </td>
					<td class="hidden-phone">
                                		<?php echo $item->location; ?>
		                        </td>
					<td class="hidden-phone">
                                		<?php echo $item->library; ?>
		                        </td>
					<td>
		                                <a href="<?php echo $link3; ?>" title="<?php echo JText::_( 'EDIT CATEGORY' );?>::<?php echo $item->category; ?>"><?php echo $item->category; ?></a>
                		        </td>
					<td class="small">
                		                <?php echo $item->catalogo; ?>
		                        </td>
					<td class="nowrap small hidden-phone">
						<?php echo JHTML::_('date', $item->dateinsert, 'd-m-Y H:i') ?>
                		        </td>
					<td class="small hidden-phone">
                                		<?php echo $this->escape($item->access_level); ?>
		                        </td>
					<td class="center nowrap hidden-phone">
                                		<?php if ($item->language=='*'):?>
		                                        <?php echo JText::_('JALL'); ?>
                		                <?php else:?>
                                			<?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
		                         	<?php endif;?>
                		         </td>
					<?php if ($lend):?>
					<td class="center">
		                                <?php 
							if ($item->lend_id){ ?>
							<a href="<?php echo JRoute::_('index.php?option=com_abook&view=lend&task=lend.edit&id='.$item->lend_id);?>"><?php echo JHtml::_("image", "administrator/components/com_abook/assets/images/arrow-red.png", JText::_('COM_ABOOK_BOOK_LEND'));?></a>
						<?php	}	?>
                        		</td>
					<?php endif; ?>
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
