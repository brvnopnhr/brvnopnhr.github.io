<?php

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
jimport('joomla.mail.helper');

class AbookViewAuthors extends JViewLegacy
{
	protected $state;
	protected $items;
	protected $pagination;

	function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$params		= $app->getParams();

		// Get some data from the models
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$document       = JFactory::getDocument();
                $document->setTitle( $params->get( 'page_title' ));
                $document->addStyleSheet('components/com_abook/assets/css/style.css');

		// Prepare the data.
		// Compute the item slug.
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$item		= &$items[$i];
			$item->slug	= $item->alias ? ($item->id.':'.$item->alias) : $item->id;
		}

		$this->assignRef('state',		$state);
		$this->assignRef('items',		$items);
		$this->assignRef('params',		$params);
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

		if ($app->getCfg('MetaTitle') == '1') {
			$this->document->setMetaData('title', $menu->title);
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
