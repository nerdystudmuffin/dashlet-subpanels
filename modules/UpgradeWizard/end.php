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
logThis('[At end.php]');
global $unzip_dir;
global $path;
if($unzip_dir == null ) {
	$unzip_dir = $_SESSION['unzip_dir'];
}











    







logThis(" Start Rebuilding the config file again", $path);



//check and set the logger before rebuilding config
if(!isset($sugar_config['logger'])){
	$sugar_config['logger'] =array (
		'level'=>'fatal',
	    'file' =>
	      array (
		      'ext' => '.log',
		      'name' => 'sugarcrm',
		      'dateFormat' => '%c',
		      'maxSize' => '10MB',
		      'maxLogs' => 10,
		      'suffix' => '%m_%Y',
	  	  ),
	);
}

if(!rebuildConfigFile($sugar_config, $sugar_version)) {
	logThis('*** WARNING: could not write config.php!', $path);
}
logThis(" Finish Rebuilding the config file again", $path);

set_upgrade_progress('end','in_progress');

if(isset($_SESSION['current_db_version']) && isset($_SESSION['target_db_version'])){
	if($_SESSION['current_db_version'] != $_SESSION['target_db_version']){















	 }

	 //keeping separate. making easily visible and readable
	 if($_SESSION['current_db_version'] == $_SESSION['target_db_version']){
	    $_REQUEST['upgradeWizard'] = true;
	    ob_start();
	    	include('modules/Administration/upgradeTeams.php');
			include('modules/ACL/install_actions.php');
			include_once('include/Smarty/internals/core.write_file.php');
		ob_end_clean();
	 	$db =& DBManagerFactory::getInstance();
		if(isset($_SESSION['upgrade_from_flavor']) && ($_SESSION['upgrade_from_flavor'] == 'SugarCE to SugarPro' || $_SESSION['upgrade_from_flavor'] == 'SugarCE to SugarEnt')){
			//Set tracker settings. Disable tracker session, performance and queries
			$category = 'tracker';
			$value =1;
			$key = array('tracker_sessions','tracker_perf','tracker_queries');
			$admin = new Administration();
			foreach($key as $k){
				$admin->saveSetting($category, $k, $value);
			}
	        //Also set license information
			$category = 'license';
			$value = '0';
			$admin->saveSetting($category, 'users', $value);
			$key = array('num_lic_oc','key','expire_date');
			$value = '';
			foreach($key as $k){
				$admin->saveSetting($category, $k, $value);
			}
		}
	}
}




 /////////////////////////Old Logger settings///////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
if(file_exists('modules/Configurator/Configurator.php')){
	require_once('include/utils/array_utils.php');
	require_once('modules/Configurator/Configurator.php');
	$Configurator = new Configurator();
	$Configurator->parseLoggerSettings();
}
//unset the logger previously instantiated
if(file_exists('include/SugarLogger/LoggerManager.php')){
	
	unset($GLOBALS['log']);
	$GLOBALS['log'] = LoggerManager::getLogger('SugarCRM');
}













//Update the license
logThis('Start Updating the license ', $path);
ob_start();
   
   check_now(get_sugarbeat());
ob_end_clean();
logThis('End Updating the license ', $path);

set_upgrade_progress('end','done');

logThis('Cleaning up the session.  Goodbye.');
unlinkTempFiles();
logThis('Cleaning up the session.  Goodbye.');
resetUwSession();
// flag to say upgrade has completed
$_SESSION['upgrade_complete'] = true;

//add the clean vardefs here
if(!class_exists('VardefManager')){
	
}
VardefManager::clearVardef();

require_once('include/TemplateHandler/TemplateHandler.php');
TemplateHandler::clearAll();

//also add the cache cleaning here.
if(function_exists('deleteCache')){
	deleteCache();
}

global $mod_strings;
global $current_language;

if(!isset($current_language) || ($current_language == null)){
	$current_language = 'en_us';
}
if(isset($GLOBALS['current_language']) && ($GLOBALS['current_language'] != null)){
	$current_language = $GLOBALS['current_language'];
}
$mod_strings = return_module_language($current_language, 'UpgradeWizard');
$stop = false;


$httpHost		= $_SERVER['HTTP_HOST'];  // cn: 8472 - HTTP_HOST includes port in some cases
if($colon = strpos($httpHost, ':')) {
	$httpHost	= substr($httpHost, 0, $colon);
}
$parsedSiteUrl	= parse_url($sugar_config['site_url']);
$host			= ($parsedSiteUrl['host'] != $httpHost) ? $httpHost : $parsedSiteUrl['host'];

// aw: 9747 - use SERVER_PORT for users who don't plug in the site_url at install correctly
if ($_SERVER['SERVER_PORT'] != 80){
	$port = ":".$_SERVER['SERVER_PORT'];
}
else if (isset($parsedSiteUrl['port']) && $parsedSiteUrl['port'] != 80){
	$port = ":".$parsedSiteUrl['port'];
}
else{
	$port = '';
}
$path			= $parsedSiteUrl['path'];
$cleanUrl		= "{$parsedSiteUrl['scheme']}://{$host}{$port}{$path}/index.php";

$uwMain =<<<eoq
<table cellpadding="3" cellspacing="0" border="0">
	<tr>
		<th align="left">
			{$mod_strings['LBL_UW_TITLE_END']}
		</th>
	</tr>
	<tr>
		<td align="left">
			<p>
			{$mod_strings['LBL_UW_END_DESC']}
			</p>
			<p>
			{$mod_strings['LBL_UW_END_DESC2']}
			</p>
		</td>
	</tr>
	<tr>
		<td align="left">
			<p>
				<b class="error">{$mod_strings['LBL_UW_END_LOGOUT']}</b>
			</p>
			<p>
				<a href="index.php?module=Users&action=Logout">{$mod_strings['LBL_UW_END_LOGOUT2']}</a>
			</p>
		</td>
	</tr>

	<tr>
		<td align="left">
			<input type="button" value="{$mod_strings['LBL_BUTTON_DONE']}" onclick="deleteCacheAjax();window.location.href='$cleanUrl?module=Home&action=About'">
		</td>
	</tr>
</table>

<script>
 function deleteCacheAjax(){
	//AJAX call for checking the file size and comparing with php.ini settings.
	var callback = {
		 success:function(r) {
		     //alert(r.responseText);
		 }
	}
	postData = '&module=UpgradeWizard&action=deleteCache&to_pdf=1';
	YAHOO.util.Connect.asyncRequest('POST', 'index.php', callback, postData);
}
</script>
eoq;

$showBack		= false;
$showCancel		= false;
$showRecheck	= false;
$showNext		= false;

$stepBack		= 0;
$stepNext		= 0;
$stepCancel	= 0;
$stepRecheck	= 0;

$_SESSION['step'][$steps['files'][$_REQUEST['step']]] = ($stop) ? 'failed' : 'success';

?>
