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
require_once('include/formbase.php');
require_once('modules/Leads/LeadFormBase.php');




global $app_strings, $app_list_strings, $sugar_config, $timedate;

$mod_strings = return_module_language($sugar_config['default_language'], 'Leads');

$app_list_strings['record_type_module'] = array('Contact'=>'Contacts', 'Account'=>'Accounts', 'Opportunity'=>'Opportunities', 'Case'=>'Cases', 'Note'=>'Notes', 'Call'=>'Calls', 'Email'=>'Emails', 'Meeting'=>'Meetings', 'Task'=>'Tasks', 'Lead'=>'Leads','Bug'=>'Bugs',




);

/**
 * To make your changes upgrade safe create a file called leadCapture_override.php and place the changes there
 */
$users = array(
	'PUT A RANDOM KEY FROM THE WEBSITE HERE' => array('name'=>'PUT THE USER_NAME HERE', 'pass'=>'PUT THE USER_HASH FOR THE RESPECTIVE USER HERE'),
);

if (isset($_POST['campaign_id']) && !empty($_POST['campaign_id'])) {
	    //adding the client ip address
	    $_POST['client_id_address'] = query_client_ip();
		$campaign_id=$_POST['campaign_id'];
		$campaign = new Campaign();
		$camp_query  = "select name,id from campaigns where id='$campaign_id'";
		$camp_query .= " and deleted=0";
        $camp_result=$campaign->db->query($camp_query);
        $camp_data=$campaign->db->fetchByAssoc($camp_result);
	    //$current_user->user_name = $users[$_POST['user']]['name'];

	    if(isset($camp_data) && $camp_data != null ){
			$leadForm = new LeadFormBase();
            $lead = new Lead();
			$prefix = '';
			if(!empty($_POST['prefix'])){
				$prefix = 	$_POST['prefix'];
			}
	       //$_POST['first_name'] = $name[0];  $_POST['last_name'] = $name[1];
			if(empty($lead->id)) {
                $lead->id = create_guid();
                $lead->new_with_id = true;
            }
            $lead = $leadForm->handleSave('', false, true, false, $lead);
            
			if(!empty($lead)){
				
	            //create campaign log
	            $camplog = new CampaignLog();
	            $camplog->campaign_id  = $_POST['campaign_id'];
	            $camplog->related_id   = $lead->id;
	            $camplog->related_type = $lead->module_dir;
	            $camplog->activity_type = "lead";
	            $camplog->target_type = $lead->module_dir;
	            $campaign_log->activity_date=$timedate->to_display_date_time(gmdate($GLOBALS['timedate']->get_db_date_time_format()));
	            $camplog->target_id    = $lead->id;
	            $camplog->save();

		        //link campaignlog and lead

		        if(isset($_POST['webtolead_email1']) && $_POST['webtolead_email1'] != null){
                    $lead->email1 = $_POST['webtolead_email1'];
		        }
		        if(isset($_POST['webtolead_email2']) && $_POST['webtolead_email2'] != null){
                    $lead->email2 = $_POST['webtolead_email2'];
		        }
		        $lead->load_relationship('campaigns');
		        $lead->campaigns->add($camplog->id);
                if(!empty($GLOBALS['check_notify'])) {
                    $lead->save($GLOBALS['check_notify']);
                }
                else {
                    $lead->save(FALSE);
                }
            }

            //in case there are forms out there still using email_opt_out
            if(isset($_POST['webtolead_email_opt_out']) || isset($_POST['email_opt_out'])){
                    
                if(isset ($lead->email1) && !empty($lead->email1)){
                    $sea = new SugarEmailAddress();
                    $sea->AddUpdateEmailAddress($lead->email1,0,1);
                }   
                if(isset ($lead->email2) && !empty($lead->email2)){
                    $sea = new SugarEmailAddress();
                    $sea->AddUpdateEmailAddress($lead->email2,0,1);
                    
                }
            }              
			if(isset($_POST['redirect_url']) && !empty($_POST['redirect_url'])){
				echo '<html><head><title>SugarCRM</title></head><body>';
				echo '<form name="redirect" action="' .$_POST['redirect_url']. '" method="POST">';

				foreach($_POST as $param => $value) {
					if($param != 'redirect_url' ||$param != 'submit') {
						echo '<input type="hidden" name="'.$param.'" value="'.$value.'">';

					}

				}
				if(empty($lead)) {
					echo '<input type="hidden" name="error" value="1">';
				}
				echo '</form><script language="javascript" type="text/javascript">document.redirect.submit();</script>';
				echo '</body></html>';
			}
			else{
				echo $mod_strings['LBL_THANKS_FOR_SUBMITTING_LEAD'];
			}
			sugar_cleanup();
			// die to keep code from running into redirect case below
			die();
	    }
	   else{
	  	  echo $mod_strings['LBL_SERVER_IS_CURRENTLY_UNAVAILABLE'];
	  }
}

echo $mod_strings['LBL_SERVER_IS_CURRENTLY_UNAVAILABLE'];
if (!empty($_POST['redirect'])) {
	echo '<html><head><title>SugarCRM</title></head><body>';
	echo '<form name="redirect" action="' .$_POST['redirect']. '" method="POST">';
	echo '</form><script language="javascript" type="text/javascript">document.redirect.submit();</script>';
	echo '</body></html>';
}
?>
