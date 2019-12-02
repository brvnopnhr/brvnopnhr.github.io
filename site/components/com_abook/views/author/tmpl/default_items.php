<?php

// no direct access
defined('_JEXEC') or die;

JHtmlBehavior::framework();

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>
<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" name="adminForm">
<?php if (empty($this->items)) : ?>
	<p> <?php echo JText::_('COM_ABOOK_NO_BOOKS'); ?>	 </p>
<?php else : ?>
	<table class="table table-hover books">
		<tbody>
			<?php foreach($this->items as $i => $item) : ?>
				<?php $linkbook=JRoute::_(AbookHelperRoute::getBookRoute($item->slug, $item->slugcat));
				$link2 = JRoute::_(AbookHelperRoute::getCategoryRoute($item->slugcat));?>
				<tr>
					<?php if ($this->params->get( 'show_bookimage', 1) == 1){ ?>
					<td width="100px">
                                                <div class="img-intro-left cover">
                        			<?php if ($item->image){ ?>
                                			<a href="<?php echo $linkbook; ?>" title="<?php JText::_('COM_ABOOK_COVEROF').' '.$item->title; ?>"><img class="img-polaroid" src="<?php echo $item->image ?>" alt="<?php JText::_('COM_ABOOK_COVEROF').' '.$item->title ;?>"/></a>
                              			<?php }else{ ?>
                                			<a href="<?php echo $link; ?>" title="<?php JText::_('COM_ABOOK_NOCOVEROF').' '.$item->title; ?>"><img class="img-polaroid" src="components/com_abook/assets/images/nocover.png" alt="<?php JText::_('COM_ABOOK_NOCOVEROF').' '.$item->title; ?>" class="nocover" /></a>
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
						<?php $n=count($item->authors);
                                                if ($n > 0){ ?>
                                                        <div><?php echo $item->editedby==1 ? JText::_('COM_ABOOK_EDITED_BY')." " : JText::_('COM_ABOOK_BY')." ";
                                                        foreach($item->authors as $k=>$author) {
                                                                $link=JRoute::_(AbookHelperRoute::getAuthorRoute($author->idauthor.':'.$author->alias));
                                                                echo '<a href="'.$link.'">'.$author->author.'</a>';
                                                                if ($k!=$n-1) echo ', ';
                                                	} ?>
                                                	</div>
						<?php } ?>
						<?php if ($this->params->get( 'view_rate', 1 )==1) { ?>
                                                        <?php echo JHTML::_('icon.votebook', $item, $item->vote);?>
                                                <?php } ?>
					<?php if ($this->params->get( 'show_year',1 )==1 || $this->params->get( 'show_bookcat', 1 )==1||$this->params->get( 'show_cat_tags', 1 )==1||$this->params->get( 'show_hits',1 )==1||$this->params->get( 'view_rate',1 )==1||$this->params->get( 'display_category_comments',1 ) == 1){?>
						<dl>
						<?php if ($item->year > 0 && $this->params->get( 'show_year',1 )==1) { ?>
                                                        <dd><span class="icon-calendar"></span> <?php echo JText::_('COM_ABOOK_YEAR') .": "; ?>
                                                                <?php echo $item->year; ?>
                                                        </dd>
                                                <?php } ?>
						<?php if ($this->params->get( 'show_bookcat', 1 )==1){?>
							<dd><span class="icon-folder-open"></span> <?php echo JText::_('COM_ABOOK_CATEGORY');?>: 
							<a href="<?php echo $link2;?>"><?php echo $item->category_title;?></a></dd>
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
                                                        <dd><?php echo substr(strip_tags($item->description), 0, 100);?></dd>
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
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
</form>
