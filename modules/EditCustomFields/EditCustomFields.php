<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
 * Middle layer access for custom fields
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



require_once('CustomFieldsTable.php');
require_once('CustomFieldsTableSchema.php');
require_once('FieldsMetaData.php');



define('CUSTOMFIELDSTABLE_CUSTOM_TABLE_SUFFIX', '_cstm');

class EditCustomFields
{
	var $module_name;

	function EditCustomFields($module_name)
	{
		$this->module_name = $module_name;
	}

	function _get_custom_tbl_name()
	{
		return strtolower($this->module_name)
			. CUSTOMFIELDSTABLE_CUSTOM_TABLE_SUFFIX;
	}

	function module_custom_fields()
	{
		global $moduleList;
		$ret_val = array();
		$module_name = $this->module_name;
		if(in_array($module_name, $moduleList))
		{
			$fields_meta_data = new FieldsMetaData();
			$ret_val = $fields_meta_data->select_by_module($module_name);
		}

		return $ret_val;
	}

	function add_custom_field($name, $label, $data_type, $max_size,
		$required_option, $default_value, $deleted, $ext1, $ext2, $ext3, $audited, $mass_update=0, $duplicate_merge=0, $reportable = true)
	{
		$module_name = $this->module_name;

		$fields_meta_data = new FieldsMetaData();
		$fields_meta_data->name = $name;
		$fields_meta_data->label = $label;
		$fields_meta_data->module = $module_name;
		$fields_meta_data->data_type = $data_type;
		$fields_meta_data->max_size = $max_size;
		$fields_meta_data->required_option = $required_option;
		$fields_meta_data->default_value = $default_value;
		$fields_meta_data->deleted = $deleted;
		$fields_meta_data->ext1 = $ext1;
		$fields_meta_data->ext2 = $ext2;
		$fields_meta_data->ext3 = $ext3;
		$fields_meta_data->audited = $audited;
        $fields_meta_data->duplicate_merge = $duplicate_merge;
		$fields_meta_data->mass_update = $mass_update;
		$fields_meta_date->reportable = $reportable;		
		$fields_meta_data->insert();

		$custom_table_name = $this->_get_custom_tbl_name();
		$custom_table_exists =
			CustomFieldsTableSchema::custom_table_exists($custom_table_name);

		$custom_fields_table_schema =
			new CustomFieldsTableSchema($custom_table_name);

		if(!$custom_table_exists)
		{
			$custom_fields_table_schema->create_table();
		}

		$result = $custom_fields_table_schema->add_column($name, $data_type,
			$required_option, $default_value);

		return $result;
	}

	function get_custom_field($id, &$name, &$label, &$data_type, &$max_size,
      &$required_option, &$default_value, &$deleted, &$ext1, &$ext2, &$ext3, &$audited,&$duplicate_merge,&$reportable)
	{
		$fields_meta_data = new FieldsMetaData($id);
		$name = $fields_meta_data->name;
		$label = $fields_meta_data->label;
		$data_type = $fields_meta_data->data_type;
		$max_size = $fields_meta_data->max_size;
		$required_option = $fields_meta_data->required_option;
		$default_value = $fields_meta_data->default_value;
		$deleted = $fields_meta_data->deleted;
		$ext1 = $fields_meta_data->ext1;
		$ext2 = $fields_meta_data->ext2;
		$ext3 = $fields_meta_data->ext3;
		$audited = $fields_meta_data->audited;		
        $duplicate_merge=$fields_meta_data->duplicate_merge;
        $reportable = $fields_meta_data->reportable;
	}

	function edit_custom_field($id, $name, $label, $data_type, $max_size,
		$required_option, $default_value, $deleted, $ext1, $ext2, $ext3, $audited,$duplicate_merge,$reportable)
	{
		$module_name = $this->module_name;

		// update the meta data
		$fields_meta_data = new FieldsMetaData();
		$fields_meta_data->id = $id;
		$fields_meta_data->name = $name;
		$fields_meta_data->label = $label;
		$fields_meta_data->module = $module_name;
		$fields_meta_data->data_type = $data_type;
		$fields_meta_data->max_size = $max_size;
		$fields_meta_data->required_option = $required_option;
		$fields_meta_data->default_value = $default_value;
		$fields_meta_data->deleted = $deleted;
		$fields_meta_data->ext1 = $ext1;
		$fields_meta_data->ext2 = $ext2;
		$fields_meta_data->ext3 = $ext3;
		$fields_meta_data->audited=$audited;
        $fields_meta_data->duplicate_merge=$duplicate_merge;   
        $fields_meta_data->reportable = $reportable;     
		$fields_meta_data->update();

		// update the schema of the custom table
		$custom_table_name = $this->_get_custom_tbl_name();
		$custom_fields_table_schema =
			new CustomFieldsTableSchema($custom_table_name);

		$custom_fields_table_schema->modify_column($name, $data_type,
			$required_option, $default_value);
	}

	function delete_custom_field($id)
	{
		$module_name = $this->module_name;

		$fields_meta_data = new FieldsMetaData($id);
		$column_name = $fields_meta_data->name;
		$fields_meta_data->delete();

		$custom_table_name = $this->_get_custom_tbl_name();
		$custom_fields_table_schema =
			new CustomFieldsTableSchema($custom_table_name);

		$custom_fields_table_schema->drop_column($column_name);
	}
}

?>
