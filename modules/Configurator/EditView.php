<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
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
 */



if(!is_admin($current_user)){
	sugar_die('Admin Only');
}
require_once('modules/Administration/Forms.php');
echo get_module_title($mod_strings['LBL_MODULE_ID'], $mod_strings['LBL_MODULE_NAME'].": ", true);
require_once('modules/Configurator/Configurator.php');
$configurator = new Configurator();
$sugarConfig = SugarConfig::getInstance();
$focus = new Administration();
$configurator->parseLoggerSettings();

if(!empty($_POST['save'])){
	$configurator->saveConfig();
	$focus->saveConfig();
	//Clear the Contacts file b/c portal flag affects rendering
	if(file_exists($GLOBALS['sugar_config']['cache_dir'].'modules/Contacts/EditView.tpl')) {
	   unlink($GLOBALS['sugar_config']['cache_dir'].'modules/Contacts/EditView.tpl');
	}
	header('Location: index.php?module=Administration&action=index');
}

$focus->retrieveSettings();
if(!empty($_POST['restore'])){
	$configurator->restoreConfig();
}


require_once('include/SugarLogger/SugarLogger.php');
$sugar_smarty = new Sugar_Smarty();


$sugar_smarty->assign('MOD', $mod_strings);
$sugar_smarty->assign('APP', $app_strings);
$sugar_smarty->assign('APP_LIST', $app_list_strings);
$sugar_smarty->assign('config', $configurator->config);
$sugar_smarty->assign('error', $configurator->errors);
$sugar_smarty->assign('THEMES', SugarThemeRegistry::availableThemes());
$sugar_smarty->assign('LANGUAGES', get_languages());
$sugar_smarty->assign("JAVASCRIPT",get_set_focus_js(). get_configsettings_js());
$sugar_smarty->assign('company_logo', SugarThemeRegistry::current()->getImageURL('company_logo.png'));
$sugar_smarty->assign("settings", $focus->settings);
$sugar_smarty->assign("mail_sendtype_options", get_select_options_with_id($app_list_strings['notifymail_sendtype'], $focus->settings['mail_sendtype']));
if(!empty($focus->settings['proxy_on'])){
	$sugar_smarty->assign("PROXY_CONFIG_DISPLAY", 'inline');
}else{
	$sugar_smarty->assign("PROXY_CONFIG_DISPLAY", 'none');
}
if(!empty($focus->settings['proxy_auth'])){
	$sugar_smarty->assign("PROXY_AUTH_DISPLAY", 'inline');
}else{
		$sugar_smarty->assign("PROXY_AUTH_DISPLAY", 'none');
}
if(!function_exists('mcrypt_cbc')){
	$sugar_smarty->assign("LDAP_ENC_KEY_READONLY", 'readonly');
	$sugar_smarty->assign("LDAP_ENC_KEY_DESC", $mod_strings['LDAP_ENC_KEY_NO_FUNC_DESC']);
}else{
	$sugar_smarty->assign("LDAP_ENC_KEY_DESC", $mod_strings['LBL_LDAP_ENC_KEY_DESC']);
}










if (!empty($configurator->config['logger']['level'])) {
	$sugar_smarty->assign('log_levels', get_select_options_with_id(  SugarLogger::$log_levels, $configurator->config['logger']['level']));
} else {
	$sugar_smarty->assign('log_levels', get_select_options_with_id(  SugarLogger::$log_levels, ''));
}
if (!empty($configurator->config['logger']['file']['suffix'])) {
	$sugar_smarty->assign('filename_suffix', get_select_options_with_id(  SugarLogger::$filename_suffix,$configurator->config['logger']['file']['suffix']));
} else {
	$sugar_smarty->assign('filename_suffix', get_select_options_with_id(  SugarLogger::$filename_suffix,''));
}

//nsingh- moved to locale.php , bug 18064.
	//$sugar_smarty->assign("exportCharsets", get_select_options_with_id($locale->getCharsetSelect(), $sugar_config['default_export_charset']));*/
$sugar_smarty->display('modules/Configurator/EditView.tpl');


$javascript = new javascript();
$javascript->setFormName("ConfigureSettings");
$javascript->addFieldGeneric("notify_fromaddress", "email", $mod_strings['LBL_NOTIFY_FROMADDRESS'], TRUE, "");
$javascript->addFieldGeneric("notify_subject", "varchar", $mod_strings['LBL_NOTIFY_SUBJECT'], TRUE, "");
$javascript->addFieldGeneric("proxy_host", "varchar", $mod_strings['LBL_PROXY_HOST'], TRUE, "");
$javascript->addFieldGeneric("proxy_port", "int", $mod_strings['LBL_PROXY_PORT'], TRUE, "");
$javascript->addFieldGeneric("proxy_password", "varchar", $mod_strings['LBL_PROXY_PASSWORD'], TRUE, "");
$javascript->addFieldGeneric("proxy_username", "varchar", $mod_strings['LBL_PROXY_USERNAME'], TRUE, "");



echo $javascript->getScript();
?>
