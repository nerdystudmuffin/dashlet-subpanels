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
 * *******************************************************************************/
/*********************************************************************************

 * Description:
 * Portions created by SugarCRM are Copyright(C) SugarCRM, Inc. All Rights
 * Reserved. Contributor(s): ______________________________________..
 *********************************************************************************/







global $app_strings;
global $app_list_strings;
global $current_language;
$current_module_strings = return_module_language($current_language, 'Feeds');
global $urlPrefix;
global $currentModule;
global $theme;

echo get_module_title($mod_strings['LBL_MODULE_ID'],$mod_strings['LNK_FEED_LIST'], true); 

if (!isset($where)) $where = "";

$seedFeed = new Feed();
require_once('modules/MySettings/StoreQuery.php');
$storeQuery = new StoreQuery();
if(!isset($_REQUEST['query'])){
	$storeQuery->loadQuery($currentModule);
	$storeQuery->populateRequest();
}else{
	$storeQuery->saveFromGet($currentModule);	
}
if(isset($_REQUEST['current_user_only']) && $_REQUEST['current_user_only'] != "")
{
		$seedFeed->my_favorites = true;
}

	// we have a query
	if(isset($_REQUEST['title'])) {
		$test = clean_xss($_REQUEST['title']);
		if(!empty($test))
			die("XSS attack detected in title.");
		else 
			$title = $_REQUEST['title'];
	}


	$where_clauses = Array();


	if(isset($_REQUEST['title']) && $_REQUEST['title'] != "") 
        $where_clauses[] = "feeds.title like '%".$GLOBALS['db']->quote($_REQUEST['title'])."%'";

	if(isset($_REQUEST['current_user_only']) && $_REQUEST['current_user_only'] != "") 
        $where_clauses[] = " users_feeds.user_id='{$current_user->id}' ";



	$where = "";
	foreach($where_clauses as $clause)
	{
		if($where != "")
		$where .= " and ";
		$where .= $clause;
	}

	$GLOBALS['log']->info("Here is the where clause for the list view: $where");


if (!isset($_REQUEST['search_form']) || $_REQUEST['search_form'] != 'false') {
echo get_form_header($current_module_strings['LBL_SEARCH_FORM_TITLE'], '', false);
	// Stick the form header out there.
	$search_form=new XTemplate ('modules/Feeds/SearchForm.html');
	$search_form->assign("MOD", $current_module_strings);
	$search_form->assign("APP", $app_strings);
	$search_form->assign("JAVASCRIPT", get_clear_form_js());

	if(isset($_REQUEST['title']) && $_REQUEST['title'] != "")
	{
		$search_form->assign("TITLE", $_REQUEST['title']);
	}

	if(isset($_REQUEST['current_user_only']) && $_REQUEST['current_user_only'] != "")
	{
		$search_form->assign("CURRENT_USER_ONLY", "CHECKED");
	}


		$search_form->parse("main");
		$search_form->out("main");
	echo "\n<BR>\n";
}


$ListView = new ListView();

$ListView->initNewXTemplate( 'modules/Feeds/ListView.html',$current_module_strings);
if(isset($_REQUEST['current_user_only']) && $_REQUEST['current_user_only'] != "")
{
$ListView->setHeaderTitle($current_module_strings['LBL_MY_LIST_FORM_TITLE'] );
}
else
{
$ListView->setHeaderTitle($current_module_strings['LBL_LIST_FORM_TITLE'] );
}
$ListView->setQuery($where, "", "title", "FEED");
$ListView->processListView($seedFeed, "main", "FEED");
?>
