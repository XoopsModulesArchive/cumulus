<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code 
 which is considered copyrighted (c) material of the original comment or credit authors.
 
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * XOOPS tag management module
 *
 * @copyright       The XOOPS project http://sourceforge.net/projects/xoops/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @since           1.0.0
 * @author          Susheng Yang <ezskyyoung@gmail.com>
 * @version         $Id: xoops_version.php $
 * @package         cumulus
 */


if (!defined('XOOPS_ROOT_PATH')) { exit(); }

$modversion = array();
$modversion["name"]         = _MI_CUMULUS_NAME;
$modversion["version"]      = "1.0 beta3";
$modversion["description"]  = _MI_CUMULUS_DESC;
$modversion["image"]        = "images/logo.png";
$modversion["dirname"]      = "cumulus";
$modversion["author"]       = "susheng yang <ezskyyoung@gmail.com>";



// Admin things
$modversion["hasAdmin"] = 0;

// Menu
$modversion["hasMain"] = 0;

// Use smarty
$modversion["use_smarty"] = 1;


// Blocks
$modversion['blocks']    = array();

/*
 * $options for cumulus:  
 *                    $options[0] - number of tags to display
 *                    $options[1] - time duration
 *                    $options[2] - max font size (px or %)
 *                    $options[3] - min font size (px or %)
 *                    $options[4] - cumulus_flash_width
 *                    $options[5] - cumulus_flash_height
 *                    $options[6] - cumulus_flash_background
 *                    $options[7] - cumulus_flash_transparency
 *                    $options[8] - cumulus_flash_min_font_color
 *                    $options[9] - cumulus_flash_max_font_color 
 *                    $options[10] - cumulus_flash_hicolor 
 *                    $options[11] - cumulus_flash_speed
 */
$modversion["blocks"][0]    = array(
    "file"          => "block.php",
    "name"          => _MI_BLOCK_CUMULUS,
    "description"   => _MI_BLOCK_CUMULUS_DESC,
    "show_func"     => "tag_block_cumulus_show",
    "edit_func"     => "tag_block_cumulus_edit",
    "options"       => "100|0|24|12|160|140|#ffffff|0|#000000|#003300|#00ff00|100",
    "template"      => "tag_block_cumulus.html",
    );


// Search
$modversion["hasSearch"] = 0;

// Comments
$modversion["hasComments"] = 0;

// Notification

$modversion["hasNotification"] = 0;
$modversion["notification"] = array();
?>