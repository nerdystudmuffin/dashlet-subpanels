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
 * *******************************************************************************/
/*********************************************************************************

 * Description:
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc. All Rights
 * Reserved. Contributor(s): ______________________________________..
 *********************************************************************************/
function getQuotesRelatedData($bean) {
	$return = array();
	$emailId = $_REQUEST['recordId'];
  	
  	require_once("modules/Emails/EmailUI.php");
	$email = new Email();
	$email->retrieve($emailId);
	$return['subject'] = $email->name;
	$return['body'] = from_html($email->description_html);
	$return['toAddress'] = $email->to_addrs;
	$ret = array();
	$ret['uid'] = $emailId;
	$ret = EmailUI::getDraftAttachments($ret);
	$return['attachments'] = $ret['attachments'];
	$return['email_id'] = $emailId;
	return $return;
} // fn

// we will need the following:
if( isset($_REQUEST['parent_type']) && !empty($_REQUEST['parent_type']) &&
	isset($_REQUEST['parent_id']) && !empty($_REQUEST['parent_id']) &&
	!isset($_REQUEST['ListView']) && !isset($_REQUEST['replyForward'])) {
	global $beanList;
	global $beanFiles;
	global $mod_strings;

	$parentName = '';
	$class = $beanList[$_REQUEST['parent_type']];
	require_once($beanFiles[$class]);

	$bean = new $class();
	$bean->retrieve($_REQUEST['parent_id']);
	if (isset($bean->full_name)) {
		$parentName = $bean->full_name;
	} else {
		$parentName = $bean->name;
	}
	$namePlusEmail = '';
	if (isset($_REQUEST['to_email_addrs'])) {
		$namePlusEmail = from_html($_REQUEST['to_email_addrs']);
		$namePlusEmail = str_replace("&nbsp;", " ", $namePlusEmail);
	} else {
		if (isset($bean->full_name)) {
			$namePlusEmail = "{$bean->full_name} <".$bean->emailAddress->getPrimaryAddress($bean).">";
		} else  if(isset($bean->emailAddress)){
			$namePlusEmail = "<".$bean->emailAddress->getPrimaryAddress($bean).">";
		}
	}

	$subject = "";
	$body = "";
	$email_id = "";
	$attachments = array();
	if ($bean->module_dir == 'Cases') {
		$subject = $mod_strings['LBL_RE'] . " " . str_replace('%1', $bean->case_number, $bean->getEmailSubjectMacro() . " ". $bean->name) ;
	}
	if ($bean->module_dir == 'KBDocuments') {
		
  		require_once("modules/Emails/EmailUI.php");
		$subject = $bean->kbdocument_name;
		$article_body = str_replace('/'.$GLOBALS['sugar_config']['cache_dir'].'images/',$sugar_config['site_url'].'/'.$GLOBALS['sugar_config']['cache_dir'].'images/',KBDocument::get_kbdoc_body_without_incrementing_count($bean->id));
		$body = from_html($article_body);
		$attachments = KBDocument::get_kbdoc_attachments_for_newemail($bean->id);
		$attachments = $attachments['attachments'];
	} // if
	if ($bean->module_dir == 'Quotes' && isset($_REQUEST['recordId'])) {
		$quotesData = getQuotesRelatedData($bean);
		global $current_language;
		$namePlusEmail = $quotesData['toAddress'];
		$subject = $quotesData['subject'];
		$body = $quotesData['body'];
		$attachments = $quotesData['attachments'];
		$email_id = $quotesData['email_id'];
	} // if
	$ret = array(
		'to_email_addrs' => $namePlusEmail,
		'parent_type'	 => $_REQUEST['parent_type'],
		'parent_id'	     => $_REQUEST['parent_id'],
		'parent_name'    => $parentName,
		'subject'		 => $subject,
		'body'			 => $body,
		'attachments'	 => $attachments,
		'email_id'		 => $email_id,
	);
} else if(isset($_REQUEST['ListView'])) {
  	
	$email = new Email();
	$namePlusEmail = $email->getNamePlusEmailAddressesForCompose($_REQUEST['action_module'], (split(",", $_REQUEST['uid'])));
	$ret = array(
		'to_email_addrs' => $namePlusEmail,
	);
} else if (isset($_REQUEST['replyForward'])) {
  	
  	require_once("modules/Emails/EmailUI.php");
  	
	$ret = array();
	$ie = new InboundEmail();
	$ie->email = new Email();
	$ie->email->email2init();
	$replyType = $_REQUEST['reply'];
	$email_id = $_REQUEST['record'];
    $ie->email->retrieve($email_id);
    $ie->email->from_addr = $ie->email->from_addr_name;
    $ie->email->to_addrs = to_html($ie->email->to_addrs_names);
    $ie->email->cc_addrs = to_html($ie->email->cc_addrs_names);
    $ie->email->bcc_addrs = $ie->email->bcc_addrs_names;
    $ie->email->from_name = $ie->email->from_addr;
    $email = $ie->email->et->handleReplyType($ie->email, $replyType);
    //if ($replyType == "forward") {
    	$emailHeader = $email->description;
    //} // if
    $ret = $ie->email->et->displayComposeEmail($email);
	//if ($replyType == "forward") {
    	$ret['description'] = $emailHeader;
    //} // if
	if ($replyType != "forward") {
    	//$ret['description'] = empty($email->description_html) ?  str_replace("\n", "\n<BR/>", $email->description) : $email->description_html;
    } // if
    if ($replyType == 'forward') {
    	$ret = $ie->email->et->getDraftAttachments($ret);
    }
    $return = $ie->email->et->getFromAllAccountsArray($ie, $ret);

	if ($replyType == "forward") {
		$return['to'] = '';
	} else {
		$return['to'] = from_html($ie->email->from_addr);
	} // else
	$ret = array(
		'to_email_addrs' => $return['to'],
		'parent_type'	 => $return['parent_type'],
		'parent_id'	     => $return['parent_id'],
		'parent_name'    => $return['parent_name'],
		'subject'		 => $return['name'],
		'body'			 => "&nbsp;<div><hr></div>" . $return['description'],
		'attachments'	 => (isset($return['attachments']) ? $return['attachments'] : array()),
		'email_id'		 => $email_id,
		'fromAccounts'   => $return['fromAccounts'],
	);

} else {
	$ret = array(
		'to_email_addrs' => '',
	);
}
$json = getJSONobj();
$composeOut = $json->encode($ret);

include('modules/Emails/index.php');
echo "<script type='text/javascript' language='javascript'>\ncomposePackage = {$composeOut};\n</script>";
