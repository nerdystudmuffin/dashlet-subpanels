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

global $current_user, $sugar_version, $sugar_config;


require_once('include/MySugar/MySugar.php');

// build dashlet cache file if not found
if(!is_file($GLOBALS['sugar_config']['cache_dir'].'dashlets/dashlets.php')) {
    require_once('include/Dashlets/DashletCacheBuilder.php');
    
    $dc = new DashletCacheBuilder();
    $dc->buildCache();
}
require_once($GLOBALS['sugar_config']['cache_dir'].'dashlets/dashlets.php');

require('modules/Dashboard/dashlets.php');

$pages = $current_user->getPreference('pages', 'Dashboard'); 
$dashlets = $current_user->getPreference('dashlets', 'Dashboard');

// BEGIN fill in with default homepage and dashlet selections
if(!isset($pages) || !isset($dashlets)) {
    $dashboardDashlets = array();

	//list of preferences to move over and to where
    $prefstomove = array(
        'mypbss_date_start' => 'MyPipelineBySalesStageDashlet',
        'mypbss_date_end' => 'MyPipelineBySalesStageDashlet',
        'mypbss_sales_stages' => 'MyPipelineBySalesStageDashlet',
        'mypbss_chart_type' => 'MyPipelineBySalesStageDashlet',
        'lsbo_lead_sources' => 'OpportunitiesByLeadSourceByOutcomeDashlet',
        'lsbo_ids' => 'OpportunitiesByLeadSourceByOutcomeDashlet',
        'pbls_lead_sources' => 'OpportunitiesByLeadSourceDashlet',
        'pbls_ids' => 'OpportunitiesByLeadSourceDashlet',
        'pbss_date_start' => 'PipelineBySalesStageDashlet',
        'pbss_date_end' => 'PipelineBySalesStageDashlet',
        'pbss_sales_stages' => 'PipelineBySalesStageDashlet',
        'pbss_chart_type' => 'PipelineBySalesStageDashlet',
        'obm_date_start' => 'OutcomeByMonthDashlet',
        'obm_date_end' => 'OutcomeByMonthDashlet',
        'obm_ids' => 'OutcomeByMonthDashlet');
    
	// upgrading from pre-5.0 dashboard
	// begin upgrade code
    

    $dashboard = new Dashboard();
    $old_dashboard = $dashboard->getUsersTopDashboard($current_user->id);
	$dashboard_def = unserialize(from_html(from_html($old_dashboard->content)));

	if (isset($dashboard_def)){



		
		foreach($dashboard_def as $def){
			if ($def['type'] == 'code'){
				$dashboardDashletName = $dashboard->getDashletName($def['id']);
				// clint - fixes bug #20398
				// only display dashlets that are from visibile modules and that the user has permission to list
				$myDashlet = new MySugar('Opportunities');
				$displayDashlet = $myDashlet->checkDashletDisplay();

				if (isset($dashletsFiles[$dashboardDashletName]) && $displayDashlet){
                   $options = array();
                   $prefsforthisdashlet = array_keys($prefstomove,$dashboardDashletName);
                   foreach ( $prefsforthisdashlet as $pref ) {
                       $options[$pref] = $current_user->getPreference($pref);
                   }
					$dashboardDashlets[create_guid()] = array('className' => $dashboardDashletName, 
												 'module' => 'Opportunities',					
		                                         'fileLocation' => $dashletsFiles[$dashboardDashletName]['file'],
                                                 'options' => $options);
				}
			}

















		}
	}
	// end upgrade code
	else{
	    foreach($defaultDashboardDashlets as $dashboardDashletName=>$module){
			// clint - fixes bug #20398
			// only display dashlets that are from visibile modules and that the user has permission to list
			$myDashlet = new MySugar($module);
			$displayDashlet = $myDashlet->checkDashletDisplay();
				
	    	if (isset($dashletsFiles[$dashboardDashletName]) && $displayDashlet){
                $options = array();
                $prefsforthisdashlet = array_keys($prefstomove,$dashboardDashletName);
                foreach ( $prefsforthisdashlet as $pref ) {
                    $options[$pref] = $current_user->getPreference($pref);
                }
                $dashboardDashlets[create_guid()] = array('className' => $dashboardDashletName, 
												 'module' => $module,
		                                         'fileLocation' => $dashletsFiles[$dashboardDashletName]['file'],
                                                'options' => $options,);
	    	}
	    }  
	}
    
    $count = 0;
    $dashboardColumns = array();
    $dashboardColumns[0] = array();
    $dashboardColumns[0]['width'] = '60%';
    $dashboardColumns[0]['dashlets'] = array();
    $dashboardColumns[1] = array();
    $dashboardColumns[1]['width'] = '40%';
    $dashboardColumns[1]['dashlets'] = array();

    foreach($dashboardDashlets as $guid=>$dashlet){
        if($count % 2 == 0) array_push($dashboardColumns[0]['dashlets'], $guid); 
        else array_push($dashboardColumns[1]['dashlets'], $guid);        
        $count++;
    }
    
























































    

	$dashlets = $dashboardDashlets;

	



    $current_user->setPreference('dashlets', $dashlets, 0, 'Dashboard');
}

if (empty($pages)){
	$pages = array();
	$pages[0]['columns'] = $dashboardColumns;
	$pages[0]['numColumns'] = '2';
	$pages[0]['pageTitle'] = $mod_strings['LBL_DASHBOARD_PAGE_1'];





	$current_user->setPreference('pages', $pages, 0, 'Dashboard');
	$activePage = 0;
}








    $activePage = 0;




$divPages[] = $activePage;
    
$numCols = $pages[$activePage]['numColumns'];




















$count = 0;
$dashletIds = array(); // collect ids to pass to javascript
$display = array();

foreach($pages[$activePage]['columns'] as $colNum => $column) {
	if ($colNum == $numCols){
		break;
	}	
    $display[$colNum]['width'] = $column['width'];
    $display[$colNum]['dashlets'] = array(); 
    foreach($column['dashlets'] as $num => $id) {
        if(!empty($id) && isset($dashlets[$id]) && is_file($dashlets[$id]['fileLocation'])) {
			$module = 'Home';
			if ( isset($dashletsFiles[$dashlets[$id]['className']]['module']) )
        		$module = $dashletsFiles[$dashlets[$id]['className']]['module'];

			$myDashlet = new MySugar($module);

			if($myDashlet->checkDashletDisplay()) {
        		require_once($dashlets[$id]['fileLocation']);






	          		$dashlet = new $dashlets[$id]['className']($id, (isset($dashlets[$id]['options']) ? $dashlets[$id]['options'] : array()));



            	array_push($dashletIds, $id);

            	$dashlet->process();
            	$display[$colNum]['dashlets'][$id]['display'] = $dashlet->display();
            	if($dashlet->hasScript) {
             	   $display[$colNum]['dashlets'][$id]['script'] = $dashlet->displayScript();
            	}
        	}
    	}
    }
}

$sugar_smarty = new Sugar_Smarty();
if(!empty($sugar_config['lock_homepage']) && $sugar_config['lock_homepage'] == true) $sugar_smarty->assign('lock_homepage', true);  







$sugar_smarty->assign('sugarVersion', $sugar_version);
$sugar_smarty->assign('sugarFlavor', $sugar_flavor);
$sugar_smarty->assign('currentLanguage', $GLOBALS['current_language']);
$sugar_smarty->assign('serverUniqueKey', $GLOBALS['server_unique_key']);
$sugar_smarty->assign('imagePath', $GLOBALS['image_path']);

$sugar_smarty->assign('jsCustomVersion', $sugar_config['js_custom_version']);
$sugar_smarty->assign('maxCount', empty($sugar_config['max_dashlets_homepage']) ? 15 : $sugar_config['max_dashlets_homepage']);
$sugar_smarty->assign('dashletCount', $count);
$sugar_smarty->assign('dashletIds', '["' . implode('","', $dashletIds) . '"]');
$sugar_smarty->assign('columns', $display);

global $theme;
$sugar_smarty->assign('theme', $theme);

$sugar_smarty->assign('divPages', $divPages);
$sugar_smarty->assign('activePage', $activePage);




$sugar_smarty->assign('current_user', $current_user->id);

$local_mod_strings = return_module_language($sugar_config['default_language'], 'Home');
$sugar_smarty->assign('lblAddDashlets', $GLOBALS['app_strings']['LBL_ADD_DASHLETS']);
$sugar_smarty->assign('lblLnkHelp', $GLOBALS['app_strings']['LNK_HELP']);










$sugar_smarty->assign('module', 'Dashboard');

echo $sugar_smarty->fetch('include/MySugar/tpls/MySugar.tpl');

?>
