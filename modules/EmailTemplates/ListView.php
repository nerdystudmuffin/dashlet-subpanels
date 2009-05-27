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






require_once('modules/MySettings/StoreQuery.php');

global $list_max_entries_per_page;
global $urlPrefix;
global $currentModule;

global $focus_list; // focus_list is the means of passing data to a ListView.
global $title;

$header_text = '';
$seedEmailTemplate = new EmailTemplate();
$storeQuery = new StoreQuery();
$list_form = new XTemplate ('modules/EmailTemplates/ListView.html');

if(empty($_POST['mass']) && empty($where) && empty($_REQUEST['query'])) {
	$_REQUEST['query']='true'; $_REQUEST['current_user_only']='checked'; 
}

if(!isset($_REQUEST['query'])) {
	$storeQuery->loadQuery($currentModule);
	$storeQuery->populateRequest();
} else {
	$storeQuery->saveFromGet($currentModule);	
}

if(!isset($_REQUEST['search_form']) || $_REQUEST['search_form'] != 'false') {
	// Stick the form header out there.
	$search_form=new XTemplate ('modules/EmailTemplates/SearchForm.html');
	$search_form->assign("MOD", $mod_strings);
	$search_form->assign("APP", $app_strings);

	if(isset($_REQUEST['query'])) {
		if(isset($_REQUEST['name'])) $search_form->assign("NAME", $_REQUEST['name']);
		if(isset($_REQUEST['description'])) $search_form->assign("DESCRIPTION", $_REQUEST['description']);
	}
	// adding custom fields:
	$seedEmailTemplate->custom_fields->populateXTPL($search_form, 'search' );
	$search_form->parse("main");
	echo "\n<p>\n";

	if(is_admin($current_user) && $_REQUEST['module'] != 'DynamicLayout' && !empty($_SESSION['editinplace'])) {	
		$header_text = "&nbsp;<a href='index.php?action=index&module=DynamicLayout&from_action=SearchForm&from_module=".$_REQUEST['module'] ."'>".SugarThemeRegistry::current()->getImage("EditLayout","border='0' alt='Edit Layout' align='bottom'")."</a>";
	}

	echo get_form_header($mod_strings['LBL_SEARCH_FORM_TITLE']. $header_text, "", false);
	$search_form->out("main");
	echo "\n</p>\n";
}

$list_form->assign("MOD", $mod_strings);
$list_form->assign("APP", $app_strings);
$list_form->assign("MODULE_NAME", $currentModule);

$where = "";

if(isset($_REQUEST['query'])) {
	// we have a query
	$name = '';
	$desc = '';
	if(isset($_REQUEST['name'])) { 
		$name = $_REQUEST['name'];
	}
	if(isset($_REQUEST['description'])) {
		$desc = $_REQUEST['description'];
	}

	$where_clauses = array();

	if(!empty($name)) {
		array_push($where_clauses, "email_templates.name like '%".$GLOBALS['db']->quote($name)."%'");
	}
	if(!empty($desc)) {
		array_push($where_clauses, "email_templates.description like '%".$GLOBALS['db']->quote($desc)."%'");
	}
	
	$seedEmailTemplate->custom_fields->setWhereClauses($where_clauses);

	$where = "";
	





	if(isset($where_clauses)) {
		foreach($where_clauses as $clause) {
			if($where != "")
			$where .= " and ";
			$where .= $clause;
		}
	}
	$GLOBALS['log']->info("Here is the where clause for the list view: $where");
}


$display_title = $mod_strings['LBL_LIST_FORM_TITLE'];

if($title) {
	$display_title = $title;
}

$ListView = new ListView();

if(is_admin($current_user) && $_REQUEST['module'] != 'DynamicLayout' && !empty($_SESSION['editinplace'])) {	
	$header_text = "&nbsp;<a href='index.php?action=index&module=DynamicLayout&from_action=ListView&from_module=".$_REQUEST['module'] ."'>".SugarThemeRegistry::current()->getImage("EditLayout","border='0' alt='Edit Layout' align='bottom'")."</a>";
}
$ListView->initNewXTemplate( 'modules/EmailTemplates/ListView.html',$mod_strings);
$ListView->setHeaderTitle($display_title . $header_text);
$ListView->setQuery($where, "", "email_templates.date_entered DESC", "EMAIL_TEMPLATE");
$ListView->processListView($seedEmailTemplate, "main", "EMAIL_TEMPLATE");
?>
