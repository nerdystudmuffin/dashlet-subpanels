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
$_REQUEST['edit']='true';



require_once('include/SugarFolders/SugarFolders.php');




require_once('include/templates/TemplateGroupChooser.php');
require_once('modules/InboundEmail/Forms.php');


// GLOBALS
global $mod_strings;
global $app_strings;
global $app_list_strings;
global $current_user;

$focus = new InboundEmail();
$focus->checkImap();
$javascript = new Javascript();
$email = new Email();
/* Start standard EditView setup logic */

if(isset($_REQUEST['record'])) {
	$GLOBALS['log']->debug("In InboundEmail edit view, about to retrieve record: ".$_REQUEST['record']);
	$result = $focus->retrieve($_REQUEST['record']);
    if($result == null)
    {
    	sugar_die($app_strings['ERROR_NO_RECORD']);
    }
}
if(isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
	$GLOBALS['log']->debug("isDuplicate found - duplicating record of id: ".$focus->id);
	$focus->id = "";
}

$GLOBALS['log']->info("InboundEmail Edit View");
/* End standard EditView setup logic */

/* Start custom setup logic */
// status drop down
$status = get_select_options_with_id_separate_key($app_list_strings['user_status_dom'],$app_list_strings['user_status_dom'], $focus->status);
// Groups
$selectGroups = '<option value="new">'.$mod_strings['LBL_CREATE_NEW_GROUP'].'</option>';

// handle if this I-E is personal or group
$isPersonal = false;
if(!empty($focus->id)) {
	$isPersonal = $focus->handleIsPersonal();
}
if($selects = $focus->getGroupsWithSelectOptions()) {
	$selectGroups .= $selects;
}
if($isPersonal) {
	// stomp out standard
	$selectGroups = '<option value="'.$focus->group_id.'">'.$focus->getUserNameFromGroupId().'</option>';
}
// default MAILBOX value
if(empty($focus->mailbox)) {
	$mailbox = 'INBOX';
} else {
	$mailbox = $focus->mailbox;
}

// service options breakdown
$tls = '';
$notls = '';
$cert = '';
$novalidate_cert = '';
$ssl = '';
if(!empty($focus->service)) {
	// will always have 2 values: /tls || /notls and /validate-cert || /novalidate-cert
	$exServ = explode('::', $focus->service);
	if($exServ[0] == 'tls') {
		$tls = "CHECKED";
	} elseif($exServ[5] == 'notls') {
		$notls = "CHECKED";
	}
	if($exServ[1] == 'validate-cert') {
		$cert = "CHECKED";
	} elseif($exServ[4] == 'novalidate-cert') {
		$novalidate_cert = 'CHECKED';
	}
	if(isset($exServ[2]) && !empty($exServ[2]) && $exServ[2] == 'ssl') {
		$ssl = "CHECKED";
	}
}
$mark_read = '';
if($focus->delete_seen == 0 || empty($focus->delete_seen)) {
	$mark_read = 'CHECKED';
}

// mailbox type
$domMailBoxType = $app_list_strings['dom_mailbox_type'];
if ($focus->is_personal) {
	array_splice($domMailBoxType, 1, 1);
} // if
$mailbox_type = get_select_options_with_id($domMailBoxType, $focus->mailbox_type);

// auto-reply email template
$email_templates_arr = get_bean_select_array(true, 'EmailTemplate','name', '','name',true);

if(!empty($focus->stored_options)) {
	$storedOptions = unserialize(base64_decode($focus->stored_options));
	$from_name = $storedOptions['from_name'];
	$from_addr = $storedOptions['from_addr'];

	$reply_to_name = (isset($storedOptions['reply_to_name'])) ? $storedOptions['reply_to_name'] : "";
	$reply_to_addr = (isset($storedOptions['reply_to_addr'])) ? $storedOptions['reply_to_addr'] : "";

	$trashFolder = (isset($storedOptions['trashFolder'])) ? $storedOptions['trashFolder'] : "";
	$sentFolder = (isset($storedOptions['sentFolder'])) ? $storedOptions['sentFolder'] : "";
	$distrib_method = (isset($storedOptions['distrib_method'])) ? $storedOptions['distrib_method'] : "";
	$create_case_email_template = (isset($storedOptions['create_case_email_template'])) ? $storedOptions['create_case_email_template'] : "";

	if($storedOptions['only_since']) {
		$only_since = 'CHECKED';
	} else {
		$only_since = '';
	}
	if(isset($storedOptions['filter_domain']) && !empty($storedOptions['filter_domain'])) {
		$filterDomain = $storedOptions['filter_domain'];
	} else {
		$filterDomain = '';
	}
	if(!isset($storedOptions['leaveMessagesOnMailServer']) || $storedOptions['leaveMessagesOnMailServer'] == 1) {
		$leaveMessagesOnMailServer = 1;
	} else {
		$leaveMessagesOnMailServer = 0;
	} // else
} else { // initialize empty vars for template
	$from_name = '';
	$from_addr = '';
	$reply_to_name = '';
	$reply_to_addr = '';
	$only_since = '';
	$filterDomain = '';
	$trashFolder = '';
	$sentFolder = '';
	$distrib_method ='';
	$create_case_email_template='';
	$leaveMessagesOnMailServer = 1;
} // else

// return action
if(isset($focus->id)) {
	$return_action = 'DetailView';
} else {
	$return_action = 'ListView';
}

// javascript
$javascript->setSugarBean($focus);
$javascript->setFormName('EditView');
$javascript->addRequiredFields();
$javascript->addFieldGeneric('email_user', 'alpha', $mod_strings['LBL_LOGIN'], true);
$javascript->addFieldGeneric('email_password', 'alpha', $mod_strings['LBL_PASSWORD'], true);

$r = $focus->db->query('SELECT value FROM config WHERE name = \'fromname\'');
$a = $focus->db->fetchByAssoc($r);
$default_from_name = $a['value'];
$r = $focus->db->query('SELECT value FROM config WHERE name = \'fromaddress\'');
$a = $focus->db->fetchByAssoc($r);
$default_from_addr = $a['value'];

/* End custom setup logic */


// TEMPLATE ASSIGNMENTS
$xtpl = new XTemplate('modules/InboundEmail/EditView.html');
// if no IMAP libraries available, disable Save/Test Settings
if(!function_exists('imap_open')) {
	$xtpl->assign('IE_DISABLED', 'DISABLED');
}
// standard assigns
$xtpl->assign('MOD', $mod_strings);
$xtpl->assign('APP', $app_strings);
$xtpl->assign('THEME', SugarThemeRegistry::current()->__toString());
$xtpl->assign('GRIDLINE', $gridline);
$xtpl->assign('MODULE', 'InboundEmail');
$xtpl->assign('RETURN_MODULE', 'InboundEmail');
$xtpl->assign('RETURN_ID', $focus->id);
$xtpl->assign('RETURN_ACTION', $return_action);
$xtpl->assign('PERSONAL', $isPersonal);
$xtpl->assign('JAVASCRIPT', get_set_focus_js().$javascript->getScript());
// module specific
$xtpl->assign('ROLLOVER', $email->rolloverStyle);
$xtpl->assign('MODULE_TITLE', get_module_title($mod_strings['LBL_MODULE_TITLE'], $mod_strings['LBL_MODULE_NAME'].": ".$focus->name, true));
$xtpl->assign('ID', $focus->id);
$xtpl->assign('NAME', $focus->name);
$xtpl->assign('STATUS', $status);
$xtpl->assign('SERVER_URL', $focus->server_url);
$xtpl->assign('USER', $focus->email_user);
$xtpl->assign('PASSWORD', $focus->email_password);
$xtpl->assign('TRASHFOLDER', $trashFolder);
$xtpl->assign('SENTFOLDER', $sentFolder);
$xtpl->assign('MAILBOX', $mailbox);
$xtpl->assign('TLS', $tls);
$xtpl->assign('NOTLS', $notls);
$xtpl->assign('CERT', $cert);
$xtpl->assign('NOVALIDATE_CERT', $novalidate_cert);
$xtpl->assign('SSL', $ssl);
$xtpl->assign('PROTOCOL', get_select_options_with_id($app_list_strings['dom_email_server_type'], $focus->protocol));
$xtpl->assign('MARK_READ', $mark_read);
$xtpl->assign('MAILBOX_TYPE', $mailbox_type);
$xtpl->assign('TEMPLATE_ID', $focus->template_id);
$xtpl->assign('EMAIL_TEMPLATE_OPTIONS', get_select_options_with_id($email_templates_arr, $focus->template_id));
$xtpl->assign('ONLY_SINCE', $only_since);
$xtpl->assign('FILTER_DOMAIN', $filterDomain);
if(!empty($focus->port)) {
	$xtpl->assign('PORT', $focus->port);
}
// groups
$xtpl->assign('GROUP_ID', $selectGroups);
// auto-reply stuff
$xtpl->assign('FROM_NAME', $from_name);
$xtpl->assign('FROM_ADDR', $from_addr);
$xtpl->assign('DEFAULT_FROM_NAME', $default_from_name);
$xtpl->assign('DEFAULT_FROM_ADDR', $default_from_addr);
$xtpl->assign('REPLY_TO_NAME', $reply_to_name);
$xtpl->assign('REPLY_TO_ADDR', $reply_to_addr);
$createCaseRowStyle = "display:none";
if($focus->template_id) {
	$xtpl->assign("EDIT_TEMPLATE","visibility:inline");
} else {
	$xtpl->assign("EDIT_TEMPLATE","visibility:hidden");
}
if($focus->port == 110 || $focus->port == 995) {
	$xtpl->assign('DISPLAY', "display:''");
} else {
	$xtpl->assign('DISPLAY', "display:none");
}
$leaveMessagesOnMailServerStyle = "display:none";
if($focus->is_personal) {
	$xtpl->assign('DISABLE_GROUP', 'DISABLED');
	$xtpl->assign('EDIT_GROUP_FOLDER_STYLE', "display:none");
	$xtpl->assign('CREATE_GROUP_FOLDER_STYLE', "display:none");
} else {
	$folder = new SugarFolder();
	$xtpl->assign('CREATE_GROUP_FOLDER_STYLE', "display:''");
	$ret = $folder->getFoldersForSettings($current_user);
	$groupFolders = Array();
	foreach($ret['groupFolders'] as $key => $value) {
		$groupFolders[$value['id']] = $value['name'];
	} // foreach
	if (!empty($focus->groupfolder_id)) {
		$xtpl->assign('EDIT_GROUP_FOLDER_STYLE', "visibility:inline");
		$leaveMessagesOnMailServerStyle = "display:''";
	} else {
		$xtpl->assign('EDIT_GROUP_FOLDER_STYLE', "visibility:hidden");
	} // else
	$xtpl->assign('GROUP_FOLDER_OPTIONS', get_select_options_with_id($groupFolders, $focus->groupfolder_id));

	if ($focus->isMailBoxTypeCreateCase()) {
		$createCaseRowStyle = "display:''";
	}
}
$xtpl->assign('LEAVEMESSAGESONMAILSERVER_STYLE', $leaveMessagesOnMailServerStyle);
$xtpl->assign('LEAVEMESSAGESONMAILSERVER', get_select_options_with_id($app_list_strings['dom_int_bool'], $leaveMessagesOnMailServer));
$distributionMethod = get_select_options_with_id($app_list_strings['dom_email_distribution_for_auto_create'], $distrib_method);
$xtpl->assign('DISTRIBUTION_METHOD', $distributionMethod);
$xtpl->assign('CREATE_CASE_ROW_STYLE', $createCaseRowStyle);
$xtpl->assign('CREATE_CASE_EMAIL_TEMPLATE_OPTIONS', get_select_options_with_id($email_templates_arr, $create_case_email_template));
if(!empty($create_case_email_template)) {
	$xtpl->assign("CREATE_CASE_EDIT_TEMPLATE","visibility:inline");
} else {
	$xtpl->assign("CREATE_CASE_EDIT_TEMPLATE","visibility:hidden");
}



















// WINDOWS work arounds
//if(is_windows()) {
//	$xtpl->assign('MAYBE', '<style> div.maybe { display:none; }</style>');
//}
// PARSE AND PRINT
$xtpl->parse("main");
$xtpl->out("main");
?>
