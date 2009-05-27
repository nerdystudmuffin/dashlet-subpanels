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

 * Description:
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc. All Rights
 * Reserved. Contributor(s): ______________________________________..
 * *******************************************************************************/

$admin = new Administration();
$admin->retrieveSettings();

require('modules/Trackers/config.php');
require_once('modules/Configurator/Configurator.php');

///////////////////////////////////////////////////////////////////////////////
////	HANDLE CHANGES
if(isset($_POST['process'])) {
   if($_POST['process'] == 'true') {
	   foreach($tracker_config as $entry) {
	   	  if(isset($entry['bean'])) {
	   	  	  //If checkbox is unchecked, we add the entry into the config table; otherwise delete it
		   	  if(empty($_POST[$entry['name']])) {
		        $admin->saveSetting('tracker', $entry['name'], 1);
		   	  }	else {
		        $db = DBManagerFactory::getInstance();
			    $db->query("DELETE FROM config WHERE category = 'tracker' and name = '" . $entry['name'] . "'");
		   	  }
	   	  }
	   } //foreach
	   
	   //save the tracker prune interval
	   if(!empty($_POST['tracker_prune_interval'])) {
	   	  $admin->saveSetting('tracker', 'prune_interval', $_POST['tracker_prune_interval']);
	   }
	   
	   //save log slow queries and slow query interval
	   $configurator = new Configurator();
	   $configurator->saveConfig();
   } //if
   header('Location: index.php?module=Administration&action=index');
}





echo get_module_title($mod_strings['LBL_MODULE_NAME_TITLE'], $mod_strings['LBL_TRACKER_SETTINGS'].": ", true);

$sugar_smarty	= new Sugar_Smarty();
$trackerManager = TrackerManager::getInstance();
$disabledMonitors = $trackerManager->getDisabledMonitors();
$trackerEntries = array();
foreach($tracker_config as $entry) {
   if(isset($entry['bean'])) {
   	  $disabled = !empty($disabledMonitors[$entry['name']]);
   	  $trackerEntries[$entry['name']] = array('label'=> $mod_strings['LBL_' . strtoupper($entry['name']) . '_DESC'], 'helpLabel'=> $mod_strings['LBL_' . strtoupper($entry['name']) . '_HELP'], 'disabled'=>$disabled);
   }
}

$configurator = new Configurator();
$sugar_smarty->assign('config', $configurator->config);

global $mod_strings;
$config_strings = return_module_language($GLOBALS['current_language'], 'Configurator');
$mod_strings['LOG_SLOW_QUERIES'] = $config_strings['LOG_SLOW_QUERIES'];
$mod_strings['SLOW_QUERY_TIME_MSEC'] = $config_strings['SLOW_QUERY_TIME_MSEC'];

$sugar_smarty->assign('mod', $mod_strings);
$sugar_smarty->assign('app', $app_strings);
$sugar_smarty->assign('trackerEntries', $trackerEntries);
$sugar_smarty->assign('tracker_prune_interval', !empty($admin->settings['tracker_prune_interval']) ? $admin->settings['tracker_prune_interval'] : 30);
$sugar_smarty->display('modules/Trackers/TrackerSettings.tpl');
?>
