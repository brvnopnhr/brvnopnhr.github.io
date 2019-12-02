<?php
/*
 * @package Sj K2 Categories II
 * @version 3.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2013 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

//check the exist of k2 component on the site?

if(file_exists(JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'route.php') && file_exists(JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'utilities.php')){

	defined( 'YT_MODULE_CACHE' ) or define('YT_MODULE_CACHE', JPATH_CACHE . DS . $module->module);

	jimport("joomla.filesystem.folder");
	jimport("joomla.filesystem.file");

	class_exists('SjK2CategoriesII') or require_once (dirname(__FILE__). DS . 'lib' . DS . 'sjk2categoriesii.php');

	// assets import
	$assets_url = 'modules/'.$module->module.'/assets/';

	if (!defined('SMART_JQUERY') && ( int ) $params->get ( 'include_jquery', '1' )) {
		JHtml::script('modules/'.$module->module.'/assets/js/jquery-1.8.2.min.js');
		JHtml::script('modules/'.$module->module.'/assets/js/jquery-noconflict.js');
		define('SMART_JQUERY', 1);
	}


	if (!defined('SMART_K2_CATEGORIESII_ASSETS')){
		
		JHtml::stylesheet('modules/'.$module->module.'/assets/css/category.css');

		define('SMART_K2_CATEGORIESII_ASSETS', 1);
	}



	if (JRequest::getCmd('dev')=='1'){
		$params->set('theme', JRequest::getCmd('theme'));
	}

	$params->def('reader', 'J17NewsReader');

	$ct_category 	= new SjK2CategoriesII($params->toArray());
	$data 			= $ct_category->getData();

	$options		= $ct_category->getOption();
	$instance		= rand().time();

	$useragent = $_SERVER['HTTP_USER_AGENT'];

	if ($options->theme=='theme1'){
		
		if (!defined('SMART_K2_CATEGORIESII_ASSETS_THEME1')){
			JHtml::script('modules/'.$module->module.'/assets/js/jsmart.tooltipmenu.js');
			JHtml::stylesheet('modules/'.$module->module.'/assets/css/theme1.css');
			define('SMART_K2_CATEGORIESII_ASSETS_THEME1', 1);
		}
		
		if (strpos($useragent,'MSIE 7.')>0){
			$paddingLeftRight	= 30;

			$itemWidth			= (int) (($options->module_width/$options->columns_max)-$paddingLeftRight);
			
			$script		= 
					'
					jQuery(document).ready(function($){
						$("#category_'.$instance.' .theme1_root_list_child").tooltipMenu({
							tooltipClass 		: ".tooltip_lev1",
							subTooltipClass 	: ".tooltip_lev2",
							addMarginLeft		: '.$itemWidth.'
						});
					});
					';
		}else {
		
			$script		= 
					'
					jQuery(document).ready(function($){
						$("#category_'.$instance.' .theme1_root_list_child").tooltipMenu({
							tooltipClass 		: ".tooltip_lev1",
							subTooltipClass 	: ".tooltip_lev2"
						});
					});
					';
		}
		

		$script		= $script.
				'
				jQuery(document).ready(function($){
					$("#category_'.$instance.' .theme1_root_list_child").MoreClick({
						
					});
				});
				';
	}

	if ($options->theme=='theme2'){

		if (!defined('SMART_K2_CATEGORIESII_ASSETS_THEME2')){
		
			JHtml::script('modules/'.$module->module.'/assets/js/jsmart.dimensions.js');
			JHtml::script('modules/'.$module->module.'/assets/js/jsmart.accordion.js');
			
			if (strpos($useragent,'MSIE 7.')>0){
				JHtml::stylesheet('modules/'.$module->module.'/assets/css/theme2_IE7.css');
			}else {
				JHtml::stylesheet('modules/'.$module->module.'/assets/css/theme2.css');
			}
			define('SMART_K2_CATEGORIESII_ASSETS_THEME2', 1);
		}

		$script		= 
			'
				jQuery(document).ready(function($){
					$("#theme2_menu_category_'.$instance.'").accordion({
						header: ".theme2_title_root",
						alwaysOpen: true,
						autoheight: false,
						event: "mouseover"
					});
				});
			';
	}

	if ($options->theme=='theme3'){

		if (!defined('SMART_K2_CATEGORIESII_ASSETS_THEME3')){		
			JHtml::script('modules/'.$module->module.'/assets/js/jsmart.changeImage.js');
			JHtml::stylesheet('modules/'.$module->module.'/assets/css/theme3.css');
			define('SMART_K2_CATEGORIESII_ASSETS_THEME3', 1);
		}

		$script		= 
			'
				jQuery(document).ready(function($){
					$("#categories_theme3_'.$instance.'").changeImage({});
				});
			';
	}



	if ($options->theme=='theme4'){

		if (!defined('SMART_K2_CATEGORIESII_ASSETS_THEME4')){
			if (strpos($useragent,'MSIE 7.')>0){
				JHtml::stylesheet('modules/'.$module->module.'/assets/css/theme4_IE7.css');
			}else{			
				JHtml::stylesheet('modules/'.$module->module.'/assets/css/theme4.css');
			}
			define('SMART_K2_CATEGORIESII_ASSETS_THEME4', 1);
		}
		
		$script	= '';
	}

	$doc = JFactory::getDocument();
	$doc->addScriptDeclaration( $script );

	if(!empty($data)) {
		require JModuleHelper::getLayoutPath($module->module);
	} else {
		echo JText::_('NO_CONTENT');
	}

}else{
	echo JText::_('PLEASE_INSTALL_K2_COMPONENT_FIRST');
}
?>