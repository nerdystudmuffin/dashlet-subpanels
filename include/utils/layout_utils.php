<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * SugarCRM is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004 - 2009 SugarCRM Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 ********************************************************************************/

require_once("include/EditView/EditView2.php");
require_once("include/MVC/View/views/view.sidequickcreate.php");

/**
 * Contains a variety of utility functions used to display UI components such as form headers and footers.
 *
 * @todo refactor out these functions into the base UI objects as indicated
 */

/**
 * Create HTML to display formatted form title of a form in the left pane
 *
 * @deprecated use ViewSidequickcreate::getLeftFormHeader() instead
 *
 * @param  $left_title string to display as the title in the header
 * @return string HTML
 */
function get_left_form_header(
    $left_title
    )
{
    $view = new ViewSidequickcreate();
    return $view->getLeftFormHeader($left_title);
}

/**
 * Create HTML to display formatted form footer of form in the left pane.
 *
 * @deprecated use ViewSidequickcreate::getLeftFormHeader() instead
 *
 * @return string HTML
 */
function get_left_form_footer() 
{
	$view = new ViewSidequickcreate();
    return $view->getLeftFormFooter();
}

/**
 * Create HTML to display formatted form title.
 * 
 * @param  $form_title string to display as the title in the header
 * @param  $other_text string to next to the title.  Typically used for form buttons.
 * @param  $show_help  boolean which determines if the print and help links are shown.
 * @return string HTML
 */
function get_form_header(
    $form_title, 
    $other_text, 
    $show_help
    )
{
    global $sugar_version, $sugar_flavor, $server_unique_key, $current_language, $current_module, $current_action;
    global $app_strings;
    
    $blankImageURL = SugarThemeRegistry::current()->getImageURL('blank.gif');
    $printImageURL = SugarThemeRegistry::current()->getImageURL("print.gif");
    $helpImageURL  = SugarThemeRegistry::current()->getImageURL("help.gif");
    
    $is_min_max = strpos($other_text,"_search.gif");
    if($is_min_max !== false)
        $form_title = "{$other_text}&nbsp;{$form_title}";

    $the_form = <<<EOHTML
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="formHeader h3Row">
<tr>
<td nowrap><h3><span>{$form_title}</span></h3></td>
EOHTML;
    
    $keywords = array("/class=\"button\"/","/class='button'/","/class=button/","/<\/form>/");
    $match="";
    foreach ($keywords as $left)
        if (preg_match($left,$other_text))
            $match = true;
    
    if ($other_text && $match) {
        $the_form .= <<<EOHTML
<td colspan='10' width='100%'><IMG height='1' width='1' src='$blankImageURL' alt=''></td>
</tr>
<tr>
<td align='left' valign='middle' nowrap style='padding-bottom: 2px;'>$other_text</td>
<td width='100%'><IMG height='1' width='1' src='$blankImageURL' alt=''></td>
EOHTML;
        if ($show_help) {
            $the_form .= "<td align='right' nowrap>";
            if ($_REQUEST['action'] != "EditView") {
                $the_form .= <<<EOHTML
    <a href='index.php?{$GLOBALS['request_string']}' class='utilsLink'>
    <img src='{$printImageURL}' alt='Print' border='0' align='absmiddle'>
    </a>&nbsp;
    <a href='index.php?{$GLOBALS['request_string']}' class='utilsLink'>
    {$app_strings['LNK_PRINT']}
    </a>
EOHTML;
            }
            $the_form .= <<<EOHTML
&nbsp;
    <a href='index.php?module=Administration&action=SupportPortal&view=documentation&version={$sugar_version}&edition={$sugar_flavor}&lang={$current_language}&help_module={$current_module}&help_action={$current_action}&key={$server_unique_key}'
       class='utilsLink'>
    <img src='{$helpImageURL}' alt='Help' border='0' align='absmiddle'>
    </a>&nbsp;
    <a href='index.php?module=Administration&action=SupportPortal&view=documentation&version={$sugar_version}&edition={$sugar_flavor}&lang={$current_language}&help_module={$current_module}&help_action={$current_action}&key={$server_unique_key}'
        class='utilsLink'>
    {$app_strings['LNK_HELP']}
    </a>
</td>
EOHTML;
        }
    } 
    else {
        if ($other_text && $is_min_max === false) {
            $the_form .= <<<EOHTML
<td width='20'><img height='1' width='20' src='$blankImageURL' alt=''></td>
<td valign='middle' nowrap width='100%'>$other_text</td>
EOHTML;
        }
        else {
            $the_form .= <<<EOHTML
<td width='100%'><IMG height='1' width='1' src='$blankImageURL' alt=''></td>
EOHTML;
        }
    
        if ($show_help) {
            $the_form .= "<td align='right' nowrap>";
            if ($_REQUEST['action'] != "EditView") {
                $the_form .= <<<EOHTML
    <a href='index.php?{$GLOBALS['request_string']}' class='utilsLink'>
    <img src='{$printImageURL}' alt='Print' border='0' align='absmiddle'>
    </a>&nbsp;
    <a href='index.php?{$GLOBALS['request_string']}' class='utilsLink'>
    {$app_strings['LNK_PRINT']}</a>
EOHTML;
            }
            $the_form .= <<<EOHTML
    &nbsp;
    <a href='index.php?module=Administration&action=SupportPortal&view=documentation&version={$sugar_version}&edition={$sugar_flavor}&lang={$current_language}&help_module={$current_module}&help_action={$current_action}&key={$server_unique_key}'
       class='utilsLink'>
    <img src='{$helpImageURL}' alt='Help' border='0' align='absmiddle'>
    </a>&nbsp;
    <a href='index.php?module=Administration&action=SupportPortal&view=documentation&version={$sugar_version}&edition={$sugar_flavor}&lang={$current_language}&help_module={$current_module}&help_action={$current_action}&key={$server_unique_key}'
        class='utilsLink'>{$app_strings['LNK_HELP']}</a>
</td>
EOHTML;
        }
    }
    
    $the_form .= <<<EOHTML
</tr>
</table>
EOHTML;
    
    return $the_form;
}

/**
 * Wrapper function for the get_module_title function, which is mostly used for pre-MVC modules.
 * 
 * @deprecated use EditView2.php EditView::getModuleTitle() for MVC modules
 *
 * @param  $module       string  to next to the title.  Typically used for form buttons.
 * @param  $module_title string  to display as the module title
 * @param  $show_help    boolean which determines if the print and help links are shown.
 * @return string HTML
 */
function get_module_title(
    $module, 
    $module_title, 
    $show_help
    )
{
    $ev = new EditView();
    return $ev->getModuleTitle($module,$module_title,$show_help);
}

/**
 * Create a header for a popup.
 *
 * @todo refactor this into the base Popup_Picker class
 *
 * @param  $theme string the name of the current theme, ignorred to use SugarThemeRegistry::current() instead.
 * @return string HTML
 */
function insert_popup_header(
    $theme = null
    )
{
    global $app_strings, $sugar_config;
    
    $charset = isset($app_strings['LBL_CHARSET']) 
        ? $app_strings['LBL_CHARSET'] : $sugar_config['default_charset'];
    
    $themeCSS = SugarThemeRegistry::current()->getCSS();
    
    echo <<<EOHTML
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset="{$charset}">
<title>{$app_strings['LBL_BROWSER_TITLE']}</title>
{$themeCSS}
</head>
<body style="margin: 10px">
EOHTML;
}

/**
 * Create a footer for a popup.
 *
 * @todo refactor this into the base Popup_Picker class
 *
 * @return string HTML
 */
function insert_popup_footer()
{
    echo <<<EOQ
</body>
</html>
EOQ;
}
