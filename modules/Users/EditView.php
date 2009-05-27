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
 * Portions created by SugarCRM are Copyright(C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

$sugar_smarty = new Sugar_Smarty();
require_once('include/export_utils.php');

require_once('modules/Users/Forms.php');
require_once('modules/Users/UserSignature.php');

global $app_strings;
global $app_list_strings;
global $mod_strings;

$admin = new Administration();
$admin->retrieveSettings();

$focus = new User();
$is_current_admin=is_admin($current_user)||is_admin_for_module($GLOBALS['current_user'],'Users');
$is_super_admin = is_admin($current_user);
if(!$is_current_admin && $_REQUEST['record'] != $current_user->id) sugar_die("Unauthorized access to administration.");

if(isset($_REQUEST['record'])) {
    $focus->retrieve($_REQUEST['record']);
}

if(isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
	$focus->id = "";
	$focus->user_name = "";
}else if(!isset($_REQUEST['record'])){
    define('SUGARPDF_USE_DEFAULT_SETTINGS', true);
}
echo get_module_title($mod_strings['LBL_MODULE_NAME'], $mod_strings['LBL_MODULE_NAME'].": ".$focus->first_name." ".$focus->last_name."(".$focus->user_name.")", true);

$GLOBALS['log']->info('User edit view');
$sugar_smarty->assign('MOD', $mod_strings);
$sugar_smarty->assign('APP', $app_strings);

if(isset($_REQUEST['error_string'])) $sugar_smarty->assign('ERROR_STRING', '<span class="error">Error: '.$_REQUEST['error_string'].'</span>');
if(isset($_REQUEST['error_password'])) $sugar_smarty->assign('ERROR_PASSWORD', '<span id="error_pwd" class="error">Error: '.$_REQUEST['error_password'].'</span>');
if(isset($_REQUEST['return_module'])) $sugar_smarty->assign('RETURN_MODULE', $_REQUEST['return_module']);
if(isset($_REQUEST['return_id'])) $sugar_smarty->assign('RETURN_ID', $_REQUEST['return_id']);
else { $sugar_smarty->assign('RETURN_ID', $focus->id); }
if(isset($_REQUEST['return_action'])) $sugar_smarty->assign('RETURN_ACTION', $_REQUEST['return_action']);
else { $sugar_smarty->assign('RETURN_ACTION', 'DetailView'); }

$sugar_smarty->assign('JAVASCRIPT',user_get_validate_record_js().user_get_chooser_js().user_get_confsettings_js().'<script type="text/javascript" language="Javascript" src="modules/Users/User.js"></script>');
$sugar_smarty->assign('PRINT_URL', 'index.php?'.$GLOBALS['request_string']);
$sugar_smarty->assign('ID', $focus->id);
$sugar_smarty->assign('USER_NAME', $focus->user_name);
$sugar_smarty->assign('FIRST_NAME', $focus->first_name);
$sugar_smarty->assign('LAST_NAME', $focus->last_name);
$sugar_smarty->assign('TITLE', $focus->title);
$sugar_smarty->assign('DEPARTMENT', $focus->department);
$sugar_smarty->assign('REPORTS_TO_ID', $focus->reports_to_id);
$sugar_smarty->assign('REPORTS_TO_NAME', $focus->reports_to_name);
$sugar_smarty->assign('PHONE_HOME', $focus->phone_home);
$sugar_smarty->assign('PHONE_MOBILE', $focus->phone_mobile);
$sugar_smarty->assign('PHONE_WORK', $focus->phone_work);
$sugar_smarty->assign('PHONE_OTHER', $focus->phone_other);
$sugar_smarty->assign('PHONE_FAX', $focus->phone_fax);
$sugar_smarty->assign('EMAIL1', $focus->email1);
$sugar_smarty->assign('EMAIL2', $focus->email2);
$sugar_smarty->assign('ADDRESS_STREET', $focus->address_street);
$sugar_smarty->assign('ADDRESS_CITY', $focus->address_city);
$sugar_smarty->assign('ADDRESS_STATE', $focus->address_state);
$sugar_smarty->assign('ADDRESS_POSTALCODE', $focus->address_postalcode);
$sugar_smarty->assign('ADDRESS_COUNTRY', $focus->address_country);
$sugar_smarty->assign('DESCRIPTION', $focus->description);
$sugar_smarty->assign('EXPORT_DELIMITER', getDelimiter());
$sugar_smarty->assign('PWDSETTINGS', $GLOBALS['sugar_config']['passwordsetting']);
$pwd_regex=str_replace( "\\","\\\\",$GLOBALS['sugar_config']['passwordsetting']['customregex']);
$sugar_smarty->assign("REGEX",$pwd_regex);  
if(!empty($GLOBALS['sugar_config']['authenticationClass'])){
		$sugar_smarty->assign('EXTERNAL_AUTH_CLASS', $GLOBALS['sugar_config']['authenticationClass']);
}else{
	if(!empty($GLOBALS['system_config']->settings['system_ldap_enabled'])){
		$sugar_smarty->assign('EXTERNAL_AUTH_CLASS', 'LDAPAuthenticate');
	}
}
if(!empty($focus->external_auth_only))$sugar_smarty->assign('EXTERNAL_AUTH_ONLY_CHECKED', 'CHECKED');
if ($is_current_admin)
	$sugar_smarty->assign('IS_ADMIN','1');
else
	$sugar_smarty->assign('IS_ADMIN', '0');

if ($is_super_admin)
    $sugar_smarty->assign('IS_SUPER_ADMIN','1');
else
    $sugar_smarty->assign('IS_SUPER_ADMIN', '0');
	

//jc:12293 - modifying to use the accessor method which will translate the
//available character sets using the translation files
$sugar_smarty->assign('EXPORT_CHARSET', get_select_options_with_id($locale->getCharsetSelect(), $locale->getExportCharset('', $focus)));
//end:12293

if($focus->getPreference('use_real_names') == 'on') {
	$sugar_smarty->assign('USE_REAL_NAMES', 'CHECKED');
}
if($focus->getPreference('no_opps') == 'on') {
    $sugar_smarty->assign('NO_OPPS', 'CHECKED');
}


















// check if the user has access to the User Management
$sugar_smarty->assign('USER_ADMIN',is_admin_for_module($current_user,'Users')&& !is_admin($current_user));

///////////////////////////////////////////////////////////////////////////////
////	NEW USER CREATION ONLY
if(empty($focus->id)) {
	$sugar_smarty->assign('SHOW_ADMIN_CHECKBOX','height="30"');
	$sugar_smarty->assign('NEW_USER','1');
	/*$sugar_smarty->assign('LBL_NEW_PASSWORD1', $mod_strings['LBL_NEW_PASSWORD1'].': <span class="required">'.$app_strings['LBL_REQUIRED_SYMBOL'].'</span>');
	$sugar_smarty->assign('LBL_NEW_PASSWORD2', $mod_strings['LBL_NEW_PASSWORD2'].': <span class="required">'.$app_strings['LBL_REQUIRED_SYMBOL'].'</span>');
	$sugar_smarty->assign('NEW_PASSWORD1', '<input id="new_password1" name="new_password1" tabindex="2" type="password" size="25" maxlength="25">');
	$sugar_smarty->assign('NEW_PASSWORD2', '<input id="new_password2" name="new_password2" tabindex="2" type="password" size="25" maxlength="25">');*/
}
else{
	$sugar_smarty->assign('NEW_USER','0');
	$sugar_smarty->assign('NEW_USER_TYPE','DISABLED');
}
	
////	END NEW USER CREATION ONLY
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
////	REDIRECTS FROM COMPOSE EMAIL SCREEN
if(isset($_REQUEST['type']) && (isset($_REQUEST['return_module']) && $_REQUEST['return_module'] == 'Emails')) {
	$sugar_smarty->assign('REDIRECT_EMAILS_TYPE', $_REQUEST['type']);
}
////	END REDIRECTS FROM COMPOSE EMAIL SCREEN
///////////////////////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////////////////////
////	LOCALE SETTINGS
////	Date/time format
$dformat = $locale->getPrecedentPreference('datef', $focus);
$tformat = $locale->getPrecedentPreference('timef', $focus);
$timeOptions = get_select_options_with_id($sugar_config['time_formats'], $tformat);
$dateOptions = get_select_options_with_id($sugar_config['date_formats'], $dformat);
$sugar_smarty->assign('TIMEOPTIONS', $timeOptions);
$sugar_smarty->assign('DATEOPTIONS', $dateOptions);























//// Timezone
if(empty($focus->id)) { // remove default timezone for new users(set later)
    $focus->user_preferences['timezone'] = '';
}
require_once('include/timezone/timezones.php');
global $timezones;

$userTZ = $focus->getPreference('timezone');
if(empty($userTZ) && !$focus->is_group && !$focus->portal_only) {
	$focus->setPreference('timezone', date('T'));
}

if(empty($userTZ) && !$focus->is_group && !$focus->portal_only)
	$userTZ = lookupTimezone();

if(!$focus->getPreference('ut')) {
	$sugar_smarty->assign('PROMPTTZ', ' checked');
}

$timezoneOptions = '';
ksort($timezones);
foreach($timezones as $key => $value) {
	$selected =($userTZ == $key) ? ' SELECTED="true"' : '';
	$dst = !empty($value['dstOffset']) ? '(+DST)' : '';
	$gmtOffset =($value['gmtOffset'] / 60);

	if(!strstr($gmtOffset,'-')) {
		$gmtOffset = '+'.$gmtOffset;
	}
  $timezoneOptions .= "<option value='$key'".$selected.">".str_replace(array('_','North'), array(' ', 'N.'),translate('timezone_dom','',$key)). "(GMT".$gmtOffset.") ".$dst."</option>";
}
$sugar_smarty->assign('TIMEZONEOPTIONS', $timezoneOptions);

//// Numbers and Currency display
require_once('modules/Currencies/ListCurrency.php');
$currency = new ListCurrency();

// 10/13/2006 Collin - Changed to use Localization.getConfigPreference
// This was the problem- Previously, the "-99" currency id always assumed
// to be defaulted to US Dollars.  However, if someone set their install to use
// Euro or other type of currency then this setting would not apply as the
// default because it was being overridden by US Dollars.
$cur_id = $locale->getPrecedentPreference('currency', $focus);
if($cur_id) {
	$selectCurrency = $currency->getSelectOptions($cur_id);
	$sugar_smarty->assign("CURRENCY", $selectCurrency);
} else {
	$selectCurrency = $currency->getSelectOptions();
	$sugar_smarty->assign("CURRENCY", $selectCurrency);
}

$currenciesVars = "";
$i=0;
foreach($locale->currencies as $id => $arrVal) {
	$currenciesVars .= "currencies[{$i}] = '{$arrVal['symbol']}';\n";
	$i++;
}
$currencySymbolsJs = <<<eoq
var currencies = new Object;
{$currenciesVars}
function setSymbolValue(id) {
	document.getElementById('symbol').value = currencies[id];
}
eoq;
$sugar_smarty->assign('currencySymbolJs', $currencySymbolsJs);


// fill significant digits dropdown
$significantDigits = $locale->getPrecedentPreference('default_currency_significant_digits', $focus);
$sigDigits = '';
for($i=0; $i<=6; $i++) {
	if($significantDigits == $i) {
	   $sigDigits .= "<option value=\"$i\" selected=\"true\">$i</option>";
	} else {
	   $sigDigits .= "<option value=\"$i\">{$i}</option>";
	}
}

$sugar_smarty->assign('sigDigits', $sigDigits);

$num_grp_sep = $focus->getPreference('num_grp_sep');
$dec_sep = $focus->getPreference('dec_sep');
$sugar_smarty->assign("NUM_GRP_SEP",(empty($num_grp_sep) ? $sugar_config['default_number_grouping_seperator'] : $num_grp_sep));
$sugar_smarty->assign("DEC_SEP",(empty($dec_sep) ? $sugar_config['default_decimal_seperator'] : $dec_sep));
$sugar_smarty->assign('getNumberJs', $locale->getNumberJs());

//// Name display format
$sugar_smarty->assign('default_locale_name_format', $locale->getLocaleFormatMacro($focus));
$sugar_smarty->assign('getNameJs', $locale->getNameJs());
////	END LOCALE SETTINGS
///////////////////////////////////////////////////////////////////////////////

//require_once($theme_path.'config.php');


$user_max_tabs = $focus->getPreference('max_tabs');
if(isset($user_max_tabs) && $user_max_tabs > 0) {
	$sugar_smarty->assign("MAX_TAB", $user_max_tabs);
} elseif(SugarThemeRegistry::current()->maxTabs > 0) {
    $sugar_smarty->assign("MAX_TAB", SugarThemeRegistry::current()->maxTabs);
} else {
    $sugar_smarty->assign("MAX_TAB", $GLOBALS['sugar_config']['default_max_tabs']);
}

$user_max_subtabs = $focus->getPreference('max_subtabs');
if(isset($user_max_subtabs) && $user_max_subtabs > 0) {
    $sugar_smarty->assign("MAX_SUBTAB", $user_max_subtabs);
} else {
    $sugar_smarty->assign("MAX_SUBTAB", $GLOBALS['sugar_config']['default_max_subtabs']);
}

$user_swap_last_viewed = $focus->getPreference('swap_last_viewed');
if(isset($user_swap_last_viewed)) {
    $sugar_smarty->assign("SWAP_LAST_VIEWED", $user_swap_last_viewed?'checked':'');
} else {
    $sugar_smarty->assign("SWAP_LAST_VIEWED", $GLOBALS['sugar_config']['default_swap_last_viewed']?'checked':'');
}

$user_swap_shortcuts = $focus->getPreference('swap_shortcuts');
if(isset($user_swap_shortcuts)) {
    $sugar_smarty->assign("SWAP_SHORTCUT", $user_swap_shortcuts?'checked':'');
} else {
    $sugar_smarty->assign("SWAP_SHORTCUT", $GLOBALS['sugar_config']['default_swap_shortcuts']?'checked':'');
}

$user_subpanel_tabs = $focus->getPreference('subpanel_tabs');
if(isset($user_subpanel_tabs)) {
    $sugar_smarty->assign("SUBPANEL_TABS", $user_subpanel_tabs?'checked':'');
} else {
    $sugar_smarty->assign("SUBPANEL_TABS", $GLOBALS['sugar_config']['default_subpanel_tabs']?'checked':'');
}

$user_subpanel_links = $focus->getPreference('subpanel_links');
$sugar_smarty->assign("SUBPANEL_LINKS", $user_subpanel_links?'checked':'');
if(isset($user_subpanel_links)) {
    $sugar_smarty->assign("SUBPANEL_LINKS", $user_subpanel_links?'checked':'');
} else {
    $sugar_smarty->assign("SUBPANEL_LINKS", $GLOBALS['sugar_config']['default_subpanel_links']?'checked':'');
}

$user_navigation_paradigm = $focus->getPreference('navigation_paradigm');
if(isset($user_navigation_paradigm)) {
    $sugar_smarty->assign("NAVADIGMS", get_select_options_with_id($app_list_strings['navigation_paradigms'], $user_navigation_paradigm));
} else {
    $sugar_smarty->assign("NAVADIGMS", get_select_options_with_id($app_list_strings['navigation_paradigms'], $GLOBALS['sugar_config']['default_navigation_paradigm']));
}

$user_module_favicon = $focus->getPreference('module_favicon');
if(isset($user_module_favicon)) {
    $sugar_smarty->assign("MODULE_FAVICON", $user_module_favicon?'checked':'');
} else {
    $sugar_smarty->assign("MODULE_FAVICON", isset($GLOBALS['sugar_config']['default_module_favicon']) && $GLOBALS['sugar_config']['default_module_favicon'] ?'checked':'');
}

$sugar_smarty->assign("MAIL_SENDTYPE", get_select_options_with_id($app_list_strings['notifymail_sendtype'], $focus->getPreference('mail_sendtype')));
$reminder_time = $focus->getPreference('reminder_time');
if(empty($reminder_time)){
	$reminder_time = -1;
}

$sugar_smarty->assign("REMINDER_TIME_OPTIONS", get_select_options_with_id($app_list_strings['reminder_time_options'],$reminder_time));
if($reminder_time > -1){
	$sugar_smarty->assign("REMINDER_TIME_DISPLAY", 'inline');
	$sugar_smarty->assign("REMINDER_CHECKED", 'checked');
}else{
	$sugar_smarty->assign("REMINDER_TIME_DISPLAY", 'none');
}

//Add Custom Fields
require_once('modules/DynamicFields/templates/Files/EditView.php');
if($is_current_admin) {
	$status  = "<td scope='row'><slot>".$mod_strings['LBL_STATUS']." <span class='required'>".$app_strings['LBL_REQUIRED_SYMBOL']."</span></slot></td>\n";
	$status .= "<td><select name='status' tabindex='1'";
	if(!empty($sugar_config['default_user_name']) &&
		$sugar_config['default_user_name']== $focus->user_name &&
		isset($sugar_config['lock_default_user_name']) &&
		$sugar_config['lock_default_user_name'] )
	{
		$status .= ' disabled="disabled" ';
	}
	$status .= ">";
	$status .= get_select_options_with_id($app_list_strings['user_status_dom'], $focus->status);
	$status .= "</select></td>\n";
	$sugar_smarty->assign("USER_STATUS_OPTIONS", $status);
}
if($is_current_admin && $_REQUEST['module'] != 'DynamicLayout' && !empty($_SESSION['editinplace'])){
	$record = '';
	if(!empty($_REQUEST['record'])){
		$record = 	$_REQUEST['record'];
	}
	$sugar_smarty->assign("ADMIN_EDIT","<a href='index.php?action=index&module=DynamicLayout&from_action=".$_REQUEST['action'] ."&from_module=".$_REQUEST['module'] ."&record=".$record. "'>".get_image($image_path."EditLayout","border='0' alt='Edit Layout' align='bottom'")."</a>");
}

if(!empty($sugar_config['default_user_name']) &&
	$sugar_config['default_user_name'] == $focus->user_name &&
	isset($sugar_config['lock_default_user_name']) &&
	$sugar_config['lock_default_user_name'])
{
	$status .= ' disabled ';
	$sugar_smarty->assign('FIRST_NAME_DISABLED', 'disabled="disabled"');
	$sugar_smarty->assign('USER_NAME_DISABLED', 'disabled="disabled"');
	$sugar_smarty->assign('LAST_NAME_DISABLED', 'disabled="disabled"');
	$sugar_smarty->assign('IS_ADMIN_DISABLED', 'disabled="disabled"');
	$sugar_smarty->assign('IS_PORTAL_ONLY_DISABLED', 'disabled="disabled"');
	$sugar_smarty->assign('IS_GROUP_DISABLED', 'disabled="disabled"');
}

if($focus->receive_notifications ||(!isset($focus->id) && $admin->settings['notify_send_by_default'])) $sugar_smarty->assign("RECEIVE_NOTIFICATIONS", "checked");


if($focus->getPreference('gridline') == 'on') {
	$sugar_smarty->assign('GRIDLINE', 'checked');
}

if($focus->getPreference('mailmerge_on') == 'on') {
	$sugar_smarty->assign('MAILMERGE_ON', 'checked');
}

$user_type_label=$mod_strings['LBL_REGULAR_USER'];
$usertype='RegularUser';
if(!empty($focus->is_admin) && $focus->is_admin){
		$usertype='Administrator';
		$user_type_label=$mod_strings['LBL_ADMIN_USER'];
}













if(!empty($focus->is_group) && $focus->is_group){
	$sugar_smarty->assign('IS_GROUP', '1');
	$usertype='GroupUser';
	$user_type_label=$mod_strings['LBL_GROUP'];
} else {
	$sugar_smarty->assign('IS_GROUP', '0');
}
$sugar_smarty->assign("USER_TYPE_LABEL", $user_type_label);
$sugar_smarty->assign('USER_TYPE',$usertype);
if((($current_user->id == $focus->id) || empty($focus->id)) || $focus->portal_only){
	$sugar_smarty->assign('CHANGE_PWD', '1');
}
else
	$sugar_smarty->assign('CHANGE_PWD', '0');

$sugar_smarty->assign('IS_FOCUS_ADMIN', is_admin($focus));

$reports_to_change_button_html = '';

if($is_current_admin) {
	//////////////////////////////////////
	///
	/// SETUP USER POPUP

	$popup_request_data = array(
		'call_back_function' => 'set_return',
		'form_name' => 'EditView',
		'field_to_name_array' => array(
			'id' => 'reports_to_id',
			'name' => 'reports_to_name',
			),
		);

	$json = getJSONobj();
	$encoded_popup_request_data = $json->encode($popup_request_data);
	$sugar_smarty->assign('encoded_popup_request_data', $encoded_popup_request_data);

	//
	///////////////////////////////////////

	$reports_to_change_button_html = '<input type="button"'
	. " title=\"{$app_strings['LBL_SELECT_BUTTON_TITLE']}\""
	. " accesskey=\"{$app_strings['LBL_SELECT_BUTTON_KEY']}\""
	. " value=\"{$app_strings['LBL_SELECT_BUTTON_LABEL']}\""
	. ' tabindex="5" class="button" name="btn1"'
	. " onclick='open_popup(\"Users\", 600, 400, \"\", true, false, {$encoded_popup_request_data});'"
	. "' />";
} else {
	$sugar_smarty->assign('IS_ADMIN_DISABLED', 'disabled="disabled"');
}





$sugar_smarty->assign('REPORTS_TO_CHANGE_BUTTON', $reports_to_change_button_html);


/* Module Tab Chooser */
require_once('include/templates/TemplateGroupChooser.php');
require_once('modules/MySettings/TabController.php');
$chooser = new TemplateGroupChooser();
$controller = new TabController();

echo "<script>SUGAR.tabChooser.freezeOptions('display_tabs', 'hide_tabs', 'Home');</script>";

if($is_current_admin || $controller->get_users_can_edit()) {
	$chooser->display_hide_tabs = true;
} else {
	$chooser->display_hide_tabs = false;
}

$chooser->args['id'] = 'edit_tabs';
$chooser->args['values_array'] = $controller->get_tabs($focus);
foreach($chooser->args['values_array'][0] as $key=>$value) {
    $chooser->args['values_array'][0][$key] = $app_list_strings['moduleList'][$key];
}

foreach($chooser->args['values_array'][1] as $key=>$value) {
    $chooser->args['values_array'][1][$key] = $app_list_strings['moduleList'][$key];
}

foreach($chooser->args['values_array'][2] as $key=>$value) {
    $chooser->args['values_array'][2][$key] = $app_list_strings['moduleList'][$key];
}

$chooser->args['left_name'] = 'display_tabs';
$chooser->args['right_name'] = 'hide_tabs';

$chooser->args['left_label'] =  $mod_strings['LBL_DISPLAY_TABS'];
$chooser->args['right_label'] =  $mod_strings['LBL_HIDE_TABS'];
$chooser->args['title'] =  $mod_strings['LBL_EDIT_TABS'];
$sugar_smarty->assign('TAB_CHOOSER', $chooser->display());
$sugar_smarty->assign('CHOOSER_SCRIPT','set_chooser();');
$sugar_smarty->assign('CHOOSE_WHICH', $mod_strings['LBL_CHOOSE_WHICH']);

///////////////////////////////////////////////////////////////////////////////
////	EMAIL OPTIONS
$sugar_smarty->assign("NEW_EMAIL", $focus->emailAddress->getEmailAddressWidgetEditView($focus->id, $focus->module_dir));

$sugar_smarty->assign('EMAIL_LINK_TYPE', get_select_options_with_id($app_list_strings['dom_email_link_type'], $focus->getPreference('email_link_type')));
/////	END EMAIL OPTIONS
///////////////////////////////////////////////////////////////////////////////


if ($is_current_admin) {
$employee_status = '<select tabindex="5" name="employee_status">';
$employee_status .= get_select_options_with_id($app_list_strings['employee_status_dom'], $focus->employee_status);
$employee_status .= '</select>';
} else {
	$employee_status = $focus->employee_status;
}
$sugar_smarty->assign('EMPLOYEE_STATUS_OPTIONS', $employee_status);
$sugar_smarty->assign('EMPLOYEE_STATUS_OPTIONS', $employee_status);

$messenger_type = '<select tabindex="5" name="messenger_type">';
$messenger_type .= get_select_options_with_id($app_list_strings['messenger_type_dom'], $focus->messenger_type);
$messenger_type .= '</select>';
$sugar_smarty->assign('MESSENGER_TYPE_OPTIONS', $messenger_type);
$sugar_smarty->assign('MESSENGER_ID', $focus->messenger_id);


$sugar_smarty->assign('CALENDAR_PUBLISH_KEY', $focus->getPreference('calendar_publish_key' ));

//$sugar_smarty->parse('main.freebusy');

$sugar_smarty->display('modules/Users/EditView.tpl');
$json = getJSONobj();
require_once('include/QuickSearchDefaults.php');
$qsd = new QuickSearchDefaults();
$sqs_objects = array('reports_to_name' => $qsd->getQSUser());
$sqs_objects['reports_to_name']['populate_list'] = array('reports_to_name', 'reports_to_id');
$quicksearch_js = '<script type="text/javascript" language="javascript">
                    sqs_objects = ' . $json->encode($sqs_objects) . '</script>';
echo $quicksearch_js;


$savedSearch = new SavedSearch();
$savedSearchSelects = $json->encode(array($GLOBALS['app_strings']['LBL_SAVED_SEARCH_SHORTCUT'] . '<br>' . $savedSearch->getSelect('Users')));
$str = "<script>
YAHOO.util.Event.addListener(window, 'load', SUGAR.util.fillShortcuts, $savedSearchSelects);
</script>";
//echo $str;
//BUG #16298
?>
