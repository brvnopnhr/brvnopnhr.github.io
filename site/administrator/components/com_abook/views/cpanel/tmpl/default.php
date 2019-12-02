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
jimport('joomla.html.html.bootstrap');
?>
<div class="row-fluid">
<div class="span3">
        <div class="cpanel-links">
		<div class="sidebar-nav quick-icons">
			<div class="j-links-groups">
				<?php echo $this->loadTemplate('button');?>
			</div>
		</div>
        </div>
	<div class="row-fluid hidden-phone">
	<?php echo JHtml::_('bootstrap.startAccordion', 'slide-cpanel', array('active' => 'donation')); ?>
	<?php echo JHtml::_('bootstrap.addSlide', 'slide-cpanel', JText::_('COM_ABOOK_FIELDSET_DONATION'), 'donation'); ?>
		<p><?php echo JText::_( 'COM_ABOOK_DONATE' ); ?></p>
                                <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                                        <div style="width: 200px; margin: 0px auto;">
                                                <input type="hidden" name="cmd" value="_s-xclick">
                                                <input type="hidden" name="hosted_button_id" value="ET22PSKAWS44L">
                                                <input type="image" src="http://service.globalhub.it/paypal/donate.php" border="0" name="submit" alt="PayPal — The safer, easier way to pay online.">
                                                <img alt="" border="0" src="https://www.paypalobjects.com/it_IT/i/scr/pixel.gif" width="1" height="1">
                                        </div>
                                </form>
		<?php echo JHtml::_('bootstrap.endSlide'); ?>
	<?php echo JHtml::_('bootstrap.addSlide', 'slide-cpanel', JText::_('COM_ABOOK_FIELDSET_LICENSE'), 'license'); ?>
        	<p><?php echo JText::_( 'COM_ABOOK_LICENSE1' ); ?></p>
                <p><?php echo JText::_( 'COM_ABOOK_LICENSE2' ); ?></p>
                <p><?php echo JText::_( 'COM_ABOOK_LICENSE3' ); ?></p>
		<?php echo JHtml::_('bootstrap.endSlide'); ?>
	</div>
	</div>
</div>
<div class="span9">
	<div class="row-fluid">
		<div class="span6">
			<div class="well well-small">
				<h2 class="module-title nav-header"><span class="badge badge-info pull-right"><?php echo $this->books['total']; ?></span> <?php echo JText::_('COM_ABOOK_BOOKS'); ?></h2>
				<div class="row-striped">
					<div class="row-fluid">
						<?php echo JText::_('COM_ABOOK_PUBLISHED'); ?>
						<span class="badge badge-success pull-right"><?php echo $this->books['published']; ?></span>
					</div>
					<div class="row-fluid">
                        	        	<?php echo JText::_('COM_ABOOK_UNPUBLISHED'); ?>
						<span class="badge badge-important pull-right"><?php echo $this->books['unpublished']; ?></span>
	                                </div>
				</div>
			</div>
		</div>
	        <div class="span6">
        	        <div class="well well-small">
                	        <h2 class="module-title nav-header"><span class="badge badge-info pull-right"><?php echo $this->categories['total']; ?></span> <?php echo JText::_('COM_ABOOK_CATEGORIES'); ?></h2>
                        	<div class="row-striped">
                                	<div class="row-fluid">
                                        	        <?php echo JText::_('COM_ABOOK_PUBLISHED'); ?>
							<span class="badge badge-success pull-right"><?php echo $this->categories['published']; ?></span>
	                                </div>
        	                        <div class="row-fluid">
                	                                <?php echo JText::_('COM_ABOOK_UNPUBLISHED'); ?>
							<span class="badge badge-important pull-right"><?php echo $this->categories['unpublished']; ?></span>
                                	</div>
	                        </div>
        	        </div>
	        </div>
	</div>
	<div class="row-fluid">
		<div class="span6">
                	<div class="well well-small">
                        	<h2 class="module-title nav-header"><?php echo JText::_('COM_ABOOK_AUTHORS'); ?><span class="badge badge-info pull-right"><?php echo $this->authors['total']; ?></span></h2>
	                </div>
        	</div>
		<div class="span6">
        	        <div class="well well-small">
                	        <h2 class="module-title nav-header"><?php echo JText::_('COM_ABOOK_EDITORS'); ?><span class="badge badge-info pull-right"><?php echo $this->editors['total']; ?></span></h2>
	                </div>
        	</div>
	</div>
	<div class="row-fluid">
		<div class="span6">
                	<div class="well well-small">
                        	<h2 class="module-title nav-header"><?php echo JText::_('COM_ABOOK_LIBRARIES'); ?><span class="badge badge-info pull-right"><?php echo $this->libraries['total']; ?></span></h2>
	                </div>
        	</div>
	        <div class="span6">
        	        <div class="well well-small">
                	        <h2 class="module-title nav-header"><?php echo JText::_('COM_ABOOK_LOCATIONS'); ?><span class="badge badge-info pull-right"><?php echo $this->locations['total']; ?></span></h2>
	                </div>
        	</div>
	</div>
	<div class="row-fluid">
                <div class="well well-small">
                        <h2 class="module-title nav-header"><?php echo JText::_('COM_ABOOK_FIELDSET_MOST_READ');?></h2>
                        <?php
                        $k = 0;
                        for ($i=0, $n=count( $this->mostread ); $i < $n; $i++){
                        	$row = &$this->mostread[$i];?>
                                <div class="row-striped">
                                	<div class="row-fluid">
                                        	<a href="<?php echo JRoute::_( 'index.php?option=com_abook&controller=book&task=edit&cid[]='. $row->id );?>"><?php echo $row->title; ?></a>
                                        	<span class="badge pull-right"><?php echo $row->hits; ?></span>
                                        </div>
                                </div>
                                <?php
                                $k = 1 - $k;
                        } ?>
                </div>
        </div>
	<div class="row-fluid">
                <div class="well well-small">
                        <h2 class="module-title nav-header"><?php echo JText::_('COM_ABOOK_FIELDSET_MOST_RATED');?></h2>
                       	<?php
                        $k = 0;
                        for ($i=0, $n=count( $this->mostrated ); $i < $n; $i++){
                                $row = &$this->mostrated[$i];?>
                                <div class="row-striped">
                                	<div class="row-fluid">
                                        	<a href="<?php echo JRoute::_( 'index.php?option=com_abook&controller=book&task=edit&cid[]='. $row->id );?>"><?php echo $row->title; ?></a>
					<?php
					$html = "";
					$img= "";
					// look for images in template if available
                			$starImageOn = JHTML::_('image','system/rating_star.png', NULL, NULL, true);
                			$starImageOff = JHTML::_('image','system/rating_star_blank.png', NULL, NULL, true);
                			for ($s=0; $s < $row->rating; $s++) {
                        			$img .= $starImageOn;
               				}
                			for ($s=$row->rating; $s < 5; $s++) {
                        			$img .= $starImageOff;
                			}
                			$html .= '<span class="content_rating pull-right">';
                			$html .= $img .'&#160;/&#160;';
                			$html .= $row->rating_count;
                			$html .= "</span>\n<br />\n";
					echo $html;
					?>
					</div>
                                </div>
                                <?php
                                $k = 1 - $k;
                	} ?>
                </div>
        </div>
	<div class="row-fluid">
                <div class="well well-small">
                        <h2 class="module-title nav-header"><?php echo JText::_('COM_ABOOK_FIELDSET_MOST_LENT');?></h2>
                        <?php
                        $k = 0;
                        for ($i=0, $n=count( $this->mostlent ); $i < $n; $i++){
                                $row = &$this->mostlent[$i];?>
                                <div class="row-striped">
                                        <div class="row-fluid">
                                                <?php echo $row->title; ?>
                                                <span class="badge pull-right"><?php echo $row->amount; ?></span>
                                        </div>
                                </div>
                                <?php
                                $k = 1 - $k;
                        } ?>
                </div>
        </div>
</div>
</div>
<div class="clearfix"></div>
<p><?php echo JHTML::_('credit.credit');?></p>
