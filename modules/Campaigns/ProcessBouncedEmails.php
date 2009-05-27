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

 * Description:
 ********************************************************************************/
//find all mailboxes of type bounce.
function campaign_process_bounced_emails(&$email, &$email_header) {
	global $sugar_config;
	$emailFromAddress = $email_header->fromaddress;
	$email_description = $email->description;
    $query1 = "SELECT id, file_mime_type FROM notes WHERE file_mime_type like 'message/r%' and parent_id = '".$email->id."'";
    $result1 = $GLOBALS['db']->query($query1);
    if (count($result1) > 0) {
		$row = $GLOBALS['db']->fetchByAssoc($result1);
		$attachId = $row['id'];
		if($fp = fopen($sugar_config['upload_dir'].$attachId, 'rb')) {
			$contents = fread($fp, filesize($sugar_config['upload_dir'].$attachId));
			$emailFromAddress = $emailFromAddress . $contents;
			$email_description = $email_description . $contents;
			fclose($fp);
		}
    }
	if (preg_match('/MAILER-DAEMON|POSTMASTER/i',$emailFromAddress)) {
		//do we have the identifier tag in the email?
		
	    $email_description=quoted_printable_decode($email_description);
		$matches=array();
		if (preg_match('/index.php\?entryPoint=removeme&identifier=[a-z0-9\-]*/',$email_description,$matches)) {
			$identifiers=preg_split('/index.php\?entryPoint=removeme&identifier=/',$matches[0],-1,PREG_SPLIT_NO_EMPTY);
			if (!empty($identifiers)) {
				
				//array should have only one element in it.
				$identifier=trim($identifiers[0]);
				if (!class_exists('CampaignLog')) {
					
				}
				$targeted = new CampaignLog();
				$where="campaign_log.activity_type='targeted' and campaign_log.target_tracker_key='{$identifier}'";
				$query=$targeted->create_new_list_query('',$where);
				$result=$targeted->db->query($query);
				$row=$targeted->db->fetchByAssoc($result);
				if (!empty($row)) {
					//found entry

					//do not create another campaign_log record is we already have an
					//invalid email or send error entry for this tracker key.
					$query_log = "select * from campaign_log where target_tracker_key='{$row['target_tracker_key']}'"; 
					$query_log .=" and (activity_type='invalid email' or activity_type='send error')";

					$result_log=$targeted->db->query($query_log);
					$row_log=$targeted->db->fetchByAssoc($result_log);

					if (empty($row_log)) {
						$bounce = new CampaignLog();

						$bounce->campaign_id=$row['campaign_id'];
						$bounce->target_tracker_key=$row['target_tracker_key'];
						$bounce->target_id= $row['target_id'];
						$bounce->target_type=$row['target_type'];
						$bounce->list_id=$row['list_id'];
						$bounce->marketing_id=$row['marketing_id'];

						$bounce->activity_date=$email->date_created;
						$bounce->related_type='Emails';
						$bounce->related_id= $email->id;
					
						//do we have the phrase permanent error in the email body.
						if (preg_match('/permanent[ ]*error/',$email_description)) {
							//invalid email address
							$bounce->activity_type='invalid email';
						} else {
							//other -bounced email.	
							$bounce->activity_type='send error';
						}			
						$return_id=$bounce->save();
					}				
				} else {
					$GLOBALS['log']->info("Warning: skipping bounced email with this tracker_key(identifier) in the message body ".$identifier);
				}			
		} else {
			//todo mark the email address as invalid. search for prospects/leads/contact associated
			//with this email address and set the invalid_email flag... also make email available.
		}
	}  else {
		$GLOBALS['log']->info("Warning: skipping bounced email because it does not have the removeme link.");	  	
  	}
  } else {
	$GLOBALS['log']->info("Warning: skipping bounced email because the sender is not MAILER-DAEMON.");
  }
}
?>
