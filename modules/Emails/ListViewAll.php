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








require_once('modules/MySettings/StoreQuery.php');


$focus				= new Email();
$header_text		= '';
$where				= '';
$type				= '';
$assigned_user_id	= '';
$group				= '';
$search_adv			= '';
$whereClauses		= array();
$error				= '';

///////////////////////////////////////////////////////////////////////////////
////
////	SEARCH FORM FUNCTIONALITY
////	SEARCH QUERY GENERATION
$storeQuery = new StoreQuery();

// this allows My Inbox, Group Inbox, etc. to have separate stored queries
// for the same ListView.php
if(isset($_REQUEST['type'])) $Qtype = $_REQUEST['type'];
else $Qtype = '';
if(isset($_REQUEST['assigned_user_id']) && $_REQUEST['assigned_user_id'] == $current_user->id) {
	$Qassigned_user_id = $_REQUEST['assigned_user_id'];
} else {
	$Qassigned_user_id = '';
}

if(!isset($_REQUEST['query'])){
	//_pp('loading: '.$currentModule.$Qtype.$Qgroup);
	//_pp($current_user->user_preferences[$currentModule.$Qtype.'Q']);
	$storeQuery->loadQuery($currentModule.$Qtype);
	$storeQuery->populateRequest();
} else {
	//_pp($current_user->user_preferences[$currentModule.$Qtype.'Q']);
	//_pp('saving: '.$currentModule.$Qtype);
	$storeQuery->saveFromGet($currentModule.$Qtype);
}


if(isset($_REQUEST['query'])) {
	// we have a query
	if (isset($_REQUEST['email_type']))		$email_type = $_REQUEST['email_type'];
	if (isset($_REQUEST['assigned_to']))	$assigned_to = $_REQUEST['assigned_to'];
	if (isset($_REQUEST['status']))			$status = $_REQUEST['status'];
	if (isset($_REQUEST['name']))			$name = $_REQUEST['name'];
	if (isset($_REQUEST['contact_name']))	$contact_name = $_REQUEST['contact_name'];
	
	if(isset($email_type) && $email_type != "")		$whereClauses['emails.type'] = "emails.type = '".$GLOBALS['db']->quote($email_type)."'";
	if(isset($assigned_to) && $assigned_to != "")	$whereClauses['emails.assigned_user_id'] = "emails.assigned_user_id = '".$GLOBALS['db']->quote($assigned_to)."'";
	if(isset($status) && $status != "")				$whereClauses['emails.status'] = "emails.status = '".$GLOBALS['db']->quote($status)."'";
	if(isset($name) && $name != "")					$whereClauses['emails.name'] = "emails.name like '".$GLOBALS['db']->quote($name)."%'";
	if(isset($contact_name) && $contact_name != '') {
		$contact_names = explode(" ", $contact_name);
		foreach ($contact_names as $name) {
			$whereClauses['contacts.name'] = "(contacts.first_name like '".$GLOBALS['db']->quote($name)."%' OR contacts.last_name like '".$GLOBALS['db']->quote($name)."%')";
		}
	}

	$focus->custom_fields->setWhereClauses($whereClauses);
	$GLOBALS['log']->info("Here is the where clause for the list view: $where");
} // end isset($_REQUEST['query'])



////	OUTPUT GENERATION

if (!isset($_REQUEST['search_form']) || $_REQUEST['search_form'] != 'false') {
	// ASSIGNMENTS pre-processing
	$email_type_sel = '';
	$assigned_to_sel = '';
	$status_sel = '';
	if(isset($_REQUEST['email_type']))		$email_type_sel = $_REQUEST['email_type'];
	if(isset($_REQUEST['assigned_to']))		$assigned_to_sel = $_REQUEST['assigned_to'];
	if(isset($_REQUEST['status']))			$status_sel = $_REQUEST['status'];
	if(isset($_REQUEST['search']))			$search_adv = $_REQUEST['search'];

	// drop-downs values
	$r = $focus->db->query("SELECT id, user_name FROM users WHERE deleted = 0 AND status = 'Active' OR users.is_group = 1 ORDER BY status");
	$users[] = '';
	while($a = $focus->db->fetchByAssoc($r)) {
		$users[$a['id']] = $a['user_name'];
	}
	
	$email_types[] = '';
	$email_types = array_merge($email_types, $app_list_strings['dom_email_types']);
	$email_status[] = '';
	$email_status = array_merge($email_status, $app_list_strings['dom_email_status']);
	$types			= get_select_options_with_id($email_types, $email_type_sel);
	$assigned_to	= get_select_options_with_id($users, $assigned_to_sel);
	$email_status	= get_select_options_with_id($email_status, $status_sel);
	
	// ASSIGNMENTS AND OUTPUT
	if(isset($_REQUEST['type']) && $_REQUEST['type'] != '') $emailType = $_REQUEST['type'];
	else $emailType = '';
	switch($emailType) {
		case 'out':
			$search_form = new XTemplate ('modules/Emails/SearchFormSent.html');
		break;

		case 'draft':
			$search_form = new XTemplate ('modules/Emails/SearchFormSent.html');
		break;

		case 'archived':
		case 'inbound':
			$search_form = new XTemplate ('modules/Emails/SearchFormMyInbox.html');
		break;
		
		default:
			$search_form = new XTemplate ('modules/Emails/SearchFormMyInbox.html');
		break;
	}
	
	$search_form->assign('MOD', $mod_strings);
	$search_form->assign('APP', $app_strings);
	$search_form->assign('ADVANCED_SEARCH_PNG', SugarThemeRegistry::current()->getImage('advanced_search','alt="'.$app_strings['LNK_ADVANCED_SEARCH'].'"  border="0"'));
	$search_form->assign('BASIC_SEARCH_PNG', SugarThemeRegistry::current()->getImage('basic_search','alt="'.$app_strings['LNK_BASIC_SEARCH'].'"  border="0"'));
	$search_form->assign('TYPE_OPTIONS', $types);
	$search_form->assign('ASSIGNED_TO_OPTIONS', $assigned_to);
	$search_form->assign('STATUS_OPTIONS', $email_status);
	$search_form->assign('ADV_URL', $_SERVER['REQUEST_URI']);
	$search_form->assign('SEARCH_ADV', $search_adv);
	

	if(isset($_REQUEST['name']))			$search_form->assign('NAME', $_REQUEST['name']);
	if(isset($_REQUEST['contact_name']))	$search_form->assign('CONTACT_NAME', $_REQUEST['contact_name']);
	if(isset($current_user_only))			$search_form->assign('CURRENT_USER_ONLY', "checked");

	// adding custom fields:
	$focus->custom_fields->populateXTPL($search_form, 'search' );
	$search_form->assign('SEARCH_ACTION', 'ListView');
	$search_form->assign('TYPE', $Qtype);
	if(!empty($_REQUEST['assigned_user_id'])) {
		$search_form->assign('ASSIGNED_USER_ID', $_REQUEST['assigned_user_id']);
	}
	$search_form->assign('JAVASCRIPT', $focus->js_set_archived().$focus->u_get_clear_form_js($Qtype, '', $Qassigned_user_id));
}
////	END SEARCH FORM FUNCTIONALITY
////	
///////////////////////////////////////////////////////////////////////////////







// STANDARD EMAIL BOX FUNCTIONS
global $email_title;
$display_title = $mod_strings['LBL_LIST_FORM_TITLE'];
if($email_title)$display_title = $email_title;

$ListView = new ListView();
$ListView->initNewXTemplate('modules/Emails/ListViewAll.html', $mod_strings);

///////////////////////////////////////////////////////////////////////////////
////	OUTPUT
///////////////////////////////////////////////////////////////////////////////
echo get_module_title("Emails", $mod_strings['LBL_MODULE_TITLE'].$display_title, true); 
// admin-edit
if(is_admin($current_user) && $_REQUEST['module'] != 'DynamicLayout' && !empty($_SESSION['editinplace'])){	
	$header_text = "&nbsp;<a href='index.php?action=index&module=DynamicLayout&from_action=SearchForm&from_module=".$_REQUEST['module'] ."'>".SugarThemeRegistry::current()->getImage("EditLayout","border='0' alt='Edit Layout' align='bottom'")."</a>";
}
// search form
echo get_form_header($mod_strings['LBL_SEARCH_FORM_TITLE']. $header_text, "", false);
// ADVANCED SEARCH
if(isset($_REQUEST['search']) && $_REQUEST['search'] == 'advanced') {
	$search_form->parse('adv');
	$search_form->out('adv');

} else {
	$search_form->parse('main');
	$search_form->out('main');
}

// CONSTRUCT WHERE STRING FROM WHERECLAUSE ARRAY
foreach($whereClauses as $clause) {
	if($where != "")
	$where .= " AND ";
	$where .= $clause;
}

//echo $where;

//echo $focus->quickCreateJS();
$ListView->setAdditionalDetails();
$ListView->xTemplateAssign('ATTACHMENT_HEADER', SugarThemeRegistry::current()->getImage('attachment',"","",""));
$ListView->xTemplateAssign('ERROR', $error);
$ListView->setHeaderTitle($display_title . $header_text );
$ListView->setQuery($where, "", "date_sent, date_entered DESC", "EMAIL");
$ListView->processListView($focus, "main", "EMAIL");
?>
