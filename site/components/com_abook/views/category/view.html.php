<?php

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
//jimport('joomla.mail.helper');

class AbookViewCategory extends JViewLegacy
{
	protected $state;
	protected $items;
	protected $category;
	protected $categories;
	protected $pagination;

	function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$params		= $app->getParams();

		// Get some data from the models
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$author		= $this->get('Author');
		$category	= $this->get('Category');
		$children	= $this->get('Children');
		$parent 	= $this->get('Parent', '', array());
		$pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		if($category == false)
		{
			return JError::raiseWarning(404, JText::_('JGLOBAL_CATEGORY_NOT_FOUND'));
		}

		/*if($parent == false)
		{
			return JError::raiseWarning(404, JText::_('JGLOBAL_CATEGORY_NOT_FOUND'));
		}*/

		// Setup the category parameters.
                $cparams = $category->getParams();
                $category->params = clone($params);
                $category->params->merge($cparams);

		$document       = JFactory::getDocument();
                $document->setTitle( $params->get( 'page_title' )." - ".$category->title);
                $document->addStyleSheet('components/com_abook/assets/css/style.css');

		// Check whether category access level allows access.
		$user	= JFactory::getUser();
		$groups	= $user->getAuthorisedViewLevels();
		if (!in_array($category->access, $groups)) {
			return JError::raiseError(403, JText::_("JERROR_ALERTNOAUTHOR"));
		}

		// Prepare the data.
		// Compute the item slug.
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$item		= &$items[$i];
			$item->slug	= $item->alias ? ($item->id.':'.$item->alias) : $item->id;
			$item->slugcat     = $item->catalias ? ($item->catid.':'.$item->catalias) : $item->catid;
			foreach ($item->authors as $author){
				$author->slugauthor     = $author->alias ? ($author->idauthor.':'.$author->alias) : $author->idauthor;
			}
			foreach ($item->tags as $tag){
                                $tag->slugtag     = $tag->alias ? ($tag->tag_id.':'.$tag->alias) : $tag->tag_id;
                        }
		}
		$children = array($category->id => $children);
//inizio plugin
                $category->text=$category->description;
                JPluginHelper::importPlugin('content');
		$dispatcher     = JDispatcher::getInstance();
                $results = $dispatcher->trigger('onContentPrepare', array ('com_abook.category', &$category, &$this->params, 0));
                $category->description=$category->text;
//fine plugin
		if ($layout = $category->params->get('category_layout')) {
                        $this->setLayout($layout);
                }

		$this->maxLevel	= $params->get('maxLevel', -1);
		$this->assignRef('state',		$state);
		$this->assignRef('items',		$items);
		$this->assignRef('category',	$category);
		$this->assignRef('children',	$children);
		$this->assignRef('params',		$params);
		$this->assignRef('parent',		$parent);
		$this->assignRef('pagination',	$pagination);

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title 		= null;


//--------------------------
		$path= null;
		$path[] = array('title' => $this->category->title, 'link' => AbookHelperRoute::getCategoryRoute($this->category->id));
		$category=$this->category->getParent();
		if (isset($category->id) && $category->id!="root"){
                        while(@$category->id > 1){
                                $path[] = array('title' => $category->title, 'link' => AbookHelperRoute::getCategoryRoute($category->id));
                                $category = $category->getParent();
                        }
                        if ($category!=''){
                                $path = array_reverse($path);
                        }
                }
		$this->assignRef('path', $path);
//--------------------------


		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('COM_ABOOK_DEFAULT_PAGE_TITLE'));
		}
		$id = (int) @$menu->query['id'];
		if($menu && $menu->query['view'] != 'book' && $id != $this->category->id)
		{
			$this->params->set('page_subheading', $this->category->title);
			$category = $this->category->getParent();
			foreach ($this->path as $item){
                                        $pathway->addItem($item['title'], $item['link']);
                        }
		}

		$title = $this->params->get('page_title', '');
		if (empty($title)) {
			$title = htmlspecialchars_decode($app->getCfg('sitename'));
		}
		elseif ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', htmlspecialchars_decode($app->getCfg('sitename')), $title);
		}
		$this->document->setTitle($title);


		if ($this->category->metadesc) {
			$this->document->setDescription($this->category->metadesc);
		}elseif (!$this->category->metadesc && $this->params->get('menu-meta_description')){
                        $this->document->setDescription($this->params->get('menu-meta_description'));
                }

		if ($this->category->metakey) {
			$this->document->setMetadata('keywords', $this->category->metakey);
		}elseif (!$this->category->metakey && $this->params->get('menu-meta_keywords')){
                        $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
                }

		if ($this->params->get('robots')){
                        $this->document->setMetadata('robots', $this->params->get('robots'));
                }

		if ($app->getCfg('MetaTitle') == '1') {
			$this->document->setMetaData('title', $this->category->getMetadata()->get('page_title'));
		}

		if ($app->getCfg('MetaAuthor') == '1') {
			$this->document->setMetaData('author', $this->category->getMetadata()->get('author'));
		}

		$mdata = $this->category->getMetadata()->toArray();

		foreach ($mdata as $k => $v) {
			if ($v) {
				$this->document->setMetadata($k, $v);
			}
		}

		// Add alternate feed link
		if ($this->params->get('show_feed_link', 1) == 1)
		{
			$link	= '&format=feed&limitstart=';
			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			$this->document->addHeadLink(JRoute::_($link.'&type=rss'), 'alternate', 'rel', $attribs);
			$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
			$this->document->addHeadLink(JRoute::_($link.'&type=atom'), 'alternate', 'rel', $attribs);
		}
	}
}
