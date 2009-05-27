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


global $app_strings;
global $app_list_strings;
global $sugar_version, $sugar_config;



 require_once('modules/Campaigns/utils.php');
global $theme;
$error_msg = '';
$selected_menu='';


global $current_language;
$mod_strings = return_module_language($current_language, 'Leads');
echo get_module_title($mod_strings['LBL_MODULE_NAME'], $mod_strings['LBL_MODULE_NAME'].": ".$mod_strings['LBL_CONVERTLEAD'], true);
$sugar_smarty = new Sugar_Smarty();
///
/// Assign the template variables
///
$sugar_smarty->assign("MOD", $mod_strings);
$sugar_smarty->assign("APP", $app_strings);
$sugar_smarty->assign("PRINT_URL", "index.php?".$GLOBALS['request_string']);

$sugar_smarty->assign("HEADER", $mod_strings['LBL_ADD_BUSINESSCARD']);

$sugar_smarty->assign("MODULE", $_REQUEST['module']);
if ($error_msg != '')
{
	$sugar_smarty->assign("ERROR", $error_msg);
}

if(isset($_POST['handle']) && $_POST['handle'] == 'Save'){
	
	require_once('modules/Contacts/ContactFormBase.php');
	$contactForm = new ContactFormBase();
	require_once('modules/Accounts/AccountFormBase.php');
	$accountForm = new AccountFormBase();
	require_once('modules/Opportunities/OpportunityFormBase.php');
	$oppForm = new OpportunityFormBase();
	require_once('modules/Leads/LeadFormBase.php');
	$leadForm = new LeadFormBase();
	$lead = new Lead();
	$lead->retrieve($_REQUEST['record']);

	$linked_beans[] = $lead->get_linked_beans('calls','Call');
    $linked_beans[] = $lead->get_linked_beans('meetings','Meeting');
    $linked_beans[] = $lead->get_linked_beans('emails','Email');
	$GLOBALS['check_notify'] = FALSE;

	//MFH #13473
	foreach ($_POST as $k => $v){
		if (is_array($v)){
			$val = implode('^,^',$_POST[$k]);
			$_POST[$k] = $val;
		}
	}
	$formbody=array();
	$sugar_smarty->assign('SAVE_BUTTON_DISPLAY', 'style="display:none;"');    
	if(!isset($_POST['selectedContact']) && !isset($_POST['ContinueContact'])){
		$duplicateContacts = $contactForm->checkForDuplicates('Contacts');
		if(isset($duplicateContacts)){
			$sugar_smarty->assign('DUPLICATEFORMBODY', $contactForm->buildTableForm($duplicateContacts,  'Contacts'));
			$selected_menu='form';
			echo $sugar_smarty->fetch('modules/Leads/ConvertLead.tpl');
            return;
		}
	}

	if(isset($_POST['newaccount']) && $_POST['newaccount']=='on' && empty($_POST['selectedAccount']) && empty($_POST['ContinueAccount'])){

		$duplicateAccounts = $accountForm->checkForDuplicates('Accounts');
		if(isset($duplicateAccounts)){
			$sugar_smarty->assign('DUPLICATEFORMBODY', $accountForm->buildTableForm($duplicateAccounts));
			$selected_menu='form';
			echo $sugar_smarty->fetch('modules/Leads/ConvertLead.tpl');
            return;
		}
	}

	if(isset($_POST['newopportunity']) && $_POST['newopportunity']=='on' &&!isset($_POST['selectedOpportunity']) && !isset($_POST['ContinueOpportunity'])){

		$duplicateOpps = $oppForm->checkForDuplicates('Opportunities');
		if(isset($duplicateOpps)){
			$sugar_smarty->assign('DUPLICATEFORMBODY', $oppForm->buildTableForm($duplicateOpps));
			$selected_menu='form';
			echo $sugar_smarty->fetch('modules/Leads/ConvertLead.tpl');
            return;
		}
	}

	if(!isset($_POST['selectedLeads']) && !isset($_POST['ContinueLead'])){
		$duplicateLeads = $leadForm->checkForDuplicates('Contacts', $_REQUEST['record']);
		if(isset($duplicateLeads)){
			$sugar_smarty->assign('DUPLICATEFORMBODY', $leadForm->buildTableForm($duplicateLeads, 'Leads'));
			$selected_menu='form';
			echo $sugar_smarty->fetch('modules/Leads/ConvertLead.tpl');
            return;
		}
	}

	if(isset($_POST['selectedContact']) && !empty($_POST['selectedContact'])){
		$contact = new Contact();
		$contact->retrieve($_POST['selectedContact']);
	}else{




		if(isset($lead->campaign_id) && $lead->campaign_id != null){
		 $_POST['Contactscampaign_id'] = $lead->campaign_id;
		}
		$contact= $contactForm->handleSave('Contacts',false, false);
		if(isset($lead->campaign_id) && $lead->campaign_id != null){
		 campaign_log_lead_entry($lead->campaign_id,$lead,$contact,'contact');
		}
	}
	if((isset($_POST['selectedAccount'])&& !empty($_POST['selectedAccount'])) || (isset($_POST['newaccount']) && $_POST['newaccount']=='on' )){
		if(isset($_POST['selectedAccount']) && !empty($_POST['selectedAccount'])){
			$account = new Account();
			$account->retrieve($_POST['selectedAccount']);
		}else{




			$account= $accountForm->handleSave('Accounts',false, false);

		}
	}
	if(isset($_POST['newopportunity']) && $_POST['newopportunity']=='on' ){

		if( isset($_POST['selectedOpportunity']) && !empty($_POST['selectedOpportunity'])){
			$opportunity = new Opportunity();
			$opportunity->retrieve($_POST['selectedOpportunity']);
		}else{
			if(isset($account)){
				$_POST['Opportunitiesaccount_id'] = $account->id;
				$_POST['Opportunitiesaccount_name'] = $account->name;
			}




			$_POST['Opportunitieslead_source'] = $lead->lead_source;
			$_POST['Opportunitiescampaign_id'] = $lead->campaign_id;
			if($current_user->getPreference('currency') ){
				
				$currency = new Currency();
				$currency->retrieve($current_user->getPreference('currency'));
				$_POST['Opportunitiescurrency_id'] = $currency->id;
			}
			$opportunity= $oppForm->handleSave('Opportunities',false, false);
		}
	}
	require_once('modules/Notes/NoteFormBase.php');

	$noteForm = new NoteFormBase();
	if(isset($account) && (isset($_POST['newaccountnote'])&& !empty($_POST['newaccountnote']))){




		$_POST['AccountNotesparent_id'] = $account->id;
		$accountnote= $noteForm->handleSave('AccountNotes',false, false);

		}
	if(isset($contact) && (isset($_POST['newcontactnote'])&& !empty($_POST['newcontactnote']))){




		$contactnote= $noteForm->handleSave('ContactNotes',false, false);

		}
	if(isset($opportunity) && (isset($_POST['newoppnote'])&& !empty($_POST['newoppnote']))){




		$opportunitynote= $noteForm->handleSave('OpportunityNotes',false, false);
		}





	if(isset($_POST['newmeeting']) && $_POST['newmeeting']=='on' ){
		if(isset($_POST['appointment']) && $_POST['appointment'] == 'Meeting'){
			require_once('modules/Meetings/MeetingFormBase.php');
			$meetingForm = new MeetingFormBase();


			$meeting= $meetingForm->handleSave('Appointments',false, false);
		}else{
			require_once('modules/Calls/CallFormBase.php');
			$callForm = new CallFormBase();
			$call= $callForm->handleSave('Appointments',false, false);
		}
	}

	if(isset($call)){
		if(isset($contact)) {
			$call->load_relationship('contacts');
			$call->contacts->add($contact->id);
		} else if(isset($account)){
			$call->load_relationship('account');
			$call->account->add($account->id);
		}else if(isset($opportunity)){
			$call->load_relationship('opportunity');
			$call->opportunity->add($opportunity->id);
		}
	}
	if(isset($meeting)){
		if(isset($contact)) {
			$meeting->load_relationship('contacts');
			$meeting->contacts->add($contact->id);
		} else if(isset($account)){
			$meeting->load_relationship('account');
			$meeting->account->add($account->id);
		}else if(isset($opportunity)){
			$meeting->load_relationship('opportunity');
			$meeting->opportunity->add($opportunity->id);
		}
	}

	if(isset($account)){
		if(isset($contact)) {
			$account->load_relationship('contacts');
			$account->contacts->add($contact->id);
		}
		if(isset($accountnote)){
			$account->load_relationship('notes');
			$account->notes->add($accountnote->id);
		}
	}
	if(isset($opportunity)){
		if(isset($contact)) {
			$opportunity->load_relationship('contacts');
			$opportunity->contacts->add($contact->id);
		}
		if(isset($opportunitynote)){
			$opportunity->load_relationship('notes');
			$opportunity->notes->add($opportunitynote->id);
		}
	}
	if(isset($contact)){
		if(isset($contactnote)){
			$contact->load_relationship('notes');
			$contact->notes->add($contactnote->id);
		}
	}
    
    $ROWVALUES= array();
    
	if(isset($contact)){

		if(isset($_POST['selectedContact']) && $_POST['selectedContact'] == $contact->id){
			array_push($ROWVALUES, "<LI>".$mod_strings['LBL_EXISTING_CONTACT']." - <a href='index.php?action=DetailView&module=Contacts&record=".$contact->id."'>".$contact->first_name ." ".$contact->last_name."</a>" );
		}else{
			array_push($ROWVALUES, "<LI>".$mod_strings['LBL_CREATED_CONTACT']." - <a href='index.php?action=DetailView&module=Contacts&record=".$contact->id."'>".$contact->first_name ." ".$contact->last_name."</a>" );
		}
	}
	if(isset($account)){

		if(isset($_POST['selectedAccount']) && $_POST['selectedAccount'] == $account->id){
			array_push($ROWVALUES, "<LI>".$mod_strings['LBL_EXISTING_ACCOUNT']. " - <a href='index.php?action=DetailView&module=Accounts&record=".$account->id."'>".$account->name."</a>");
		}else{
			array_push($ROWVALUES, "<LI>".$mod_strings['LBL_CREATED_ACCOUNT']. " - <a href='index.php?action=DetailView&module=Accounts&record=".$account->id."'>".$account->name."</a>");
		}

	}
	if(isset($opportunity)){

		if(isset($_POST['selectedOpportunity']) && $_POST['selectedOpportunity'] == $opportunity->id){
			array_push($ROWVALUES, "<LI>".$mod_strings['LBL_EXISTING_OPPORTUNITY']. " - <a href='index.php?action=DetailView&module=Opportunities&record=".$opportunity->id."'>".$opportunity->name."</a>");
		}else{
			array_push($ROWVALUES, "<LI>".$mod_strings['LBL_CREATED_OPPORTUNITY']. " - <a href='index.php?action=DetailView&module=Opportunities&record=".$opportunity->id."'>".$opportunity->name."</a>");
		}

	}
	$accountid = 'NULL';
	$contactid = 'NULL';
	$opportunityid = 'NULL';
	if(isset($account)){
		$account->track_view($current_user->id, 'Accounts');
		$accountid = "'".$account->id."'";
		clone_history($lead->db, $lead->id, $account->id ,'Accounts');
	}
	if(isset($contact)){
		$contact->track_view($current_user->id, 'Contacts');
		$contactid = "'".$contact->id."'";
		clone_history($lead->db, $lead->id, $contact->id, 'Contacts');
		clone_relationship($lead->db,array('calls_contacts', 'meetings_contacts',), 'contact_id', $lead->id, $contact->id);
		clone_relationship($lead->db,array('emails_beans'), 'bean_id', $lead->id, $contact->id);
	}
	if(isset($opportunity)){
		/*track entry for opportunities is created during save
		$opportunity->track_view($current_user->id, 'Opportunities');
		*/
		$opportunityid = "'".$opportunity->id."'";
		clone_history($lead->db, $lead->id, $opportunity->id ,'Opportunities');
	}

    if(isset($contact)) {
        //Set relationships to the new contact
        foreach($linked_beans as $linked_bean)
        {
            foreach($linked_bean as $bean_val)
            {
                $bean_val->load_relationship('contacts');
                $bean_val->contacts->add($contact->id);
            }

        }
    }

	$lead = new Lead();
	$mod_strings = return_module_language($current_language, 'Leads');	
	$lead->retrieve($_REQUEST['record']);
	$lead->converted_lead( "'".$_REQUEST['record']."'", $contactid, $accountid, $opportunityid);
	if(isset($_POST['selectedLeads']) && sizeof($_POST['selectedLeads']) > 0){
		foreach($_POST['selectedLeads'] as $aLead){
			$lead->converted_lead( "'".$aLead."'", $contactid, $accountid, $opportunityid);
		}
	}
	if(isset($call)){
		$call->track_view($current_user->id, 'Calls');
		array_push($ROWVALUES, "<LI>".$mod_strings['LBL_CREATED_CALL']. " - <a href='index.php?action=DetailView&module=Calls&record=".$call->id."'>".$call->name."</a>");
		}
	if(isset($meeting)){
		$meeting->track_view($current_user->id, 'Meetings');
		array_push($ROWVALUES, "<LI>".$mod_strings['LBL_CREATED_MEETING']. " - <a href='index.php?action=DetailView&module=Meetings&record=".$meeting->id."'>".$meeting->name."</a>");
		}
	array_push($ROWVALUES,"&nbsp;");
	array_push($ROWVALUES,"<a href='index.php?module=Leads&action=ListView'>{$mod_strings['LBL_BACKTOLEADS']}</a>");
	$sugar_smarty->assign('ROWVALUES',$ROWVALUES);
  echo $sugar_smarty->fetch('modules/Leads/ConvertLead.tpl');
}else{
    $lead = new Lead();
    $lead->retrieve($_REQUEST['record']);
    $sugar_smarty->assign('RECORD', $_REQUEST['record']);
    $sugar_smarty->assign('TABLECLASS', 'tabForm');
    //CONTACT
    $sugar_smarty->assign('FORMHEADER',$mod_strings['LNK_NEW_CONTACT']);
    $sugar_smarty->assign('OPPNEEDSACCOUNT',$mod_strings['NTC_OPPORTUNITY_REQUIRES_ACCOUNT']);
    if ($sugar_config['require_accounts']) {
    	$sugar_smarty->assign('CHECKOPPORTUNITY', "&& checkOpportunity()");
    }
    else {
    	$sugar_smarty->assign('CHECKOPPORTUNITY', "");
    }
    require_once('modules/Contacts/ContactFormBase.php');
    $contactForm = new ContactFormBase();
    $sugar_smarty->assign('FORMBODY',$contactForm->getWideFormBody('Contacts', 'Contacts','ConvertLead', $lead, false));
    //$sugar_smarty->assign('FORMFOOTER',get_form_footer());
    $sugar_smarty->assign('CLASS', 'dataLabel');
    require_once('modules/Notes/NoteFormBase.php');
    $noteForm = new NoteFormBase();
    $postform = "<h5 class='dataLabel'><input class='checkbox' type='checkbox' name='newcontactnote' onclick='toggleDisplay(\"contactnote\");'> ${mod_strings['LNK_NEW_NOTE']}</h5>";
    $postform .= '<div id="contactnote" style="display:none">'.$noteForm->getFormBody('ContactNotes', 'Notes','ConvertLead', 80).'</div>';
    $sugar_smarty->assign('POSTFORM',$postform);
    
    
    $sugar_smarty->assign('RELATED_RECORDS_HEADER', $app_strings['LBL_RELATED_RECORDS']);
    $Relateds = array();
    $Related = array();
    
    //Account
   
    ///////////////////////////////////////
    ///
    /// SETUP PARENT POPUP
    
    $popup_request_data = array(
    	'call_back_function' => 'set_return',
    	'form_name' => 'ConvertLead',
    	'field_to_name_array' => array(
    		'id' => 'selectedAccount',
    		'name' => 'display_account_name',
    		),
    	);
    
    $json = getJSONobj();
    $encoded_popup_request_data = $json->encode($popup_request_data);
    
    //
    ///////////////////////////////////////
    
    
    $selectAccountButton = "<div id='newaccountdivlink' style='display:inline'><b>{$mod_strings['LNK_SELECT_ACCOUNT']}</b>&nbsp;<input readonly='readonly' name='display_account_name' id='display_account_name' type=\"text\" value=\"\"><input name='selectedAccount' id='selectedAccount' type=\"hidden\" value=''>&nbsp;<input type='button' title=\"{$app_strings['LBL_SELECT_BUTTON_TITLE']}\" accessKey=\"{$app_strings['LBL_SELECT_BUTTON_KEY']}\" type=\"button\"  class=\"button\" value='{$app_strings['LBL_SELECT_BUTTON_LABEL']}' name=btn1 onclick='open_popup(\"Accounts\", 600, 400, \"\", true, false, $encoded_popup_request_data);'> <input type='button' title=\"{$app_strings['LBL_CLEAR_BUTTON_TITLE']}\" accessKey=\"{$app_strings['LBL_CLEAR_BUTTON_KEY']}\" type=\"button\"  class=\"button\" value='{$app_strings['LBL_CLEAR_BUTTON_LABEL']}' name=btn1 LANGUAGE=javascript onclick='document.forms[\"ConvertLead\"].selectedAccount.value=\"\";document.forms[\"ConvertLead\"].display_account_name.value=\"\"; '><br><b>{$app_strings['LBL_OR']}</b></div>";
    $Related['FORMHEADER']=get_form_header($mod_strings['LNK_NEW_ACCOUNT'], '', '');
    require_once('modules/Accounts/AccountFormBase.php');
    $accountForm = new AccountFormBase();
    $Related['Class']= 'evenListRow';
    $Related['FORMBODY']=$selectAccountButton."<h5 class='dataLabel'><input class='checkbox' type='checkbox' name='newaccount' onclick='document.forms[\"ConvertLead\"].selectedAccount.value=\"\";document.forms[\"ConvertLead\"].display_account_name.value=\"\";toggleDisplay(\"newaccountdiv\");'> ".$mod_strings['LNK_NEW_ACCOUNT']."</h5><div id='newaccountdiv' style='display:none'>".$accountForm->getWideFormBody('Accounts', 'Accounts','ConvertLead', $lead );
    //$Related['FORMFOOTER']=get_form_footer();
    require_once('modules/Notes/NoteFormBase.php');
    $noteForm = new NoteFormBase();
    $postform = "<h5 class='dataLabel'><input class='checkbox' type='checkbox' name='newaccountnote' onclick='toggleDisplay(\"accountnote\");'> ${mod_strings['LNK_NEW_NOTE']}</h5>";
    $postform .= '<div id="accountnote" style="display:none">'.$noteForm->getFormBody('AccountNotes', 'Notes','ConvertLead', 85).'</div><br>';
    if(!empty($lead->account_name)){
    	$postform.='<script>document.forms["ConvertLead"].newaccount.checked=true;toggleDisplay("newaccountdiv");</script>';
    }
    $Related['POSTFORM']=$postform;
    array_push($Relateds, $Related);
    
    
    //OPPORTUNITTY
    $Related['FORMHEADER']=get_form_header($mod_strings['LNK_NEW_OPPORTUNITY'], '', '');
    require_once('modules/Opportunities/OpportunityFormBase.php');
    $oppForm = new OpportunityFormBase();
    $Related['CLASS']='evenListRow';
    $Related['FORMBODY']="<h5 class='dataLabel'><input class='checkbox' type='checkbox' name='newopportunity' onclick='toggleDisplay(\"newoppdiv\");'> ".$mod_strings['LNK_NEW_OPPORTUNITY']."</h5><div id='newoppdiv' style='display:none'>".$oppForm->getWideFormBody('Opportunities', 'Opportunities','ConvertLead', $lead , false);
    //$Related['FORMFOOTER']=get_form_footer(); 
    require_once('modules/Notes/NoteFormBase.php');
    $noteForm = new NoteFormBase();
    $postform = "<h5 class='dataLabel'><input class='checkbox' type='checkbox' name='newoppnote' onclick='toggleDisplay(\"oppnote\");'> ${mod_strings['LNK_NEW_NOTE']}</h5>";
    $postform .= '<div id="oppnote" style="display:none">'.$noteForm->getFormBody('OpportunityNotes', 'Notes','ConvertLead', 85).'</div><br>';
    $Related['POSTFORM']=$postform;
    array_push($Relateds, $Related);
    
    
    //Appointment
    require_once('modules/Calls/CallFormBase.php');
    $callForm = new CallFormBase();
    $Related['FORMBODY']="<h5 class='dataLabel'><input class='checkbox' type='checkbox' name='newmeeting' onclick='toggleDisplay(\"newmeetingdiv\");'> ".$mod_strings['LNK_NEW_APPOINTMENT']."</h5><div id='newmeetingdiv' style='display:none'>".$callForm->getWideFormBody('Appointments', 'Calls','ConvertLead')."</div><br>";
    //Related['FORMFOOTER']=get_form_footer();
    $Related['POSTFORM']='';
    array_push($Relateds, $Related);
    $sugar_smarty->assign('Related_records',$Relateds);
    echo $sugar_smarty->fetch('modules/Leads/ConvertLead.tpl');
    }
?>
