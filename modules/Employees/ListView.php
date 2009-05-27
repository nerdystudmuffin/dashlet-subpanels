<?php
if(!defined('sugarEntry') || !sugarEntry)
	die('Not A Valid Entry Point');
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
 * Portions created by SugarCRM are Copyright(C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/



require_once('include/ListView/ListViewSmarty.php');
if(file_exists('custom/modules/Employees/metadata/listviewdefs.php')){
	require_once('custom/modules/Employees/metadata/listviewdefs.php');	
}else{
	require_once('modules/Employees/metadata/listviewdefs.php');
}

require_once('include/SearchForm/SearchForm.php');


global $mod_strings;
global $app_strings;
global $app_list_strings;
global $current_user;

global $urlPrefix;


global $currentModule;
global $current_language;
$current_module_strings = return_module_language($current_language, 'Employees');


global $theme;


// clear the display columns back to default when clear query is called
if(!empty($_REQUEST['clear_query']) && $_REQUEST['clear_query'] == 'true')  
    $current_user->setPreference('ListViewDisplayColumns', array(), 0, 'Employees');

$savedDisplayColumns = $current_user->getPreference('ListViewDisplayColumns', 'Employees'); // get user defined display columns

$json = getJSONobj();

$seedEmployee = new Employee();
$seedEmployee->object_name = 'Employee';
$searchForm = new SearchForm('Employees', $seedEmployee); // new searchform instance

// setup listview smarty
$lv = new ListViewSmarty();
if(isset($_REQUEST['Employees2_EMPLOYEE_offset'])) {//if you click the pagination button, it will poplate the search criteria here
    if(!empty($_REQUEST['current_query_by_page'])) {//The code support multi browser tabs pagination
    
        $blockVariables = array('mass', 'uid', 'massupdate', 'delete', 'merge', 'selectCount', 'request_data', 'current_query_by_page' , 'Employees2_EMPLOYEE_ORDER_BY');
        if(isset($_REQUEST['lvso'])){
        	$blockVariables[] = 'lvso';
        }
		
        $current_query_by_page = unserialize(base64_decode($_REQUEST['current_query_by_page']));
        foreach($current_query_by_page as $search_key=>$search_value) {
            if($search_key != 'Employees2_EMPLOYEE_offset' && !in_array($search_key, $blockVariables)) {
                $_REQUEST[$search_key] = $search_value;
            }
        }
    }
}
if(!empty($_REQUEST['saved_search_select']) && $_REQUEST['saved_search_select']!='_none') {
    if(empty($_REQUEST['button']) && (empty($_REQUEST['clear_query']) || $_REQUEST['clear_query']!='true')) {
        $saved_search = loadBean('SavedSearch');
        $saved_search->retrieveSavedSearch($_REQUEST['saved_search_select']);
        $saved_search->populateRequest();
    }
    elseif(!empty($_REQUEST['button'])) { // click the search button, after retrieving from saved_search
        $_SESSION['LastSavedView'][$_REQUEST['module']] = '';
        unset($_REQUEST['saved_search_select']);
        unset($_REQUEST['saved_search_select_name']);
    }
}

require_once('modules/MySettings/StoreQuery.php');
$storeQuery = new StoreQuery();
if(!isset($_REQUEST['query'])){
    $storeQuery->loadQuery('Employees');
    $storeQuery->populateRequest();
}else{
    $storeQuery->saveFromRequest('Employees');   
}

$displayColumns = array();
// check $_REQUEST if new display columns from post
if(!empty($_REQUEST['displayColumns'])) {
    foreach(explode('|', $_REQUEST['displayColumns']) as $num => $col) {
        if(!empty($listViewDefs['Employees'][$col])) 
            $displayColumns[$col] = $listViewDefs['Employees'][$col];
    }    
}
elseif(!empty($savedDisplayColumns)) { // use user defined display columns from preferences 
    $displayColumns = $savedDisplayColumns;
}
else { // use columns defined in listviewdefs for default display columns
    foreach($listViewDefs['Employees'] as $col => $params) {
        if(!empty($params['default']) && $params['default'])
            $displayColumns[$col] = $params;
    }
} 

if(!empty($_REQUEST['orderBy'])) { // order by coming from $_REQUEST
    $params['orderBy'] = $_REQUEST['orderBy'];
    $params['overrideOrder'] = true;
    if(!empty($_REQUEST['sortOrder'])) $params['sortOrder'] = $_REQUEST['sortOrder'];
}

$lv->displayColumns = $displayColumns;
$lv->delete = false;

if(!empty($_REQUEST['search_form_only']) && $_REQUEST['search_form_only']) { // handle ajax requests for search forms only
    switch($_REQUEST['search_form_view']) {
        case 'basic_search':
            $searchForm->setup();
            $searchForm->displayBasic(false);
            break;
        case 'advanced_search':
            $searchForm->setup();
            $searchForm->displayAdvanced(false);
            break;
        case 'saved_views':
            echo $searchForm->displaySavedViews($listViewDefs, $lv, false);
            break;
    }
    return;
}

// use the stored query if there is one
if (!isset($where)) $where = "(is_group=0) AND (portal_only=0)";

if(isset($_REQUEST['query']))
{
    // we have a query
    // first save columns 
    $current_user->setPreference('ListViewDisplayColumns', $displayColumns, 0, 'Employees'); 
    if(!empty($_SERVER['HTTP_REFERER']) && preg_match('/action=EditView/', $_SERVER['HTTP_REFERER'])) { // from EditView cancel
        $searchForm->populateFromArray($storeQuery->query);
    }
    else {
        $searchForm->populateFromRequest();
    }
    $where_clauses = $searchForm->generateSearchWhere(true, "Employees"); // builds the where clause from search field inputs
    if (count($where_clauses) > 0 )$where .= ' AND ('.implode(' ) AND ( ', $where_clauses) . ')';
    $GLOBALS['log']->info("Here is the where clause for the list view: $where");
}

// start display
// which tab of search form to display
if(!isset($_REQUEST['search_form']) || $_REQUEST['search_form'] != 'false') {
    $searchForm->setup();
    if(isset($_REQUEST['searchFormTab']) && $_REQUEST['searchFormTab'] == 'advanced_search') {
        $searchForm->displayAdvanced();
    }
    elseif(isset($_REQUEST['searchFormTab']) && $_REQUEST['searchFormTab'] == 'saved_views'){
        $searchForm->displaySavedViews($listViewDefs, $lv);
    }
    else {
        $searchForm->displayBasic();
    }
}

if (!is_admin($current_user)){
	$params = array( 'massupdate' => false );
	$lv->export = false;
	$lv->setup($seedEmployee, 'include/ListView/ListViewNoMassUpdate.tpl', $where, $params);
}
else{
	$params = array( 'massupdate' => true);
	$lv->export = true;
	$lv->setup($seedEmployee, 'include/ListView/ListViewGeneric.tpl', $where, $params);
}
$savedSearchName = empty($_REQUEST['saved_search_select_name']) ? '' : (' - ' . $_REQUEST['saved_search_select_name']);
echo get_form_header($current_module_strings['LBL_LIST_FORM_TITLE'] . $savedSearchName, '', false);
echo $lv->display();


$savedSearch = new SavedSearch();
$json = getJSONobj();
// fills in saved views select box on shortcut menu
$savedSearchSelects = $json->encode(array($GLOBALS['app_strings']['LBL_SAVED_SEARCH_SHORTCUT'] . '<br>' . $savedSearch->getSelect('Employees')));
$str = "<script>
YAHOO.util.Event.addListener(window, 'load', SUGAR.util.fillShortcuts, $savedSearchSelects);
</script>";
echo $str;
?>
