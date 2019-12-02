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
?>

<?php
$mod_width 			= intval($options->module_width);
$mod_style_width 	= $mod_width>0 ? "style=\"width:{$mod_width}px;\"" : ""

?>
<?php if (!empty( $options->introtext )): ?>
	<div class="sj_introtext" <?php echo $mod_style_width; ?>><?php echo $options->introtext; ?></div>
<?php endif; ?>

<div id="category_<?php echo $instance; ?>" class="sj_category <?php echo $options->theme; ?>" <?php echo $mod_style_width; ?>>
	<?php require JModuleHelper::getLayoutPath( $module->module, $options->theme ); ?>
</div>

<?php if (!empty( $options->footertext )): ?>
	<div class="sj_footertext" <?php echo $mod_style_width; ?>><?php echo $options->footertext; ?></div>
<?php endif; ?>