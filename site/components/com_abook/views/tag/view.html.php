<?php

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
jimport('joomla.mail.helper');

class AbookViewTag extends JViewLegacy
{
	protected $state;
	protected $items;
	protected $tag;
	protected $pagination;

	function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$params		= $app->getParams();
		$dispatcher = JDispatcher::getInstance();

		// Get some data from the models
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$tag		= $this->get('Tag');
		$pagination	= $this->get('Pagination');
		
		$document       = JFactory::getDocument();
                $document->setTitle( $params->get( 'page_title' )." - ".$tag->name);
		$document->addStyleSheet('components/com_abook/assets/css/style.css');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		if($tag == false)
		{
			return JError::raiseWarning(404, JText::_('COM_ABOOK_NO_RESULT'));
		}

		// Prepare the data.
		// Compute the item slug.
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$item		= $items[$i];
			$item->slug	= $item->alias ? ($item->id.':'.$item->alias) : $item->id;
			$item->slugcat     = $item->catalias ? ($item->catid.':'.$item->catalias) : $item->catid;
			foreach ($item->tags as $booktag){
				$booktag->slugtag = $booktag->alias ? ($booktag->tag_id.':'.$booktag->alias) : $booktag->tag_id;
			}
		}

		$this->assignRef('state',		$state);
		$this->assignRef('items',		$items);
		$this->assignRef('tag',			$tag);
		$this->assignRef('params',		$params);
		$this->assignRef('pagination',		$pagination);

		//inizio plugin
                $tag->text=$tag->description;
                JPluginHelper::importPlugin('content');
                $results = $dispatcher->trigger('onContentPrepare', array ('com_abook.tag', &$tag, &$this->params, 0));
                $tag->description=$tag->text;
		//fine plugin

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
		if($menu && $menu->query['view'] != 'book' && $id != $this->tag->id)
		{
			$this->params->set('page_subheading', $this->tag->name);
			$link = JRoute::_('index.php?option=com_abook&view=tag&id='.$this->tag->id);
			if ($this->params->get('breadcrumb')==1){
				$pathway->addItem($this->tag->name, $link);
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

		if ($this->tag->metadesc) {
			$this->document->setDescription($this->tag->metadesc);
		}else if (!$this->tag->metadesc && $this->params->get('menu-meta_description')){
                        $this->document->setDescription($this->params->get('menu-meta_description'));
                }
		if ($this->tag->metakey) {
			$this->document->setMetadata('keywords', $this->tag->metakey);
		}else if (!$this->tag->metakey&& $this->params->get('menu-meta_keywords')){
                        $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
                }

		if ($this->params->get('robots')){
                        $this->document->setMetadata('robots', $this->params->get('robots'));
                }

		if ($app->getCfg('MetaTitle') == '1') {
			$this->document->setMetaData('title', $this->tag->name);
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
