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
/*********************************************************************************

 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/







require_once('modules/Administration/Forms.php');

global $mod_strings;
global $app_list_strings;
global $app_strings;
global $current_user;

if (!is_admin($current_user)) sugar_die("Unauthorized access to administration.");


$title = get_module_title($mod_strings['LBL_MODULE_NAME'], $mod_strings['LBL_MODULE_NAME'].": ".$mod_strings['LBL_CONFIGURE_TABS'], true);

global $theme;
global $currentModule;



$GLOBALS['log']->info("Administration ConfigureTabs view");
require_once("modules/MySettings/TabController.php");
$controller = new TabController();
$tabs = $controller->get_tabs_system();
$groups = array();
$groups[$mod_strings['LBL_DISPLAY_TABS']] = array();
foreach ($tabs[0] as $key=>$value)
{
	
$groups[$mod_strings['LBL_DISPLAY_TABS']][$key] = array('label'=>'<span>'.$app_list_strings['moduleList'][$key] . '</span>');
}
$groups[ $mod_strings['LBL_HIDE_TABS']]= array();
foreach ($tabs[1] as $key=>$value)
{
$groups[ $mod_strings['LBL_HIDE_TABS']][$key]  = array('label'=>$app_list_strings['moduleList'][$key]);
}


global $app_list_strings, $app_strings;

$smarty = new Sugar_Smarty();
$user_can_edit = $controller->get_users_can_edit();
$smarty->assign('APP', $GLOBALS['app_strings']);
$smarty->assign('MOD', $GLOBALS['mod_strings']);
$smarty->assign('title',  $title);
$smarty->assign('user_can_edit',  $user_can_edit);
$smarty->assign('hideKeys', true);
$smarty->assign('groups',$groups);
$smarty->assign('description',  $mod_strings['LBL_CONFIG_TABS']);
$buttons = $smarty->fetch("modules/Administration/ConfigureTabForm.tpl");
$smarty->assign('buttons', $buttons);
$smarty->display("modules/Studio/ListViewEditor/EditView.tpl");




?>
