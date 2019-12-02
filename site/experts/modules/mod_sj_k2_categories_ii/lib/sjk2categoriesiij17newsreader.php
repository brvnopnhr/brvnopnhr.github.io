<?php
/*
 * @package Sj K2 Categories II
 * @version 3.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2013 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */
 
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if (!class_exists('SjK2CategoriesIIJ17NewsReader')){
	class SjK2CategoriesIIJ17NewsReader{
		
		public  $db	;//= 	JFactory::getDBO();
		
		public function __construct(){
			require_once ( JPath::clean(JPATH_SITE.'/components/com_k2/helpers/route.php') );
			class_exists('YtUtils') or include_once 'ytutils.php';
		}
		
		public function getRootCategories($catIds,$params){
			$categories = array();
			
			$query = "
				SELECT c.id, c.name as title, c.alias, c.description, c.image as img
				FROM #__k2_categories c
				WHERE
					c.published = 1
					AND c.id IN ($catIds)
			";
			
			$this->db->setQuery($query);
			$rows = $this->db->loadObjectList();
			
			// prepare for image resize.
			$imgResizeConfig = array(
				'background' => $params->item_thumbnail_background,
				'thumbnail_mode' => $params->item_thumbnail_mode
			);
			YtUtils::getImageResizerHelper($imgResizeConfig);
			
			foreach($rows as $category){
				$categories[$category->id] = $category;
				
				// category url
				$category->url = urldecode(JRoute::_(K2HelperRoute::getCategoryRoute($category->id.':'.urlencode($category->alias))));
				// category title truncate
				$category->title 		= YtUtils::shorten($category->title, $params->title_max_chars);
				// $category->image_urls 	= YtUtils::extractImages(& $category->description);
				
				if (!empty($category->img) && (file_exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'categories'.DS.$category->img))){
					$category->image_urls = array('media'.DS.'k2'.DS.'categories'.DS.$category->img);
					YtUtils::extractImages($category->description);
				} else {
					$category->image_urls = YtUtils::extractImages( $category->description);
				}
				
				$category->description	= strip_tags($category->description);
				
				if ('none' == $params->item_thumbnail_mode){
					$category->image = empty($category->image_urls) ? 
											JURI::base().'modules/mod_sj_k2_categories_ii/assets/images/nophoto.gif' : 
											$category->image_urls[0];
				} else {
					$category->image = empty($category->image_urls) ? 
											JURI::base().'modules/mod_sj_k2_categories_ii/assets/images/nophoto.gif' : 
											$category->image_urls[0];
					if (false != $category->image && !YtUtils::isUrl($category->image)){
						$imagefile 			= JPath::find(JPATH_SITE, $category->image);
						$category->image 	= YtUtils::resize($imagefile, $params->item_thumbnail_width, $params->item_thumbnail_height, $params->item_thumbnail_mode);
						
						if (! $category->image){
							$category->image	= JURI::base().'modules/mod_sj_k2_categories_ii/assets/images/nophoto.gif';
						}
					}
				}
			}
			
			return $categories;
		}
		
		public function getChildCategories($catId,$titleMax){
			$categories = array();
			
			$query = "
				SELECT c.id, c.name as title, c.alias
				FROM #__k2_categories c
				WHERE
					c.published = 1
					AND c.parent = $catId
			";
			
			$this->db->setQuery($query);
			$rows = $this->db->loadObjectList();
			
			foreach($rows as $category){
				$categories[$category->id] = $category;
				
				// // category url
				// $slug = $category->alias ? $category->id.':'.$category->alias : $category->id;
				// $category->url = JRoute::_( ContentHelperRoute::getCategoryRoute($slug) );
				$category->url = urldecode(JRoute::_(K2HelperRoute::getCategoryRoute($category->id.':'.urlencode($category->alias))));
				// category title truncate
				$category->title = YtUtils::shorten($category->title, $titleMax);
			}
			
			return $categories;
		}
		
		public function getList(&$params){
			$this->db   		=	JFactory::getDBO();
			
			// $categories = array();
			
			// $child_category_ids = array();
			
			// $user 		=&	JFactory::getUser();
			// $app 		=&	JFactory::getApplication();
			// $db   		=&	JFactory::getDBO();
			// $jnow		=& 	JFactory::getDate();
			// $now		= 	$jnow->toMySQL();
			// $nullDate	=	$db->getNullDate();
			// $noauth		= !	$app->getParams()->get('show_noauth');
		
			// $params->source must be an array of IDs
			if (!isset($params->source) || empty($params->source)){
				$this->errors = "No selected or all selected is unpublished.";
				return array();
			}
			$category_ids = is_array($params->source) ? $params->source : array($params->source);
			$category_ids_sql = implode(',', $category_ids);
			
			// var_dump( $this->getRootCategories($category_ids_sql,$params->title_max_chars));
			
			//list result
			$list = $this->getRootCategories($category_ids_sql,$params);
			
			foreach ($list as $cid => $category){
				$listLev1	= $this->getChildCategories($cid, $params->title_max_chars);
				foreach ($listLev1 as $cidLev1 => $categoryLev1){
					$listLev2	= $this->getChildCategories($cidLev1, $params->title_max_chars);
					foreach ($listLev2 as $cidLev2 => $categoryLev2){
						$listLev3	= $this->getChildCategories($cidLev2, $params->title_max_chars);
						
						$listLev2[$cidLev2]->childCat = $listLev3;
					}
					$listLev1[$cidLev1]->childCat = $listLev2;
				}
				$list[$cid]->childCat	= $listLev1;
			}
			
			return $list;
			
		}
	}
}