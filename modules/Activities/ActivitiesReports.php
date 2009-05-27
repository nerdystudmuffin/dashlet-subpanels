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

if (!isset($_REQUEST['export_report']) || $_REQUEST['export_report'] != '1') {
	function js_setup() {
		global $global_json;
		$global_json = getJSONobj();
		require_once('include/QuickSearchDefaults.php');
		$qsd = new QuickSearchDefaults();
		if (isset($_REQUEST['parent_type'])) 
				$sqs_objects = array('parent_name' => $qsd->getQSParent($_REQUEST['parent_type']));
		else 
			$sqs_objects = array('parent_name' => $qsd->getQSParent());
		
		/*
		if (isset($_REQUEST['parent_type']) && $_REQUEST['parent_type'] != 'Users') 
			$sqs_objects = array('parent_name' => $qsd->getQSParent($_REQUEST['parent_type']));
		else if (isset($_REQUEST['parent_type']) && $_REQUEST['parent_type'] == 'Users') 
			$sqs_objects = array('parent_name' => $qsd->getQSUser());

		else 
			$sqs_objects = array('parent_name' => $qsd->getQSParent());
			*/

		$quicksearch_js = '<script type="text/javascript" language="javascript">sqs_objects = ' . $global_json->encode($sqs_objects) . '</script>';
		return $quicksearch_js;
	}
	
	
	global $theme,$mod_strings,$current_user,$timedate;
	
	echo get_module_title($mod_strings['LBL_MODULE_NAME'], $mod_strings['LBL_ACTIVITIES_REPORTS'], false);
			
	global $app_list_strings;
	$parent_types = $app_list_strings['parent_type_display'];
	$disabled_parent_types = ACLController::disabledModuleList($parent_types,false, 'list');
	foreach($disabled_parent_types as $disabled_parent_type){
		if($disabled_parent_type != $this->parent_type){
			unset($parent_types[$disabled_parent_type]);
		}
	}
	global $timedate;
	$parent_types['Users']='User';
	$sugar_smarty = new Sugar_Smarty();
	$sugar_smarty->assign('MOD', $mod_strings);
	$sugar_smarty->assign('APP', $app_strings);
	$sugar_smarty->assign('PARENT_TYPES', $parent_types);
	if (isset($_REQUEST['parent_type']))
		$sugar_smarty->assign('PARENT_TYPE', $_REQUEST['parent_type']);
	else
		$sugar_smarty->assign('PARENT_TYPE', '0');
	
	if (isset($_REQUEST['object_name']))
		$sugar_smarty->assign('object_name', $_REQUEST['object_name']);
	else
		$sugar_smarty->assign('object_name', '');
		
	if (isset($_REQUEST['parent_id']))
		$sugar_smarty->assign('object_id', $_REQUEST['parent_id']);
	else
		$sugar_smarty->assign('object_id', '');
	
	if (isset($_REQUEST['date_start']))
		$sugar_smarty->assign('DATE_START', $_REQUEST['date_start']);
	else
		$sugar_smarty->assign('DATE_START', '');
	if (isset($_REQUEST['date_finish']))
		$sugar_smarty->assign('DATE_FINISH', $_REQUEST['date_finish']);
	else
		$sugar_smarty->assign('DATE_FINISH', '');
	
	$sugar_smarty->assign("CALENDAR_DATEFORMAT", $timedate->get_cal_date_format());
	$sugar_smarty->assign("DATE_FORMAT", $current_user->getPreference('datef'));
	$sugar_smarty->assign("CURRENT_USER", $current_user->id);
	$sugar_smarty->assign("quicksearch_js", js_setup());
}

$activities = array();

if ((isset($_REQUEST['run_report']) && $_REQUEST['run_report'] == '1') ||
	(isset($_REQUEST['export_report']) && $_REQUEST['export_report'] == '1')) {

	$focus = new SugarBean();
	$query = "";
	if(ACLController::checkAccess('Calls', 'list', true)) {
	 	$query = "select 'Calls' as ";
		if ($focus->db->dbType == 'mysql')
		 	$query .= "'call' ";
	 	else 
		 	$query .= "call ";
	 		
	 	$query .=",calls.description,calls.id, calls.name,calls.date_start,calls.status from calls  ";



	
		if (isset($_REQUEST['parent_type']) && $_REQUEST['parent_type'] == 'Users') {
			$query .= " INNER JOIN calls_users on calls_users.call_id=calls.id and calls_users.deleted=0 where calls_users.user_id=".
			"'" .$_REQUEST['parent_id']."'"; 	
		}
		else {
			$query .= " where 1=1 ";
		}
	 	if (!empty($_REQUEST['date_start'])) {
	 		$query .= " and calls.date_start >= ".db_convert("'".$timedate->to_db_date($_REQUEST['date_start'], false)."'", 'date');
	 	}
	 	if (!empty($_REQUEST['date_finish'])) {
	 		$query .= " and calls.date_start <= ".db_convert("'".$timedate->to_db_date($_REQUEST['date_finish'], false)."'", 'date');
	 	}
		if (isset($_REQUEST['parent_type']) && $_REQUEST['parent_type'] != 'Users') {
			$query .="  and calls.parent_id='".$_REQUEST['parent_id']."'";
		}
		$query .= " and calls.deleted=0 ";
	}
	if(ACLController::checkAccess('Tasks', 'list', true)){
		if ($query != "")
			$query .= " union all ";
		$query .="select 'Tasks',tasks.description,tasks.id, tasks.name,tasks.date_due,tasks.status from tasks ";



		$query .=" where ";
	 	if (!empty($_REQUEST['date_start'])) {
	 		$query .= "tasks.date_due >= ".db_convert("'".$timedate->to_db_date($_REQUEST['date_start'], false)."'", 'date')." and ";
	 	}
	 	if (!empty($_REQUEST['date_finish'])) {
	 		$query .= "tasks.date_due <= ".db_convert("'".$timedate->to_db_date($_REQUEST['date_finish'], false)."'", 'date')." and ";
	 	}
		$query .= "tasks.deleted=0 and ";
		if (isset($_REQUEST['parent_type']) && $_REQUEST['parent_type'] != 'Users') {
			$query .= "tasks.parent_id='".$_REQUEST['parent_id']."' ";
		}
		else if (isset($_REQUEST['parent_type']) && $_REQUEST['parent_type'] == 'Users') {
			$query .= "tasks.assigned_user_id='".$_REQUEST['parent_id']."' ";
		}
	}
	if(ACLController::checkAccess('Meetings', 'list', true)) {
		if ($query != "")
			$query .= " union all ";
		
		$query .="select 'Meetings',meetings.description,meetings.id, meetings.name,meetings.date_start,meetings.status from meetings ";



		
		if (isset($_REQUEST['parent_type']) && $_REQUEST['parent_type'] == 'Users') {
			$query .= " INNER JOIN meetings_users on meetings_users.meeting_id=meetings.id and meetings_users.deleted=0 where meetings_users.user_id=".
			"'" .$_REQUEST['parent_id']."'"; 	
		}
		else {
			$query .= " where 1=1 ";
		}
		
	 	if (!empty($_REQUEST['date_start'])) {
	 		$query .= " and meetings.date_start >= ".db_convert("'".$timedate->to_db_date($_REQUEST['date_start'], false)."'", 'date');
	 	}
	 	if (!empty($_REQUEST['date_finish'])) {
	 		$query .= " and meetings.date_start <= ".db_convert("'".$timedate->to_db_date($_REQUEST['date_finish'], false)."'", 'date');
	 	}
		if (isset($_REQUEST['parent_type']) && $_REQUEST['parent_type'] != 'Users') {
			$query .="  and meetings.parent_id='".$_REQUEST['parent_id']."' ";
		}	 	
		$query .= " and meetings.deleted=0 union all select 'Notes',notes.description,notes.id, notes.name,notes.date_entered,'None' from notes ";



		$query .= " where notes.deleted=0 and notes.parent_id='".$_REQUEST['parent_id']."'";
	}
	if(ACLController::checkAccess('Emails', 'list', true)) {	
		if ($query != "")
			$query .= " union all ";
		$query .="select 'Emails', '', emails.id,emails.name,emails.date_sent,emails.status from emails ";



		$query .= "	where emails.deleted=0 and ";
		
		if (isset($_REQUEST['parent_type']) && $_REQUEST['parent_type'] != 'Users') {
			$query .= "emails.parent_id='".$_REQUEST['parent_id']."' ";
		}
		else if (isset($_REQUEST['parent_type']) && $_REQUEST['parent_type'] == 'Users') {
			$query .= "emails.assigned_user_id='".$_REQUEST['parent_id']."' ";
		}
	}	
    $result = $focus->db->query($query, true, "");
    $row = $focus->db->fetchByAssoc($result);

    while ($row != null) {
        $activity = new SugarBean();
        $activity->name = $row['name'];
        $activity->description = $row['description'];
        $activity->date_start = $timedate->to_display_date_time($row['date_start']);
        if ($row['status'] == 'None')
        	$activity->status = $mod_strings['LBL_NONE_STRING'];
        else 
        	$activity->status = $row['status'];
        $activity->type = $app_list_strings['moduleListSingular'][$row['call']];
        $activity->id = $row['id'];        
        array_push($activities, $activity);
        $row = $focus->db->fetchByAssoc($result);
    }
    if (isset($_REQUEST['export_report']) && $_REQUEST['export_report'] == '1' ) {
		require_once('include/export_utils.php');
		$content = '"'.preg_replace("/\"/","\"\"",$mod_strings['LBL_TYPE']).'"'.getDelimiter().
		'"'.preg_replace("/\"/","\"\"",$mod_strings['LBL_SUBJECT']).'"'. getDelimiter().
		'"'.preg_replace("/\"/","\"\"",$mod_strings['LBL_DATE']).'"'. getDelimiter().
		'"'.preg_replace("/\"/","\"\"",$mod_strings['LBL_STATUS']).'"'. getDelimiter().
		'"'.preg_replace("/\"/","\"\"",$mod_strings['LBL_CHART_DESCRIPTION']).'"'. "\r\n";
		foreach($activities as $activity) {
			$content .= '"'.preg_replace("/\"/","\"\"",$activity->type). '"'.getDelimiter().'"'.preg_replace("/\"/","\"\"",$activity->name).
				'"'.getDelimiter().'"'.preg_replace("/\"/","\"\"",$timedate->to_display_date_time($activity->date_start)).'"'.getDelimiter().'"'.preg_replace("/\"/","\"\"",$activity->status).
				'"'.getDelimiter().'"'.preg_replace("/\"/","\"\"",$activity->description).'"'."\r\n";
		}
	    ob_clean();
		header("Pragma: cache");
		header("Content-type: application/octet-stream; charset=".$locale->getExportCharset());
		header("Content-Disposition: attachment; filename={$_REQUEST['module']}.csv");
		header("Content-transfer-encoding: binary");
		header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
		header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
		header( "Cache-Control: post-check=0, pre-check=0", false );
		header("Content-Length: ".strlen($content));
		print $GLOBALS['locale']->translateCharset($content, 'UTF-8', $locale->getExportCharset());
		exit;
    	
    }
}
$sugar_smarty->assign('count',count($activities));
$sugar_smarty->assign('Activities',$activities);
echo $sugar_smarty->fetch('modules/Activities/ActivitiesReports.tpl');

?>
