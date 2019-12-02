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

jimport( 'joomla.application.component.view' );

class AbookViewAuthor extends JViewLegacy
{
	protected $state;
        protected $items;
	protected $pagination;

        function display($tpl = null)
        {
	// Initialise variables.
                $app = JFactory::getApplication();
		$params         = $app->getParams();
                $user = JFactory::getUser();
                $dispatcher = JDispatcher::getInstance();
		// Get model data.
                $state = $this->get('State');
                $items = $this->get('Items');
//print_r($items);
                $author = $this->get('Author');
                $categories = $this->get('Categories');
		$category       = $this->get('Category');
                $parent = $this->get('Parent');
		$pagination     = $this->get('Pagination');

		$document       = JFactory::getDocument();
                $document->setTitle( $params->get( 'page_title' )." - ".$author->author_name);
                $document->addStyleSheet('components/com_abook/assets/css/style.css');


                // Check for errors.
                // @TODO Maybe this could go into JComponentHelper::raiseErrors($this->get('Errors'))
                if (count($errors = $this->get('Errors')))
                {
                        JError::raiseWarning(500, implode("\n", $errors));
                        return false;
                }
		/*if($category == false)
                {
                        return JError::raiseWarning(404, JText::_('JGLOBAL_CATEGORY_NOT_FOUND'));
                }

                if($parent == false)
                {
                        return JError::raiseWarning(404, JText::_('JGLOBAL_CATEGORY_NOT_FOUND'));
                }*/
// Check whether category access level allows access.
                $user   = JFactory::getUser();
                $groups = $user->getAuthorisedViewLevels();
                /*if (!in_array($category->access, $groups)) {
                        return JError::raiseError(403, JText::_("JERROR_ALERTNOAUTHOR"));
                }*/
		for ($i = 0, $n = count($items); $i < $n; $i++)
                {
                        $item           = &$items[$i];
                        $item->slug     = $item->alias ? ($item->id.':'.$item->alias) : $item->id;
			$item->slugcat     = $item->category_alias ? ($item->catid.':'.$item->category_alias) : $item->catid;
		}	

                $this->assignRef('params',              $params);
                //$this->assignRef('return',              $return);
                $this->assignRef('state', $state);
                $this->assignRef('items', $items);
                $this->assignRef('user', $user);
                $this->assignRef('author', $author);
                $this->assignRef('categories', $categories);
                $this->assignRef('parent',              $parent);
		$this->assignRef('pagination', $pagination);

		$offset = $this->state->get('list.offset');
//inizio plugin
                $author->text=$author->description;
                JPluginHelper::importPlugin('content');
                $results = $dispatcher->trigger('onContentPrepare', array ('com_abook.author', &$author, &$this->params, $offset));
                $author->description=$author->text;
//fine plugin

                $this->_prepareDocument();

                parent::display($tpl);
        }
	protected function _prepareDocument()
        {
                $app            = JFactory::getApplication();
                $menus          = $app->getMenu();
                $pathway        = $app->getPathway();
                $title          = null;
		$menu = $menus->getActive();
		if($menu)
                {
                        $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
                } else {
                        $this->params->def('page_heading', JText::_('COM_ABOOK_DEFAULT_PAGE_TITLE'));
                }
                $this->document->setTitle($this->author->author_name);

                if ($this->author->metadesc)
                {
                        $this->document->setDescription($this->author->metadesc);
                }else if (!$this->author->metadesc && $this->params->get('menu-meta_description')){
                        $this->document->setDescription($this->params->get('menu-meta_description'));
                }


                if ($this->author->metakey)
                {
                        $this->document->setMetadata('keywords', $this->author->metakey);
                }else if (!$this->author->metakey&& $this->params->get('menu-meta_keywords')){
                        $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
                }
		if ($this->params->get('robots'))
                {
                        $this->document->setMetadata('robots', $this->params->get('robots'));
                }
                if ($this->params->get( 'breadcrumb' ) == 1) {
                        $pathway->addItem($this->author->author_name, '');
                }

        }
		
}
