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
if(!is_admin($current_user)) sugar_die("Unauthorized access to administration.");


require_once('modules/Reports/Report.php');

$altered_cols = array (
    'project_task'=>array('milestone_flag'),
 	'tasks'=>array('date_start_flag','date_due_flag','date_start','time_start','date_due','time_due'),
	'calls'=>array('date_start', 'time_start'),
	'meetings'=>array('date_start', 'time_start'),
	'email_marketing'=>array('date_start', 'time_start'),
	'emails'=>array('date_start', 'time_start', 'date_sent'),
	'leads'=>array('do_not_call'),
	'contacts'=>array('do_not_call'),
	'prospects'=>array('do_not_call'),
	'workflow_alerts'=>array('where_filter'),		
	'workflow_triggershells'=>array('show_past'),		
	'workflow'=>array('status'),		
	'reports'=>array('is_published'),
    );
//$bad_reports = array();
    
function checkEachColInArr ($arr, $full_table_list, $report_id, $report_name, $user_name){
	foreach ($arr as $column) {
		global $beanFiles;
		if(empty($beanFiles)) {
			include('include/modules.php');
		}
		if(is_array($column))
		{
			$module_name = $full_table_list[$column['table_key']]['module'];
		}
		if(!isset($module_name))
		{
			continue;
		}
		$bean_name = get_singular_bean_name($module_name);
		require_once($beanFiles[$bean_name]);
		$module = new $bean_name;	
		$table = $module->table_name;	
		$colName = $column['name'];

		if((isset($altered_cols[$table]) && isset($altered_cols[$table][$colName]))
			|| $colName == 'email1' || $colName == 'email2') {
			echo $user_name.'------'.$report_name."------".$colName;
			//array_push($bad_reports[$report_id], $column);
		}
	}
}
function displayBadReportsList() {
	foreach($bad_reports as $key=>$cols) {
		echo $key.'***'.$cols;
	}
}

function checkReports() {	
	$savedReportBean = new SavedReport();
	$savedReportQuery = "select * from saved_reports where deleted=0";
	
	$result = $savedReportBean->db->query($savedReportQuery, true, "");
	$row = $savedReportBean->db->fetchByAssoc($result);
	while ($row != null) {
		$saved_report_seed = new SavedReport();
		$saved_report_seed->retrieve($row['id'], false);
		$report = new Report($saved_report_seed->content);
	
	
		$display_columns =  $report->report_def['display_columns'];
		$filters_def = $report->report_def['filters_def'];
		$group_defs = $report->report_def['group_defs'];
		if (!empty($report->report_def['order_by']))
			$order_by = $report->report_def['order_by'];
		else 
			$order_by = array();
		$summary_columns = $report->report_def['summary_columns'];
		$full_table_list = $report->report_def['full_table_list'];
		$owner_user = new User();
		$owner_user->retrieve($row['assigned_user_id']);
		checkEachColInArr($display_columns, $full_table_list, $row['id'], $row['name'], $owner_user->name);
		checkEachColInArr($group_defs, $full_table_list, $row['id'], $row['name'], $owner_user->name);
		checkEachColInArr($order_by, $full_table_list, $row['id'], $row['name'], $owner_user->name);
		checkEachColInArr($summary_columns, $full_table_list, $row['id'], $row['name'], $owner_user->name);
		foreach($filters_def as $filters_def_row)
		{
			checkEachColInArr($filters_def_row, $full_table_list, $row['id'], $row['name'], $owner_user->name);
		}
		$row = $savedReportBean->db->fetchByAssoc($result);
	}
}

checkReports();
//displayBadReportsList();


echo $mod_strings['LBL_DIAGNOSTIC_DONE'];

