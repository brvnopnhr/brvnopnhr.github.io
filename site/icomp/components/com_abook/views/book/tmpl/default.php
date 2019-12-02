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
JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers');
$pageClass = $this->params->get('pageclass_sfx');
//-------------------------------------------------
JPluginHelper::importPlugin('abook');
$dispatcher = JDispatcher::getInstance();
$payment=$dispatcher->trigger('onBookPrepare', array("com_abook.book", &$this->book, &$this->params));
//-------------------------------------------------
?>
<div class="item-page<?php echo $this->escape($pageClass); ?>" itemscope itemtype="http://schema.org/Book">
	<?php if ($this->params->def('show_page_heading', 1)) : ?>
		<div class="page-header">
			<h1><?php echo $this->params->get( 'page_heading');?></h1>
		</div>
	<?php endif; ?>
	<?php if ($this->params->get( 'search', 1 )== 1 && !$this->print) { ?>
                <?php echo JHTML::_('icon.search');?>
                <div class="clr"></div>
        <?php } ?>
        <?php if ($this->params->get( 'breadcrumb', 2 )== 2 && !$this->print) { ?>
                <div class="abook-path">
                        <?php echo JHTML::_('icon.breadcrumb', $this->path);?>
                </div>
        <div class="clearfix"></div>
        <?php } ?>
	<?php if (!$this->print) : ?>
		<?php if ($this->params->get('show_print_icon') || $this->params->get('show_email_icon')) : ?>
        		<div class="btn-group pull-right">
                	        <a class="btn dropdown-toggle" data-toggle="dropdown" href="#"> <span class="icon-cog"></span> <span class="caret"></span> </a>
				<ul class="dropdown-menu actions">
        	        		<?php if ($this->params->get('show_print_icon')) : ?>
		                        <li class="print-icon">
        		                <?php echo JHtml::_('icon.print_popup',  $this->book, $this->params); ?>
                		        </li>
                			<?php endif; ?>
                			<?php if ($this->params->get('show_email_icon')) : ?>
	                        	<li class="email-icon">
		                        <?php echo JHtml::_('icon.email',  $this->book, $this->params); ?>
        		                </li>
                			<?php endif; ?>
        			</ul>
			</div>
		<?php endif; ?>
	<?php else : ?>
		<div id="pop-print" class="btn hidden-print">
                                <?php echo JHtml::_('icon.print_screen', $this->book, $this->params); ?>
                        </div>
	<?php endif; ?>
	<div class="page-header">
	<h2 itemprop="name"><?php echo $this->book->title;?>
	<p><small><?php echo $this->book->subtitle;?></small></p>
	</h2>
	</div>
	<?php if (isset($payment) && $payment != ''){?>
	<div class="row-fluid">
		<div class="span12">
			<div class="pull-right" id="abook_paypal"><?php echo $this->book->payment;?></div>
		</div>
	</div>
	<?php } ?>
	<div class="row-fluid">
	<?php if ($this->params->get('show_bookcover', 1)){	?>
		<div class="span4">
			<div class="img-intro-left cover">
			<?php if ($this->book->image){
				echo '<a href="'.$this->book->image.'" 
                	        style="cursor: pointer;" class="modal" title="'.JText::_('COM_ABOOK_COVEROF').' '.$this->book->title.'">
                        	<img itemprop="image" class="img-polaroid" src="'.$this->book->image.'" alt="'.JText::_('COM_ABOOK_COVEROF').' '.$this->book->title.'" />
                        	</a>';
                	}else{
                		echo '<img class="img-polaroid" src="components/com_abook/assets/images/nocover.png" alt="'.JText::_('COM_ABOOK_NOCOVEROF').'" />';
                	}
                	?>
                	</div>
		</div>
	<?php }?>
		<div class="span8">
			<dl class="bookdetails">
				<?php if ($this->params->get('show_category', 1)){ ?>
				<dd>
					<span class="icon-folder-open"></span> <strong><?php echo JText::_('COM_ABOOK_CATEGORY');?>: </strong>
					<a href="<?php echo AbookHelperRoute::getCategoryRoute($this->book->catslug);?>"><?php echo $this->book->category_title;?></a>
				</dd>
				<?php } ?>
				<?php
                                $a=count($this->authors);
                                if ($a > 0){?>
				<dd itemtype="http://schema.org/Person" itemscope itemprop="author">
					<span class="icon-user"></span> <strong><?php echo JText::_('COM_ABOOK_AUTHOR');?>: </strong>
					<?php
                        		$n=count($this->authors);
                        		$k=0;
                        		foreach ($this->authors as $author){
                                		$k=$k +1;
						$link = AbookHelperRoute::getAuthorRoute(!empty($author->alias) ? ($author->author_id.':'.$author->alias) : $author->author_id);
                                		echo '<a itemprop="url" href="' .$link .'"><span itemprop="name">' .$author->author_name .'</span></a>';
                                		if ($n != $k) echo ", ";
                        		}
                        		?>
				</dd>
				<?php } ?>
				<?php if ($this->book->editor_name!='' && $this->params->get('show_editor', 1)){ ?>
			        <dd itemtype="http://schema.org/Organization" itemscope itemprop="publisher">
                			<span class="icon-briefcase"></span> <strong><?php echo JText::_('COM_ABOOK_EDITOR');?>: </strong><span itemprop="name"><?php echo $this->book->editor_name;?></span>
			        </dd>
				<?php } ?>
			        <?php if ($this->book->pag>0 && $this->params->get('show_pag', 1)){ ?>
			        <dd>
			                <strong><?php echo JText::_('COM_ABOOK_PAGES');?>: </strong><span itemprop="numberOfPages"><?php echo $this->book->pag;?></span>
			        </dd>
			        <?php } ?>
			        <?php if ($this->book->price!=''){ ?>
			        <dd itemprop="offers" itemscope itemtype="http://schema.org/Offer">
			                <strong><?php echo JText::_('COM_ABOOK_PRICE');?>: </strong><span itemprop="price"><?php echo $this->book->price;?></span>
			        </dd>
			        <?php } ?>
			        <?php if ($this->book->isbn!='' && $this->params->get('show_isbn', 1)){ ?>
			        <dd>
			                <strong><?php echo JText::_('COM_ABOOK_ISBN');?>: </strong><span itemprop="isbn"><?php echo $this->book->isbn;?></span>
			        </dd>
			        <?php } ?>
			        <?php if ($this->book->library_name!='' && $this->params->get('show_library', 1)){ ?>
			        <dd>
			                <span class="icon-home"></span> <strong><?php echo JText::_('COM_ABOOK_LIBRARY');?>: </strong><?php echo $this->book->library_name;?>
			        </dd>
			        <?php } ?>
				<?php if ($this->book->location_name!='' && $this->params->get('show_location', 1)){ ?>
                                <dd>
                                        <span class="icon-flag"></span> <strong><?php echo JText::_('COM_ABOOK_LOCATION');?>: </strong><span itemprop="contentLocation"><?php echo $this->book->location_name;?></span>
                                </dd>
                                <?php } ?>
			        <?php if ($this->book->year!=0 && $this->params->get('show_year', 1)){ ?>
			        <dd>
			                <span class="icon-calendar"></span> <strong><?php echo JText::_('COM_ABOOK_YEAR');?>: </strong><span itemprop="copyrightYear"><?php echo $this->book->year;?></span>
			        </dd>
			        <?php } ?>
				<?php if ($this->book->catalogo!="" && $this->params->get('show_catalog', 1)){ ?>
                                <dd>
                                        <strong><?php echo JText::_('COM_ABOOK_CATALOG');?>: </strong><?php echo $this->book->catalogo;?>
                                </dd>
                                <?php } ?>
				<?php if ($this->book->file!="" && in_array($this->params->get('allow_download'), $this->user->getAuthorisedViewLevels())){ ?>
                                <dd>
                                        <span class="icon-file"></span> <strong><?php echo JText::_('COM_ABOOK_FILE');?>: </strong>
						<a href="images/<?php echo $this->params->get('file_path').'/'.$this->book->file;?>"><?php echo $this->filename; ?></a>
                                </dd>
                                <?php } ?>
			        <?php if ($this->book->url!='' && ($this->params->get('linkto')==1)){ ?>
			        <dd>
			                <span class="icon-link"></span> <strong><?php echo JText::_('COM_ABOOK_LINK');?>: </strong> <a href="<?php echo $this->book->url;?>"><img src="components/com_abook/assets/images/link/<?php echo $this->params->get('linkimage');?>" alt="<?php echo JText::_('COM_ABOOK_LINK');?>" /> <?php echo $this->book->url_label;?></a>
			        </dd>
			        <?php } ?>
				<?php if ($this->book->note!='' && $this->params->get('show_note', 1)==1){ ?>
                                <dd>
                                        <strong><span class="icon-info"></span> <?php echo JText::_('COM_ABOOK_NOTE');?>: </strong><?php echo $this->book->note;?>
                                </dd>
                                <?php } ?>
			</dl>
		</div>
	</div>
	<?php if(($this->params->get('show_hits', 1)==1) || ($this->params->get('view_rate', 1)==1) || ($this->params->get('show_lend_request', 1)==1) || ($this->params->get('show_lend_availability', 1)==1)){ ?>
	<div class="row-fluid">
		<div class="span4 muted">
			<?php if ($this->params->get( 'show_hits', 1 )==1){ ?>
				<p>
				<span class="icon-eye-open"></span> <strong><?php echo JText::_('COM_ABOOK_HITS');?>: </strong>
				<meta itemprop="interactionCount" content="UserPageVisits:<?php echo $this->book->hits;?>"/><?php echo $this->book->hits;?>
				</p>
			<?php } ?>
			<?php
			$lendstate=isset($this->lend->state)?$this->lend->state:1;
			if ($this->params->get( 'show_lend_availability', 1 )==1) {
	                        switch($lendstate){
        	                        case 0:
                	                        $state="label-important";
                        	                $state_label=JText::_('COM_ABOOK_LEND_LENT');
                                	        break;
					default:
	                                case 1:
        	                                $state="label-success";
                	                        $state_label=JText::_('COM_ABOOK_LEND_AVAILABLE');
                        	                break;
                                	case 2:
                                        	$state="label-warning";
	                                        $state_label=JText::_('COM_ABOOK_LEND_REQUESTED');
        	                                break;
                	        }?>
				<p>
				<span class="icon-share-alt"></span>  <strong><?php echo JText::_('COM_ABOOK_LENT_STATUS');?></strong><span class="label <?php echo $state;?>"><?php echo $state_label;?></span>
				</p>
				<p>
				<div><strong><?php echo JText::_('COM_ABOOK_LEND_AVAILABILITY');?>: </strong><?php echo $this->book->qty - isset($this->lend->lent) - isset($this->lend->requested);?>/<?php echo $this->book->qty;?></div>
				</p>
                	<?php }?>
		</div>
		<div class="span5">
			<?php if ($this->params->get( 'view_rate', 1 )==1) { ?>
				<?php echo JHTML::_('icon.votebook', $this->book, $this->vote);?>	
			<?php } ?>
		</div>
		<?php if ($this->params->get( 'show_lend_request', 1 )==1 && in_array($this->params->get('allow_lend_request'), $this->user->getAuthorisedViewLevels()) && $lendstate == 1) {?>
		<div class="span3">
			<button type="button" class="btn btn-info" data-toggle="collapse" data-target="#collapseRequest">
				<?php echo JText::_( 'COM_ABOOK_LEND_REQUEST' );?> <span class="icon-arrow-right"></span>
			</button>
		</div>
		<?php }?>
	</div>
	<?php if ($this->params->get( 'show_lend_request', 1 )==1 && in_array($this->params->get('allow_lend_request'), $this->user->getAuthorisedViewLevels())) {
        	$uri = JFactory::getURI();
                $uri->setQuery($uri->getQuery().'&hitcount=0');
        ?>
	<div id="collapseRequest" class="row-fluid collapse">
        	<form action="<?php echo $uri->toString();?>" method="post" class="form-horizontal">
			<legend><?php echo JText::_( 'COM_ABOOK_LEND_REQUEST_FORM' );?></legend>
			<?php if ($this->user->guest){?>
				<div class="control-group">
                                	<div class="control-label"><label for="custom_user_name"><?php echo JText::_( 'COM_ABOOK_CUSTOM_USERNAME' );?></label></div>
	                                <div class="controls"><input type="text" name="custom_user_name" value="" /></div>
				</div>  
                	        <div class="control-group">
					<div class="control-label"><label for="custom_user_email"><?php echo JText::_( 'COM_ABOOK_CUSTOM_USEREMAIL' );?></label></div>
                                	<div class="controls"><input type="text" name="custom_user_email" value="" /></div>
	                        </div>
			<?php }else{?>
				<div class="control-group">
                                        <div class="control-label"><label for="custom_user_name"><?php echo JText::_( 'COM_ABOOK_CUSTOM_USERNAME' );?></label></div>
                                        <div class="controls"><?php echo $this->user->name;?></div>
                                </div>
                                <div class="control-group">
                                        <div class="control-label"><label for="custom_user_email"><?php echo JText::_( 'COM_ABOOK_CUSTOM_USEREMAIL' );?></label></div>
                                        <div class="controls"><?php echo $this->user->email;?></div>
                                </div>
			<?php }?>
			<div class="control-group">
				<div class="control-label"><label for="lend_out"><?php echo JText::_( 'COM_ABOOK_LEND_OUT' );?></label></div>
                                <div class="controls"><?php echo JHtml::_("calendar", JHtml::_("date", 'now', 'Y-m-d'), "lend_out", "lend_out",'%Y-%m-%d');?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><label for="lend_in"><?php echo JText::_( 'COM_ABOOK_LEND_IN' );?></label></div>
        	                <div class="controls"><?php echo JHtml::_("calendar", JHtml::_("date", 'now', 'Y-m-d'), "lend_in", "lend_in", '%Y-%m-%d');?></div>
			</div>
			<?php
			if ($this->user->guest){
			JPluginHelper::importPlugin('captcha');
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger('onInit','dynamic_recaptcha_1');
			?>
			<div class="control-group">
                                <div class="control-label"><label for="dynamic_recaptcha_1"><?php echo JText::_( 'COM_ABOOK_CAPTCHA_LABEL' );?></label></div>
				<div class="controls"><div id="dynamic_recaptcha_1"></div></div>
			</div>
			<?php }?>
			<div class="control-group">
				<div class="controls">
		                        <input class="btn" type="submit" name="submit_vote" value="<?php echo JText::_( 'COM_ABOOK_LEND_SEND_REQUEST' );?>" />
				</div>
			</div>
                        <input type="hidden" name="task" value="book.lendrequest" />
			<input type="hidden" name="url" value="<?php echo $uri->toString();?>" />
			<?php echo JHtml::_('form.token'); ?>
                </form>
	</div>
        <?php }?>
	<?php } ?>
	<?php if ($this->book->description!=''){ ?>
	<div class="row-fluid">
                <div class="span12">
			<?php if ($this->book->authorreview!= '' && $this->params->get('show_writtenby')){?>
			<div class="description" itemprop="review" itemscope itemtype="http://schema.org/Review">
			<?php }else{?>
			<div class="description" itemprop="description">
			<?php }?>
				<div>
					<h3><?php echo JText::_('COM_ABOOK_REVIEW');?></h3>
				</div>
				<?php if ($this->book->authorreview!= '' && $this->params->get('show_writtenby')){?>
					<div class="muted createdby"><?php JText::printf( 'COM_ABOOK_WRITTEN_BY', '<span itemprop="reviewBody">'.$this->book->authorreview.'</span>'); ?></div>
					<span itemprop="reviewBody"><?php echo $this->book->description;?></span>
				<?php }else{?>
					<?php echo $this->book->description;?>
				<?php }?>
			</div> 
		</div>
	</div>
	<?php } ?>
	<div class="clr"></div>
	<?php if ($this->params->get('show_tags', 1) && !empty($this->tags)) : ?>
		<?php $this->book->tagLayout = new JLayoutFile('book.tags'); ?>
        	<div class="row-fluid">
                	<div class="span12"><?php echo $this->book->tagLayout->render($this->tags); ?></div>
		</div>
        <?php endif; ?>
		<?php if ($this->book->docsfolder && $this->params->get('show_docs', 0)){
                $t=count($this->docslist);
                ?>
                <h6><?php echo JText::_('COM_ABOOK_DOCUMENTS_DESC');?></h6>
                <div id="documents">
                        <ul class="col1">
                        <?php for($i=0; $i<($t / 2); $i++){?>
                                <li><a href="<?php echo $this->book->docsfolder.'/'.$this->docslist[$i];?>"><?php echo JFile::stripExt($this->docslist[$i]);?></a></li>
                        <?php }?>
                        </ul>
                        <ul class="col2">
                        <?php for($i=($t / 2); $i<$t; $i++){?>
                        <li><a href="<?php echo $this->book->docsfolder.'/'.$this->docslist[$i];?>"><?php echo JFile::stripExt($this->docslist[$i]);?></a></li>
                        <?php }?>
                        </ul>
                        <div class="clr"></div>
                </div>
                <?php }?>
	<div class="row-fluid muted createdate">
		<div class="span12">
			<div class="span8">
				<?php
					if($this->params->get( 'view_date' )==1){?>
					<span class="icon-calendar"></span>
					<?php echo JText::_('COM_ABOOK_DATE_INSERT');?>: <span itemprop="datePublished"><?php echo JHTML::_('date', $this->book->dateinsert, JText::_('DATE_FORMAT_LC1')) ?></span>
				<?php } ?>
			</div>
			<div class="span4">
				<span class="pull-right"><?php echo JHtml::_('credit.credit') ?></span>
			</div>
		</div>
	</div>
	<div class="clr"></div>
	<?php if ($this->params->get( 'display_book_comments',1 ) == 1) {
        	//$jcomments_isenabled=JComponentHelper::getComponent('com_jcomments', true);
                $jcomments_plugin=JFile::exists(JPATH_BASE.DS.'components'.DS.'com_jcomments'.DS.'jcomments.php');
                if ($jcomments_plugin) {
                	include_once(JPATH_BASE.DS.'components'.DS.'com_jcomments'.DS.'jcomments.php');
                        echo '<br />'.JComments::show($this->book->id, 'com_abook', JText::_('COM_ABOOK_CATEGORY') .' '. $this->book->title);
                }
        }?>
</div>
<div class="clr"></div>
