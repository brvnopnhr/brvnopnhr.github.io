<?php

// no direct access
defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
//require_once("/var/www/joomla3new/libraries/legacy/categories/categories.php");
abstract class AbookHelperRoute
{
	protected static $lookup;
	/**
	 * @param	int	The route of the newsfeed
	 */
	public static function getBookRoute($id, $catid = 0, $language = 0)
	{
		$needles = array(
			'book'  => array((int) $id)
		);
		//Create the link
		$link = 'index.php?option=com_abook&view=book&id='. $id;
		if ($catid > 1)
		{
			$categories = JCategories::getInstance('Abook');
			$category = $categories->get($catid);
			if ($category) {
				$needles['category'] = array_reverse($category->getPath());
				$needles['category'][]=0;//aggiunto perchè il menu categories ha id 0 e non combacia mai in _findItem con alcun id categoria perchè non esiste una categoria con id 0. Stesso problema per com_content, controllare se lo risolvono.
				$needles['categories'] = $needles['category'];
				$link .= '&catid='.$catid;
			}
		}
		
		if ($language && $language != "*" && JLanguageMultilang::isEnabled())
                {
                        self::buildLanguageLookup();

                        if (isset(self::$lang_lookup[$language]))
                        {
                                $link .= '&lang=' . self::$lang_lookup[$language];
                                $needles['language'] = $language;
                        }
                }
		if ($item = self::_findItem($needles)) {
                        $link .= '&Itemid='.$item;
                }

		return $link;
	}

	public static function getCategoryRoute($catid, $language = 0)
	{
		if ($catid instanceof JCategoryNode){
                        $id = $catid->id;
                        $category = $catid;
                }else{
                        $id = (int) $catid;
                        $category = JCategories::getInstance('Abook')->get($id);
                }
                if($id <= 1){
			$needles = array(
                                'categories' => array($id)
                        );
			if ($item = self::_findItem($needles)) {
                                $link = 'index.php?Itemid='.$item;
                        } else {
                        	$link = 'index.php?option=com_abook&view=categories&id=1';
			}
                }else{
                        $needles = array();
                              //Create the link
                                $link = 'index.php?option=com_abook&view=category&id='.$id;
                                        $catids = array_reverse($category->getPath());
					$catids[] = "0:root";
                                        $needles = array(
                                                'category' => $catids,
                                                'categories' => $catids
                                        );
					if ($language && $language != "*" && JLanguageMultilang::isEnabled())
                        		{
                                		self::buildLanguageLookup();

                                		if(isset(self::$lang_lookup[$language]))
                                		{
                                        		$link .= '&lang=' . self::$lang_lookup[$language];
                                        		$needles['language'] = $language;
                                		}
                        		}
                                        if ($item = self::_findItem($needles)) {
                                                $link .= '&Itemid='.$item;
                                        }
                }
                return $link;
	}

        public static function getAuthorRoute($id, $language = 0)
        {
                $needles = array(
                        'author'  => array((int) $id),
			'authors'  => array(0)
                );
                //Create the link
                $link = 'index.php?option=com_abook&view=author&id='. $id;
		$app = JFactory::getApplication('site');
                $params = $app->getParams();

		$itemid= $params->get("author_itemid");
                if ($itemid!=''){
                        $link .= '&Itemid='.$itemid;
                }elseif ($item = self::_findItem($needles)) {
                        $link .= '&Itemid='.$item;
                }

                return $link;
        }

	public static function getAuthorsRoute($language = 0)
        {
                $needles = array(
                        'authors' => array(0)
                );
                //Create the link
                $link = 'index.php?option=com_abook&view=authors';
                $app = JFactory::getApplication('site');
                $params = $app->getParams();

                if ($item = self::_findItem($needles)) {
                        $link .= '&Itemid='.$item;
                }

                return $link;
        }

	public static function getTagRoute($id, $language = 0)
        {
                $needles = array(
                        'tag' => array((int) $id),
			'tags' => array(0)
                );
                //Create the link
                $link = 'index.php?option=com_abook&view=tag&id='.$id;
		$app = JFactory::getApplication('site');
                $params = $app->getParams();

		$itemid= $params->get("tag_itemid");
		if ($itemid!=''){
                        $link .= '&Itemid='.$itemid;
                }elseif ($item = self::_findItem($needles)) {
                        $link .= '&Itemid='.$item;
                }

                return $link;
        }

	public static function getTagsRoute($language = 0)
        {
                $needles = array(
                        'search' => array(0)
                );
                //Create the link
                $link = 'index.php?option=com_abook&view=tags';
                $app = JFactory::getApplication('site');
                $params = $app->getParams();

                if ($item = self::_findItem($needles)) {
                        $link .= '&Itemid='.$item;
                }

                return $link;
        }
	public static function getSearchRoute($language = 0)
        {
		$needles = array(
                        'search' => array(0)
                );
                //Create the link
                $link = 'index.php?option=com_abook&view=search';
                $app = JFactory::getApplication('site');
                $params = $app->getParams();

                if ($item = self::_findItem($needles)) {
                        $link .= '&Itemid='.$item;
                }

                return $link;
        }

	protected static function _findItem($needles=null)
	{
		$app    = JFactory::getApplication();
                $menus  = $app->getMenu('site');
		$language       = isset($needles['language']) ? $needles['language'] : '*';

                // Prepare the reverse lookup array.
                if (!isset(self::$lookup[$language])){
                        self::$lookup[$language] = array();
                        $component      = JComponentHelper::getComponent('com_abook');
			$attributes = array('component_id');
                        $values = array($component->id);

                        if ($language != '*')
                        {
                                $attributes[] = 'language';
                                $values[] = array($needles['language'], '*');
                        }
                        $items          = $menus->getItems($attributes, $values);

                        foreach ($items as $item){
                                if (isset($item->query) && isset($item->query['view'])){
                                        $view = $item->query['view'];
                                        if (!isset(self::$lookup[$language][$view])) {
                                                self::$lookup[$language][$view] = array();
                                        }
					// Only match menu items that list one tag
                                	if (isset($item->query['id']))
                                	{
						if (!isset(self::$lookup[$language][$view][$item->query['id']]) || $item->language != '*')
                                        	{
                                        		self::$lookup[$language][$view][$item->query['id']] = $item->id;
                                        	}
					}else{
						self::$lookup[$language][$view][0] = $item->id;
					}
                                }
                        }
                }

                if ($needles){
                        foreach ($needles as $view => $ids){
                                if (isset(self::$lookup[$language][$view])){
                                        foreach($ids as $id){
						//$id= explode(":", $id);
                                                if (isset(self::$lookup[$language][$view][(int)$id])) {
                                                        return self::$lookup[$language][$view][(int)$id];
                                                }
                                        }
                                }
                        }
                }

		// Check if the active menuitem matches the requested language
                $active = $menus->getActive();
                if ($active && $active->component == 'com_abook' && ($language == '*' || in_array($active->language, array('*', $language)) || !JLanguageMultilang::isEnabled()))
                {
                        return $active->id;
                }

                // If not found, return language specific home link
                $default = $menus->getDefault($language);
                return !empty($default->id) ? $default->id : null;
	}
}
