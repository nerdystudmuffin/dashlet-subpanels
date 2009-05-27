<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
 * The popup window for displaying the details of a custom field
 *
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
 */



global $theme;




require_once('modules/EditCustomFields/EditCustomFields.php');

global $app_strings;
global $mod_strings;
global $currentModule;
global $app_list_strings;

//mysql max is 64, allow for few additional chars added by sugar.
//oracle max is 30, 
$name_max_length=60;   
if ($GLOBALS['db']->dbType=='oci8') {
	$name_max_length=26;
}


///////////////////////////////////////
// Populate the template
///////////////////////////////////////
$style = 'embeded';
if(isset($_REQUEST['style'])){
	$style = $_REQUEST['style'];	
}
$xtpl = new XTemplate ('modules/EditCustomFields/EditView.html');
$xtpl->assign('MOD', $mod_strings);
$xtpl->assign('APP', $app_strings);

$focus = new FieldsMetaData();
$data_type_array = array('varchar' => 'Text', 'text' => 'Text Area','int' => 'Integer', 'float' => 'Decimal',
	 'bool' => 'Checkbox', 'date' => 'Date', 'enum' => 'Dropdown');
$enum_keys = array();
foreach($app_list_strings as $key => $value)
{
  	if(is_array($value)){
  		$enum_keys[$key] = $key;	
  	}
  		
}
if(!empty($_REQUEST['file_type'])){
	$xtpl->assign('FILE_TYPE', $_REQUEST['file_type']);
}
if(!empty($_REQUEST['field_count'])){
	$xtpl->assign('FIELD_COUNT', $_REQUEST['field_count']);
}

$return_module = 'EditCustomFields';
$return_action = 'index';
if(isset($_REQUEST['module_name'])){
	$return_module = $_REQUEST['module_name'];	
}
if(isset($_REQUEST['module_action'])){
	$return_action = $_REQUEST['module_action'];	
}
$xtpl->assign('RETURN_MODULE', $return_module);
$xtpl->assign('RETURN_ACTION', $return_action);
$xtpl->assign('STYLE', $style);
if(empty($_REQUEST['record']))
{
	$xtpl->assign('form', 'insert');
	$header = get_form_header($mod_strings['POPUP_INSERT_HEADER_TITLE'], '', false);
	$xtpl->assign('header', $header);
	$xtpl->assign('custom_module', $_REQUEST['module_name']);
	
   $data_type_options_html = get_select_options_with_id($data_type_array,
		'');
	$xtpl->assign('data_type_options', $data_type_options_html);
	$xtpl ->assign('ENUM_OPTIONS', get_select_options_with_id($enum_keys, ''));
}
else
{
	$xtpl->assign('form', 'edit');
	$header = get_form_header($mod_strings['POPUP_EDIT_HEADER_TITLE'], '', false);
	$xtpl->assign('header', $header);
	// populate the fields if a custom_field_id is given -> editing
	$record_id = $_REQUEST['record'];

	$focus->retrieve($record_id);
	if(!empty($_REQUEST['duplicate'])){
		$record_id = '';
		$focus->id = '';	
	}
	$xtpl->assign('NOEDIT', 'disabled');
	$xtpl->assign('custom_field_id', $focus->id);
	$xtpl->assign('name', $focus->name);
	$xtpl->assign('label', $focus->label);
	$xtpl->assign('custom_module', $focus->custom_module);
	
   $data_type_options_html = get_select_options_with_id($data_type_array,
		$focus->data_type);
	
	$xtpl->assign('data_type_options', $data_type_options_html);
	$xtpl->assign('max_size', $focus->max_size);
	$xtpl->assign('required_option', $focus->required_option);
	if($focus->required_option == 'required'){
		$xtpl->assign('REQUIRED_CHECKED', 'checked');
	}
	$xtpl->assign('default_value', $focus->default_value);
	
	$xtpl ->assign('ENUM_OPTIONS', get_select_options_with_id($enum_keys, $focus->ext1));
	$xtpl->assign('ext1', $focus->ext1);
	$xtpl->assign('ext2', $focus->ext2);
	$xtpl->assign('ext3', $focus->ext3);

	if ($focus->audited)
		$xtpl->assign('AUDIT_CHECKED', 'checked');
	if ($focus->mass_update)
		$xtpl->assign('MASS_UPDATE_CHECKED', 'checked');

    $xtpl ->assign('duplicate_merge_options', get_select_options_with_id($app_list_strings['custom_fields_merge_dup_dom'], $focus->duplicate_merge));
        
}

$xtpl->assign("NAMEMAXLENGTH",$name_max_length);
$xtpl->assign('module', $currentModule);
$action = basename(__FILE__, '.php');
$xtpl->assign('action', $action);

///////////////////////////////////////
// Start the output
///////////////////////////////////////
if($style == 'popup'){
	insert_popup_header($theme);
	$xtpl->parse("popup");
	$xtpl->out("popup");
	$xtpl->parse("body.topsave");
	$xtpl->parse("body.cancel");
}else{
	$xtpl->parse("embeded");
	$xtpl->out("embeded");
	if(!empty($record_id)){

		$xtpl->parse("body.cancel");
		$xtpl->parse("body.topsave");
	}else{
		$xtpl->parse("body.botsave");	
	}
}
$xtpl->parse("body");
$xtpl->out("body");

// Reset the sections that are already in the page so that they do not print again later.
$xtpl->reset("main");

if($style == 'popup'){
	insert_popup_footer();
}

$javascript = new javascript();
$javascript->setFormName('popup_form');
$javascript->setSugarBean($focus);
$javascript->addAllFields('');
echo $javascript->getScript();
?>
