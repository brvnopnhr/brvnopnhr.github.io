<?php

// no direct access
defined('_JEXEC') or die;

JHtmlBehavior::framework();
JHTML::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select');
JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers/html');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$letter       = $this->state->get('filter.letter');
$author       = $this->state->get('filter.author_id');
$year       = $this->state->get('filter.year');
$editor       = $this->state->get('filter.editor_id');
$category       = $this->state->get('filter.category_id');
$tag       = $this->state->get('filter.tag_id');
?>
<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
	<?php if ($this->params->get('filter_field')):?>
                <div class="filters btn-toolbar">
			<div class="btn-group">
				<div class="input-append">
				<label for="filter-keyword"><?php echo JText::_('COM_ABOOK_TITLE'); ?></label>
                        	<input type="text" name="filter-keyword" id="filter-keyword" style="width:167px" value="<?php echo $this->escape($this->state->get('filter.keyword')); ?>" class="inputbox" onchange="document.adminForm.submit();" title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />
				<button class="btn" type="submit"><?php echo JText::_('COM_ABOOK_SEARCHBUTTON_TEXT'); ?></button>
				</div>
			</div>
			<?php if ($this->params->get('filter_category')):?>
			<div class="btn-group">
			<label for="filter-category_id"><?php echo JText::_('COM_ABOOK_CATEGORY'); ?></label>
			<?php 
				$filtercategory= JHtml::_('categoryparent.options', 'com_abook', array('filter.published' => array(0, 1)), $this->state->get('category.id', 0));
				echo JHtml::_('select.genericlist', $filtercategory, 'filter-category_id', 'onchange="this.form.submit()"', 'value', 'text', $this->escape($category));
			?>
			</div>
			<?php endif; ?>
			<?php if ($this->params->get('filter_tag')):?>
			<div class="btn-group">
			<label for="filter-tag_id"><?php echo JText::_('COM_ABOOK_TAGS'); ?></label>
                        <?php
                                $filtertag= JHtml::_('abtag.options', $this->state->get('category.id'), $this->state->get('filter.subcategories', false), $this->state->get('filter.max_category_levels', 2));
                                echo JHtml::_('select.genericlist', $filtertag, 'filter-tag_id', 'onchange="this.form.submit()"', 'value', 'text', $this->escape($tag));
                        ?>
			</div>
			<?php endif; ?>
			<div class="btn-group">
			<label for="filter-author_id"><?php echo JText::_('COM_ABOOK_AUTHOR'); ?></label>
			<?php
				$filterauthor= JHtml::_('abauthor.options', $this->state->get('category.id'), $this->state->get('filter.subcategories', false), $this->state->get('filter.max_category_levels', 2));
                        	echo JHtml::_('select.genericlist', $filterauthor, 'filter-author_id', 'onchange="this.form.submit()"', 'value', 'text', $this->escape($author));
			?>
			</div>
			<div class="btn-group">
			<label for="filter-year"><?php echo JText::_('COM_ABOOK_YEAR'); ?></label>
			<?php
				$filteryear= JHtml::_('abyear.options', $this->state->get('category.id'), $this->state->get('filter.subcategories', false), $this->state->get('filter.max_category_levels', 2));
                                echo JHtml::_('select.genericlist', $filteryear, 'filter-year', 'onchange="this.form.submit()"', 'value', 'text', $this->escape($year));
			?>
			</div>
			<?php if ($this->params->get('filter_editor')):?>
			<div class="btn-group">
			<label for="filter-editor_id"><?php echo JText::_('COM_ABOOK_EDITOR'); ?></label>
			<?php
				$filtereditor = JHtml::_('abeditor.options', $this->state->get('category.id'), $this->state->get('filter.subcategories', false), $this->state->get('filter.max_category_levels', 2));
                                echo JHtml::_('select.genericlist', $filtereditor, 'filter-editor_id', 'onchange="this.form.submit()"', 'value', 'text', $this->escape($editor));
			?>
			</div>
			<?php endif; ?>
                </div>
        <?php endif; ?>

	
<?php if ($this->params->get( 'alphabet_bar', 1 )== 1) { ?>
        <fieldset class="filters"><?php echo JHTML::_('icon.alphabet');?></fieldset>
        <?php if ($letter!=''){ ?>
                <div class="thisletter"><h2><?php echo $letter;?></h2></div>
        <?php }

} ?>
<?php if (empty($this->items)) : ?>
        <p><?php echo JText::_('COM_ABOOK_NO_BOOKS'); ?></p>
<?php else:?>
	<table class="table table-hover books">
		<tbody>
			<?php foreach($this->items as $i => $item) : ?>
				<?php $linkbook=JRoute::_(AbookHelperRoute::getBookRoute($item->slug, $item->slugcat));?>
				<tr>
					<?php if ($this->params->get( 'show_bookimage', 1) == 1){ ?>
					<td width="100px">
					<div class="img-intro-left cover">
                        			<?php if ($item->image){ ?>
                                			<a href="<?php echo $linkbook; ?>" title="<?php echo JText::_('COM_ABOOK_COVEROF').' '.$item->title; ?>"><img class="img-polaroid" src="<?php echo $item->image ?>" alt="<?php echo JText::_('COM_ABOOK_COVEROF').' '.$item->title ;?>"/></a>
                              			<?php }else{ ?>
                                			<a href="<?php echo $linkbook; ?>" title="<?php echo JText::_('COM_ABOOK_NOCOVEROF').' '.$item->title; ?>"><img class="img-polaroid" src="components/com_abook/assets/images/nocover.png" alt="<?php echo JText::_('COM_ABOOK_NOCOVEROF').' '.$item->title; ?>" /></a>
                              			<?php }?>
                        		</div>
					</td>
				<?php }?>
					<td>
						<h3 class="book-title">
							<?php if ($this->params->get( 'link_titles', 1) == 1){ ?>
							<a href="<?php echo $linkbook; ?>"><?php echo $item->title; ?>
								<?php if ($item->subtitle){?>
                                        				<p><small><?php echo $item->subtitle; ?></small></p>
                                				<?php }?>
							</a>
							<?php }else{ ?>
								<?php echo $item->title; ?>
								<p><small><?php echo $item->subtitle; ?></small></p>
							<?php }?>
						</h3>
						<?php if ($this->params->get( 'show_pag_index',0 )==1 && $item->pag_index > 0) { ?>
                                                        <div><label><?php echo JText::_('COM_ABOOK_PAG_INDEX') ." "; ?></label>
                                                                <?php echo $item->pag_index; ?>
                                                        </div>
                                                <?php } ?>
						<?php $n=count($item->authors);
						if ($n > 0){ ?>
							<div><?php echo $item->editedby==1 ? JText::_('COM_ABOOK_EDITED_BY')." " : JText::_('COM_ABOOK_BY')." ";
                                                        foreach($item->authors as $k=>$author) {
                                                                $link=JRoute::_(AbookHelperRoute::getAuthorRoute($author->slugauthor));
                                                                echo '<a href="'.$link.'">'.$author->author.'</a>';
                                                                if ($k!=$n-1) echo ', '; ?>

                                                	<?php } ?>
							</div>
						<?php } ?>
						<?php if ($this->params->get( 'view_rate',1 )==1) { ?>
                                                        <?php echo JHTML::_('icon.votebook', $item, $item->vote);?>
                                                <?php } ?>
					<?php if ($this->params->get( 'show_year',1 )==1 || $this->params->get( 'show_bookcat', 1 )==1||$this->params->get( 'show_cat_tags', 1 )==1||$this->params->get( 'show_hits',1 )==1||$this->params->get( 'view_rate',1 )==1||$this->params->get( 'display_category_comments',1 ) == 1){?>
						<dl>
						<?php if ($item->year > 0 && $this->params->get( 'show_year',1 )==1) { ?>
                                                        <dd><span class="icon-calendar"></span> <?php echo JText::_('COM_ABOOK_YEAR') .": "; ?>
							<?php echo $item->year; ?></dd>
                                                <?php } ?>
						<?php
						if ($this->params->get( 'show_bookcat', 1 )==1){ 
							$link2 = JRoute::_(AbookHelperRoute::getCategoryRoute($item->slugcat));?>
							<dd><span class="icon-folder-open"></span> <?php echo JText::_('COM_ABOOK_CATEGORY') .': ';?>
							<a href="<?php echo $link2;?>"><?php echo $item->cattitle;?></a></dd>
						<?php } ?>
						<?php if ($this->params->get( 'show_hits',1 )==1) { ?>
							<dd><span class="icon-eye-open"></span> <?php echo JText::_('COM_ABOOK_HITS') .": "; ?>
        	                                        	<?php echo $item->hits; ?>
							</dd>
						<?php } ?>
						<?php if ($this->params->get( 'show_file',0 )==1 && $item->file != '') { ?>
							<dd><span class="icon-file"></span> <a href="images/<?php echo $this->params->get('file_path').'/'.$item->file;?>"><?php echo JText::_('COM_ABOOK_FILE');?></a></dd>
                                                <?php } ?>
						<?php
                                                if ($this->params->get( 'show_bookdesc', 1 )==1){ ?>
                                                        <dd><div><?php echo substr(strip_tags($item->description), 0, 100);?></div></dd>
                                                <?php } ?>
						<?php if ($this->params->get('show_cat_tags', 1) && !empty($item->tags)) : ?>
                                                        <?php $item->tagLayout = new JLayoutFile('book.tags'); ?>
                                                        <dd><?php echo $item->tagLayout->render($item->tags); ?></dd>
                                                <?php endif; ?>
						<?php if ($this->params->get( 'display_category_comments',1 ) == 1) {
                                                        //$jcomments_isenabled=JComponentHelper::getComponent('com_jcomments', true);
                                                        $jcomments_plugin=JFile::exists(JPATH_BASE.DS.'components'.DS.'com_jcomments'.DS.'jcomments.php');
                                                        if ($jcomments_plugin) {
                                                                include_once(JPATH_BASE.DS.'components'.DS.'com_jcomments'.DS.'jcomments.php');
                                                                $count = JComments::getCommentsCount($item->id, 'com_abook');
                                                                echo '<dd><span class="icon-comment"></span> <a href="'.$linkbook.'">';
                                                                echo $count ? JText::_('COM_ABOOK_COMMENT').' <span class="badge">'. $count . '</span>' : JText::_('COM_ABOOK_COMMENT').' <span class="badge">0</span>';
                                                                echo '</a></dd>';
                                                        }
                                                }?>
						<?php
                                                        if($this->params->get( 'view_date', 1 )==1){?>
                                                                <dd><span class="muted">
                                                                <span class="icon-calendar"></span>
                                                                <?php echo JText::_('COM_ABOOK_DATE_INSERT');?>: <?php echo JHTML::_('date', $item->dateinsert, JText::_('DATE_FORMAT_LC1')) ?>
                                                                </span></dd>
                                                        <?php } ?>
						</dl>
					<?php } ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>
<div class="pull-right"><?php echo JHTML::_('credit.credit') ?></div>
<div class="clearfix"></div>
<?php // Add pagination links ?>
<?php if (!empty($this->items)) : ?>
        <?php if ($this->params->def('showpagination', 1)) : ?>
        <div class="pagination">
                <?php if ($this->params->def('show_pagination_results', 1)) : ?>
                        <p class="counter pull-right">
                                <?php echo $this->pagination->getPagesCounter(); ?>
                        </p>
                <?php endif; ?>
                <?php echo $this->pagination->getPagesLinks(); ?>
        </div>
	<?php endif; ?>
<?php endif; ?>
	<div>
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<input type="hidden" name="letter" value="<?php echo $letter; ?>" />
		<input type="hidden" name="limitstart" value="" />
                <input type="hidden" name="task" value="" />
	</div>
</form>
