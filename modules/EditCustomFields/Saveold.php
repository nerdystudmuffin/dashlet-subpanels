<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
 * Save functionality for EditCustomFields
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




require_once('modules/EditCustomFields/CustomFieldsTableSchema.php');

$fields_meta_data = new FieldsMetaData();

////
//// save the metadata to the fields_meta_data table
////

foreach($fields_meta_data->column_fields as $field)
{
	if(isset($_REQUEST[$field]))
	{
		$fields_meta_data->$field = $_REQUEST[$field];
	}
}

$fields_meta_data->save();

////
//// create/modify the custom field table
////

$new_field = empty($_REQUEST['id']);
$new_field = true;

$custom_table_name = strtolower($fields_meta_data->custom_module) . '_cstm';
$custom_fields_table_schema = new
	CustomFieldsTableSchema($custom_table_name);
if(!CustomFieldsTableSchema::custom_table_exists($custom_table_name))
{
	$custom_fields_table_schema->create_table();
}

$column_name = $fields_meta_data->name;
$field_label = $fields_meta_data->label;
$data_type = $fields_meta_data->data_type;
$max_size = $fields_meta_data->max_size;
$required = $fields_meta_data->required_option;
$default_value = $fields_meta_data->default_value;

$module_dir = $fields_meta_data->custom_module;

if($new_field)
{
	$custom_fields_table_schema->add_column($column_name, $data_type,
		$required, $default_value);

	$class_name = $beanList[$fields_meta_data->custom_module];
	$custom_field = new DynamicField($fields_meta_data->custom_module);
	require_once("modules/$module_dir/$class_name.php");
	$sugarbean_module = new $class_name();
	$custom_field->setup($sugarbean_module);

	$custom_field->addField($field_label, $data_type, $max_size, 'optional',
		$default_value, '', '');
}






if(isset($_REQUEST['form']))
{
	// we are doing the save from a popup window
	echo '<script>opener.window.location.reload();self.close();</script>';
	die();
}
else
{
	// need to refresh the page properly

	$return_module = empty($_REQUEST['return_module']) ? 'EditCustomFields'
		: $_REQUEST['return_module'];

	$return_action = empty($_REQUEST['return_action']) ? 'index'
		: $_REQUEST['return_action'];

	$return_module_select = empty($_REQUEST['return_module_select']) ? 0
		: $_REQUEST['return_module_select'];

	header("Location: index.php?action=$return_action&module=$return_module&module_select=$return_module_select");

}
?>
