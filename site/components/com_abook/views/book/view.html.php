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

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.application' );
jimport( 'joomla.application.component.view' );
jimport( 'joomla.filesystem.file' );
class AbookViewBook extends JViewLegacy
{
	protected $state;
        protected $item;
        protected $print;
	protected $params;

	function display($tpl = null)
        {
                // Initialise variables.
                $app = JFactory::getApplication();
                $user = JFactory::getUser();
                $dispatcher = JDispatcher::getInstance();

                // Get model data.
                $state = $this->get('State');
                $item = $this->get('Item');
		$authors = $this->get('Authors');
		$tags = $this->get('Tags');

                // Check for errors.
                if (count($errors = $this->get('Errors'))){
                        JError::raiseWarning(500, implode("\n", $errors));
                        return false;
                }
		
		if($item == false)
                {
                        return JError::raiseWarning(404, JText::_('JGLOBAL_ITEM_NOT_FOUND'));
                }

		$filename= JFile::getName($item->file);
	        $document       = JFactory::getDocument();
                $document->addStyleSheet('components/com_abook/assets/css/style.css');

		JHTML::_('behavior.modal');
                $document->addScriptDeclaration("
                window.addEvent('domready', function() {
                        document.preview = SqueezeBox;
                });");

                // Get view related request variables.
                $print = JRequest::getBool('print');

		//start get params
		//questi sono i parametri libro già sovrascritti a quelli globali
		$params=$item->params;
		//verifica menu attivo
		$active = $app->getMenu()->getActive();
		//se il menu è attivo sovrascrivono i vecchi
		if ($active) {
			//paramtri del menu
			$temp= clone ($state->get('params'));
			//unisci i paramtri vince il menu
			$params->merge($temp);
		}
		if ($layout = $params->get('book_layout')) {
                                $this->setLayout($layout);
                }
		//end get params
		if ($params->get('view_rate', 1)){ 
			$vote = $this->get('Voting');
			$this->assignRef('vote',              $vote);			
		}

		$lend=$params->get('show_lend_availability', 1); 
                if($lend){
                        $this->lend = $this->get('Lend');
                }


		// check if access is not public
                $groups = $user->getAuthorisedViewLevels();

                $return ="";
                if ((!in_array($item->access, $groups)) || (!in_array($item->category_access, $groups))) {
                        $uri            = JFactory::getURI();
                        $return         = (string)$uri;

                                JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
                                return;

                }
		$offset = $state->get('list.offset');
//inizio plugin
		$item->text=$item->description;
		JPluginHelper::importPlugin('content');
                $results = $dispatcher->trigger('onContentPrepare', array ('com_abook.book', &$item, &$this->params, $offset));

		$item->event = new stdClass;
                $results = $dispatcher->trigger('onContentAfterTitle', array('com_abook.book', &$item, &$this->params, $offset));
                $item->event->afterDisplayTitle = trim(implode("\n", $results));

                $results = $dispatcher->trigger('onContentBeforeDisplay', array('com_abook.book', &$item, &$this->params, $offset));
                $item->event->beforeDisplayContent = trim(implode("\n", $results));

                $results = $dispatcher->trigger('onContentAfterDisplay', array('com_abook.book', &$item, &$this->params, $offset));
                $item->event->afterDisplayContent = trim(implode("\n", $results));

		$item->description=$item->text;
//fine plugin
		//incremento il numero delle visite
                $model = $this->getModel();
                $model->hit();

		// Add router helpers.
                $item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
                $item->catslug = $item->category_alias ? ($item->catid . ':' . $item->category_alias) : $item->catid;
                $item->parent_slug = $item->category_alias ? ($item->parent_id . ':' . $item->parent_alias) : $item->parent_id;
		/*foreach ($tags as $tag){
                	$tag->slugtag     = $tag->alias ? ($tag->idtag.':'.$tag->alias) : $tag->idtag;
                }*/
		/*foreach ($authors as $author){
                        $author->slugauthor     = !empty($author->alias) ? ($author->author_id.':'.$author->alias) : $author->author_id;
                }*/
		$item->authorreview = $item->created_by_alias==''?$item->written_by:$item->created_by_alias;
		JHtml::_('behavior.formvalidation');


		if ($params->get( 'breadcrumb' ) != 0){
                        $categories = $this->get('Categories');
                        $category = $this->get('Category');
                        $parent = $this->get('Parent');
                        $this->assignRef('categories',        $categories);
                        $this->assignRef('category',  $category);
                        $this->assignRef('parent',      $parent);
                }

                $this->assignRef('params',	$params);
                $this->assignRef('return',      $return);
                $this->assignRef('state',	$state);
                $this->assignRef('book',	$item);
                $this->assignRef('user',	$user);
		$this->assignRef('authors',	$authors);
		$this->assignRef('tags',	$tags);
		$this->assignRef('filename',    $filename);
		$this->assignRef('print',    $print);
		
		if ($this->book->docsfolder != ''){
                        $this->docslist         = JFolder::files(JPATH_ROOT.DS.$this->book->docsfolder.DS, '', '', '', array('.svn', 'CVS','.DS_Store','__MACOSX', 'index.html'));
                }

                $this->_prepareDocument();

                parent::display($tpl);
        }

	protected function _prepareDocument()
        {
                $app            = JFactory::getApplication();
                $menus          = $app->getMenu();
                $pathway        = $app->getPathway();
                $title          = null;

		$menus  = $app->getMenu();
                $menu   = $menus->getActive();

                if ($menu){
                        $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
                }else{
                        $this->params->def('page_heading', $this->params->def('comp_name'));
                }
	if ($this->params->get( 'breadcrumb' ) != 0){
		$path= null;
		$path[] = array('title' => $this->book->category_title, 'link' => AbookHelperRoute::getCategoryRoute($this->book->catslug));
		$category=$this->category->getParent();
		/*if ($category->id!="root"){
			while($category->id > 1){
				$path[] = array('title' => $category->title, 'link' => AbookHelperRoute::getCategoryRoute($category->id.':'.$category->alias));
				$category = $category->getParent();
			}
			if ($category!=''){
				$path = array_reverse($path);
				foreach ($path as $item){
					$pathway->addItem($item['title'], $item['link']);
				}
			}
		}
		$pathway->addItem($this->book->title);*/

//suggerito da evpadallas http://forum.voxpopulix.org/errori-(bugs)/not-working-sef_advanced_link/
		if ($category->id != "root"){
			while($category->id > 1){
				$path[] = array('title' => $category->title, 'link' => AbookHelperRoute::getCategoryRoute($category->id.':'.$category->alias));
				$category = $category->getParent();
			}
		}

		if ($category != ''){
			$path = array_reverse($path);
			foreach ($path as $item){
				$pathway->addItem($item['title'], $item['link']);
			}
		}			
		if ($this->params->get( 'breadcrumb' ) == 1) {
			$pathway->addItem($this->book->title);
		}
//-----------------------------------------------------------

		$this->assignRef('path', $path);
	}
		$title = $this->params->get('page_title', '');
                if (empty($title)) {
			$title = $this->book->cat_title." - ".$this->book->title;
		}elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
                        $title = $this->params->get('comp_name').' - '.$this->book->category_title." - ".$this->book->title;
		}elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
                        $title = $this->book->category_title." - ".$this->book->title.' - '.$this->params->get('comp_name');
                }else{
                        $title = $this->params->get('page_title', '').' - '.$this->book->category_title." - ".$this->book->title;
                }
                $this->document->setTitle($title);

                if ($this->book->metadesc){
                        $this->document->setDescription($this->book->metadesc);
                }else if (!$this->book->metadesc && $this->params->get('menu-meta_description')){
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

                if ($this->book->metakey){
                        $this->document->setMetadata('keywords', $this->book->metakey);
                }else if (!$this->book->metakey&& $this->params->get('menu-meta_keywords')){
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}


		if ($this->print){
                        $this->document->setMetaData('robots', 'noindex, nofollow');
                }else{
			if ($this->params->get('robots'))
                	{
                        	$this->document->setMetadata('robots', $this->params->get('robots'));
                	}
		}

		if ($app->getCfg('MetaAuthor') == '1'){
                        $this->document->setMetaData('author', $this->book->authorreview);
                }

                $mdata = $this->book->metadata->toArray();

                foreach ($mdata as $k => $v){
                        if ($v){
                                $this->document->setMetadata($k, $v);
                        }
                }
        }
}
