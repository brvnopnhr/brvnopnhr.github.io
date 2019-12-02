<?php
/**
 * @version             $Id: default_children.php 17017 2010-05-13 10:48:48Z eddieajau $
 * @package             Joomla.Site
 * @subpackage  com_content
 * @copyright   Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license             GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
$class = 'first';
//funzione alternativa per ordinare le categorie
//jimport( 'joomla.utilities.arrayhelper' );
//JArrayHelper::sortObjects(&$this->children[$this->category->id], "title", $direction=-1)
?>

<?php if (count($this->children[$this->category->id]) > 0) : ?>
        <ul class="bookcategories">
        <?php foreach($this->children[$this->category->id] as $id => $child) : ?>
                <?php
                if ($this->params->get('show_empty_categories', 1) || $child->getNumItems(true) || count($child->getChildren())) :
                        if (!isset($this->children[$this->category->id][$id + 1])) :
                                $class = 'last';
                        endif;
                ?>

                <li class="<?php echo $class; ?> folder">
			<h3 class="item-title">
                        <?php $class = ''; ?>
			<?php if ($this->params->get('catimage')!="-1"){
			if($this->params->get('catimage')=='icon-folder'){?>
                        <i class="icon-folder"></i>
                <?php }else{?>
			<?php $catimage=$this->params->get('catimage')== "cat_custom_image"?$child->getParams()->get('image', 'components/com_abook/assets/images/no_img_cat.png') : "components/com_abook/assets/images/folder/".$this->params->get('catimage', "folder_blue.png"); ?>
			<img class="cat-img-folder" src="<?php echo $catimage; ?>" style="vertical-align:middle;" alt="<?php echo JText::_('COM_ABOOK_CATEGORY').' '.$this->escape($child->title); ?>" />
			<?php }
		}?>
                        <a href="<?php echo JRoute::_(AbookHelperRoute::getCategoryRoute($child->id));?>">
                                <?php echo $this->escape($child->title); ?></a>
				<?php if ( $this->params->get('show_item_count', 0)) : ?>
					<span class="badge tip" title="<?php echo JHtml::tooltipText('COM_ABOOK_NUM_ITEMS'); ?>">
					<?php echo $child->getNumItems(true); ?>
					</span>
				<?php endif ; ?>
				<?php if (count($child->getChildren()) > 0 && ($child->level < $this->params->get('maxLevel') || $this->params->get('maxLevel')=="-1")) : ?>
                                        <a href="#category-<?php echo $child->id;?>" data-toggle="collapse" data-toggle="button" class="btn btn-mini pull-right collapsed"><span class="icon-plus"></span></a>
                                <?php endif;?>
                        </h3>

                        <?php if ($child->description && $this->params->get('show_list_description', 0) == 1) : ?>
                                <div class="category-desc">
                                        <?php echo JHtml::_('content.prepare', $child->description); ?>
                                </div>
                        <?php endif; ?>

                        <?php if (count($child->getChildren()) > 0 ) :?>
				<div class="collapse fade" id="category-<?php echo $child->id;?>">
                                <?php $this->children[$child->id] = $child->getChildren();
                                $this->category = $child;
                                $this->maxLevel--;
                                if ($this->maxLevel != 0) :
                                        echo $this->loadTemplate('children');
                                endif;
                                $this->category = $child->getParent();
                                $this->maxLevel++;
                        endif; ?>
                        </li>
                <?php endif; ?>
        <?php endforeach; ?>
        </ul>
<?php endif; ?>
