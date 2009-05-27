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
require_once('soap/SoapHelperFunctions.php');
require_once('soap/SoapTypes.php');





/*************************************************************************************

THIS IS FOR SUGARCRM USERS


*************************************************************************************/
$disable_date_format = true;

$server->register(
    'new_get_entry',
    array('session'=>'xsd:string', 'module_name'=>'xsd:string', 'id'=>'xsd:string', 'select_fields'=>'tns:select_fields','link_name_to_fields_array'=>'tns:link_name_to_fields_array'),
    array('return'=>'tns:get_entry_result_version2'),
    $NAMESPACE);

/**
 * Retrieve a single SugarBean based on ID.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param String $module_name -- The name of the module to return records from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 * @param String $id -- The SugarBean's ID value.
 * @param Array  $select_fields -- A list of the fields to be included in the results. This optional parameter allows for only needed fields to be retrieved.
* @param Array $link_name_to_fields_array – A list of link_names and for each link_name, what fields value to be returned. For ex.'link_name_to_fields_array' => array(array('name' =>  'email_addresses', 'value' => array('id', 'email_address', 'opt_out', 'primary_address'))) 
* @return Array 
*        'entry_list' -- The records name value pair for the simple data types excluding link field data.
*	     'relationship_list' – The records link field data. The example is if asked about accounts email address then return data would look like Array ( [0] => Array ( [name] => email_addresses [records] => Array ( [0] => Array ( [0] => Array ( [name] => id [value] => 3fb16797-8d90-0a94-ac12-490b63a6be67 ) [1] => Array ( [name] => email_address [value] => hr.kid.qa@example.com ) [2] => Array ( [name] => opt_out [value] => 0 ) [3] => Array ( [name] => primary_address [value] => 1 ) ) [1] => Array ( [0] => Array ( [name] => id [value] => 403f8da1-214b-6a88-9cef-490b63d43566 ) [1] => Array ( [name] => email_address [value] => kid.hr@example.name ) [2] => Array ( [name] => opt_out [value] => 0 ) [3] => Array ( [name] => primary_address [value] => 0 ) ) ) ) )							     		    
* @exception 'SoapFault' -- The SOAP error, if any
*/
function new_get_entry($session, $module_name, $id,$select_fields, $link_name_to_fields_array){
	return new_get_entries($session, $module_name, array($id), $select_fields, $link_name_to_fields_array);
}

$server->register(
    'new_get_entries',
    array('session'=>'xsd:string', 'module_name'=>'xsd:string', 'ids'=>'tns:select_fields', 'select_fields'=>'tns:select_fields', 'link_name_to_fields_array'=>'tns:link_name_to_fields_array'),
    array('return'=>'tns:get_entry_result_version2'),
    $NAMESPACE);

/**
 * Retrieve a list of SugarBean's based on provided IDs. This API will not wotk with report module 
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param String $module_name -- The name of the module to return records from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 * @param Array $ids -- An array of SugarBean IDs.
 * @param Array $select_fields -- A list of the fields to be included in the results. This optional parameter allows for only needed fields to be retrieved.
* @param Array $link_name_to_fields_array – A list of link_names and for each link_name, what fields value to be returned. For ex.'link_name_to_fields_array' => array(array('name' =>  'email_addresses', 'value' => array('id', 'email_address', 'opt_out', 'primary_address'))) 
* @return Array 
*        'entry_list' -- The records name value pair for the simple data types excluding link field data.
*	     'relationship_list' – The records link field data. The example is if asked about accounts email address then return data would look like Array ( [0] => Array ( [name] => email_addresses [records] => Array ( [0] => Array ( [0] => Array ( [name] => id [value] => 3fb16797-8d90-0a94-ac12-490b63a6be67 ) [1] => Array ( [name] => email_address [value] => hr.kid.qa@example.com ) [2] => Array ( [name] => opt_out [value] => 0 ) [3] => Array ( [name] => primary_address [value] => 1 ) ) [1] => Array ( [0] => Array ( [name] => id [value] => 403f8da1-214b-6a88-9cef-490b63d43566 ) [1] => Array ( [name] => email_address [value] => kid.hr@example.name ) [2] => Array ( [name] => opt_out [value] => 0 ) [3] => Array ( [name] => primary_address [value] => 0 ) ) ) ) )							     		    
* @exception 'SoapFault' -- The SOAP error, if any
*/
function new_get_entries($session, $module_name, $ids, $select_fields, $link_name_to_fields_array){
	global  $beanList, $beanFiles;
	$error = new SoapError();
		
	$linkoutput_list = array();
	$output_list = array();
    $using_cp = false;
    if($module_name == 'CampaignProspects'){
        $module_name = 'Prospects';
        $using_cp = true;
    }
	
	if (!checkSessionAndModuleAccess($session, 'invalid_session', $module_name, 'read', 'no_access', $error)) {
		return;
	} // if
	
	if($module_name == 'Reports'){
		$error->set_error('invalid_call_error');
		setFaultObject($error);
		return;
	}
	
	$class_name = $beanList[$module_name];
	require_once($beanFiles[$class_name]);

	$temp = new $class_name();
	foreach($ids as $id) {
		$seed = @clone($temp);
	    if($using_cp){
	        $seed = $seed->retrieveTarget($id);
	    }else{
			$seed->retrieve($id);
	    }
	    if (!checkACLAccess($seed, 'DetailView', $error, 'no_access')) {
	    	return;
	    }
		$output_list[] = get_return_value_for_fields($seed, $module_name, $select_fields);
		$linkoutput_list[] = get_return_value_for_link_fields($seed, $module_name, $link_name_to_fields_array);
	}
	return array('entry_list'=>$output_list, 'relationship_list' => $linkoutput_list);
}

$server->register(
    'new_get_entry_list',
    array('session'=>'xsd:string', 'module_name'=>'xsd:string', 'query'=>'xsd:string', 'order_by'=>'xsd:string','offset'=>'xsd:int', 'select_fields'=>'tns:select_fields', 'link_name_to_fields_array'=>'tns:link_name_to_fields_array', 'max_results'=>'xsd:int', 'deleted'=>'xsd:int'),
    array('return'=>'tns:get_entry_list_result_version2'),
    $NAMESPACE);

/**
 * Retrieve a list of beans.  This is the primary method for getting list of SugarBeans from Sugar using the SOAP API.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param String $module_name -- The name of the module to return records from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 * @param String $query -- SQL where clause without the word 'where'
 * @param String $order_by -- SQL order by clause without the phrase 'order by'
 * @param String $offset -- The record offset to start from.
 * @param Array  $select_fields -- A list of the fields to be included in the results. This optional parameter allows for only needed fields to be retrieved.
 * @param Array $link_name_to_fields_array – A list of link_names and for each link_name, what fields value to be returned. For ex.'link_name_to_fields_array' => array(array('name' =>  'email_addresses', 'value' => array('id', 'email_address', 'opt_out', 'primary_address'))) 
* @param String $max_results -- The maximum number of records to return.  The default is the sugar configuration value for 'list_max_entries_per_page'
 * @param Number $deleted -- false if deleted records should not be include, true if deleted records should be included.
 * @return Array 'result_count' -- The number of records returned
 *               'next_offset' -- The start of the next page (This will always be the previous offset plus the number of rows returned.  It does not indicate if there is additional data unless you calculate that the next_offset happens to be closer than it should be.
 *               'entry_list' -- The records that were retrieved
 *	     		 'relationship_list' – The records link field data. The example is if asked about accounts email address then return data would look like Array ( [0] => Array ( [name] => email_addresses [records] => Array ( [0] => Array ( [0] => Array ( [name] => id [value] => 3fb16797-8d90-0a94-ac12-490b63a6be67 ) [1] => Array ( [name] => email_address [value] => hr.kid.qa@example.com ) [2] => Array ( [name] => opt_out [value] => 0 ) [3] => Array ( [name] => primary_address [value] => 1 ) ) [1] => Array ( [0] => Array ( [name] => id [value] => 403f8da1-214b-6a88-9cef-490b63d43566 ) [1] => Array ( [name] => email_address [value] => kid.hr@example.name ) [2] => Array ( [name] => opt_out [value] => 0 ) [3] => Array ( [name] => primary_address [value] => 0 ) ) ) ) )							     		    
* @exception 'SoapFault' -- The SOAP error, if any
*/
function new_get_entry_list($session, $module_name, $query, $order_by,$offset, $select_fields, $link_name_to_fields_array, $max_results, $deleted ){
	global  $beanList, $beanFiles;
	$error = new SoapError();
    $using_cp = false;
    if($module_name == 'CampaignProspects'){
        $module_name = 'Prospects';
        $using_cp = true;
    }
    
	if (!checkSessionAndModuleAccess($session, 'invalid_session', $module_name, 'read', 'no_access', $error)) {
		return;
	} // if
    
	// If the maximum number of entries per page was specified, override the configuration value.
	if($max_results > 0){
		global $sugar_config;
		$sugar_config['list_max_entries_per_page'] = $max_results;
	} // if

	$class_name = $beanList[$module_name];
	require_once($beanFiles[$class_name]);
	$seed = new $class_name();
	
    if (!checkACLAccess($seed, 'Export', $error, 'no_access')) {
    	return;
    } // if
	
	if($query == ''){
		$where = '';
	} // if
	if($offset == '' || $offset == -1){
		$offset = 0;
	} // if
    if($using_cp){
        $response = $seed->retrieveTargetList($query, $select_fields, $offset,-1,-1,$deleted);
    }else{
	   $response = $seed->get_list($order_by, $query, $offset,-1,-1,$deleted);
    } // else
	$list = $response['list'];

	$output_list = array();
	$linkoutput_list = array();

	foreach($list as $value) {
		if(isset($value->emailAddress)){
			$value->emailAddress->handleLegacyRetrieve($value);
		} // if
		$value->fill_in_additional_detail_fields();
		$output_list[] = get_return_value_for_fields($value, $module_name, $select_fields);
		$linkoutput_list[] = get_return_value_for_link_fields($value, $module_name, $link_name_to_fields_array);
	} // foreach

	// Calculate the offset for the start of the next page
	$next_offset = $offset + sizeof($output_list);

	return array('result_count'=>sizeof($output_list), 'next_offset'=>$next_offset, 'entry_list'=>$output_list, 'relationship_list' => $linkoutput_list);
} // fn


$server->register(
    'new_set_relationship',
    array('session'=>'xsd:string','module_name'=>'xsd:string','module_id'=>'xsd:string','link_field_name'=>'xsd:string', 'related_ids'=>'tns:select_fields'),
    array('return'=>'tns:new_set_relationship_list_result'),
    $NAMESPACE);

/**
 * Set a single relationship between two beans.  The items are related by module name and id.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param String $module_name – name of the module that the primary record is from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 * @param String $module_id - The ID of the bean in the specified module_name
 * @param String link_field_name – name of the link field which relates to the other module for which the relationship needs to be generated.
 * @param array related_ids – array of related record ids for which relationships needs to be generated
 * @return Array - created - How many relationships has been created
 *               - failed - How many relationsip creation failed
 * @exception 'SoapFault' -- The SOAP error, if any
 */
function new_set_relationship($session, $module_name, $module_id, $link_field_name, $related_ids){
	$error = new SoapError();
	if (!checkSessionAndModuleAccess($session, 'invalid_session', '', '', '', $error)) {
		return;
	} // if

	$count = 0;
	$failed = 0;
	if (new_handle_set_relationship($module_name, $module_id, $link_field_name, $related_ids)) {
		$count++;
	} else {
		$failed++;
	} // else
	return array('created'=>$count , 'failed'=>$failed);	
}

$server->register(
    'new_set_relationships',
    array('session'=>'xsd:string','module_names'=>'tns:select_fields','module_ids'=>'tns:select_fields','link_field_names'=>'tns:select_fields','related_ids'=>'tns:new_set_relationhip_ids'),
    array('return'=>'tns:new_set_relationship_list_result'),
    $NAMESPACE);

/**
 * Set a single relationship between two beans.  The items are related by module name and id.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param array $module_names – Array of the name of the module that the primary record is from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 * @param array $module_ids - The array of ID of the bean in the specified module_name
 * @param array $link_field_names – Array of the name of the link field which relates to the other module for which the relationships needs to be generated.
 * @param array $related_ids – array of an array of related record ids for which relationships needs to be generated
 * @return Array - created - How many relationships has been created
 *               - failed - How many relationsip creation failed
 * @exception 'SoapFault' -- The SOAP error, if any
*/
function new_set_relationships($session, $module_names, $module_ids, $link_field_names, $related_ids) {
	$error = new SoapError();
	if (!checkSessionAndModuleAccess($session, 'invalid_session', '', '', '', $error)) {
		return;
	} // if
	
	if ((empty($module_names) || empty($module_ids) || empty($link_field_names) || empty($related_ids)) ||
		(sizeof($module_names) != (sizeof($module_ids) || sizeof($link_field_names) || sizeof($related_ids)))) {
		$error->set_error('invalid_data_format');
		setFaultObject($error);
		return;		
	} // if
		
	$count = 0;
	$failed = 0;
	$counter = 0;
	foreach($module_names as $module_name) {
		if (new_handle_set_relationship($module_name, $module_ids[$counter], $link_field_names[$counter], $related_ids[$counter])) {
			$count++;
		} else {
			$failed++;
		} // else
		$counter++;
	} // foreach
	return array('created'=>$count , 'failed'=>$failed);
} // fn

$server->register(
    'new_get_relationships',
    array('session'=>'xsd:string', 'module_name'=>'xsd:string', 'module_id'=>'xsd:string', 'link_field_name'=>'xsd:string', 'related_module_query'=>'xsd:string', 'related_fields'=>'tns:select_fields', 'related_module_link_name_to_fields_array'=>'tns:link_name_to_fields_array', 'deleted'=>'xsd:int'),
    array('return'=>'tns:get_entry_result_version2'),
    $NAMESPACE);

/**
 * Retrieve a collection of beans that are related to the specified bean and optionally return relationship data for those related beans.
 * So in this API you can get contacts info for an account and also return all those contact's email address or an opportunity info also.
 * 
 * @param String $session -- Session ID returned by a previous call to login.
 * @param String $module_name -- The name of the module that the primary record is from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 * @param String $module_id -- The ID of the bean in the specified module
 * @param String $link_field_name -- The name of the lnk field to return records from.  This name should be the name the relationship.
 * @param String $related_module_query -- A portion of the where clause of the SQL statement to find the related items.  The SQL query will already be filtered to only include the beans that are related to the specified bean.
 * @param Array $related_fields - Array of related bean fields to be returned.
 * @param Array $related_module_link_name_to_fields_array - For every related bean returrned, specify link fields name to fields info for that bean to be returned. For ex.'link_name_to_fields_array' => array(array('name' =>  'email_addresses', 'value' => array('id', 'email_address', 'opt_out', 'primary_address'))). 
 * @param Number $deleted -- false if deleted records should not be include, true if deleted records should be included.
 * @return Array 'entry_list' -- The records that were retrieved
 *	     		 'relationship_list' – The records link field data. The example is if asked about accounts contacts email address then return data would look like Array ( [0] => Array ( [name] => email_addresses [records] => Array ( [0] => Array ( [0] => Array ( [name] => id [value] => 3fb16797-8d90-0a94-ac12-490b63a6be67 ) [1] => Array ( [name] => email_address [value] => hr.kid.qa@example.com ) [2] => Array ( [name] => opt_out [value] => 0 ) [3] => Array ( [name] => primary_address [value] => 1 ) ) [1] => Array ( [0] => Array ( [name] => id [value] => 403f8da1-214b-6a88-9cef-490b63d43566 ) [1] => Array ( [name] => email_address [value] => kid.hr@example.name ) [2] => Array ( [name] => opt_out [value] => 0 ) [3] => Array ( [name] => primary_address [value] => 0 ) ) ) ) )							     		    
* @exception 'SoapFault' -- The SOAP error, if any
*/    
function new_get_relationships($session, $module_name, $module_id, $link_field_name, $related_module_query, $related_fields, $related_module_link_name_to_fields_array, $deleted){

	global  $beanList, $beanFiles;
	$error = new SoapError();
	if (!checkSessionAndModuleAccess($session, 'invalid_session', $module_name, 'read', 'no_access', $error)) {
		return;
	} // if
	
	$class_name = $beanList[$module_name];
	require_once($beanFiles[$class_name]);
	$mod = new $class_name();
	$mod->retrieve($module_id);
	
    if (!checkACLAccess($mod, 'DetailView', $error, 'no_access')) {
    	return;
    } // if
		  
    $output_list = array();
	$linkoutput_list = array();

	// get all the related mmodules data.
    $result = getRelationshipResults($mod, $link_field_name, $related_fields, $related_module_query);
	if ($result) {
		$list = $result['rows'];
		$filterFields = $result['fields_set_on_rows'];

		if (sizeof($list) > 0) {
			// get the related module name and instantiate a bean for that.
			$submodulename = $mod->$link_field_name->getRelatedModuleName();
			$submoduleclass = $beanList[$submodulename];
			require_once($beanFiles[$submoduleclass]);
	
			$submoduletemp = new $submoduleclass();
			foreach($list as $row) {
				$submoduleobject = @clone($submoduletemp);
				// set all the database data to this object
				foreach ($filterFields as $field) {
					$submoduleobject->$field = $row[$field];
				} // foreach
				if (isset($row['id'])) {
					$submoduleobject->id = $row['id'];
				}
				$output_list[] = get_return_value_for_fields($submoduleobject, $submodulename, $filterFields);
				$linkoutput_list[] = get_return_value_for_link_fields($submoduleobject, $submodulename, $related_module_link_name_to_fields_array);
				
			} // foreach
		}
		
	} // if
	
	return array('entry_list'=>$output_list, 'relationship_list' => $linkoutput_list);
	
} // fn

$server->register(
    'new_set_entry',
    array('session'=>'xsd:string', 'module_name'=>'xsd:string',  'name_value_list'=>'tns:name_value_list'),
    array('return'=>'tns:new_set_entry_result'),
    $NAMESPACE);

/**
 * Update or create a single SugarBean.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param String $module_name -- The name of the module to return records from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 * @param Array $name_value_list -- The keys of the array are the SugarBean attributes, the values of the array are the values the attributes should have.
 * @return Array    'id' -- the ID of the bean that was written to (-1 on error)
 * @exception 'SoapFault' -- The SOAP error, if any
*/
function new_set_entry($session,$module_name, $name_value_list){
	global  $beanList, $beanFiles;

	$error = new SoapError();
	if (!checkSessionAndModuleAccess($session, 'invalid_session', $module_name, 'write', 'no_access', $error)) {
		return;
	} // if
	$class_name = $beanList[$module_name];
	require_once($beanFiles[$class_name]);
	$seed = new $class_name();

	foreach($name_value_list as $value){
		if($value['name'] == 'id'){
			$seed->retrieve($value['value']);
			break;
		}
	}
	foreach($name_value_list as $value){
		$seed->$value['name'] = $value['value'];
	}
    if (!checkACLAccess($seed, 'Save', $error, 'no_access') || ($seed->deleted == 1  && checkACLAccess($seed, 'Delete', $error, 'no_access'))) {
    	return;
    } // if
	
	$seed->save();
	if($seed->deleted == 1){
		$seed->mark_deleted($seed->id);
	}
	return array('id'=>$seed->id);
} // fn

$server->register(
    'new_set_entries',
    array('session'=>'xsd:string', 'module_name'=>'xsd:string',  'name_value_lists'=>'tns:name_value_lists'),
    array('return'=>'tns:new_set_entries_result'),
    $NAMESPACE);

/**
 * Update or create a list of SugarBeans
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param String $module_name -- The name of the module to return records from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 * @param Array $name_value_lists -- Array of Bean specific Arrays where the keys of the array are the SugarBean attributes, the values of the array are the values the attributes should have.
 * @return Array    'ids' -- Array of the IDs of the beans that was written to (-1 on error)
 * @exception 'SoapFault' -- The SOAP error, if any                 
 */
function new_set_entries($session,$module_name, $name_value_lists){
	$error = new SoapError();
	if (!checkSessionAndModuleAccess($session, 'invalid_session', $module_name, 'write', 'no_access', $error)) {
		return;
	} // if
		
	return new_handle_set_entries($module_name, $name_value_lists, FALSE);
}

$server->register(
        'new_login',
        array('user_auth'=>'tns:user_auth', 'application_name'=>'xsd:string'),
        array('return'=>'tns:new_set_entry_result'),
        $NAMESPACE);

/**
 * Log the user into the application
 *
 * @param UserAuth array $user_auth -- Set user_name and password (password needs to be
 *      in the right encoding for the type of authentication the user is setup for.  For Base
 *      sugar validation, password is the MD5 sum of the plain text password.
 * @param String $application -- The name of the application you are logging in from.  (Currently unused).
 * @return Array(session_id) -- session_id is the id of the session that was created.
 * @exception 'SoapFault' -- The SOAP error, if any
 */
function new_login($user_auth, $application){
	global $sugar_config, $system_config;

	$error = new SoapError();
	$user = new User();
	$success = false;
	//rrs
		$system_config = new Administration();
	$system_config->retrieveSettings('system');
	$authController = new AuthenticationController((!empty($sugar_config['authenticationClass'])? $sugar_config['authenticationClass'] : 'SugarAuthenticate'));
	//rrs
	$user = $user->retrieve_by_string_fields(array('user_name'=>$user_auth['user_name'],'user_hash'=>$user_auth['password'], 'deleted'=>0, 'status'=>'Active', 'portal_only'=>0) );
	if(!empty($user) && !empty($user->id)) {
		$success = true;
		global $current_user;
		$current_user = $user;
	} else if(function_exists('mcrypt_cbc')){
		$password = decrypt_string($user_auth['password']);
		if($authController->login($user_auth['user_name'], $password) && isset($_SESSION['authenticated_user_id'])){
			$success = true;
		} // if
	} // else if

	if($success){
		session_start();
		global $current_user;
		//$current_user = $user;
		login_success();
		$current_user->loadPreferences();
		$_SESSION['is_valid_session']= true;
		$_SESSION['ip_address'] = query_client_ip();
		$_SESSION['user_id'] = $current_user->id;
		$_SESSION['type'] = 'user';
		$_SESSION['avail_modules']= get_user_module_list($current_user);
		$_SESSION['authenticated_user_id'] = $current_user->id;
		$_SESSION['unique_key'] = $sugar_config['unique_key'];

		$current_user->call_custom_logic('after_login');
		return array('id'=>session_id());
	} // if
	LogicHook::initialize();
	$GLOBALS['logic_hook']->call_custom_logic('Users', 'login_failed');
	$error->set_error('invalid_login');
	setFaultObject($error);
}

$server->register(
        'new_logout',
        array('session'=>'xsd:string'),
        array(),
        $NAMESPACE);

/**
 * Log out of the session.  This will destroy the session and prevent other's from using it.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @return Empty
 * @exception 'SoapFault' -- The SOAP error, if any
 */
function new_logout($session){
	global $current_user;

	$error = new SoapError();
	LogicHook::initialize();
	if (!checkSessionAndModuleAccess($session, 'invalid_session', '', '', '', $error)) {
		$GLOBALS['logic_hook']->call_custom_logic('Users', 'after_logout');
		return;
	} // if
	
	$current_user->call_custom_logic('before_logout');
	session_destroy();
	$GLOBALS['logic_hook']->call_custom_logic('Users', 'after_logout');
} // fn

$server->register(
    'get_server_info',
    array(),
    array('return'=>'tns:get_server_info_result'),
    $NAMESPACE);

/**
 * Gets server info. This will return information like version, flavor and gmt_time.
 * @return Array - flavor - Retrieve the specific flavor of sugar.
 * 				 - version - Retrieve the version number of Sugar that the server is running.
 * 				 - gmt_time - Return the current time on the server in the format 'Y-m-d H:i:s'. This time is in GMT.
 * @exception 'SoapFault' -- The SOAP error, if any
 */
function get_server_info(){
	global $sugar_flavor;
	
	

	$admin  = new Administration();
	$admin->retrieveSettings('info');
	$sugar_version = '';
	if(isset($admin->settings['info_sugar_version'])){
		$sugar_version = $admin->settings['info_sugar_version'];
	}else{
		$sugar_version = '1.0';
	}
 
	return array('flavor' => $sugar_flavor, 'version' => $sugar_version, 'gmt_time' => gmdate('Y-m-d H:i:s'));
} // fn

$server->register(
    'new_get_user_id',
    array('session'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

/**
 * Return the user_id of the user that is logged into the current session.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @return String -- the User ID of the current session
 * @exception 'SoapFault' -- The SOAP error, if any
 */
function new_get_user_id($session){
	if (!checkSessionAndModuleAccess($session, 'invalid_session', '', '', '', $error)) {
		return;
	} // if
	global $current_user;
	return $current_user->id;
} // fn

$server->register(
    'new_get_module_fields',
    array('session'=>'xsd:string', 'module_name'=>'xsd:string'),
    array('return'=>'tns:new_module_fields'),
    $NAMESPACE);

/**
 * Retrieve vardef information on the fields of the specified bean.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param String $module_name -- The name of the module to return records from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 * @return Array    'module_fields' -- The vardef information on the selected fields.
 *                  'link_fields' -- The vardef information on the link fields
 * @exception 'SoapFault' -- The SOAP error, if any
 */
function new_get_module_fields($session, $module_name){
	global  $beanList, $beanFiles;
	$error = new SoapError();
	$module_fields = array();
	
	if (!checkSessionAndModuleAccess($session, 'invalid_session', $module_name, 'read', 'no_access', $error)) {
		return;
	} // if
	
	$class_name = $beanList[$module_name];
	require_once($beanFiles[$class_name]);
	$seed = new $class_name();
	if($seed->ACLAccess('ListView', true) || $seed->ACLAccess('DetailView', true) || 	$seed->ACLAccess('EditView', true) ) {
    	return new_get_return_module_fields($seed, $module_name);
    }
    $error->set_error('no_access');
	setFaultObject($error);
    
}

$server->register(
    'new_seamless_login',
    array('session'=>'xsd:string'),
    array('return'=>'xsd:int'),
    $NAMESPACE);

/**
 * Perform a seamless login. This is used internally during the sync process.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @return true -- if the session was authenticated
 * @return false -- if the session could not be authenticated
 */
function new_seamless_login($session){
		if(!validate_authenticated($session)){
			return 0;
		}
		$_SESSION['seamless_login'] = true;
		return 1;
}

$server->register(
        'new_set_note_attachment',
        array('session'=>'xsd:string','note'=>'tns:new_note_attachment'),
        array('return'=>'tns:new_set_entry_result'),
        $NAMESPACE);

/**
 * Add or replace the attachment on a Note. 
 * Optionally you can set the relationship of this note to Accounts/Contacts and so on by setting related_module_id, related_module_name
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param Array 'note' -- Array String 'id' -- The ID of the Note containing the attachment
 *                              String 'filename' -- The file name of the attachment
 *                              Binary 'file' -- The binary contents of the file.
 * 								String 'related_module_id' -- module id to which this note to related to
 * 								String 'related_module_name' - module name to which this note to related to
 * 
 * @return Array 'id' -- The ID of the Note
 * @exception 'SoapFault' -- The SOAP error, if any
 */
function new_set_note_attachment($session, $note) {
	$error = new SoapError();
	$module_name = '';
	$module_access = '';
	$module_id = '';
	if (!empty($note['related_module_id']) && !empty($note['related_module_name'])) {
		$module_name = $note['related_module_name'];
		$module_id = $note['related_module_id'];
		$module_access = 'read';
	}
	if (!checkSessionAndModuleAccess($session, 'invalid_session', $module_name, $module_access, 'no_access', $error)) {
		return;
	} // if

	require_once('modules/Notes/NoteSoap.php');
	$ns = new NoteSoap();
	return array('id'=>$ns->newSaveFile($note));
} // fn

$server->register(
    'new_get_note_attachment',
    array('session'=>'xsd:string', 'id'=>'xsd:string'),
    array('return'=>'tns:new_return_note_attachment'),
    $NAMESPACE);

/**
 * Retrieve an attachment from a note
 * @param String $session -- Session ID returned by a previous call to login.
 * @param String $id -- The ID of the appropriate Note.
 * @return Array 'note_attachment' -- Array String 'id' -- The ID of the Note containing the attachment
 *                                          String 'filename' -- The file name of the attachment
 *                                          Binary 'file' -- The binary contents of the file.
 * 											String 'related_module_id' -- module id to which this note is related
 * 											String 'related_module_name' - module name to which this note is related
 * @exception 'SoapFault' -- The SOAP error, if any
 */
function new_get_note_attachment($session,$id) {
	$error = new SoapError();
	if (!checkSessionAndModuleAccess($session, 'invalid_session', '', '', '', $error)) {
		return;
	} // if
	
	$note = new Note();

	$note->retrieve($id);
    if (!checkACLAccess($note, 'DetailView', $error, 'no_access')) {
    	return;
    } // if
	
	require_once('modules/Notes/NoteSoap.php');
	$ns = new NoteSoap();
	if(!isset($note->filename)){
		$note->filename = '';
	}
	$file= $ns->retrieveFile($id,$note->filename);
	if($file == -1){
		$file = '';
	}

	return array('note_attachment'=>array('id'=>$id, 'filename'=>$note->filename, 'file'=>$file, 'related_module_id' => $note->parent_id, 'related_module_name' => $note->parent_type));

} // fn

$server->register(
        'new_set_document_revision',
        array('session'=>'xsd:string','note'=>'tns:document_revision'),
        array('return'=>'tns:new_set_entry_result'),
        $NAMESPACE);

/**
 * sets a new revision for this document
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param Array $document_revision -- Array String 'id' -- 	The ID of the document object
 * 											String 'document_name' - The name of the document
 * 											String 'revision' - The revision value for this revision
 *                                         	String 'filename' -- The file name of the attachment
 *                                          String 'file' -- The binary contents of the file.
 * @return Array - 'id' - document revision id
 * @exception 'SoapFault' -- The SOAP error, if any
 */
function new_set_document_revision($session, $document_revision) {
	$error = new SoapError();
	if (!checkSessionAndModuleAccess($session, 'invalid_session', '', '', '', $error)) {
		return;
	} // if

	require_once('modules/Documents/DocumentSoap.php');
	$dr = new DocumentSoap();
	return array('id'=>$dr->saveFile($document_revision));
}

$server->register(
        'new_get_document_revision',
        array('session'=>'xsd:string','i'=>'xsd:string'),
        array('return'=>'tns:new_return_document_revision'),
        $NAMESPACE);

/**
 * This method is used as a result of the .htaccess lock down on the cache directory. It will allow a
 * properly authenticated user to download a document that they have proper rights to download.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param String $id      -- ID of the document revision to obtain
 * @return new_return_document_revision - Array String 'id' -- The ID of the document revision containing the attachment
 * 												String document_name - The name of the document
 * 												String revision - The revision value for this revision
 *                                         		String 'filename' -- The file name of the attachment
 *                                          	Binary 'file' -- The binary contents of the file.
 * @exception 'SoapFault' -- The SOAP error, if any
 */
function new_get_document_revision($session, $id) {
    global $sugar_config;

    $error = new SoapError();
	if (!checkSessionAndModuleAccess($session, 'invalid_session', '', '', '', $error)) {
		return;
	} // if

    
    $dr = new DocumentRevision();
    $dr->retrieve($id);
    if(!empty($dr->filename)){
        $filename = $sugar_config['upload_dir']."/".$dr->id;
        $handle = sugar_fopen($filename, "r");
        $contents = fread($handle, filesize($filename));
        fclose($handle);
        $contents = base64_encode($contents);

        $fh = sugar_fopen($sugar_config['upload_dir']."/rogerrsmith.doc", 'w');
        fwrite($fh, base64_decode($contents));
        return array('document_revision'=>array('id' => $dr->id, 'document_name' => $dr->document_name, 'revision' => $dr->revision, 'filename' => $dr->filename, 'file' => $contents));
    }else{
        $error->set_error('no_records');
        setFaultObject($error);
    }

}

$server->register(
        'new_search_by_module',
        array('user_name'=>'xsd:string','password'=>'xsd:string','search_string'=>'xsd:string', 'modules'=>'tns:select_fields', 'offset'=>'xsd:int', 'max_results'=>'xsd:int'),
        array('return'=>'tns:return_search_result'),
        $NAMESPACE);

/**
 * Given a list of modules to search and a search string, return the id, module_name, along with the fields
 * We will support Accounts, Bugs, Calls, Cases, Contacts, Leads, Opportunities, Project, ProjectTask, Quotes
 * 
 * @param string $user_name 		- username of the Sugar User
 * @param string $password			- password of the Sugar User
 * @param string $search_string 	- string to search
 * @param string[] $modules			- array of modules to query
 * @param int $offset				- a specified offset in the query
 * @param int $max_results			- max number of records to return
 * @return Array return_search_result 	- Array('Accounts' => array(array('name' => 'first_name', 'value' => 'John', 'name' => 'last_name', 'value' => 'Do')))
 * @exception 'SoapFault' -- The SOAP error, if any
 */
function new_search_by_module($user_name, $password, $search_string, $modules, $offset, $max_results){
	global  $beanList, $beanFiles;
	global $sugar_config;

	$error = new SoapError();
	$output_list = array();
	if(!validate_user($user_name, $password)){
		$error->set_error('invalid_login');
		setFaultObject($error);
		return;
	}
	global $current_user;
	if($max_results > 0){
		$sugar_config['list_max_entries_per_page'] = $max_results;
	}
	
	require_once('modules/Home/UnifiedSearchAdvanced.php');
	$usa = new UnifiedSearchAdvanced();
    if(!file_exists($GLOBALS['sugar_config']['cache_dir'].'modules/unified_search_modules.php')) {
        $usa->buildCache();
    }
	include($GLOBALS['sugar_config']['cache_dir'].'modules/unified_search_modules.php');
	$modules_to_search = array();
    foreach($unified_search_modules as $module=>$data) {
    	if (in_array($module, $modules)) {
        	$modules_to_search[$module] = $beanList[$module];
    	} // if
    } // foreach
    
	if(!empty($search_string) && isset($search_string)){
    	foreach($modules_to_search as $name => $beanName) {
    		$where_clauses_array = array();
			foreach($unified_search_modules[$name]['fields'] as $field=>$def) {
	            $clause = '';
	            if(isset($def['table']))  {// if field is from joining table
	                $clause = "{$def['table']}.{$def['rname']} ";
	            } else {
	                $clause = "{$unified_search_modules[$name]['table']}.$field ";
	            } // else
	
	            switch($def['type']) {
	                case 'int':
	                    if(is_numeric($_REQUEST['query_string']))  
	                        $clause .=  "in ('{$_REQUEST['query_string']}')";
	                    else
	                        $clause .=  "in ('-1')";
	                    break;
	                default:
	                	//MFH BUG 15405 - added support for seaching full names in global search
	                	if ($field == 'last_name'){
	                		if(strpos($_REQUEST['query_string'], ' ')){
	                			$string = explode(' ', $_REQUEST['query_string']);
	                			$clause .=  "LIKE '{$string[1]}%'";
	                		} else {
	                			$clause .=  "LIKE '{$_REQUEST['query_string']}%'";
	                		}
	                	} else {
	                		$clause .=  "LIKE '{$_REQUEST['query_string']}%'";
	                	}
	                    break;
	            } // switch
	
	            array_push($where_clauses_array, $clause);
			} // foreach
		
			$where = '('.implode(' or ', $where_clauses_array).')';
			
			require_once($beanFiles[$beanName]);
			$seed = new $beanName();
			$mod_strings = return_module_language($current_language, $seed->module_dir);
			if(file_exists('custom/modules/'.$seed->module_dir.'/metadata/listviewdefs.php')){
				require_once('custom/modules/'.$seed->module_dir.'/metadata/listviewdefs.php');	
			}else{
				require_once('modules/'.$seed->module_dir.'/metadata/listviewdefs.php');
			}
            $filterFields = array();
			foreach($listViewDefs[$seed->module_dir] as $colName => $param) {
                if(!empty($param['default']) && $param['default'] == true) {
                    $filterFields[] = strtolower($colName);
                } // if
            } // foreach
			
            if (!in_array('id', $filterFields)) {
            	$filterFields[] = 'id';
            } // if
			$ret_array = $seed->create_new_list_query('', $where, $filterFields, array(), 0, '', true, $seed, true);
	        if(!is_array($params)) $params = array();
	        if(!isset($params['custom_select'])) $params['custom_select'] = '';
	        if(!isset($params['custom_from'])) $params['custom_from'] = '';
	        if(!isset($params['custom_where'])) $params['custom_where'] = '';
	        if(!isset($params['custom_order_by'])) $params['custom_order_by'] = '';
			$main_query = $ret_array['select'] . $params['custom_select'] . $ret_array['from'] . $params['custom_from'] . $ret_array['where'] . $params['custom_where'] . $ret_array['order_by'] . $params['custom_order_by'];
            
			
	   		if($max_results < -1) {
				$result = $seed->db->query($main_query);
			}
			else {
				if($max_results == -1) {
					$limit = $sugar_config['list_max_entries_per_page'];
	            } else {
	            	$limit = $max_results;
	            }
	            $result = $seed->db->limitQuery($main_query, $offset, $limit + 1);
			}
			
			$rowArray = array();
			while($row = $seed->db->fetchByAssoc($result)) {
				$nameValueArray = array();
				foreach ($filterFields as $field) {
					$nameValue = array();
					if (isset($row[$field])) {
						$nameValue['name'] = $field;
						$nameValue['value'] = $row[$field];
						$nameValueArray[] = $nameValue;
					} // if
				} // foreach
				$rowArray[] = $nameValueArray;
			} // while
			$output_list[] = array('name' => $name, 'records' => $rowArray);
    	} // foreach
		
	return array('entry_list'=>$output_list);
	} // if
} // fn

?>
