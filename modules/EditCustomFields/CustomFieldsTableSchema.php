<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
 * Database manipulation for custom field tables
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






class CustomFieldsTableSchema
{
	var $db;
	var $table_name;

	function CustomFieldsTableSchema($tbl_name = '')
	{
		global $db;
		$this->db = $db;
		$this->table_name = $tbl_name;
	}

	function _get_column_definition($col_name, $type, $required, $default_value)
	{
		$ret_val = "$col_name $type";
		if($required)
		{
			$ret_val .= ' NOT NULL';
		}

		if(!empty($default_value))
		{
			$ret_val .= " DEFAULT '$default_value'";
		}

		return $ret_val;
	}

	function create_table()
	{
		$column_definition = $this->_get_column_definition('id', 'varchar(100)',
			true, '');
		$query = "CREATE TABLE {$this->table_name} ($column_definition);";

		$result = $this->db->query($query, true,
			'CustomFieldsTableSchema::create_table');

		return $result;
	}

	function add_column($column_name, $data_type, $required, $default_value)
	{
		$column_definition = $this->_get_column_definition($column_name,
			$data_type,
			$required, $default_value);

		$query = "ALTER TABLE {$this->table_name} "
			. "ADD COLUMN $column_definition;";

		$result = $this->db->query($query, true,
			'CustomFieldsTableSchema::add_column');

		return $result;
	}

	function modify_column($column_name, $data_type, $required, $default_value)
	{
		$column_definition = $this->_get_column_definition($column_name,
			$data_type, $required, $default_value);

		$query = "ALTER TABLE {$this->table_name} "
			. "MODIFY COLUMN $column_definition;";

		$result = $this->db->query($query, true,
			'CustomFieldsTableSchema::modify_column');

		return $result;
	}

	function drop_column($column_name)
	{
		$query = "ALTER TABLE $this->table_name "
			. "DROP COLUMN $column_name;";

		$result = $this->db->query($query, true,
			'CustomFieldsTableSchema::drop_column');

		return $result;
	}

	function _get_custom_tables()
	{
		$pattern = '%' . CUSTOMFIELDSTABLE_CUSTOM_TABLE_SUFFIX;
		
        if ($this->db){
            if ($this->db->dbType == 'mysql'){
                $result = $this->db->query("SHOW TABLES LIKE '".$pattern."'");
                $rows=$this->db->fetchByAssoc($result);
                return $rows;                
            }else if ($this->dbType == 'oci8') {






            }
        }
        return false;
	}

	/**
	 * @static
	 */
	function custom_table_exists($tbl_name)
	{
		$db = DBManagerFactory::getInstance();
		return 	$db->tableExists($tbl_name);		
	}
}

?>
