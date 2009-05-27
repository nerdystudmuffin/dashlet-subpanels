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
require_once('modules/DynamicFields/DynamicField.php');

//this was added to address problems in oracle when creating a custom field with
//upper case characters in column name.
if (!empty($_REQUEST['name'])) {
	$_REQUEST['name']=strtolower($_REQUEST['name']);
}

$module = $_REQUEST['module_name'];
$custom_fields = new DynamicField($module);
if(!empty($module)){
			if(!isset($beanList[$module])){
				if(isset($beanList[ucfirst($module)]))
				$module = ucfirst($module);
			}
			$class_name = $beanList[$module];
			require_once($beanFiles[$class_name]);
			$mod = new $class_name();
			$custom_fields->setup($mod);
}else{
	echo "\nNo Module Included Could Not Save";	
}
$label = '';
if(isset($_REQUEST['label']))$label = $_REQUEST['label'];
$ext1 = '';
if(isset($_REQUEST['ext1'])){		
	$ext1 = $_REQUEST['ext1'];
}
$ext2 = '';
if(isset($_REQUEST['ext2'])){		
	$ext2 = $_REQUEST['ext2'];
}
$ext3 = '';
if(isset($_REQUEST['ext3'])){		
	$ext3 = $_REQUEST['ext3'];
}
$max_size = '255';
if(isset($_REQUEST['max_size'])){		
	$max_size = $_REQUEST['max_size'];
}
$required_opt = 'optional';
if(isset($_REQUEST['required_option'])){
	$required_opt = 'required';
}
$default_value = '';
if(isset($_REQUEST['default_value'])){
	$default_value = $_REQUEST['default_value'];
}

$reportable = true;
if(isset($_REQUEST['reportable'])) {
   $reportable = $_REQUEST['reportable'];	
}

$audit_value=0;

if(isset($_REQUEST['audited'])){
	$audit_value = 1;
 
}
$mass_update = 0;
if(isset($_REQUEST['mass_update'])){
	$mass_update = 1;

}
$id = '';
if(isset($_REQUEST['id']))$id = $_REQUEST['id'];
if(empty($id)){
	
	$custom_fields->addField($_REQUEST['name'],$label, $_REQUEST['data_type'],$max_size,$required_opt, $default_value, $ext1, $ext2, $ext3,$audit_value, $mass_update ,$_REQUEST['duplicate_merge']);
}else{
	$custom_fields->updateField($id, array('max_size'=>$max_size,'required_option'=>$required_opt, 'default_value'=>$default_value, 'audited'=>$audit_value, 'mass_update'=>$mass_update,'duplicate_merge'=>$_REQUEST['duplicate_merge'])); 
}
if($_REQUEST['style'] == 'popup'){
	$name = $_REQUEST['name'];
	$html = $custom_fields->getFieldHTML($name, $_REQUEST['file_type']);

	set_register_value('dyn_layout', 'field_counter', $_REQUEST['field_count']);
	$label = $custom_fields->getFieldLabelHTML($name, $_REQUEST['data_type']);
	require_once('modules/DynamicLayout/AddField.php');
	$af = new AddField();
	$af->add_field($name, $html,$label, 'window.opener.');
	echo $af->get_script('window.opener.');
	echo "\n<script>window.close();</script>";
}else{
	header("Location: index.php?action=index&module=EditCustomFields&module_name=" . $_REQUEST['module_name']);
}

?>
