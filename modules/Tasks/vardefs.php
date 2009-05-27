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
$dictionary['Task'] = array('table' => 'tasks'
                               ,'fields' => array (
  'name' =>
  array (
    'name' => 'name',
    'vname' => 'LBL_SUBJECT',
    'dbType' => 'varchar',
    'type' => 'name',
    'len' => '50',
    'importable' => 'required',
  ),
  'status' =>
  array (
    'name' => 'status',
    'vname' => 'LBL_STATUS',
    'type' => 'enum',
    'options' => 'task_status_dom',
    'len' => 25,
  ),
  'date_due_flag' =>
  array (
    'name' => 'date_due_flag',
    'vname' => 'LBL_DATE_DUE_FLAG',
    'type' =>'bool',
    'default'=>1,
    'group'=>'date_due',
  ),
  'date_due' =>
  array (
    'name' => 'date_due',
    'vname' => 'LBL_DUE_DATE',
    'type' => 'datetime',
    'group'=>'date_due',
    ),
  'time_due' =>
  array (
    'name' => 'time_due',
    'vname' => 'LBL_DUE_TIME',
    'type' => 'datetime',
    //'db_concat_fields'=> array(0=>'date_due'),
    'source' => 'non-db',
    'importable' => 'false',
    'massupdate' => false,
    ),

'date_due_field' => array(
    'name' => 'date_due_field',
    'group'=>'date_due',
    'vname' => 'LBL_DUE_DATE_AND_TIME',
    'type' => 'datetimecombo',
    'date' => 'date_due',
    'time' => 'time_due',
    'date_readonly' => 'date_due_readonly',
    'time_readonly' => 'time_due_readonly',
    'noneCheckbox' => true,
    'noneCheckboxJavascript' => 'onClick="set_date_due_values(this.form);"',
    'checkboxId' => 'date_due_flag',
    'checked' => 'date_due_checked',
    'meridian' => 'date_due_meridian',
    'showFormats' => true,
    'source' => 'non-db',
    'comment' => 'Used for meta-data framework',
    'importable' => 'false',
),


  'date_start_flag' =>
  array (
    'name' => 'date_start_flag',
    'vname' => 'LBL_DATE_START_FLAG',
    'type' =>'bool',
    'group'=>'date_start',
    'default'=>1,
  ),
  'date_start' =>
  array (
    'name' => 'date_start',
    'vname' => 'LBL_START_DATE',
    'type' => 'datetime',
     'group'=>'date_start',
    ),

'date_start_field' => array(
	 'group'=>'date_start',
    'name' => 'date_start_field',
    'vname' => 'LBL_DUE_DATE_AND_TIME',
    'type' => 'datetimecombo',
    'date' => 'date_start',
    'time' => 'time_start',
    'date_readonly' => 'date_start_readonly',
    'time_readonly' => 'time_start_readonly',
    'noneCheckbox' => true,
    'noneCheckboxJavascript' => 'onClick="set_date_start_values(this.form);"',
    'checkboxId' => 'date_start_flag',
    'checked' => 'date_start_checked',
    'meridian' => 'date_start_meridian',
    'showFormats' => true,
    'source' => 'non-db',
    'comment' => 'Used for meta-data framework',
),



 'parent_type'=>
  array(
  	'name'=>'parent_type',
  	'vname'=>'LBL_PARENT_NAME',
    'type' => 'parent_type',
    'dbType'=>'varchar',
  	 'group'=>'parent_name',
  	'required'=>false,
    'reportable'=>false,
  	'len'=>'25',
    'comment' => 'The Sugar object to which the call is related'
  ),

  'parent_name'=>
  array(
	'name'=> 'parent_name',
	'parent_type'=>'record_type_display' ,
	'type_name'=>'parent_type',
	'id_name'=>'parent_id',
    'vname'=>'LBL_LIST_RELATED_TO',
	'type'=>'parent',
	'group'=>'parent_name',
	'source'=>'non-db',
	'options'=> 'parent_type_display',
  ),

  'parent_id' =>
  array (
    'name' => 'parent_id',
    'type' => 'id',
    'group'=>'parent_name',
    'reportable'=>false,
    'vname'=>'LBL_PARENT_ID',
  ),
  'contact_id' =>
  array (
    'name' => 'contact_id',
    'type' => 'id',
    'group'=>'contact_name',
    'reportable'=>false,
    'vname'=>'LBL_CONTACT_ID',
  ),

  'contact_name' =>
  array (
    'name' => 'contact_name',
    'rname'=>'last_name',
    'db_concat_fields'=> array(0=>'first_name', 1=>'last_name'),
    'source' => 'non-db',
    'len' => '510',
    'group'=>'contact_name',
    'vname' => 'LBL_CONTACT_NAME',
    'reportable'=>false,
    'id_name' => 'contact_id',
    'join_name' => 'contacts',
    'type' => 'relate',
    'module' => 'Contacts',
    'link'=>'contacts',
    'table'=>'contacts',
  ),

  'contact_phone'=>
    array(
        'name'=>'contact_phone',
        'type'=>'phone',
        'source'=>'non-db',
        'vname'=>'LBL_CONTACT_PHONE',
    ),

 'contact_email'=>
    array(
        'name'=>'contact_email',
        'type'=>'varchar',
		'vname' => 'LBL_EMAIL_ADDRESS',
		'source' => 'non-db',
    ),

  'priority' =>
  array (
    'name' => 'priority',
    'vname' => 'LBL_PRIORITY',
    'type' => 'enum',
    'options' => 'task_priority_dom',
    'len'=>25,
  ),
	'contacts'=>	array(
		'name' => 'contacts',
		'type' => 'link',
		'relationship' => 'contact_tasks',
		'source'=>'non-db',
		'side'=>'right',
		'vname'=>'LBL_CONTACT',
	),
  'accounts' =>
  array (
  	'name' => 'accounts',
    'type' => 'link',
    'relationship' => 'account_tasks',
    'source'=>'non-db',
		'vname'=>'LBL_ACCOUNT',
  ),
  'opportunities' =>
  array (
    'name' => 'opportunities',
    'type' => 'link',
    'relationship' => 'opportunity_tasks',
    'source'=>'non-db',
    'vname'=>'LBL_TASKS',
  ),
  'cases' =>
  array (
    'name' => 'cases',
    'type' => 'link',
    'relationship' => 'case_tasks',
    'source'=>'non-db',
    'vname'=>'LBL_CASE',
  ),
  'bugs' =>
  array (
    'name' => 'bugs',
    'type' => 'link',
    'relationship' => 'bug_tasks',
    'source'=>'non-db',
    'vname'=>'LBL_BUGS',
  ),
  'leads' =>
  array (
    'name' => 'leads',
    'type' => 'link',
    'relationship' => 'lead_tasks',
    'source'=>'non-db',
    'vname'=>'LBL_LEADS',
  ),
  'projects' =>
    array (
    'name' => 'projects',
    'type' => 'link',
    'relationship' => 'projects_tasks',
    'source'=>'non-db',
    'vname'=>'LBL_PROJECTS',
  ),
  'project_tasks' =>
    array (
    'name' => 'project_tasks',
    'type' => 'link',
    'relationship' => 'project_tasks_tasks',
    'source'=>'non-db',
    'vname'=>'LBL_PROJECT_TASKS',
  ),










)
,
 'relationships' => array (

  'tasks_assigned_user' =>
   array('lhs_module'=> 'Users', 'lhs_table'=> 'users', 'lhs_key' => 'id',
   'rhs_module'=> 'Tasks', 'rhs_table'=> 'tasks', 'rhs_key' => 'assigned_user_id',
   'relationship_type'=>'one-to-many')

   ,'tasks_modified_user' =>
   array('lhs_module'=> 'Users', 'lhs_table'=> 'users', 'lhs_key' => 'id',
   'rhs_module'=> 'Tasks', 'rhs_table'=> 'tasks', 'rhs_key' => 'modified_user_id',
   'relationship_type'=>'one-to-many')

   ,'tasks_created_by' =>
   array('lhs_module'=> 'Users', 'lhs_table'=> 'users', 'lhs_key' => 'id',
   'rhs_module'=> 'Tasks', 'rhs_table'=> 'tasks', 'rhs_key' => 'created_by',
   'relationship_type'=>'one-to-many')
)
                                                      , 'indices' => array (
       array('name' =>'idx_tsk_name', 'type'=>'index', 'fields'=>array('name')),
       array('name' =>'idx_task_con_del', 'type'=>'index', 'fields'=>array('contact_id','deleted')),
       array('name' =>'idx_task_par_del', 'type'=>'index', 'fields'=>array('parent_id','parent_type','deleted')),
       array('name' =>'idx_task_assigned', 'type'=>'index', 'fields'=>array('assigned_user_id')),
             )

        //This enables optimistic locking for Saves From EditView
	,'optimistic_locking'=>true,
                            );
VardefManager::createVardef('Tasks','Task', array('default', 'assignable',



));
?>
