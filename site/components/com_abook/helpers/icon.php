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
jimport('joomla.application.component.helper');
class JHtmlIcon 
{
	static function pdf($book, $params, $access, $attribs = array())
        {
               	$url  = 'index.php?view=book&option=com_abook';
		$url .=  @$book->catid ? '&catid='.$book->catid : '';
		$url .= '&id='.$book->id.'&format=pdf';
               	$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';
                // checks template image directory for image, if non found default are loaded
       	        if ($params->get('show_icons')) {
               	        $text = JHTML::_('image.site', 'pdf_button.png', '/images/M_images/', NULL, NULL, JText::_('PDF'));
                } else {
       	                $text = JText::_('PDF').'&nbsp;';
                }
       	        $attribs['title']       = JText::_( 'PDF' );
               	$attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";
                $attribs['rel']     = 'nofollow';
       	        return JHTML::_('link', JRoute::_($url), $text, $attribs);
       }

	static function print_popup($book, $params, $attribs = array())
        {
                $url  = 'index.php?view=book';
		$url .=  @$book->catid ? '&catid='.$book->catid : '';
		$url .= '&id='.$book->id.'&tmpl=component&print=1&layout=default&page='.@ $request->limitstart;

                $status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';

                // checks template image directory for image, if non found default are loaded
		if ($params->get('show_icons')) {
                        $text = '<span class="icon-print"></span>&#160;'.JText::_('JGLOBAL_PRINT'). '&#160;';
                } else {
                        $text = JText::_('JGLOBAL_ICON_SEP') .'&#160;'. JText::_('JGLOBAL_PRINT') .'&#160;'. JText::_('JGLOBAL_ICON_SEP');
                }

                $attribs['title']       = JText::_( 'Print' );
                $attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";
                $attribs['rel']     = 'nofollow';

                return JHTML::_('link', JRoute::_($url), $text, $attribs);
        }

	static function print_screen($book, $params, $attribs = array(), $legacy = false)
        {
                // checks template image directory for image, if non found default are loaded
                if ( $params->get( 'show_icons' ) ) {
			if ($legacy)
                        {
                                $text = JHtml::_('image', 'system/printButton.png', JText::_('JGLOBAL_PRINT'), null, true);
                        }
                        else
                        {
                                $text = '<span class="icon-print"></span>&#160;' . JText::_('JGLOBAL_PRINT') . '&#160;';
                        }
                } else {
                        $text = JText::_( 'ICON_SEP' ) .'&nbsp;'. JText::_( 'Print' ) .'&nbsp;'. JText::_( 'ICON_SEP' );
                }
                return '<a href="#" onclick="window.print();return false;">'.$text.'</a>';
        }

	public static function search(){
		$searchlink = JRoute::_(AbookHelperRoute::getSearchRoute());
	?>
		<form action="<?php echo $searchlink;?>" method="post" id="searchForm" name="searchForm">
                <?php echo JHTML::_( 'form.token' ); ?>
                <div class="input-append pull-right">
				<input type="text" name="filter[search]" id="filter_search" size="25" maxlength="50" value="" placeholder="<?php echo JText::_('COM_ABOOK_TITLE'); ?>" />
                        	<button type="submit" name="Search" value="<?php echo JText::_( 'COM_ABOOK_SEARCH' );?>" class="btn" />
					<i class="icon-search"></i>
				</button>
				<a href="<?php echo $searchlink; ?>" class="btn hidden-phone"><?php echo JText::_('COM_ABOOK_ADVANCED_SEARCH'); ?> <span class="icon-arrow-right"></span></a>
                </div>
		</form>
	<?php
	}
	
	public static function breadcrumb($category_parent){
	?>
		<ul class="breadcrumb">
		<li><a class="pathway" href="<?php echo JRoute::_(AbookHelperRoute::getCategoryRoute("0:root"));?>"><?php echo JText::_('COM_ABOOK_BACK_TO_TOP'); ?></a> <?php echo '&raquo;';?></li>
        	<?php
	        $n=count($category_parent);
		if ($n>=1){
	        	foreach ($category_parent as $k => $catname){
        	        	echo '<li><a class="pathway" href="' .$catname['link'].'">' .$catname['title'] .'</a></li>';
	                	if ($k!=$n-1) echo ' &raquo; ';
        		}
		}
        	?>
		</ul>
	<?php
	}

	static function votebook($book, $vote)
        {
                $html = '';
                $rating = $vote['rating'];
                $rating_count = $vote['rating_count'];

                $view = JRequest::getString('view', '');
                $img = '';

                // look for images in template if available
                $starImageOn = JHTML::_('image','system/rating_star.png', NULL, NULL, true);
                $starImageOff = JHTML::_('image','system/rating_star_blank.png', NULL, NULL, true);

                for ($i=0; $i < $rating; $i++) {
                	$img .= $starImageOn;
                }
                for ($i=$rating; $i < 5; $i++) {
                	$img .= $starImageOff;
                }
                $html .= '<span class="content_rating" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">';
                $html .= $img .'&nbsp;<span itemprop="ratingValue">'.$vote['rating'].'</span> / ';
                $html .= '<span itemprop="reviewCount">'.count($rating_count).'</span>';
                $html .= "</span>\n";

                if ( $view == 'book' && $book->state == 1){
                	$uri = JFactory::getURI();
                        $uri->setQuery($uri->getQuery().'&hitcount=0');
			$html .= '<form method="post" action="' . htmlspecialchars($uri->toString()) . '">';
                        $html .= '<span class="content_vote">';
                        $html .= JText::_( 'COM_ABOOK_VOTE_POOR' );
                        $html .= '<input type="radio" alt="vote 1 star" name="user_rating" value="1" />';
                        $html .= '<input type="radio" alt="vote 2 star" name="user_rating" value="2" />';
                        $html .= '<input type="radio" alt="vote 3 star" name="user_rating" value="3" />';
                        $html .= '<input type="radio" alt="vote 4 star" name="user_rating" value="4" />';
                        $html .= '<input type="radio" alt="vote 5 star" name="user_rating" value="5" checked="checked" />';
                        $html .= JText::_( 'COM_ABOOK_VOTE_BEST' );
                        $html .= '&#160;<input class="btn" type="submit" name="submit_vote" value="'. JText::_( 'COM_ABOOK_VOTE_RATE' ) .'" />';
                        $html .= '<input type="hidden" name="task" value="book.vote" />';
                        $html .= '<input type="hidden" name="hitcount" value="0" />';
                        $html .= '<input type="hidden" name="url" value="'.  htmlspecialchars($uri->toString()) .'" />';
			$html .= JHtml::_('form.token');
                        $html .= '</span>';
                        $html .= '</form>';
		}
                return $html;
	}

	public static function alphabet(){
		$alphabet=explode(', ',JText::_( 'COM_ABOOK_ALPHABET'));
		$link=JURI::getInstance();
		$selected=$link->getVar('letter');
		$link->delVar('letter');
		echo '<div class="navbar"><div class="navbar-inner"><ul class="nav">';
		echo '<li class="letter">'.JHTML::_('link', $link->toString(), JText::_( 'COM_ABOOK_ALL'), array('onclick'=>"this.form.submit()")).'</li>';
		foreach ($alphabet as $abc){
			$link->setVar('letter',$abc);
			$active=$selected==$abc?"active":'';
			echo '<li class="letter '.$active.'">'.JHTML::_('link', $link->toString(), $abc, array('onclick'=>"this.form.submit()"))."</li>";
                }
		echo '</ul></div></div>';
        }
}
