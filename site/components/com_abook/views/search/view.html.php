<?php

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class AbookViewSearch extends JViewLegacy
{
	protected $state;
	protected $items;
	protected $category;
	protected $categories;
	protected $pagination;

	function display($tpl = null)
	{
		$app		= JFactory::getApplication();

		// Get some data from the models
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->total            = $this->get('Total');
		$this->params = $this->state->get('params');
		// Prepare the data.
                // Compute the item slug.
                for ($i = 0, $n = count($this->items); $i < $n; $i++)
                {
                        $item           = &$this->items[$i];
                        $item->slug     = $item->alias ? ($item->id.':'.$item->alias) : $item->id;
                        $item->slugcat     = $item->catalias ? ($item->catid.':'.$item->catalias) : $item->catid;
                        foreach ($item->authors as $author){
                                $author->slugauthor     = $author->alias ? ($author->idauthor.':'.$author->alias) : $author->idauthor;
                        }
                        foreach ($item->tags as $tag){
                                $tag->slugtag     = $tag->alias ? ($tag->tag_id.':'.$tag->alias) : $tag->tag_id;
                        }
                }

		$this->pagination	= $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
                $this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

/*		if($category == false)
		{
			return JError::raiseWarning(404, JText::_('JGLOBAL_CATEGORY_NOT_FOUND'));
		}
		if($parent == false)
		{
			return JError::raiseWarning(404, JText::_('JGLOBAL_CATEGORY_NOT_FOUND'));
		}
*/
		$document       = JFactory::getDocument();
                $document->setTitle( $this->params->get( 'page_title' ));
                $document->addStyleSheet('components/com_abook/assets/css/style.css');

		//$this->maxLevel	= $params->get('maxLevel', -1);

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

		$title = $this->params->get('page_title', '');
		if (empty($title)) {
			$title = htmlspecialchars_decode($app->getCfg('sitename'));
		}
		elseif ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', htmlspecialchars_decode($app->getCfg('sitename')), $title);
		}
		$this->document->setTitle($title);


                $this->document->setDescription($this->params->get('menu-meta_description'));

                $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));

		if ($this->params->get('robots')){
                        $this->document->setMetadata('robots', $this->params->get('robots'));
                }
		if (!$menu){
                        if ($layout = $params->get('search_layout')) {
                                $this->setLayout($layout);
                        }
                }else {
                        // We need to set the layout from the query in case this is an alternative menu item (with an alternative layout)
                        $this->setLayout($menu->params->get('search_layout'));
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
