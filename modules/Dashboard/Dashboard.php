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





//require_once('modules/Charts/code/predefined_charts.php');

class Dashboard extends SugarBean {

	var $db;
	var $field_name_map;

	// Stored fields
	var $id;
	var $date_entered;
	var $date_modified;
	var $modified_user_id;
	var $assigned_user_id;
	var $created_by;
	var $created_by_name;
	var $modified_by_name;



	var $name;
	var $description;
	var $content;
	var $user_id;

	var $table_name = "dashboards";
	var $object_name = "Dashboard";

	var $new_schema = true;


	var $additional_column_fields = array();

	var $module_dir = 'Dashboard';
	var $field_defs = array();
	var $field_defs_map = array();

	function Dashboard()
	{
		parent::SugarBean();
		$this->setupCustomFields('Dashboard');
		foreach ($this->field_defs as $field)
		{
			$this->field_name_map[$field['name']] = $field;
		}





	}

	function create_tables ()
	{
		parent::create_tables();
	}

	function get_summary_text()
	{
		return $this->name;
	}

	function getUsersTopDashboard($user_id)
	{
		$where = "dashboards.assigned_user_id='$user_id'";
		$response = $this->get_list("", $where, 0);

		if ( count($response['list']) > 0)
		{
			return $response['list'][0];
		}

		return $this->createUserDashboard($user_id);
	}

	function &createUserDashboard($user_id)
	{
		$test = array();
		$dashboard = new Dashboard();

		$dashboard->assigned_user_id = $user_id;
		$dashboard->created_by = $user_id;
		$dashboard->modified_user_id = $user_id;
		$dashboard->name = "Home";
		$dashboard->content = $this->getDefaultDashboardContents();
		$dashboard->save();
		return $dashboard;
	}

	function getDefaultDashboardContents()
	{
		$contents = array(
		array('type'=>'code','id'=>'Chart_pipeline_by_sales_stage'),
		array('type'=>'code','id'=>'Chart_lead_source_by_outcome'),
		array('type'=>'code','id'=>'Chart_outcome_by_month'),
		array('type'=>'code','id'=>'Chart_pipeline_by_lead_source'),
		);
		return serialize($contents);

	}


	function move ($dir='up',$chart_index)
	{
		$dashboard_def = unserialize(from_html($this->content));
		if ( $dir == 'up' && $chart_index != 0)
		{
			$extracted_array = $dashboard_def[$chart_index];
			array_splice($dashboard_def,$chart_index,1);
			array_splice($dashboard_def,$chart_index-1,0,array($extracted_array));
		}
		else if ( $dir == 'down' && $chart_index != (count($dashboard_def) - 1))
		{
			$extracted_array = $dashboard_def[$chart_index];
			array_splice($dashboard_def,$chart_index,1);
			array_splice($dashboard_def,$chart_index+1,0,array($extracted_array));
		}
		
		$this->content = serialize($dashboard_def);
		$this->save();
	}

	function arrange($chart_order) {
		$dashboard_def = unserialize(from_html($this->content));
		$dashboard_def_new = array();
		foreach($chart_order as $chart_index) {
			array_push($dashboard_def_new, $dashboard_def[$chart_index]);
		}

		$this->content = serialize($dashboard_def_new);
		$this->save();
	}
	
	function delete ($chart_index)
	{
		$dashboard_def = unserialize(from_html($this->content));
		array_splice($dashboard_def,$chart_index,1);
		$this->content = serialize($dashboard_def);
		$this->save();
	}

	function add ($chart_type,$chart_id,$chart_index)
	{
		global $predefined_charts;
		$dashboard_def = unserialize(from_html($this->content));
		if ( $chart_type == 'code')
		{
			if ( isset($predefined_charts[$chart_id]))
			{
				array_splice($dashboard_def,$chart_index,0,array($predefined_charts[$chart_id]));
			}
		} else if ($chart_type=='report')
		{
			$chart_def = array('type'=>'report','id'=>$chart_id);
			array_splice($dashboard_def,$chart_index,0,array($chart_def));

		}
		$this->content = serialize($dashboard_def);
		$this->save();
	}
	
	// return correct dashlet name based on array for 4.5.1 to 5.0 upgrade 
	function getDashletName($id){
		$dashletNames = array(
			'Chart_lead_source_by_outcome' 	=> 'OpportunitiesByLeadSourceByOutcomeDashlet',
			'Chart_pipeline_by_sales_stage' => 'PipelineBySalesStageDashlet',
			'Chart_outcome_by_month' 		=> 'OutcomeByMonthDashlet',
			'Chart_pipeline_by_lead_source' => 'OpportunitiesByLeadSourceDashlet', 
		);
		
		if (isset($dashletNames[$id]))
			return $dashletNames[$id];
		else
			return 'custom_dashlet';
	}
}

?>
