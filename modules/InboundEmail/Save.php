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






global $current_user;

$focus = new InboundEmail();
$focus->retrieve($_REQUEST['record']);

foreach($focus->column_fields as $field) {
	if(isset($_REQUEST[$field])) {
		if ($field != "group_id") {
			$focus->$field = $_REQUEST[$field];
		}
	}
}
foreach($focus->additional_column_fields as $field) {
	if(isset($_REQUEST[$field])) {
		$value = $_REQUEST[$field];
		$focus->$field = $value;
	}
}
foreach($focus->required_fields as $field) {
	if(isset($_REQUEST[$field])) {
		$value = $_REQUEST[$field];
		$focus->$field = $value;
	}
}
$focus->email_password = $_REQUEST['email_password'];
$focus->protocol = $_REQUEST['protocol'];
$groupFolderId=null;
if (isset($_REQUEST['group_folder_id'])) {
     $groupFolderId = $_REQUEST['group_folder_id'];
}

$focus->groupfolder_id = $groupFolderId;

/////////////////////////////////////////////////////////
////	SERVICE STRING CONCATENATION
$useSsl = (isset($_REQUEST['ssl']) && $_REQUEST['ssl'] == 1) ? true : false;
$optimum = $focus->getSessionConnectionString($focus->server_url, $focus->email_user, $focus->port, $focus->protocol);
if (empty($optimum)) {
	$optimum = $focus->findOptimumSettings($useSsl, $focus->email_user, $focus->email_password, $focus->server_url, $focus->port, $focus->protocol, $focus->mailbox);
} // if
$delimiter = $focus->getSessionInboundDelimiterString($focus->server_url, $focus->email_user, $focus->port, $focus->protocol);

//added check to ensure the $optimum['serial']) is not empty.
if(is_array($optimum) && (count($optimum) > 0) && !empty( $optimum['serial'])) {
	$focus->service = $optimum['serial'];
} else {
	// no save
	// allowing bad save to allow Email Campaigns configuration to continue even without IMAP
	$focus->service = "::::::".$focus->protocol."::::"; // save bogus info.
	$error = "&error=true";
}
////	END SERVICE STRING CONCAT
/////////////////////////////////////////////////////////

if(isset($_REQUEST['mark_read']) && $_REQUEST['mark_read'] == 1) {
	$focus->delete_seen = 0;
} else {
	$focus->delete_seen = 0;
}

// handle stored_options serialization
if(isset($_REQUEST['only_since']) && $_REQUEST['only_since'] == 1) {
	$onlySince = true;
} else {
	$onlySince = false;
}
$stored_options = array();
$stored_options['from_name'] = $_REQUEST['from_name'];
$stored_options['from_addr'] = $_REQUEST['from_addr'];
$stored_options['reply_to_name'] = $_REQUEST['reply_to_name'];
$stored_options['reply_to_addr'] = $_REQUEST['reply_to_addr'];
$stored_options['only_since'] = $onlySince;
$stored_options['filter_domain'] = $_REQUEST['filter_domain'];
if (!empty($focus->groupfolder_id)) {
	if ($_REQUEST['leaveMessagesOnMailServer'] == "1") {
		$stored_options['leaveMessagesOnMailServer'] = 1;
	} else {
		$stored_options['leaveMessagesOnMailServer'] = 0;
	}
} // if
if (!$focus->isPop3Protocol()) {
	$stored_options['trashFolder'] = (isset($_REQUEST['trashFolder']) ? $_REQUEST['trashFolder'] : "");
	$stored_options['sentFolder'] = (isset($_REQUEST['sentFolder']) ? $_REQUEST['sentFolder'] : "");
} // if
if ($focus->isMailBoxTypeCreateCase()) {
	$stored_options['distrib_method'] = (isset($_REQUEST['distrib_method'])) ? $_REQUEST['distrib_method'] : "";
	$stored_options['create_case_email_template'] = (isset($_REQUEST['create_case_template_id'])) ? $_REQUEST['create_case_template_id'] : "";
} // if
$storedOptions['folderDelimiter'] = $delimiter;
$focus->stored_options = base64_encode(serialize($stored_options));

$GLOBALS['log']->info('----->InboundEmail now saving self');

////////////////////////////////////////////////////////////////////////////////
////    CREATE MAILBOX QUEUE
////////////////////////////////////////////////////////////////////////////////
if (!isset($focus->id)) {
	if(isset($_REQUEST['group_id']) && $_REQUEST['group_id'] == 'new') {
		if($uid = $focus->groupUserDupeCheck()) {
			$focus->group_id = $uid;
		} else {
			$focus->group_id = createGroupUser($focus->name);
		}
	} elseif(!empty($_REQUEST['group_id']) && $_REQUEST['group_id'] != 'new') {
		$focus->group_id = $_REQUEST['group_id'];
	}
}








////////////////////////////////////////////////////////////////////////////////
////    SEND US TO SAVE DESTINATION
////////////////////////////////////////////////////////////////////////////////
//_ppd($focus);
$focus->save();

$_REQUEST['return_id'] = $focus->id;


$edit='';
if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] != "") {
	$return_module = $_REQUEST['return_module'];
} else {
	$return_module = "InboundEmail";
}
if(isset($_REQUEST['return_action']) && $_REQUEST['return_action'] != "") {
	$return_action = $_REQUEST['return_action'];
} else {
	$return_action = "DetailView";
}
if(isset($_REQUEST['return_id']) && $_REQUEST['return_id'] != "") {
	$return_id = $_REQUEST['return_id'];
}
if(!empty($_REQUEST['edit'])) {
	$return_id='';
	$edit='&edit=true';
}

$GLOBALS['log']->debug("Saved record with id of ".$return_id);

/*
// cache results
if(!file_exists($focus->InboundEmailCachePath) || !file_exists($focus->InboundEmailCachePath.'/'.$focus->InboundEmailCacheFile)) {
	// create directory if not existent
	mkdir_recursive($focus->InboundEmailCachePath, false);
}
// write cache file
write_array_to_file('InboundEmailCached', $focus->getInboundEmailWithGuids(), $focus->InboundEmailCachePath.'/'.$focus->InboundEmailCacheFile);
*/


header("Location: index.php?module=$return_module&action=$return_action&record=$return_id$edit$error");
?>
