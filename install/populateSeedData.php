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






require_once('include/language/en_us.lang.php');

require_once('install/UserDemoData.php');
require_once('install/TeamDemoData.php');
require_once('install/seed_data/basicSeedData.php');
if(isset($sugar_config['i18n_test']) && $sugar_config['i18n_test'] == true)
	require_once('modules/Contacts/contactSeedData_jp.php');
else
	require_once('modules/Contacts/contactSeedData.php');


















global $first_name_array;
global $first_name_count;
global $last_name_array;
global $last_name_count;
global $company_name_array;
global $company_name_count;
global $street_address_array;
global $street_address_count;
global $city_array;
global $city_array_count;
global $app_list_strings;
global $sugar_config;

$_REQUEST['useEmailWidget'] = "true";

if(empty($app_list_strings)) {
	$app_list_strings = return_app_list_strings_language('en_us');
}

/*
 * Seed the random number generator with a fixed constant.  This will make all installs of the same code have the same
 * seed data.  This facilitates cross database testing..
 */
mt_srand(93285903);


$db = DBManagerFactory::getInstance();
$timedate = new TimeDate();

// Set the max time to ~10 minutes (helps Windows load the seed data)
ini_set("max_execution_time", "601");

// ensure we have enough memory
$memory_needed  = 256;
$memory_limit   = ini_get('memory_limit');
if( $memory_limit != "" && $memory_limit != "-1" ){ // if memory_limit is set
    rtrim($memory_limit, 'M');
    $memory_limit_int = (int) $memory_limit;
    if( $memory_limit_int < $memory_needed ){
        ini_set("memory_limit", "$memory_needed" . "M");
    }
}

$large_scale_test = empty($sugar_config['large_scale_test']) ?
	false : $sugar_config['large_scale_test'];

$seed_user = new User();
$user_demo_data = new UserDemoData($seed_user, $large_scale_test);

if(isset($sugar_config['i18n_test']) && $sugar_config['i18n_test'] == true) {
	if($_SESSION['setup_db_type'] == 'mssql') {
		$user_demo_data->create_demo_data_jp_mssql();
	} else {
		$user_demo_data->create_demo_data_jp();
	}
} else {
	$user_demo_data->create_demo_data();
}
$number_contacts = 200;
$number_companies = 50;
$number_leads = 200;

$large_scale_test = empty($sugar_config['large_scale_test']) ? false : $sugar_config['large_scale_test'];

// If large scale test is set to true, increase the seed data.
if($large_scale_test) {
	// increase the cuttoff time to 1 hour
	ini_set("max_execution_time", "3600");
	$number_contacts = 100000;
	$number_companies = 15000;
	$number_leads = 100000;
}

















$possible_duration_hours_arr = array( 0, 1, 2, 3);
$possible_duration_minutes_arr = array('00' => '00','15' => '15', '30' => '30', '45' => '45');

$account_ids = Array();
$accounts = Array();
$opportunity_ids = Array();

// Determine the assigned user for all demo data.  This is the default user if set, or admin
$assigned_user_name = "admin";
if(!empty($sugar_config['default_user_name']) &&
	!empty($sugar_config['create_default_user']) &&
	$sugar_config['create_default_user'])
{
	$assigned_user_name = $sugar_config['default_user_name'];
}

// Look up the user id for the assigned user
$seed_user = new User();

$assigned_user_id = $seed_user->retrieve_user_id($assigned_user_name);

$casePriorityTemp = $app_list_strings['case_priority_dom'];
$caseStatusTemp = $app_list_strings['case_status_dom'];
foreach($casePriorityTemp as $k => $p) { $casePriority[] = $k; }
foreach($caseStatusTemp as $s) { $caseStatus[] = $s; }

$patterns[] = '/ /';
$patterns[] = '/\./';
$patterns[] = '/&/';
$patterns[] = '/\//';

$replacements[] = '';
$replacements[] = '';
$replacements[] = '';
$replacements[] = '';

///////////////////////////////////////////////////////////////////////////////
////	ACCOUNTS

for($i = 0; $i < $number_companies; $i++) {
	$account_name = $company_name_array[mt_rand(0,$company_name_count-1)].' '.mt_rand(1,1000000);

	// Create new accounts.
	$account = new Account();
	$account->name = $account_name;
	$account->phone_office = create_phone_number();
	$account->assigned_user_id = $assigned_user_id;
	$account->emailAddress->addAddress(createEmailAddress(), true);
	$account->emailAddress->addAddress(createEmailAddress());
	$account->website = createWebAddress();
	$account->billing_address_street = $street_address_array[mt_rand(0,$street_address_count-1)];
	$account->billing_address_city = $city_array[mt_rand(0,$city_array_count-1)];

	if($i % 3 == 1)	{
		$account->billing_address_state = "NY";
		$assigned_user_id = mt_rand(9,10);
		if($assigned_user_id == 9) {
			$account->assigned_user_name = "seed_will";
			$account->assigned_user_id = $account->assigned_user_name."_id";
		} else {
			$account->assigned_user_name = "seed_chris";
			$account->assigned_user_id = $account->assigned_user_name."_id";
		}

		$account->assigned_user_id = $account->assigned_user_name."_id";
	} else {
		$account->billing_address_state = "CA";
		$assigned_user_id = mt_rand(6,8);

		if($assigned_user_id == 6) {
			$account->assigned_user_name = "seed_sarah";
		} elseif($assigned_user_id == 7) {
			$account->assigned_user_name = "seed_sally";
		} else {
			$account->assigned_user_name = "seed_max";
		}

		$account->assigned_user_id = $account->assigned_user_name."_id";
	}





























	$account->billing_address_postalcode = mt_rand(10000, 99999);
	$account->billing_address_country = 'USA';

	$account->shipping_address_street = $account->billing_address_street;
	$account->shipping_address_city = $account->billing_address_city;
	$account->shipping_address_state = $account->billing_address_state;
	$account->shipping_address_postalcode = $account->billing_address_postalcode;
	$account->shipping_address_country = $account->billing_address_country;

	$key = array_rand($app_list_strings['industry_dom']);
	$account->industry = $app_list_strings['industry_dom'][$key];

	$account->account_type = "Customer";
	$account->save();

	$account_ids[] = $account->id;
	$accounts[] = $account;

	// Create a case for the account
	$case = new aCase();
	$case->account_id = $account->id;
	$case->priority = $casePriority[mt_rand(0,2)];
	$case->status = $caseStatus[mt_rand(0,4)];
	$case->name = $case_seed_names[mt_rand(0,4)];
	$case->assigned_user_id = $account->assigned_user_id;
	$case->assigned_user_name = $account->assigned_user_name;




	$case->save();


	$note = new Note();
	$note->parent_type = 'Accounts';
	$note->parent_id = $account->id;
	$seed_data_index = mt_rand(0,3);
	$note->name = $note_seed_names_and_Descriptions[$seed_data_index][0];
	$note->description = $note_seed_names_and_Descriptions[$seed_data_index][1];
	$note->assigned_user_id = $account->assigned_user_id;
	$note->assigned_user_name = $account->assigned_user_name;




	$note->save();


	$call = new Call();
	$call->parent_type = 'Accounts';
	$call->parent_id = $account->id;
	$call->name = $call_seed_data_names[mt_rand(0,3)];
	$call->assigned_user_id = $account->assigned_user_id;
	$call->assigned_user_name = $account->assigned_user_name;
	$call->direction='Outbound';
	$call->date_start = create_date(). ' ' . create_time();
	$call->duration_hours='0';
	$call->duration_minutes='30';
	$call->account_id =$account->id;
	$call->status='Planned';






	$call->save();


	//Create new opportunities
	$opp = new Opportunity();






	$opp->assigned_user_id = $account->assigned_user_id;
	$opp->assigned_user_name = $account->assigned_user_name;

	$opp->name = substr($account_name." - 1000 units", 0, 50);
	$opp->date_closed = create_date();

	$key = array_rand($app_list_strings['lead_source_dom']);
	$opp->lead_source = $app_list_strings['lead_source_dom'][$key];

	$key = array_rand($app_list_strings['sales_stage_dom']);
	$opp->sales_stage = $app_list_strings['sales_stage_dom'][$key];

	// If the deal is already one, make the date closed occur in the past.
	if($opp->sales_stage == "Closed Won" || $opp->sales_stage == "Closed Lost")
	{
		$opp->date_closed = create_past_date();
	}

	$key = array_rand($app_list_strings['opportunity_type_dom']);
	$opp->opportunity_type = $app_list_strings['opportunity_type_dom'][$key];

	$amount = array("10000", "25000", "50000", "75000");
	$key = array_rand($amount);
	$opp->amount = $amount[$key];

	$probability = array("10", "70", "40", "60");
	$key = array_rand($probability);
	$opp->probability = $probability[$key];

	$opp->save();

	$opportunity_ids[] = $opp->id;
	// Create a linking table entry to assign an account to the opportunity.
	$opp->set_relationship('accounts_opportunities', array('opportunity_id'=>$opp->id ,'account_id'=> $account->id), false);

}

$titles = array("President",
				"VP Operations",
				"VP Sales",
				"Director Operations",
				"Director Sales",
				"Mgr Operations",
				"IT Developer",
				"");

$account_max = count($account_ids) - 1;

$first_name_max = $first_name_count - 1;
$last_name_max = $last_name_count - 1;
$street_address_max = $street_address_count - 1;
$city_array_max = $city_array_count - 1;
$lead_source_max = count($app_list_strings['lead_source_dom']) - 1;
$lead_status_max = count($app_list_strings['lead_status_dom']) - 1;
$title_max = count($titles) - 1;
$lead_status_keys = array_keys($app_list_strings['lead_status_dom']);
$lead_source_keys = array_keys($app_list_strings['lead_source_dom']);

///////////////////////////////////////////////////////////////////////////////
////	DEMO CONTACTS
for($i=0; $i<$number_contacts; $i++) {
	$contact = new Contact();

	$contact->first_name = $first_name_array[mt_rand(0,$first_name_max)];
	$contact->last_name = $last_name_array[mt_rand(0,$last_name_max)];
	$contact->assigned_user_id = $account->assigned_user_id;
	$contact->primary_address_street = $street_address_array[mt_rand(0,$street_address_max)];
	$contact->primary_address_city = $city_array[mt_rand(0,$city_array_max)];
	$contact->lead_source = $app_list_strings['lead_source_dom'][array_rand($app_list_strings['lead_source_dom'])];
	$contact->title = $titles[mt_rand(0,$title_max)];
	$contact->emailAddress->addAddress(createEmailAddress(), true, true);
	$contact->emailAddress->addAddress(createEmailAddress(), false, false, false, true);

	$assignedUser = new User();
	$assignedUser->retrieve($contact->assigned_user_id);






	$contact->assigned_user_id = $assigned_user_id;
	$contact->email1 = createEmailAddress();
	$key = array_rand($street_address_array);
	$contact->primary_address_street = $street_address_array[$key];
	$key = array_rand($city_array);
	$contact->primary_address_city = $city_array[$key];

	$key = array_rand($app_list_strings['lead_source_dom']);
	$contact->lead_source = $app_list_strings['lead_source_dom'][$key];

	$key = array_rand($titles);
	$contact->title = $titles[$key];




	$contact->phone_work = create_phone_number();
	$contact->phone_home = create_phone_number();
	$contact->phone_mobile = create_phone_number();

	$account_number = mt_rand(0,$account_max);
	$account_id = $account_ids[$account_number];

	// Fill in a bogus address
	$contacts_account = $accounts[$account_number];
	$contact->primary_address_state = $contacts_account->billing_address_state;




	$contact->assigned_user_id = $contacts_account->assigned_user_id;
	$contact->assigned_user_name = $contacts_account->assigned_user_name;

	$contact->primary_address_postalcode = mt_rand(10000,99999);
	$contact->primary_address_country = 'USA';

	$contact->save();

	// Create a linking table entry to assign an account to the contact.
	$contact->set_relationship('accounts_contacts', array('contact_id'=>$contact->id ,'account_id'=> $account_id), false);

	// This assumes that there will be one opportunity per company in the seed data.
	$opportunity_key = array_rand($opportunity_ids);
	$contact->set_relationship('opportunities_contacts', array('contact_id'=>$contact->id ,'opportunity_id'=> $opportunity_ids[$opportunity_key], 'contact_role'=>$app_list_strings['opportunity_relationship_type_default_key']), false);

	//Create new tasks
	$task = new Task();

	$key = array_rand($task->default_task_name_values);
	$task->name = $task->default_task_name_values[$key];
	//separate date and time field have been merged into one.
	$task->date_due = create_date() . ' ' . create_time();
	$task->date_due_flag = 0;




	$task->assigned_user_id = $contacts_account->assigned_user_id;
	$task->assigned_user_name = $contacts_account->assigned_user_name;

	$key = array_rand($app_list_strings['task_priority_dom']);
	$task->priority = $app_list_strings['task_priority_dom'][$key];
	$key = array_rand($app_list_strings['task_status_dom']);
	$task->status = $app_list_strings['task_status_dom'][$key];
	$task->contact_id = $contact->id;
	if ($contact->primary_address_city == "San Mateo") {
		$task->parent_id = $account_id;
		$task->parent_type = 'Accounts';
		$task->save();
	}

	//Create new meetings
	$meeting = new Meeting();

	$key = array_rand($meeting->default_meeting_name_values);
	$meeting->name = $meeting->default_meeting_name_values[$key];
	$meeting->date_start = create_date(). ' ' . create_time();
	//$meeting->time_start = date("H:i",time());
	$meeting->duration_hours = array_rand($possible_duration_hours_arr);
	$meeting->duration_minutes = array_rand($possible_duration_minutes_arr);
	$meeting->assigned_user_id = $assigned_user_id;




	$meeting->assigned_user_id = $contacts_account->assigned_user_id;
	$meeting->assigned_user_name = $contacts_account->assigned_user_name;
	$meeting->description = 'Meeting to discuss project plan and hash out the details of implementation';

	$key = array_rand($app_list_strings['meeting_status_dom']);
	$meeting->status = $app_list_strings['meeting_status_dom'][$key];
	$meeting->contact_id = $contact->id;
	$meeting->parent_id = $account_id;
	$meeting->parent_type = 'Accounts';

    // dont update vcal
    $meeting->update_vcal  = false;

	$meeting->save();

	// leverage the seed user to set the acceptance status on the meeting.
	$seed_user->id = $meeting->assigned_user_id;
    $meeting->set_accept_status($seed_user,'accept');

	//Create new emails
	$email = new Email();

	$key = array_rand($email->default_email_subject_values);
	$email->name = $email->default_email_subject_values[$key];
	$email->date_start = create_date();
	$email->time_start = create_time();
	$email->duration_hours = array_rand($possible_duration_hours_arr);
	$email->duration_minutes = array_rand($possible_duration_minutes_arr);
	$email->assigned_user_id = $assigned_user_id;




	$email->assigned_user_id = $contacts_account->assigned_user_id;
	$email->assigned_user_name = $contacts_account->assigned_user_name;
	$email->description = 'Discuss project plan and hash out the details of implementation';

	$email->status = 'sent';
	$email->parent_id = $account_id;
	$email->parent_type = 'Accounts';

	$email->to_addrs = $contact->emailAddress->getPrimaryAddress($contact);
	$email->from_addr = $assignedUser->emailAddress->getPrimaryAddress($assignedUser);
	$email->from_addr_name = $email->from_addr;
	$email->to_addrs_names = $email->to_addrs;
	$email->type = 'out';
	$email->save();
	$email->load_relationship('contacts');
	$email->contacts->add($contact->id);
	$email->load_relationship('accounts');
	$email->contacts->add($account_id);
}

for($i=0; $i<$number_leads; $i++)
{
	$lead = new Lead();

	$lead->account_name = $company_name_array[mt_rand(0,$company_name_count-1)].' '.mt_rand(1,1000000);

	$lead->first_name = $first_name_array[mt_rand(0,$first_name_max)];
	$lead->last_name = $last_name_array[mt_rand(0,$last_name_max)];
	$lead->primary_address_street = $street_address_array[mt_rand(0,$street_address_max)];
	$lead->primary_address_city = $city_array[mt_rand(0,$city_array_max)];
	$lead->lead_source = $app_list_strings['lead_source_dom'][array_rand($app_list_strings['lead_source_dom'])];
	$lead->title = $titles[mt_rand(0,$title_max)];
	$lead->phone_work = create_phone_number();
	$lead->phone_home = create_phone_number();
	$lead->phone_mobile = create_phone_number();
	$lead->emailAddress->addAddress(createEmailAddress(), true);

	// Fill in a bogus address
	$lead->primary_address_state = "CA";

	$leads_account = $accounts[$account_number];
	$lead->primary_address_state = $leads_account->billing_address_state;

	$lead->status = $lead_status_keys[mt_rand(0,$lead_status_max)];
	$lead->lead_source = $lead_source_keys[mt_rand(0,$lead_source_max)];

	if($i % 3 == 1)
	{
		$lead->billing_address_state = "NY";
			$assigned_user_id = mt_rand(9,10);
			if($assigned_user_id == 9)
			{
				$lead->assigned_user_name = "seed_will";
				$lead->assigned_user_id = $lead->assigned_user_name."_id";
			}
			else
			{
				$lead->assigned_user_name = "seed_chris";
				$lead->assigned_user_id = $lead->assigned_user_name."_id";
			}

			$lead->assigned_user_id = $lead->assigned_user_name."_id";
		}
		else
		{
			$lead->billing_address_state = "CA";
			$assigned_user_id = mt_rand(6,8);
			if($assigned_user_id == 6)
			{
				$lead->assigned_user_name = "seed_sarah";
			}
			else if($assigned_user_id == 7)
			{
				$lead->assigned_user_name = "seed_sally";
			}
			else
			{
				$lead->assigned_user_name = "seed_max";
			}

			$lead->assigned_user_id = $lead->assigned_user_name."_id";
		}


	// If this is a large scale test, switch to the bulk teams 90% of the time.
	if ($large_scale_test)
	{
		if(mt_rand(0,100) < 90) {
			$assigned_team = $team_demo_data->get_random_team();




			$lead->assigned_user_name = $assigned_team;
		} else {






		} 
	} 
	













	
	$lead->primary_address_postalcode = mt_rand(10000,99999);
	$lead->primary_address_country = 'USA';

	$lead->save();

}








































































































































































































































































































































































































///
/// SEED DATA FOR PROJECT AND PROJECT TASK
///




// Project: Audit Plan

$project = new Project();
$project->name = 'Create new project plan for audit';
$project->description = 'Annual audit coming up next month.';
$project->assigned_user_id = 1;
$project->estimated_start_date = '2007-11-01';
$project->estimated_end_date = '2007-12-31';
$project->status = 'Draft';
$project->priority = 'medium';




$audit_plan_id = $project->save();

$project_task = new ProjectTask();
$project_task->assigned_user_id = 1;




$project_task->name = 'Communicate to stakeholders';
$project_task->date_start = create_date('2007', '11', '1');
//$project_task->time_start = create_time(8,0,0);
$project_task->date_finish = create_date('2007', '11', '8');
//$project_task->time_finish = create_time(8,0,0);
$project_task->project_id = $audit_plan_id;
$project_task->project_task_id = '1';
$project_task->description = "Schedule individual meetings with Will, Max, and Sarah.";
$project_task->duration = '6';
$project_task->duration_unit = 'Days';
$project_task->percent_complete = 100;
$communicate_stakeholders_id = $project_task->save();

$project_task = new ProjectTask();
$project_task->assigned_user_id = 1;




$project_task->name = 'Create draft of the plan';
$project_task->date_start = create_date('2007', '11', '5');
//$project_task->time_start = create_time(8,0,0);
$project_task->date_finish = create_date('2007', '11', '20');
//$project_task->time_finish = create_time(8,0,0);
$project_task->project_id = $audit_plan_id;
$project_task->project_task_id = '2';
$project_task->description = 'Need to include new audit points from the current meeting minutes.';
$project_task->duration = '12';
$project_task->duration_unit = 'Days';
$project_task->percent_complete = 38;
$create_draft_id = $project_task->save();

$project_task = new ProjectTask();
$project_task->assigned_user_id = 1;




$project_task->name = 'Field work for collecting data.';
$project_task->date_start = create_date('2007', '11', '5');
//$project_task->time_start = create_time(8,0,0);
$project_task->date_finish = create_date('2007', '11', '13');
//$project_task->time_finish = create_time(8,0,0);
$project_task->project_id = $audit_plan_id;
$project_task->project_task_id = '3';
$project_task->description = 'We need to get approval from all stakeholders on the plan';
$project_task->duration = '17';
$project_task->duration_unit = 'Days';
$project_task->parent_task_id = '2';
$project_task->percent_complete = 75;
$project_task->save();

$project_task = new ProjectTask();
$project_task->assigned_user_id = 1;




$project_task->name = 'Hold kick-off meeting with business units.';
$project_task->status = 'Not Started';
$project_task->date_start = create_date('2007', '11', '12');
//$project_task->time_start = create_time(8,0,0);
$project_task->date_finish = create_date('2007', '11', '19');
//$project_task->time_finish = create_time(8,0,0);
$project_task->project_id = $audit_plan_id;
$project_task->project_task_id = '4';
$project_task->description = "Schedule the meeting with the head of business units to solicit help.";
$project_task->duration = '6';
$project_task->duration_unit = 'Days';
$project_task->parent_task_id = '2';
$project_task->percent_complete = 0;
$project_task->save();


$project_task = new ProjectTask();
$project_task->assigned_user_id = 1;




$project_task->name = 'Gather data from meetings';
$project_task->date_start = create_date('2007', '11', '20');
//$project_task->time_start = create_time(8,0,0);
$project_task->date_finish = create_date('2007', '11', '20');
//$project_task->time_finish = create_time(8,0,0);
$project_task->project_id = $audit_plan_id;
$project_task->project_task_id = '5';
$project_task->description = 'Need to organize the data and put it in the right spreadsheet.';
$project_task->duration = '1';
$project_task->duration_unit = 'Days';
$project_task->parent_task_id = '2';
$project_task->predecessor = '4';
$project_task->percent_complete = 0;
$project_task->save();

/*
// Project: Move Mountain

$project = new Project();
$project->name = 'IPO Launch Party';
$project->description = 'Need to organize the IPO party for the upcoming Tuesday.';
$project->assigned_user_id = 1;



$move_mountain_id = $project->save();

$project_task = new ProjectTask();
$project_task->assigned_user_id = 1;



$project_task->name = 'Book the facility';
$project_task->status = 'Completed';
$project_task->date_due = create_date() . ' '. create_time(8,0,0);
//$project_task->time_due = create_time(8,0,0);
$project_task->date_start = create_past_date() . ' '. create_time(8,0,0);
//$project_task->time_start = create_time(8,0,0);
$project_task->parent_id = $move_mountain_id;
$project_task->priority = 'High';
$project_task->description = "We can either do it at the Promenade or the Galleria.";
$project_task->order_number = 1;
$project_task->task_number = 101;
$project_task->estimated_effort = 40;
$project_task->actual_effort = 36;
$project_task->utilization = 100;
$project_task->percent_complete = 100;
$project_task->save();

$project_task = new ProjectTask();
$project_task->assigned_user_id = 1;



$project_task->name = 'Get a band';
$project_task->status = 'In Progress';
$project_task->date_due = create_date() . ' '. create_time(8,0,0);
//$project_task->time_due = create_time(8,0,0);
$project_task->date_start = create_past_date() . ' '. create_time(8,0,0);
//$project_task->time_start = create_time(8,0,0);
$project_task->parent_id = $move_mountain_id;
$project_task->priority = 'High';
$project_task->description = "Jim likes something Jazzy.";
$project_task->order_number = 2;
$project_task->task_number = 102;
$project_task->estimated_effort = 40;
$project_task->actual_effort = 20;
$project_task->utilization = 100;
$project_task->percent_complete = 50;
$project_task->save();

$project_task = new ProjectTask();
$project_task->assigned_user_id = 1;



$project_task->name = 'Get a Caterer';
$project_task->status = 'Not Started';
$project_task->date_due = create_date() . ' '.create_time(8,0,0);
//$project_task->time_due = create_time(8,0,0);
$project_task->date_start = create_past_date(). ' '.create_time(8,0,0);
//$project_task->time_start = create_time(8,0,0);
$project_task->parent_id = $move_mountain_id;
$project_task->priority = 'High';
$project_task->description = "Everyone like French food.";
$project_task->order_number = 3;
$project_task->task_number = 103;
$project_task->estimated_effort = 40;
$project_task->utilization = 100;
$project_task->percent_complete = 0;
$project_task->save();

// Project: Setup Booth At Tradeshow
/*
$project = new Project();
$project->name = 'Setup Booth At Tradeshow';
$project->description = "The annual Widgets Tradeshow will be held in Springfield this year.  We are going to design a booth so good, it will knock the socks off of our competition.  No more fish heads thrown at us this year!";
$project->assigned_user_id = 1;



$tradeshow_id = $project->save();

$project_task = new ProjectTask();
$project_task->assigned_user_id = 1;



$project_task->name = 'Build the marketing message theme';
$project_task->status = 'Completed';
$project_task->date_due = create_date() . ' '. create_time(8,0,0);
//$project_task->time_due = create_time(8,0,0);
$project_task->date_start = create_past_date() . ' '. create_time(8,0,0);
///$project_task->time_start = create_time(8,0,0);
$project_task->parent_id = $tradeshow_id;
$project_task->priority = 'High';
$project_task->description = "Thinking along the lines of a post-apocalyptic world in which the human race is gone.  Where the robots have taken over the world.  Gray color palette, blinking LED lights.";
$project_task->order_number = 1;
$project_task->task_number = 111;
$project_task->estimated_effort = 32;
$project_task->actual_effort = 40;
$project_task->utilization = 100;
$project_task->percent_complete = 100;
$project_task_id = $project_task->save();

$project_task = new ProjectTask();
$project_task->assigned_user_id = 1;



$project_task->name = 'Order tradeshow booth';
$project_task->status = 'Completed';
$project_task->date_due = create_date() . ' ' .create_time(8,0,0);
//$project_task->time_due = create_time(8,0,0);
$project_task->date_start = create_past_date() . ' ' . create_time(8,0,0);
//$project_task->time_start = create_time(8,0,0);
$project_task->parent_id = $tradeshow_id;
$project_task->priority = 'High';
$project_task->description = "";
$project_task->order_number = 2;
$project_task->task_number = 109;
$project_task->depends_on_id = $project_task_id;
$project_task->estimated_effort = 80;
$project_task->actual_effort = 70;
$project_task->utilization = 100;
$project_task->percent_complete = 100;
$project_task_id = $project_task->save();

$project_task = new ProjectTask();
$project_task->assigned_user_id = 1;



$project_task->name = 'Order tradeshow graphics';
$project_task->status = 'Completed';
$project_task->date_due = create_date() . ' ' . create_time(8,0,0);
//$project_task->time_due = create_time(8,0,0);
$project_task->date_start = create_past_date() . ' '.create_time(8,0,0);
//$project_task->time_start = create_time(8,0,0);
$project_task->parent_id = $tradeshow_id;
$project_task->priority = 'High';
$project_task->description = "We need a big poster of a fallen Statue of Liberty--a la Planet of the Apes.  And flying cars--we need flying cars.";
$project_task->order_number = 3;
$project_task->task_number = 110;
$project_task->depends_on_id = $project_task_id;
$project_task->estimated_effort = 40;
$project_task->actual_effort = 50;
$project_task->utilization = 100;
$project_task->percent_complete = 100;
$project_task_id = $project_task->save();

$project_task = new ProjectTask();
$project_task->assigned_user_id = 1;



$project_task->name = 'Confirm booth number with the tradeshow';
$project_task->status = 'Completed';
$project_task->date_due = create_date() . ' ' . create_time(17,0,0);
//$project_task->time_due = create_time(17,0,0);
$project_task->date_start = create_past_date() . ' '. create_time(10,0,0);
//$project_task->time_start = create_time(10,0,0);
$project_task->parent_id = $tradeshow_id;
$project_task->priority = 'High';
$project_task->description = "Make sure we get a good booth location near the center of the show floor.";
$project_task->order_number = 4;
$project_task->task_number = 112;
$project_task->depends_on_id = $project_task_id;
$project_task->estimated_effort = 4;
$project_task->actual_effort = 2;
$project_task->utilization = 50;
$project_task->percent_complete = 100;
$confirm_booth_id = $project_task->save();

$project_task = new ProjectTask();
$project_task->assigned_user_id = 1;



$project_task->name = 'Organize union help';
$project_task->status = 'Completed';
$project_task->date_due = create_date() . ' ' . create_time(10,0,0);
//$project_task->time_due = create_time(10,0,0);
$project_task->date_start = create_past_date() . ' ' . create_time(10,0,0);
//$project_task->time_start = create_time(10,0,0);
$project_task->parent_id = $tradeshow_id;
$project_task->priority = 'Medium';
$project_task->description = "";
$project_task->order_number = 5;
$project_task->task_number = 108;
$project_task->depends_on_id = $confirm_booth_id;
$project_task->estimated_effort = 24;
$project_task->actual_effort = 10;
$project_task->utilization = 100;
$project_task->percent_complete = 100;
$project_task_id = $project_task->save();

$project_task = new ProjectTask();
$project_task->assigned_user_id = 1;



$project_task->name = 'Order drayage';
$project_task->status = 'Completed';
$project_task->date_due = create_date() . ' ' . create_time(17,0,0);
//$project_task->time_due = create_time(17,0,0);
$project_task->date_start = create_past_date() . ' ' . create_time(10,0,0);
//$project_task->time_start = create_time(10,0,0);
$project_task->parent_id = $tradeshow_id;
$project_task->priority = 'High';
$project_task->description = "";
$project_task->order_number = 6;
$project_task->task_number = 107;
$project_task->depends_on_id = $project_task_id;
$project_task->estimated_effort = 40;
$project_task->actual_effort = 40;
$project_task->utilization = 100;
$project_task->percent_complete = 100;
$project_task_id = $project_task->save();

$project_task = new ProjectTask();
$project_task->assigned_user_id = 1;



$project_task->name = 'Order chotskies';
$project_task->status = 'Completed';
$project_task->date_due = create_date() . ' ' .create_time(17,0,0);
//$project_task->time_due = create_time(17,0,0);
$project_task->date_start = create_past_date() . ' ' . create_time(10,0,0);
//$project_task->time_start = create_time(10,0,0);
$project_task->parent_id = $tradeshow_id;
$project_task->priority = 'Medium';
$project_task->description = "The edible pencils we gave our last year did well.";
$project_task->order_number = 7;
$project_task->task_number = 105;
$project_task->depends_on_id = $confirm_booth_id;
$project_task->estimated_effort = 16;
$project_task->actual_effort = 40;
$project_task->utilization = 100;
$project_task->percent_complete = 100;
$project_task_id = $project_task->save();

$project_task = new ProjectTask();
$project_task->assigned_user_id = 1;



$project_task->name = 'Order lead capture device';
$project_task->status = 'Completed';
$project_task->date_due = create_date() . ' ' .create_time(17,0,0);
//$project_task->time_due = create_time(17,0,0);
$project_task->date_start = create_past_date() . ' ' . create_time(10,0,0);
//$project_task->time_start = create_time(10,0,0);
$project_task->parent_id = $tradeshow_id;
$project_task->priority = 'Medium';
$project_task->description = "Leads will be piped straight into SugarCRM from a swipe of the admission badge this year.";
$project_task->order_number = 8;
$project_task->task_number = 106;
$project_task->depends_on_id = $confirm_booth_id;
$project_task->estimated_effort = 4;
$project_task->actual_effort = 16;
$project_task->utilization = 100;
$project_task->percent_complete = 100;
$project_task_id = $project_task->save();

$project_task = new ProjectTask();
$project_task->assigned_user_id = 1;



$project_task->name = 'Assign booth duty';
$project_task->status = 'Completed';
$project_task->date_due = create_date() . ' ' .create_time(17,0,0);
//$project_task->time_due = create_time(17,0,0);
$project_task->date_start = create_past_date() . ' ' . create_time(10,0,0);
//$project_task->time_start = create_time(10,0,0);
$project_task->parent_id = $tradeshow_id;
$project_task->priority = 'Medium';
$project_task->description = "No more than 3 hour shifts.  Let the employees have a look around the tradeshow floor.";
$project_task->order_number = 9;
$project_task->task_number = 103;
$project_task->depends_on_id = $confirm_booth_id;
$project_task->estimated_effort = 4;
$project_task->actual_effort = 3;
$project_task->utilization = 100;
$project_task->percent_complete = 100;
$project_task_id = $project_task->save();

$project_task = new ProjectTask();
$project_task->assigned_user_id = 1;



$project_task->name = 'Remind booth workers to wear their uniforms';
$project_task->status = 'Not Started';
$project_task->date_due = create_date() . ' ' . create_time(17,0,0);
//$project_task->time_due = create_time(17,0,0);
$project_task->date_start = create_past_date() . ' ' . create_time(10,0,0);
//$project_task->time_start = create_time(10,0,0);
$project_task->parent_id = $tradeshow_id;
$project_task->priority = 'Low';
$project_task->description = "Be sure to suggest to dress up as a famous suppressive robot (e.g. HAL2000).";
$project_task->order_number = 10;
$project_task->task_number = 104;
$project_task->depends_on_id = $project_task_id;
$project_task->estimated_effort = 1;
$project_task->utilization = 100;
$project_task->percent_complete = 0;
$project_task_id = $project_task->save();

$project_task = new ProjectTask();
$project_task->assigned_user_id = 1;



$project_task->name = 'Build press kits';
$project_task->status = 'In Progress';
$project_task->date_due = create_date() . ' ' . create_time(17,0,0);
//$project_task->time_due = create_time(17,0,0);
$project_task->date_start = create_past_date() . ' ' . create_time(10,0,0);
//$project_task->time_start = create_time(10,0,0);
$project_task->parent_id = $tradeshow_id;
$project_task->priority = 'High';
$project_task->description = "";
$project_task->order_number = 11;
$project_task->task_number = 101;
$project_task->depends_on_id = $confirm_booth_id;
$project_task->estimated_effort = 16;
$project_task->actual_effort = 3;
$project_task->utilization = 100;
$project_task->percent_complete = 25;
$project_task_id = $project_task->save();

$project_task = new ProjectTask();
$project_task->assigned_user_id = 1;



$project_task->name = 'Arrange partner meetings';
$project_task->status = 'In Progress';
$project_task->date_due = create_date() . ' ' . create_time(17,0,0);
//$project_task->time_due = create_time(17,0,0);
$project_task->date_start = create_past_date() . ' ' . create_time(10,0,0);
//$project_task->time_start = create_time(10,0,0);
$project_task->parent_id = $tradeshow_id;
$project_task->priority = 'Medium';
$project_task->description = "Get the usual bunch.";
$project_task->order_number = 12;
$project_task->task_number = 102;
$project_task->depends_on_id = $project_task_id;
$project_task->estimated_effort = 40;
$project_task->actual_effort = 10;
$project_task->utilization = 100;
$project_task->percent_complete = 25;
$project_task_id = $project_task->save();
*/














?>
