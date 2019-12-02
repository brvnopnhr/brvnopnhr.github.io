<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
jimport( 'joomla.html.html.sliders' );
class AbookViewCategories extends JViewLegacy
{
	protected $state = null;
	protected $item = null;
	protected $items = null;
	protected $parent = null;
	protected $params = null;

	/**
	 * Display the view
	 *
	 * @return	mixed	False on error, null otherwise.
	 */
	function display($tpl = null)
	{
		                // Initialise variables
                $state          = $this->get('State');
                $items          = $this->get('Items');
		$this->parent           = $this->get('Parent');
                /*if ($state->get('id') > 0){
                        $this->parent           = $this->get('Parent');
                }else {
                        $this->parent->description = $state->params->get('categories_description');
                }*/
                $pagination          = $this->get('Pagination');
                $document       = JFactory::getDocument();
                $document->addStyleSheet('components/com_abook/assets/css/style.css');

                // Check for errors.
                if (count($errors = $this->get('Errors'))) {
                        JError::raiseWarning(500, implode("\n", $errors));
                        return false;
                }

                if ($items === false) {
                        return JError::raiseWarning(404, JText::_('JGLOBAL_CATEGORY_NOT_FOUND'));

                }
                /*if ($parent == false) {
                        return JError::raiseWarning(404, JText::_('JGLOBAL_CATEGORY_NOT_FOUND'));
                }*/

                $params = $state->params;

                for ($i = 0, $n = count($items); $i < $n; $i++) {
                        $item = &$items[$i];
                        // Convert parameter fields to objects.
                        $registry = new JRegistry;
                        $registry->loadString($item->params);
                        $item->params = clone $params;
                        $item->params->merge($registry);
                }
                //$items = array($parent->id => $items);

                $this->assign('maxLevel',       $params->get('maxLevel', -1));
                $this->assignRef('params',              $params);
                $this->assignRef('items',               $items);
                $this->assignRef('pagination',               $pagination);
                $this->_prepareDocument();

                parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$title	= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('COM_ABOOK_DEFAULT_PAGE_TITLE'));
		}
		$title = $this->params->get('page_title', '');
		if (empty($title)) {
			$title = htmlspecialchars_decode($app->getCfg('sitename'));
		}
		elseif ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', htmlspecialchars_decode($app->getCfg('sitename')), $title);
		}
		$this->document->setTitle($title);
		if ($this->params->get('menu-meta_keywords')){
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}
		if ($this->params->get('robots'))
                {
                        $this->document->setMetadata('robots', $this->params->get('robots'));
                }
		if ($this->params->get('menu-meta_description'))
                {
                        $this->document->setDescription($this->params->get('menu-meta_description'));
                }
	}
}
