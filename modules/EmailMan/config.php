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




require_once('modules/EmailMan/Forms.php');

global $mod_strings;
global $app_list_strings;
global $app_strings;
global $current_user;

if (!is_admin($current_user)&&!is_admin_for_module($GLOBALS['current_user'],'Emails')&&!is_admin_for_module($GLOBALS['current_user'],'Campaigns')) sugar_die("Unauthorized access to administration.");

echo get_module_title($mod_strings['LBL_MODULE_ID'], $mod_strings['LBL_MODULE_NAME'].": ".$mod_strings['LBL_CONFIGURE_SETTINGS'], true);
global $currentModule;





$focus = new Administration();
$focus->retrieveSettings(); //retrieve all admin settings.
$GLOBALS['log']->info("Mass Emailer(EmailMan) ConfigureSettings view");

$xtpl=new XTemplate ('modules/EmailMan/config.html');
$xtpl->assign("MOD", $mod_strings);
$xtpl->assign("APP", $app_strings);

$xtpl->assign("RETURN_MODULE", "Administration");
$xtpl->assign("RETURN_ACTION", "index");

$xtpl->assign("MODULE", $currentModule);
$xtpl->assign("PRINT_URL", "index.php?".$GLOBALS['request_string']);
$xtpl->assign("HEADER", get_module_title("EmailMan", "{MOD.LBL_CONFIGURE_SETTINGS}", true));

$xtpl->assign("notify_fromaddress", $focus->settings['notify_fromaddress']);
$xtpl->assign("notify_send_by_default", ($focus->settings['notify_send_by_default']) ? "checked='checked'" : "");
$xtpl->assign("notify_send_from_assigning_user", (isset($focus->settings['notify_send_from_assigning_user']) && !empty($focus->settings['notify_send_from_assigning_user'])) ? "checked='checked'" : "");
$xtpl->assign("notify_on", ($focus->settings['notify_on']) ? "checked='checked'" : "");
$xtpl->assign("notify_fromname", $focus->settings['notify_fromname']);

// show Gmail defaults link
$showGmail = ($focus->settings['mail_sendtype'] == 'SMTP') ? 'inline' : 'none';
$xtpl->assign("gmailSmtpLink", $showGmail);
$xtpl->assign("mail_smtpserver", $focus->settings['mail_smtpserver']);
$xtpl->assign("mail_smtpport", $focus->settings['mail_smtpport']);
$xtpl->assign("mail_sendtype_options", get_select_options_with_id($app_list_strings['notifymail_sendtype'], $focus->settings['mail_sendtype']));
$xtpl->assign("mail_smtpuser", $focus->settings['mail_smtpuser']);
$xtpl->assign("mail_smtppass", $focus->settings['mail_smtppass']);
$xtpl->assign("mail_smtpauth_req", ($focus->settings['mail_smtpauth_req']) ? "checked='checked'" : "");
$xtpl->assign("MAIL_SSL_OPTIONS", get_select_options_with_id($app_list_strings['email_settings_for_ssl'], $focus->settings['mail_smtpssl']));

///////////////////////////////////////////////////////////////////////////////
////	USER EMAIL DEFAULTS
// editors
$editors = $app_list_strings['dom_email_editor_option'];
$newEditors = array();
foreach($editors as $k => $v) {
	if($k != "") { $newEditors[$k] = $v; }
}
$defaultEditor = '';
if(isset($sugar_config['email_default_editor'])) {
	$defaultEditor = $sugar_config['email_default_editor'];
}
$xtpl->assign('DEFAULT_EDITOR', get_select_options_with_id($newEditors, $defaultEditor));

//clients
$clients = $app_list_strings['dom_email_link_type'];
$newClients = array();
foreach($clients as $k => $v) {
	if($k != '') { $newClients[$k] = $v; }
}
$defaultClient = '';
if(isset($sugar_config['email_default_client'])) {
	$defaultClient = $sugar_config['email_default_client'];
}
$xtpl->assign('DEFAULT_CLIENT', get_select_options_with_id($newClients, $defaultClient));

// charset
$charsets = get_select_options_with_id($locale->getCharsetSelect(), $sugar_config['default_email_charset']);
$xtpl->assign('DEFAULT_EMAIL_CHARSET', $charsets);

// preserve attachments
$preserveAttachments = '';
if(isset($sugar_config['email_default_delete_attachments']) && $sugar_config['email_default_delete_attachments'] == true) {
	$preserveAttachments = 'CHECKED';
} 
$xtpl->assign('DEFAULT_EMAIL_DELETE_ATTACHMENTS', $preserveAttachments);
////	END USER EMAIL DEFAULTS
///////////////////////////////////////////////////////////////////////////////


//setting to manage.
//emails_per_run
//tracking_entities_location_type default or custom
//tracking_entities_location http://www.sugarcrm.com/track/
$outboundRaw = (isset($sugar_config['email_outbound_save_raw']) && $sugar_config['email_outbound_save_raw'] == true) ? 'email_outbound_save_raw_yes' : 'email_outbound_save_raw_no';
$xtpl->assign($outboundRaw, 'CHECKED');

//////////////////////////////////////////////////////////////////////////////
////	EMAIL SECURITY
if(!isset($sugar_config['email_xss']) || empty($sugar_config['email_xss'])) {
	$sugar_config['email_xss'] = getDefaultXssTags();
}

foreach(unserialize(base64_decode($sugar_config['email_xss'])) as $k => $v) {
	$xtpl->assign($k."Checked", 'CHECKED');
}

if(!isset($sugar_config['email_preserve_raw']) || empty($sugar_config['email_preserve_raw'])) {
	$sugar_config['email_preserve_raw'] = 0;
}
$xtpl->assign("preserve_rawChecked", empty($sugar_config['email_preserve_raw']) ? "" : "CHECKED");
//clean_xss('here');
////	END EMAIL SECURITY
///////////////////////////////////////////////////////////////////////////////








$xtpl->assign("JAVASCRIPT",get_validate_record_js());
$xtpl->parse("main");

$xtpl->out("main");
?>
