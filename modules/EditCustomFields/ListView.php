<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
 * Display of ListView for EditCustomFields
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








require_once('modules/EditCustomFields/EditCustomFields.php');

$module_name = empty($_REQUEST['module_name']) ? '' :
	$_REQUEST['module_name'];

$search_form = new XTemplate('modules/EditCustomFields/SearchForm.html');

function get_customizable_modules()
{
	$customizable_modules = array();
	$base_path = 'modules';
	$blocked_modules = array('iFrames', 'Dropdown', 'Feeds');
	$customizable_files = array('EditView.html', 'DetailView.html', 'ListView.html');

	$mod_dir = dir($base_path);

	while(false !== ($mod_dir_entry = $mod_dir->read()))
	{
		if($mod_dir_entry != '.'
			&& $mod_dir_entry != '..'
			&& !in_array($mod_dir_entry, $blocked_modules)
			&& is_dir($base_path . '/' . $mod_dir_entry))
		{
			$mod_sub_dir = dir($base_path . '/' . $mod_dir_entry);
			$add_to_array = false;

			while(false !== ($mod_sub_dir_entry = $mod_sub_dir->read()))
			{
				if(in_array($mod_sub_dir_entry, $customizable_files))
				{
					$add_to_array = true;
					break;
				}
			}

			if($add_to_array)
			{
				$customizable_modules[$mod_dir_entry] = $mod_dir_entry;
			}
		}
	}

	ksort($customizable_modules);
	return $customizable_modules;
}

$customizable_modules = get_customizable_modules();
$module_options_html = get_select_options_with_id($customizable_modules,
	$module_name);

global $current_language;
$mod_strings = return_module_language($current_language,
	'EditCustomFields');
global $app_strings;

// the title label and arrow pointing to the module search form
$header = get_form_header($mod_strings['LBL_SEARCH_FORM_TITLE'], '', false);
$search_form->assign('header', $header);
$search_form->assign('module_options', $module_options_html);
$search_form->assign('mod', $mod_strings);
$search_form->assign('app', $app_strings);

$search_form->parse('main');
$search_form->out('main');

if(!empty($module_name))
{
	require_once('modules/DynamicFields/DynamicField.php');
	$seed_fields_meta_data = new FieldsMetaData();
	$where_clause = "custom_module='$module_name'";
	$listview = new ListView();
	$listview->initNewXTemplate('modules/EditCustomFields/ListView.html', $mod_strings);
	$listview->setHeaderTitle($module_name . ' ' . $mod_strings['LBL_MODULE']);
	$listview->setQuery($where_clause, '', 'data_type', 'FIELDS_META_DATA');
	$listview->xTemplateAssign('DELETE_INLINE_PNG',
		SugarThemeRegistry::current()->getImage("delete_inline", 'align="absmiddle" alt="'
		. $app_strings['LNK_DELETE'] . '" border="0"'));
	$listview->xTemplateAssign('EDIT_INLINE_PNG',
		SugarThemeRegistry::current()->getImage("edit_inline", 'align="absmiddle" alt="'
		. $app_strings['LNK_EDIT'] . '" border="0"'));
	$listview->xTemplateAssign('return_module_name', $module_name);
	$listview->processListView($seed_fields_meta_data,  'main', 'FIELDS_META_DATA');
}

?>
