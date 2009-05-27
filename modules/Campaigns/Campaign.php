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








class Campaign extends SugarBean {
	var $field_name_map;
	
	// Stored fields
	var $id;
	var $date_entered;
	var $date_modified;
	var $modified_user_id;
	var $assigned_user_id;
	var $created_by;
	var $created_by_name;
    var $currency_id;
	var $modified_by_name;




	var $name;
	var $start_date;
	var $end_date;
	var $status;
	var $expected_cost;
	var $budget;
	var $actual_cost;
	var $expected_revenue;
	var $campaign_type;
	var $objective;
	var $content;
	var $tracker_key;
	var $tracker_text;
	var $tracker_count;
	var $refer_url;
    var $impressions;
	
	// These are related
	var $assigned_user_name;

	// module name definitions and table relations
	var $table_name = "campaigns";
	var $rel_prospect_list_table = "prospect_list_campaigns";
	var $object_name = "Campaign";
	var $module_dir = 'Campaigns';
	var $importable = true;
    
  	// This is used to retrieve related fields from form posts.
	var $additional_column_fields = array(
				'assigned_user_name', 'assigned_user_id',
	);

	var $relationship_fields = Array('prospect_list_id'=>'prospect_lists');
    
	function Campaign() {
		global $sugar_config;
		parent::SugarBean();
	}

	var $new_schema = true;
	
	function list_view_parse_additional_sections(&$listTmpl) {
		// take $assigned_user_id and get the Username value to assign
		$assId = $this->getFieldValue('assigned_user_id');
		
		$query = "SELECT first_name, last_name FROM users WHERE id = '".$assId."'";
		$result = $this->db->query($query);
		$user = $this->db->fetchByAssoc($result);
		
		//_ppd($user);
		if(!empty($user)) {
			$fullName = $user["first_name"]." ".$user["last_name"];
			$listTmpl->assign('ASSIGNED_USER_NAME', $fullName);
		}
	}
	

	function get_summary_text()
	{
		return "$this->name";
	}

        function create_export_query(&$order_by, &$where, $relate_link_join='')
        {
        	$custom_join = $this->custom_fields->getJOIN(true, true,$where);
			if($custom_join)
				$custom_join['join'] .= $relate_link_join;
            $query = "SELECT
            campaigns.*,
            users.user_name as assigned_user_name ";



        	if($custom_join){
				$query .=  $custom_join['select'];
			}
	        $query .= " FROM campaigns ";




			$query .= "LEFT JOIN users
                      ON campaigns.assigned_user_id=users.id";



        	if($custom_join){
				$query .=  $custom_join['join'];
			}

		$where_auto = " campaigns.deleted=0";

        if($where != "")
                $query .= " where $where AND ".$where_auto;
        else
                $query .= " where ".$where_auto;

        if($order_by != "")
                $query .= " ORDER BY $order_by";
        else
                $query .= " ORDER BY campaigns.name";
        return $query;
    }



	function clear_campaign_prospect_list_relationship($campaign_id, $prospect_list_id='')
	{
		if(!empty($prospect_list_id))
			$prospect_clause = " and prospect_list_id = '$prospect_list_id' ";
		else
			$prospect_clause = '';
			
		$query = "DELETE FROM $this->rel_prospect_list_table WHERE campaign_id='$campaign_id' AND deleted = '0' " . $prospect_clause;
	 	$this->db->query($query, true, "Error clearing campaign to prospect_list relationship: ");
	}
	
		

	function mark_relationships_deleted($id)
	{
		$this->clear_campaign_prospect_list_relationship($id);
	}

	function fill_in_additional_list_fields()
	{
		parent::fill_in_additional_list_fields();
	}

	function fill_in_additional_detail_fields()
	{
        parent::fill_in_additional_detail_fields();		
		//format numbers.
		
		//don't need additional formatting here.
		//$this->budget=format_number($this->budget);
		//$this->expected_cost=format_number($this->expected_cost);
		//$this->actual_cost=format_number($this->actual_cost);
		//$this->expected_revenue=format_number($this->expected_revenue);
	}

	
	function update_currency_id($fromid, $toid){
	}


	function get_list_view_data(){

		$temp_array = $this->get_list_view_array();
		if ($this->campaign_type != 'Email') {
			$temp_array['OPTIONAL_LINK']="display:none";
		}
		return $temp_array;
	}
	/**
		builds a generic search based on the query string using or
		do not include any $this-> because this is called on without having the class instantiated
	*/
	function build_generic_where_clause ($the_query_string) 
	{
		$where_clauses = Array();
		$the_query_string = $this->db->quote($the_query_string);
		array_push($where_clauses, "campaigns.name like '$the_query_string%'");

		$the_where = "";
		foreach($where_clauses as $clause)
		{
			if($the_where != "") $the_where .= " or ";
			$the_where .= $clause;
		}


		return $the_where;
	}

	function save($check_notify = FALSE) {
		
			//US DOLLAR
			if(isset($this->amount) && !empty($this->amount)){

				$currency = new Currency();
				$currency->retrieve($this->currency_id);
				$this->amount_usdollar = $currency->convertToDollar($this->amount);

			}
			
		$this->unformat_all_fields();
			
		return parent::save($check_notify);

	}

	function set_notification_body($xtpl, $camp)
	{
		$xtpl->assign("CAMPAIGN_NAME", $camp->name);
		$xtpl->assign("CAMPAIGN_AMOUNT", $camp->budget);
		$xtpl->assign("CAMPAIGN_CLOSEDATE", $camp->end_date);
		$xtpl->assign("CAMPAIGN_STATUS", $camp->status);
		$xtpl->assign("CAMPAIGN_DESCRIPTION", $camp->content);

		return $xtpl;
	}



	function track_log_entries($type=array()) {
        //get arguments being passed in
        $args = func_get_args();
        $mkt_id ='';
        //if one of the arguments is marketing ID, then we need to filter by it
        foreach($args as $arg){
            if(isset($arg['EMAIL_MARKETING_ID_VALUE'])){
                $mkt_id = $arg['EMAIL_MARKETING_ID_VALUE'];
            }
        }
		if (empty($type)) 
			$type[0]='targeted';
		$this->load_relationship('log_entries');
		$query_array = $this->log_entries->getQuery(true);
		$query_array['select'] ="SELECT campaign_log.* ";
		$query_array['where'] = $query_array['where']. " AND activity_type='{$type[0]}' AND archived=0";
        //add filtering by marketing id, if it exists
        if (!empty($mkt_id)) $query_array['where'] = $query_array['where']. " AND marketing_id ='$mkt_id' ";
		return (implode(" ",$query_array));
	}


	function get_queue_items() {
        //get arguments being passed in
        $args = func_get_args();
        $mkt_id ='';
        //if one of the arguments is marketing ID, then we need to filter by it
        foreach($args as $arg){
            if(isset($arg['EMAIL_MARKETING_ID_VALUE'])){
                $mkt_id = $arg['EMAIL_MARKETING_ID_VALUE'];
            }
        }
		$this->load_relationship('queueitems');
		$query_array = $this->queueitems->getQuery(true);
        //add filtering by marketing id, if it exists, and if where key is not empty
        if (!empty($mkt_id) && !empty($query_array['where'])){
             $query_array['where'] = $query_array['where']. " AND marketing_id ='$mkt_id' ";
        }
		//get select query from email man.
		
		$man = new EmailMan();
		$listquery= $man->create_new_list_query('',str_replace(array("WHERE","where"),"",$query_array['where']));	
		return ($listquery);
		
	}
//	function get_prospect_list_entries() {
//		$this->load_relationship('prospectlists');
//		$query_array = $this->prospectlists->getQuery(true);
//
//		$query=<<<EOQ
//			SELECT distinct prospect_lists.*,
//			(case  when (email_marketing.id is null) then default_message.id else email_marketing.id end) marketing_id, 
//			(case  when  (email_marketing.id is null) then default_message.name else email_marketing.name end) marketing_name
//	
//			FROM prospect_lists 
//
//			INNER JOIN prospect_list_campaigns ON (prospect_lists.id=prospect_list_campaigns.prospect_list_id AND prospect_list_campaigns.campaign_id='{$this->id}')
//		
//			LEFT JOIN email_marketing on email_marketing.message_for = prospect_lists.id and email_marketing.campaign_id = '{$this->id}'
//			and email_marketing.deleted =0 and email_marketing.status='active'
//
//			LEFT JOIN email_marketing default_message on default_message.message_for = prospect_list_campaigns.campaign_id and 
//			default_message.campaign_id = '{$this->id}' and default_message.deleted =0 
//			and default_message.status='active'
//
//			WHERE prospect_list_campaigns.deleted=0 AND prospect_lists.deleted=0
//
//EOQ;
//		return $query;
//	}
	
	 function bean_implements($interface){
		switch($interface){
			case 'ACL':return true;
		}
		return false;
	}
}
?>
