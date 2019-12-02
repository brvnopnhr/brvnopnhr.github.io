<?php
/**
 * @package SJ Scattered Gallery for K2
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2015 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */
defined('_JEXEC') or die;
if (!empty($list)) {
    JHtml::stylesheet('modules/' . $module->module . '/assets/css/style.css');
    if (!defined('SMART_JQUERY') && $params->get('include_jquery', 0) == "1") {
        JHtml::script('modules/' . $module->module . '/assets/js/jquery-1.8.2.min.js');
        JHtml::script('modules/' . $module->module . '/assets/js/jquery-noconflict.js');
        define('SMART_JQUERY', 1);
    }

    JHtml::script('modules/' . $module->module . '/assets/js/modernizr.min.js');
    JHtml::script('modules/' . $module->module . '/assets/js/classie.js');
    JHtml::script('modules/' . $module->module . '/assets/js/photostack.js');


    ImageHelper::setDefault($params);

    $options = $params->toObject();
    $count_item = (count($list));
    $height_module = $options->height_module;
    if (empty($height_module) || $height_module == 0 || $height_module <= 0) {
        $height_module = '500px';
    } else {
        $height_module = $options->height_module . 'px';
    }

    $tag_id = 'sj_scatteredgallery_' . rand() . time().$module->id;
	$ds_overlay = (int)$params->get('display_overlay');
	$photo_start = ($ds_overlay) ? ' photostack-start ':'';
	$label_overlay = $params->get('label_overlay');
	$label_overlay = ($ds_overlay && !empty($label_overlay)) ? ' data-label="'.$label_overlay.'"' : '';
	$show_btngallery = ($ds_overlay && !empty($label_overlay)) ? ' sg-btnempty ': '';
    $itemCount = $options->itemCount;
    ?>
    <!--[if lt IE 9]>
    <div id="<?php echo $tag_id;?>" class="sj-sg-gallery lt-ie9 ">
    <![endif]-->
    <!--[if IE 9]>
    <div id="<?php echo $tag_id;?>" class="sj-sg-gallery msie9 ">
    <![endif]-->
    <!--[if gt IE 9]><!-->
    <div id="<?php echo $tag_id; ?>" class="sj-sg-gallery "><!--<![endif]-->
        <!--        <div class="sg-loading"></div>-->
        <?php if (!empty($options->pretext)) { ?>
            <div class="pre-text"><?php echo $options->pretext; ?></div>
        <?php } ?>
        <section id="sg_photostack_<?php echo $module->id ?>" <?php echo $label_overlay ; ?> class="sg-photostack <?php echo $photo_start.$show_btngallery ; ?>"
                 style="height: <?php echo $height_module; ?>;">
		 
	    
            <div class="sg-container">
                <?php $count = 0;
                foreach ($list as $key => $item) {
                    $count++;
                    $img = K2ScatteredGalleryHelper::getK2Image($item, $params);
                    ?>
                    <?php if ($key <= $itemCount-1){?>
                    <figure>
                        <?php if ($img) { ?>
                            <a href="<?php echo $item->link; ?>"
                               title="<?php echo $item->title ?>" <?php echo K2ScatteredGalleryHelper::parseTarget($params->get('item_link_target')); ?>>
                                <img src="<?php echo K2ScatteredGalleryHelper::imageSrc($img); ?>"
                                     alt="<?php echo $item->title ?>" title="<?php echo $item->title ?>"/>
                            </a>
                        <?php } ?>
                        <figcaption>
                            <?php if ($options->item_title_display == 1) { ?>
                                <div class="sg-title">
                                    <a href="<?php echo $item->link; ?>"
                                       title="<?php echo $item->title ?>" <?php echo K2ScatteredGalleryHelper::parseTarget($params->get('item_link_target')); ?>>
                                        <?php echo K2ScatteredGalleryHelper::truncate($item->title, $params->get('item_title_max_characs', 25)); ?>
                                    </a>
                                </div>
                            <?php } ?>
                            <?php if ($options->item_desc_display == 1 || !empty($item->displayIntrotext) || $options->item_tags_display == 1 || !empty($item->tags) || $options->item_readmore_display == 1) { ?>
                                <div class="sg-back">
				    <div class="sg-back-inner">
                                    
                                    <?php if ($params->get('item_created_display') == 1) { ?>
                                        <div class="item-date">
                                                <span class="item-date">
                                                             <?php echo JHTML::_('date', $item->created, "F"); ?>
                                                             <?php echo K2ScatteredGalleryHelper::Convertday(JHTML::_('date', $item->created, "d")) . ","; ?>
                                                             <?php echo JHTML::_('date', $item->created, "Y"); ?>
                                                </span>
                                        </div>
                                    <?php } ?>

                                    <?php if ($params->get('itemPostBy', 1) == 1) { ?>
                                        <div class="item-postby">
                                            <?php echo 'By: ' . $item->author; ?>
                                        </div>
                                    <?php } ?>

                                    <?php if ($options->item_desc_display == 1 && $item->displayIntrotext != '') { ?>
                                        <div class="item-description">
					    <?php $introtext_after= str_replace('...', '',$item->displayIntrotext);
						echo $introtext_after; ?>
                                        </div>
                                    <?php } ?>

                                    <?php if ($options->item_tags_display == 1 && !empty($item->tags)) { ?>
                                        <div class="item-tags">
                                            <div class="tags">
                                                <?php $hd = -1;
                                                foreach ($item->tags as $tag): $hd++; ?>
                                                    <span
                                                        class="tag-<?php echo $tag->id . ' tag-list' . $hd; ?>">
                                                                    <a class="label label-info"
                                                                       href="<?php echo $tag->link; ?>"
                                                                       title="<?php echo $tag->name; ?>"
                                                                       target="_blank">
                                                                        <?php echo $tag->name; ?>
                                                                    </a>
                                                            </span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php } ?>
				</div>
                                </div>
                            <?php } ?>
                        </figcaption>
                    </figure>
                        <?php } else{?>
                        <figure data-dummy>
                            <?php if ($img) { ?>
                                <a href="<?php echo $item->link; ?>"
                                   title="<?php echo $item->title ?>" <?php echo K2ScatteredGalleryHelper::parseTarget($params->get('item_link_target')); ?>>
                                    <img src="<?php echo K2ScatteredGalleryHelper::imageSrc($img); ?>"
                                         alt="<?php echo $item->title ?>" title="<?php echo $item->title ?>"/>
                                </a>
                            <?php } ?>
                            <figcaption>
                                <?php if ($options->item_title_display == 1) { ?>
                                    <div class="sg-title">
                                        <a href="<?php echo $item->link; ?>"
                                           title="<?php echo $item->title ?>" <?php echo K2ScatteredGalleryHelper::parseTarget($params->get('item_link_target')); ?>>
                                            <?php echo K2ScatteredGalleryHelper::truncate($item->title, $params->get('item_title_max_characs', 25)); ?>
                                        </a>
                                    </div>
                                <?php } ?>
                                <?php if ($options->item_desc_display == 1 || !empty($item->displayIntrotext) || $options->item_tags_display == 1 || !empty($item->tags) || $options->item_readmore_display == 1) { ?>
                                    <div class="sg-back">
                                        <div class="sg-back-inner">
                                            <?php if ($params->get('itemCommentsCounter') == 1) { ?>
                                                <div class="item-comment">
                                                    <?php
                                                    if ($item->numOfComments == 1) {
                                                        echo $item->numOfComments . '&nbsp;' . 'comment';
                                                    } else {
                                                        echo $item->numOfComments . '&nbsp;' . 'comments';
                                                    }
                                                    ?>
                                                </div>
                                            <?php } ?>
					 
                                            <?php if($options->item_rating_display) { ?>
					    <div class="item-rating">
						<div class="itemRatingForm">
						    <ul class="itemRatingList">
							<li class="itemCurrentRating" id="itemCurrentRating<?php echo $item->id; ?>" style="width:<?php echo $item->votingPercentage; ?>%;"></li>
							<li><a href="#" data-id="<?php echo $item->id; ?>" title="<?php echo JText::_('K2_1_STAR_OUT_OF_5'); ?>" class="one-star">1</a></li>
							<li><a href="#" data-id="<?php echo $item->id; ?>" title="<?php echo JText::_('K2_2_STARS_OUT_OF_5'); ?>" class="two-stars">2</a></li>
							<li><a href="#" data-id="<?php echo $item->id; ?>" title="<?php echo JText::_('K2_3_STARS_OUT_OF_5'); ?>" class="three-stars">3</a></li>
							<li><a href="#" data-id="<?php echo $item->id; ?>" title="<?php echo JText::_('K2_4_STARS_OUT_OF_5'); ?>" class="four-stars">4</a></li>
							<li><a href="#" data-id="<?php echo $item->id; ?>" title="<?php echo JText::_('K2_5_STARS_OUT_OF_5'); ?>" class="five-stars">5</a></li>
						    </ul>
						    <div id="itemRatingLog<?php echo $item->id; ?>" class="itemRatingLog"><?php echo $item->numOfvotes; ?></div>
						    <div class="clr"></div>
						</div>
					    </div>
					<?php } ?>

                                            <?php if ($params->get('item_created_display') == 1) { ?>
                                                <div class="item-date">
                                                <span class="item-date">
                                                             <?php echo JHTML::_('date', $item->created, "F"); ?>
                                                             <?php echo K2ScatteredGalleryHelper::Convertday(JHTML::_('date', $item->created, "d")) . ","; ?>
                                                             <?php echo JHTML::_('date', $item->created, "Y"); ?>
                                                </span>
                                                </div>
                                            <?php } ?>

                                            <?php if ($params->get('itemPostBy', 1) == 1) { ?>
                                                <div class="item-postby">
                                                    <?php echo 'By: ' . $item->author; ?>
                                                </div>
                                            <?php } ?>

                                            <?php if ($options->item_desc_display == 1 && $item->displayIntrotext != '') { ?>
                                                <div class="item-description">
                                                    <?php echo $item->displayIntrotext; ?>
                                                </div>
                                            <?php } ?>

                                            <?php if ($options->item_tags_display == 1 && !empty($item->tags)) { ?>
                                                <div class="item-tags">
                                                    <div class="tags">
                                                        <?php $hd = -1;
                                                        foreach ($item->tags as $tag): $hd++; ?>
                                                            <span
                                                                class="tag-<?php echo $tag->id . ' tag-list' . $hd; ?>">
                                                                    <a class="label label-info"
                                                                       href="<?php echo $tag->link; ?>"
                                                                       title="<?php echo $tag->name; ?>"
                                                                       target="_blank">
                                                                        <?php echo $tag->name; ?>
                                                                    </a>
                                                            </span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </figcaption>
                        </figure>
                        <?php } ?>
                <?php } ?>
            </div>
	    <div class="buttom-click" style="line-height: <?php echo $height_module; ?>; "><span><?php echo JText::_('CHECK_OUR_CAMPUS_LIFE'); ?></span> </div>
        </section>
        <?php if (!empty($options->posttext)) { ?>
            <div class="post-text">
                <?php echo $options->posttext; ?>
            </div>
        <?php } ?>
    </div>

    <script type="text/javascript">
        var tag_id = document.getElementById('<?php echo $tag_id; ?>');
		
        new Photostack(tag_id.querySelector('#sg_photostack_<?php echo $module->id; ?>'), {
            callback: function (item) {

            }
        });
    </script>


<?php
} else {
    echo JText::_('NO_PRODUCT');
} ?>


