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
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @author          susheng yang <ezskyyoung@gmail.com> 
 * @version         $Id: block.php 
 * @package         cumulus
 */

if (!defined('XOOPS_ROOT_PATH')) { exit(); }
include XOOPS_ROOT_PATH . "/modules/tag/include/vars.php";
include_once XOOPS_ROOT_PATH . "/modules/tag/include/functions.php";

xoops_loadLanguage("blocks", "cumulus");

/*
 * $options:  
 *                    $options[0] - number of tags to display
 *                    $options[1] - time duration
 *                    $options[2] - max font size (px or %)
 *                    $options[3] - min font size (px or %)
 *                    $options[4] - cumulus_flash_width
 *                    $options[5] - cumulus_flash_height
 *                    $options[6] - cumulus_flash_background
 *                    $options[7] - cumulus_flash_transparency
 *                    $options[8] - cumulus_flash_color
 *                    $options[9] - cumulus_flash_hicolor 
 *                    $options[10] - cumulus_flash_speed
 */
function tag_block_cumulus_show( $options, $dirname = "", $catid = 0 )
{
    global $xoopsDB;

    if (empty($dirname)) {
        $modid = 0;
    } elseif (isset($GLOBALS["xoopsModule"]) && is_object($GLOBALS["xoopsModule"]) && $GLOBALS["xoopsModule"]->getVar("dirname") == $dirname) {
        $modid = $GLOBALS["xoopsModule"]->getVar("mid");
    } else {
        $module_handler =& xoops_gethandler("module");
        $module = $module_handler->getByDirname($dirname);
        $modid = $module->getVar("mid");
    }
    
    $block = array();
    $tag_handler = xoops_getModuleHandler("tag", "tag");
    tag_define_url_delimiter();
    
    $criteria = new CriteriaCompo();
    $criteria->setSort("count");
    $criteria->setOrder("DESC");
    $criteria->setLimit($options[0]);
    $criteria->add( new Criteria("o.tag_status", 0) );
    if (!empty($modid)) {
        $criteria->add( new Criteria("l.tag_modid", $modid) );
        if ($catid >= 0) {
            $criteria->add( new Criteria("l.tag_catid", $catid) );
        }
    }
    if (!$tags = $tag_handler->getByLimit($criteria, empty($options[1]))) {
        return $block;
    }
    
    $count_max = 0;
    $count_min = 0;
    $tags_term = array();
    foreach (array_keys($tags) as $key) {
        if ($tags[$key]["count"] > $count_max) $count_max = $tags[$key]["count"];
        if ($tags[$key]["count"] < $count_min || $count_min == 0) $count_min = $tags[$key]["count"];
        $tags_term[] = strtolower($tags[$key]["term"]);
    }
    array_multisort($tags_term, SORT_ASC, $tags);
    $count_interval = $count_max - $count_min;
    $level_limit = 5;
    
    $font_max = $options[2];
    $font_min = $options[3];
    $font_ratio = ($count_interval) ? ($font_max - $font_min) / $count_interval : 1;
    
    $tags_data = array();
    foreach (array_keys($tags) as $key) {
        $tags_data[] = array(
                        "id"    => $tags[$key]["id"],
                        "font"    => ($count_interval) ? floor( ($tags[$key]["count"] - $count_min) * $font_ratio + $font_min ) : 12,
                        "level"    => empty($count_max) ? 0 : floor( ($tags[$key]["count"] - $count_min) * $level_limit / $count_max ),
                        "term"    => $tags[$key]["term"],
                        "count"    => $tags[$key]["count"],
                        );
    }
    unset($tags, $tags_term);    
    $block["tags"] =& $tags_data;

    $block["tag_dirname"] = "tag";
    if (!empty($modid)) {
        $module_handler = xoops_getHandler('module');
        if ($module_obj = $module_handler->get($modid)) {
            $block["tag_dirname"] = $module_obj->getVar("dirname");
        }
    }
    $flash_params = array(
    'flash_url' => XOOPS_URL."/modules/cumulus/include/cumulus.swf",
    'width' => $options[4],
    'height' => $options[5],
    'background' => preg_replace('/(#)/ie','',$options[6]),
    'tcolor' => "0x".preg_replace('/(#)/ie','',$options[8]),
    'hicolor' => "0x".preg_replace('/(#)/ie','',$options[9]),
    'tcolor2' => "0x".preg_replace('/(#)/ie','',$options[10]),
    'speed' => $options[11]    
    ); 
    
    $output = '<tags>';
    $xoops_url = XOOPS_URL;
    foreach ($tags_data as $term) {
    // assign font size
    $output .= <<<EOT
<a href='{$xoops_url}/modules/tag/view.tag.php?{$term['id']}' style='{$term['font']}'>{$term['term']}</a> 
EOT;
    }
    $output .= '</tags>';
    $flash_params['tags_formatted_flash'] = urlencode($output) ; 
	if ($options[7] === "transparent" ) {
        $flash_params['transparency'] = 'widget_so.addParam("wmode", "transparent");';
      }
    $block["flash_params"] =$flash_params;

    return $block;
   
}

function tag_block_cumulus_edit($options)
{

include_once(XOOPS_ROOT_PATH."/class/xoopsformloader.php");
if(!class_exists('XoopsBlockForm')) { //changed by trabis
class XoopsBlockForm extends XoopsForm
{

	/**
	 * create HTML to output the form as a table
	 * 
     * @return	string
	 */
	function render()
	{
$ele_name = $this->getName();
		$ret = "
				<div>
		";
		$hidden = '';
		foreach ( $this->getElements() as $ele ) {
			if (!is_object($ele)) {
				$ret .= $ele;
			} elseif ( !$ele->isHidden() ) {
				if ( ($caption = $ele->getCaption()) != '' ) {
				    $ret .= 
				        "<div class='xoops-form-element-caption" . ($ele->isRequired() ? "-required" : "" ) . "'>".
				        "<span class='caption-text'>{$caption}</span>".
				        "<span class='caption-marker'>*</span>".
				        "</div>";
			    }
				
				$ret .= "<div style='margin:5px 0 8px 0; '>".$ele->render()."</div>\n";
			} else {
				$hidden .= $ele->render();
			}
		}
		$ret .= "</div>";
		$ret .= $this->renderValidationJS( true );
		return $ret;
	}
}
}


    $form  = new XoopsBlockForm("","",""); 
    $form->addElement(new XoopsFormText(TAG_MB_ITEMS, "options[0]", 25, 25,$options[0]));
    $form->addElement(new XoopsFormText(TAG_MB_TIME_DURATION, "options[1]", 25, 25,$options[1]));
    $form->addElement(new XoopsFormText(TAG_MB_FONTSIZE_MAX, "options[2]", 25, 25,$options[2]));
    $form->addElement(new XoopsFormText(TAG_MB_FONTSIZE_MIN, "options[3]", 25, 25,$options[3]));
    $form->addElement(new XoopsFormText(TAG_MB_FLASH_WIDTH, "options[4]", 25, 25,$options[4]));
    $form->addElement(new XoopsFormText(TAG_MB_FLASH_HEIGHT, "options[5]", 25, 25,$options[5]));
    $form->addElement(new XoopsFormColorPicker(TAG_MB_FLASH_TRANSPARENCY,"options[6]",$options[6]));
    $form_cumulus_flash_transparency = new XoopsFormSelect(TAG_MB_FLASH_TRANSPARENCY,"options[7]",$options[7]);
    $form_cumulus_flash_transparency->addOption(0,_NO); 
    $form_cumulus_flash_transparency->addOption("transparent",TAG_MB_FLASH_TRANSPARENT); 
    $form->addElement($form_cumulus_flash_transparency);    
    $form->addElement(new XoopsFormColorPicker(TAG_MB_FLASH_MINFONTCOLOR,"options[8]",$options[8]));
    $form->addElement(new XoopsFormColorPicker(TAG_MB_FLASH_MAXFONTCOLOR,"options[9]",$options[9]));
    $form->addElement(new XoopsFormColorPicker(TAG_MB_FLASH_HILIGHTFONTCOLOR,"options[10]",$options[10]));
    $form->addElement(new XoopsFormText(TAG_MB_FLASH_SPEED, "options[11]", 25, 25,$options[11]));
    
    return $form->render(); 
} 

