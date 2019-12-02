<?php
/**
 * @package		Joomla.Site
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

//get copyright
$app = JFactory::getApplication();
$date		= JFactory::getDate();
$template = $app->getTemplate(true);
$params = $template->params;
$cur_year	= $date->format('Y');
$ytcopyright = $params->get('ytcopyright' );
$ytcopyright = str_replace('{year}', $cur_year, $ytcopyright);


//get language and direction
$doc = JFactory::getDocument();
$this->language = $doc->language;
$this->direction = $doc->direction;
?>

<html  lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<title><?php echo $this->error->getCode(); ?> - <?php echo $this->title; ?></title>
	<meta content="text/html; charset=utf-8" http-equiv="content-type">
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="HandheldFriendly" content="true">
	<link type="text/css" href="<?php echo $this->baseurl.'/templates/'.$this->template; ?>/asset/fonts/awesome/css/font-awesome.min.css" rel="stylesheet">	
	<link rel="stylesheet" href="<?php echo $this->baseurl.'/templates/'.$this->template; ?>/css/error.css" type="text/css" />	
</head>
<body>
	<div class="wrapall">
		<div class="wrap-inner">
			<div class="contener">
				<div class="block-top">
					<img class="img_404" src="<?php echo JURI::base() . 'templates/' . JFactory::getApplication()->getTemplate();?>/images/404/404.png" alt="" />
					<span class="mes-error"><?php echo $this->error->getMessage(); ?></span>
				</div>
			</div>
		</div>
	</div>
	<div class="wrapall yt-block-main">
		<div class="wrap-inner">
			<div class="contener">
				
				<div class="block-main">
					<img class="img_404" src="<?php echo JURI::base() . 'templates/' . JFactory::getApplication()->getTemplate();?>/images/404/grado.png" alt="" />
					<span class="mess-code"><?php echo JText::_('JERROR_SORRY'); ?></span>
					<div class="second-block">
							<a class="btn" href="<?php echo $this->baseurl; ?>/index.php" title="<?php echo JText::_('JERROR_LAYOUT_GO_TO_THE_HOME_PAGE'); ?>">
								<?php echo JText::_('JERROR_LAYOUT_HOME_PAGE'); ?>
							</a>
						<span><?php echo JText::_('JERROR_LAYOUT_PLEASE_CONTACT_THE_SYSTEM_ADMINISTRATOR'); ?>.</span>
						<div id="techinfo">
						<span><?php echo $this->error->getMessage(); ?></span>
						<span>
							<?php if ($this->debug) :
								echo $this->renderBacktrace();
							endif; ?>
						</span>
						</div>
					</div>
					
				</div>
			
			</div>
		</div>
	</div>
</body>
</html>
