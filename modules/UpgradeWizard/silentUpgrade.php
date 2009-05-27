<?php

/**
 * UpgradeWizardCommon
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

//////////////////////////////////////////////////////////////////////////////////////////
//// This is a stand alone file that can be run from the command prompt for upgrading a
//// Sugar Instance. Three parameters are required to be defined in order to execute this file.
//// php.exe -f silentUpgrade.php [Path to Upgrade Package zip] [Path to Log file] [Path to Instance]
//// See below the Usage for more details.
/////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
////	UTILITIES THAT MUST BE LOCAL :(
function prepSystemForUpgradeSilent() {
	global $subdirs;
	global $cwd;
	global $sugar_config;

	// make sure dirs exist
	foreach($subdirs as $subdir) {
	    mkdir_recursive(clean_path("{$cwd}/{$sugar_config['upload_dir']}upgrades/{$subdir}"));
	}
}

//local function for clearing cache
function clearCacheSU($thedir, $extension) {
	if ($current = @opendir($thedir)) {
		while (false !== ($children = readdir($current))) {
			if ($children != "." && $children != "..") {
				if (is_dir($thedir . "/" . $children)) {
					clearCacheSU($thedir . "/" . $children, $extension);
				}
				elseif (is_file($thedir . "/" . $children) && substr_count($children, $extension)) {
					unlink($thedir . "/" . $children);
				}
			}
		}
	}
 }
 //Bug 24890, 24892. default_permissions not written to config.php. Following function checks and if
 //no found then adds default_permissions to the config file.
 function checkConfigForPermissions(){
     if(file_exists(getcwd().'/config.php')){
         require(getcwd().'/config.php');
     }
     global $sugar_config;
     if(!isset($sugar_config['default_permissions'])){
             $sugar_config['default_permissions'] = array (
                     'dir_mode' => 02770,
                     'file_mode' => 0660,
                     'user' => '',
                     'group' => '',
             );
         ksort($sugar_config);
         if(is_writable('config.php') && write_array_to_file("sugar_config", $sugar_config,'config.php')) {
        	//writing to the file
 		}
     }
}
function checkLoggerSettings(){
	if(file_exists(getcwd().'/config.php')){
         require(getcwd().'/config.php');
     }
    global $sugar_config;
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
		 ksort($sugar_config);
         if(is_writable('config.php') && write_array_to_file("sugar_config", $sugar_config,'config.php')) {
        	//writing to the file
 		}
	 }
}

function checkResourceSettings(){
	if(file_exists(getcwd().'/config.php')){
         require(getcwd().'/config.php');
     }
    global $sugar_config;
	if(!isset($sugar_config['resource_management'])){
	  $sugar_config['resource_management'] =
		  array (
		    'special_query_limit' => 50000,
		    'special_query_modules' =>
		    array (
		      0 => 'Reports',
		      1 => 'Export',
		      2 => 'Import',
		      3 => 'Administration',
		      4 => 'Sync',
		    ),
		    'default_limit' => 1000,
		  );
		 ksort($sugar_config);
         if(is_writable('config.php') && write_array_to_file("sugar_config", $sugar_config,'config.php')) {
        	//writing to the file
 		}
	}
}


//rebuild all relationships...
function rebuildRelations(){
	$_REQUEST['silent'] = true;
	include('modules/Administration/RebuildRelationship.php');
	 $_REQUEST['upgradeWizard'] = true;
	 include('modules/ACL/install_actions.php');
}

function createMissingRels(){
	$relForObjects = array('leads'=>'Leads','campaigns'=>'Campaigns','prospects'=>'Prospects');
	foreach($relForObjects as $relObjName=>$relModName){
		//assigned_user
		$guid = create_guid();
		$query = "SELECT id FROM relationships WHERE relationship_name = '{$relObjName}_assigned_user'";
		$result= $GLOBALS['db']->query($query, true);
		$a = null;
		$a = $GLOBALS['db']->fetchByAssoc($result);
		if($GLOBALS['db']->checkError()){
			//log this
		}
		if(!isset($a['id']) && empty($a['id']) ){
			$qRel = "INSERT INTO relationships (id,relationship_name, lhs_module, lhs_table, lhs_key, rhs_module, rhs_table, rhs_key, join_table, join_key_lhs, join_key_rhs, relationship_type, relationship_role_column, relationship_role_column_value, reverse, deleted)
						VALUES ('{$guid}', '{$relObjName}_assigned_user','Users','users','id','{$relModName}','{$relObjName}','assigned_user_id',NULL,NULL,NULL,'one-to-many',NULL,NULL,'0','0')";
			$GLOBALS['db']->query($qRel);
			if($GLOBALS['db']->checkError()){
				//log this
			}
		}
		//modified_user
		$guid = create_guid();
		$query = "SELECT id FROM relationships WHERE relationship_name = '{$relObjName}_modified_user'";
		$result= $GLOBALS['db']->query($query, true);
		if($GLOBALS['db']->checkError()){
			//log this
		}
		$a = null;
		$a = $GLOBALS['db']->fetchByAssoc($result);
		if(!isset($a['id']) && empty($a['id']) ){
			$qRel = "INSERT INTO relationships (id,relationship_name, lhs_module, lhs_table, lhs_key, rhs_module, rhs_table, rhs_key, join_table, join_key_lhs, join_key_rhs, relationship_type, relationship_role_column, relationship_role_column_value, reverse, deleted)
						VALUES ('{$guid}', '{$relObjName}_modified_user','Users','users','id','{$relModName}','{$relObjName}','modified_user_id',NULL,NULL,NULL,'one-to-many',NULL,NULL,'0','0')";
			$GLOBALS['db']->query($qRel);
			if($GLOBALS['db']->checkError()){
				//log this
			}
		}
		//created_by
		$guid = create_guid();
		$query = "SELECT id FROM relationships WHERE relationship_name = '{$relObjName}_created_by'";
		$result= $GLOBALS['db']->query($query, true);
		$a = null;
		$a = $GLOBALS['db']->fetchByAssoc($result);
    	if(!isset($a['id']) && empty($a['id']) ){
			$qRel = "INSERT INTO relationships (id,relationship_name, lhs_module, lhs_table, lhs_key, rhs_module, rhs_table, rhs_key, join_table, join_key_lhs, join_key_rhs, relationship_type, relationship_role_column, relationship_role_column_value, reverse, deleted)
						VALUES ('{$guid}', '{$relObjName}_created_by','Users','users','id','{$relModName}','{$relObjName}','created_by',NULL,NULL,NULL,'one-to-many',NULL,NULL,'0','0')";
			$GLOBALS['db']->query($qRel);
			if($GLOBALS['db']->checkError()){
				//log this
			}
    	}
















	}
	//Also add tracker perf relationship





















}

function addDefaultModuleRoles($defaultRoles = array()) {
	foreach($defaultRoles as $roleName=>$role){
        foreach($role as $category=>$actions){
            foreach($actions as $name=>$access_override){
                    $query = "SELECT * FROM acl_actions WHERE name='$name' AND category = '$category' AND acltype='$roleName' AND deleted=0 ";
					$result = $GLOBALS['db']->query($query);
					//only add if an action with that name and category don't exist
					$row=$GLOBALS['db']->fetchByAssoc($result);
					if ($row == null) {
	                	$guid = create_guid();
	                	$currdate = gmdate($GLOBALS['timedate']->get_db_date_time_format());
	                	$query= "INSERT INTO acl_actions (id,date_entered,date_modified,modified_user_id,name,category,acltype,aclaccess,deleted ) VALUES ('$guid','$currdate','$currdate','1','$name','$category','$roleName','$access_override','0')";
						$GLOBALS['db']->query($query);
						if($GLOBALS['db']->checkError()){
							//log this
						}
	                }
            }
        }
	}
}

global $cwd;
$cwd = getcwd(); // default to current, assumed to be in a valid SugarCRM root dir.

function instanceOrNot($argv,$usage){
	///////////////////////////////////////////////////////////////////////////////
	////	CONFIRM NECESSARY ARGS
	if(count($argv) < 3) {
		echo "*******************************************************************************\n";
		echo "*** ERROR: Missing required parameters.  Received ".count($argv)." argument(s), require 7.\n";
		echo $usage;
		echo "FAILURE\n";
		die();
	}

	if(count($argv) < 3) {
		echo $usage;
		die();
	}
	$upgradeType = '';
	///////////////////////////////////////////////////////////////////////////////
	////	HANDLE RUNNING FROM PATH OUTSIDE OF INSTANCE
	$cwd = getcwd(); // default to current, assumed to be in a valid SugarCRM root dir.
	if(isset($argv[3])) {
		if(is_dir($argv[3])) {
			$cwd = $argv[3];
			chdir($cwd);
		} else {
			echo "*******************************************************************************\n";
			echo "*** ERROR: 3rd parameter must be a valid directory.  Tried to cd to [ {$argv[3]} ].\n";
			echo $usage;
			echo "FAILURE\n";
			die();
		}
	}

   //check if this is an instance
   if(is_file("{$cwd}/ini_setup.php")){
   	 // this is an instance
      $upgradeType = 'DCE_Instance';
      //now that this is dce instance we want to make sure that there are
      // 7 arguments
      if(count($argv) < 7) {
		echo "*******************************************************************************\n";
		echo "*** ERROR: Missing required parameters.  Received ".count($argv)." argument(s), require 7.\n";
		echo $usage;
		echo "FAILURE\n";
		die();
	}
   }
   else if(is_file("{$cwd}/include/entryPoint.php")) {
		//this should be a regular sugar install
	   $upgradeType = 'DCE_Template';
	}

  return $upgradeType;
}


function verifyArguments($argv,$usage_dce,$usage_regular){
    if(isset($argv[7]) && file_exists($argv[7].'SugarTemplateUtilties.php')){
        require_once($argv[7].'SugarTemplateUtilties.php');
    }
	///////////////////////////////////////////////////////////////////////////////
	////	CONFIRM NECESSARY ARGS
	if(count($argv) < 4) {
		echo "*******************************************************************************\n";
		echo "*** ERROR: Missing required parameters.  Received ".count($argv)." argument(s), require 7.\n";
		echo $usage_regular;
		echo "FAILURE\n";
		die();
	}

	if(count($argv) < 4) {
		echo $usage_regular;
		die();
	}
	$upgradeType = '';
	///////////////////////////////////////////////////////////////////////////////
	////	HANDLE RUNNING FROM PATH OUTSIDE OF INSTANCE
	$cwd = getcwd(); // default to current, assumed to be in a valid SugarCRM root dir.
	if(isset($argv[3])) {
		if(is_dir($argv[3])) {
			$cwd = $argv[3];
			chdir($cwd);
		} else {
			echo "*******************************************************************************\n";
			echo "*** ERROR: 3rd parameter must be a valid directory.  Tried to cd to [ {$argv[3]} ].\n";
			echo $usage_regular;
			echo "FAILURE\n";
			die();
		}
	}

   //check if this is an instance
   if(is_file("{$cwd}/ini_setup.php")){
   	  if(count($argv) < 7) {
		echo "*******************************************************************************\n";
		echo "*** ERROR: Missing required parameters.  Received ".count($argv)." argument(s), require 7.\n";
		echo $usage_dce;
		echo "FAILURE\n";
		die();
	   }
   	 // this is an instance
   	 if(!is_dir($argv[1])) { // valid directory . template path?
		echo "*******************************************************************************\n";
		echo "*** ERROR: First argument must be a full path to the template. Got [ {$argv[1]} ].\n";
		echo $usage_dce;
		echo "FAILURE\n";
		die();
	 }
	 elseif(is_dir($argv[1])){
	 	//if DCEUpgrade directory exists.
	 }
   }
   else if(!is_file("{$cwd}/include/entryPoint.php")) {
		//this should be a regular sugar install
		echo "*******************************************************************************\n";
		echo "*** ERROR: Tried to execute in a non-SugarCRM root directory.  Pass a 3rd parameter.\n";
		echo $usage_regular;
		echo "FAILURE\n";
		die();
		//check if this is a valid zip file
		if(!is_file($argv[1])) { // valid zip?
			echo "*******************************************************************************\n";
			echo "*** ERROR: First argument must be a full path to the patch file. Got [ {$argv[1]} ].\n";
			echo $usage_regular;
			echo "FAILURE\n";
			die();
	    }
	}

	////	CONFIRM NECESSARY ARGS
	///////////////////////////////////////////////////////////////////////////////
  return $upgradeType;
}

function upgradeDCEFiles($argv,$instanceUpgradePath){
	//copy and update following files from upgrade package
	$upgradeTheseFiles = array('cron.php','download.php','index.php','install.php','soap.php','sugar_version.php','vcal_server.php');
	foreach($upgradeTheseFiles as $file){
		$srcFile = clean_path("{$instanceUpgradePath}/$file");
		$destFile = clean_path("{$argv[3]}/$file");
		if(file_exists($srcFile)){
			if(!is_dir(dirname($destFile))) {
				mkdir_recursive(dirname($destFile)); // make sure the directory exists
			}
			copy_recursive($srcFile,$destFile);
			$_GET['TEMPLATE_PATH'] = $destFile;
			$_GET['CONVERT_FILE_ONLY'] = true;
			if(!class_exists('TemplateConverter')){
				include($argv[7].'templateConverter.php');
			}else{
				TemplateConverter::convertFile($_GET['TEMPLATE_PATH']);
			}


		}
	}
}



function threeWayMerge(){
	//using threeway merge apis
}
////	END UTILITIES THAT MUST BE LOCAL :(
///////////////////////////////////////////////////////////////////////////////


// only run from command line
if(isset($_SERVER['HTTP_USER_AGENT'])) {
	die('This utility may only be run from the command line or command prompt.');
}
//Clean_string cleans out any file  passed in as a parameter
$_SERVER['PHP_SELF'] = 'silentUpgrade.php';


///////////////////////////////////////////////////////////////////////////////
////	USAGE
$usage_dce =<<<eoq1
Usage: php.exe -f silentUpgrade.php [upgradeZipFile] [logFile] [pathToSugarInstance]

On Command Prompt Change directory to where silentUpgrade.php resides. Then type path to
php.exe followed by -f silentUpgrade.php and the arguments.

Example:
    [path-to-PHP/]php.exe -f silentUpgrade.php [path-to-upgrade-package/]SugarEnt-Upgrade-4.5.1-to-5.0.0b.zip [path-to-log-file/]silentupgrade.log  [path-to-sugar-instance/]Sugar451e
                             [Old Template path] [skipdbupgrade] [exitOrContinue]

Arguments:
    New Template Path or Upgrade Package : Upgrade package name. Template2 (upgrade to)location.
    silentupgrade.log                    : Silent Upgarde log file.
    Sugar451e/DCE                        : Sugar or DCE Instance instance being upgraded.
    Old Template path                    : Template1 (upgrade from) Instance is being upgraded.
    skipDBupgrade 						 : If set to Yes then silentupgrade will only upgrade files. Default is No.
    exitOnConflicts 					 : If set to No and conflicts are found then Upgrade continues. Default Yes.
    pathToDCEClient 					 : This is path to to DCEClient directory

eoq1;

$usage_regular =<<<eoq2
Usage: php.exe -f silentUpgrade.php [upgradeZipFile] [logFile] [pathToSugarInstance]

On Command Prompt Change directory to where silentUpgrade.php resides. Then type path to
php.exe followed by -f silentUpgrade.php and the arguments.

Example:
    [path-to-PHP/]php.exe -f silentUpgrade.php [path-to-upgrade-package/]SugarEnt-Upgrade-4.5.1-to-5.0.0b.zip [path-to-log-file/]silentupgrade.log  [path-to-sugar-instance/]Sugar451e
                             [Old Template path] [skipdbupgrade] [exitOrContinue]

Arguments:
    New Template Path or Upgrade Package : Upgrade package name. Template2 (upgrade to)location.
    silentupgrade.log                    : Silent Upgarde log file.
    Sugar451e/DCE                        : Sugar or DCE Instance instance being upgraded.
    admin-user                           : admin user performing the upgrade
eoq2;
////	END USAGE
///////////////////////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////////////////////////////
////	STANDARD REQUIRED SUGAR INCLUDES AND PRESETS
if(!defined('sugarEntry')) define('sugarEntry', true);

$_SESSION = array();
$_SESSION['schema_change'] = 'sugar'; // we force-run all SQL
$_SESSION['silent_upgrade'] = true;
$_SESSION['step'] = 'silent'; // flag to NOT try redirect to 4.5.x upgrade wizard

$_REQUEST = array();
$_REQUEST['addTaskReminder'] = 'remind';


define('SUGARCRM_INSTALL', 'SugarCRM_Install');
define('DCE_INSTANCE', 'DCE_Instance');
define('DCE_TEMPLATE', 'DCE_Template');

$upgradeType = verifyArguments($argv,$usage_dce,$usage_regular);

///////////////////////////////////////////////////////////////////////////////
//////  Verify that all the arguments are appropriately placed////////////////

///////////////////////////////////////////////////////////////////////////////
////	PREP LOCALLY USED PASSED-IN VARS & CONSTANTS
//$GLOBALS['log']	= LoggerManager::getLogger('SugarCRM');
//require_once('/var/www/html/eddy/sugarnode/SugarTemplateUtilities.php');

$path			= $argv[2]; // custom log file, if blank will use ./upgradeWizard.log
//$db				= &DBManagerFactory::getInstance();  //<---------


//$UWstrings		= return_module_language('en_us', 'UpgradeWizard');
//$adminStrings	= return_module_language('en_us', 'Administration');
//$mod_strings	= array_merge($adminStrings, $UWstrings);
$subdirs		= array('full', 'langpack', 'module', 'patch', 'theme', 'temp');

//$_REQUEST['zip_from_dir'] = $zip_from_dir;

define('SUGARCRM_PRE_INSTALL_FILE', 'scripts/pre_install.php');
define('SUGARCRM_POST_INSTALL_FILE', 'scripts/post_install.php');
define('SUGARCRM_PRE_UNINSTALL_FILE', 'scripts/pre_uninstall.php');
define('SUGARCRM_POST_UNINSTALL_FILE', 'scripts/post_uninstall.php');



echo "\n";
echo "********************************************************************\n";
echo "***************This Upgrade process may take sometime***************\n";
echo "********************************************************************\n";
echo "\n";

global $sugar_config;
$isDCEInstance = false;
$errors = '';
if(instanceOrNot($argv,$usage_dce) == constant('DCE_INSTANCE')){
   	//$instanceUpgradePath = "{$argv[1]}/DCEUpgrade/{$zip_from_dir}";
   	//$instanceUpgradePath = "{$argv[1]}";
	include ("ini_setup.php");
   	require_once("{$argv[4]}/include/entryPoint.php");
	require_once("{$argv[4]}/include/dir_inc.php");
	require_once("{$argv[4]}/include/utils/zip_utils.php");
	require_once("{$argv[4]}/modules/Administration/UpgradeHistory.php");
	require_once("{$argv[4]}/include/utils.php");
//	require_once("{$argv[5]}/modules/Users/User.php");
	//require_once("{$argv[5]}/modules/UpgradeWizard/uw_utils.php");
       //This is DCE instance
      global $sugar_config;
      global $sugar_version;
//    require_once("{$cwd}/sugar_version.php"); //provides instance version, flavor etc..
     //provides instance version, flavor etc..
    $isDCEInstance = true;
	if(!is_dir(clean_path("{$sugar_config['upload_dir']}/upgrades"))) {
		prepSystemForUpgradeSilent();
	}
	/////retrieve admin user
	$configOptions = $sugar_config['dbconfig'];

	$GLOBALS['log']	= LoggerManager::getLogger('SugarCRM');
	$db				= &DBManagerFactory::getInstance();
       		///////////////////////////////////////////////////////////////////////////////
	////	MAKE SURE PATCH IS COMPATIBLE

	if(is_file("{$argv[1]}/manifest.php")) {
		// provides $manifest array
		include("{$argv[1]}/manifest.php");
	}
	//If Instance then the files will be accessed from Template/DCEUpgrade folder
	$zip_from_dir = '';
    if( isset( $manifest['copy_files']['from_dir'] ) && $manifest['copy_files']['from_dir'] != "" ){
	    $zip_from_dir   = $manifest['copy_files']['from_dir'];
	}
	$instanceUpgradePath = "{$argv[1]}/{$zip_from_dir}";
	global $instancePath;
	$instancePath = $instanceUpgradePath;
	$_SESSION['instancePath'] = $instancePath;
	if(file_exists("{$instanceUpgradePath}/modules/UpgradeWizard/uw_utils.php")){
		require_once("{$instanceUpgradePath}/modules/UpgradeWizard/uw_utils.php");
	}
	else{
		require_once("{$argv[4]}/modules/UpgradeWizard/uw_utils.php");
	}
    if(function_exists('set_upgrade_vars')){
		set_upgrade_vars();
    }
	if(is_file("$argv[1]/manifest.php")) {
		// provides $manifest array
		//include("$instanceUpgradePath/manifest.php");
		if(!isset($manifest)) {
			die("\nThe patch did not contain a proper manifest.php file.  Cannot continue.\n\n");
		} else {
//			copy("{$cwd}/{$sugar_config['upload_dir']}upgrades/temp/su_temp/manifest.php", "{$cwd}/{$sugar_config['upload_dir']}upgrades/patch/{$zip_from_dir}-manifest.php");
			copy("$argv[1]/manifest.php", "{$sugar_config['upload_dir']}upgrades/patch/{$zip_from_dir}-manifest.php");
			$error = validate_manifest($manifest);
			if(!empty($error)) {
				$error = strip_tags(br2nl($error));
				die("\n{$error}\n\nFAILURE\n");
			}
		}
	} else {
		die("\nThe patch did not contain a proper manifest.php file.  Cannot continue.\n\n");
	}

	//get the latest uw_utils.php
//	require_once("{$instanceUpgradePath}/modules/UpgradeWizard/uw_utils.php");
    logThis("*** SILENT DCE UPGRADE INITIATED.", $path);
	logThis("*** UpgradeWizard Upgraded  ", $path);
	$_SESSION['sugar_version_file'] = '';
	$srcFile = clean_path("{$instanceUpgradePath}/sugar_version.php");
	if(file_exists($srcFile)) {
		logThis('Save the version file in session variable', $path);
		$_SESSION['sugar_version_file'] = $srcFile;
	}



    //check exit on conflicts
    $exitOnConflict = 'yes'; //default
    if($argv[5] != null && !empty($argv[5])){
    	if(strtolower($argv[5]) == 'no'){
    	  $exitOnConflict = 'no'; //override
    	}
    }
    if($exitOnConflict == 'yes'){
    	$customFiles = array();
    	$customFiles = findAllFiles(clean_path("{$argv[3]}/custom"), $customFiles);
    	if($customFiles != null){
    		logThis("*** ****************************  ****", $path);
			logThis("*** START LOGGING CUSTOM FILES  ****", $path);
			$existsCustomFile = false;
			foreach($customFiles as $file) {
			$srcFile = clean_path($file);
			//$targetFile = clean_path(getcwd() . '/' . $srcFile);
			    if (strpos($srcFile,".svn") !== false) {
				  //do nothing
			    }
			    else{
			     $existsCustomFile = true;
			     //log the custom file in
			     logThis($file, $path);
			    }
			}
			logThis("*** END LOGGING CUSTOM FILES  ****", $path);
			logThis("*** ****************************  ****", $path);
			if($existsCustomFile){
				echo 'Stop and Exit Upgrade. There are customized files. Take a look in the upgrade log';
				logThis("Stop and Exit Upgrade. There are customized files. Take a look in the upgrade log", $path);
				die();
			}
			else{
			    upgradeDCEFiles($argv,$instanceUpgradePath);
			}
    	}
    	else{
			   //copy and update following files from upgrade package
				upgradeDCEFiles($argv,$instanceUpgradePath);
		 }
    }
    else{
	   //copy and update following files from upgrade package
	   upgradeDCEFiles($argv,$instanceUpgradePath);
    }
    //check for db upgrade
    //check exit on conflicts
    $skipDBUpgrade = 'no'; //default
    if($argv[6] != null && !empty($argv[6])){
    	if(strtolower($argv[6]) == 'yes'){
    	  $skipDBUpgrade = 'yes'; //override
    	}
    }
    global $unzip_dir;
    $unzip_dir = $argv[1];
    $_SESSION['unzip_dir'] = $unzip_dir;
    global $path;
    $path = $argv[2];

    if($skipDBUpgrade == 'no'){
    	//upgrade the db
	    	///////////////////////////////////////////////////////////////////////////////
		////	HANDLE PREINSTALL SCRIPTS
		//if(empty($errors)) {
			$file = "{$argv[1]}/".constant('SUGARCRM_PRE_INSTALL_FILE');
			if(is_file($file)) {
				include($file);
				logThis('Running pre_install()...', $path);
				pre_install();
				logThis('pre_install() done.', $path);
			}
		//}

    		///////////////////////////////////////////////////////////////////////////////
	////	HANDLE POSTINSTALL SCRIPTS
		//if(empty($errors)) {
		logThis('Starting post_install()...', $path);
		$file = "{$argv[1]}/".constant('SUGARCRM_POST_INSTALL_FILE');
		if(is_file($file)) {
			include($file);
			post_install();
		}
			//clean vardefs
		logThis('Performing UWrebuild()...', $path);
			UWrebuild();
		logThis('UWrebuild() done.', $path);

		logThis('begin check default permissions .', $path);
	    	checkConfigForPermissions();
	    logThis('end check default permissions .', $path);

	    logThis('begin check logger settings .', $path);
	    	checkLoggerSettings();
	    logThis('begin check logger settings .', $path);

	    if (file_exists($newtemplate_path.'/modules/Administration/upgradeTeams.php'))
	    	include($newtemplate_path.'/modules/Administration/upgradeTeams.php');	    

        
		require("sugar_version.php");
		require('config.php');
		global $sugar_config;

		require("{$instanceUpgradePath}/sugar_version.php");
		if(!rebuildConfigFile($sugar_config, $sugar_version)) {
			logThis('*** ERROR: could not write config.php! - upgrade will fail!', $path);
			$errors[] = 'Could not write config.php!';
		}
		checkConfigForPermissions();
		logThis('post_install() done.', $path);
		 //}
    }
    logThis("***** SilentUpgrade completed successfully.", $path);
    logThis("***** SUCCESS.", $path);

	echo "********************************************************************\n";
	echo "*************************** SUCCESS*********************************\n";
	echo "********************************************************************\n";
}
else{
	ini_set('error_reporting',1);
	require_once('include/entryPoint.php');
	
	require_once('include/utils/zip_utils.php');
	
	
	
	require('config.php');
	//require_once('modules/UpgradeWizard/uw_utils.php'); // must upgrade UW first
	if(isset($argv[3])) {
		if(is_dir($argv[3])) {
			$cwd = $argv[3];
			chdir($cwd);
		}
	}

	require_once("{$cwd}/sugar_version.php"); // provides $sugar_version & $sugar_flavor

    $GLOBALS['log']	= LoggerManager::getLogger('SugarCRM');
	$patchName		= basename($argv[1]);
	$zip_from_dir	= substr($patchName, 0, strlen($patchName) - 4); // patch folder name (minus ".zip")
	$path			= $argv[2]; // custom log file, if blank will use ./upgradeWizard.log

	if($sugar_version < '5.1.0'){
		$db				= &DBManager :: getInstance();
	}
	else{
		$db				= &DBManagerFactory::getInstance();
	}
	$UWstrings		= return_module_language('en_us', 'UpgradeWizard');
	$adminStrings	= return_module_language('en_us', 'Administration');
	$mod_strings	= array_merge($adminStrings, $UWstrings);
	$subdirs		= array('full', 'langpack', 'module', 'patch', 'theme', 'temp');
	global $unzip_dir;
    $license_accepted = false;
    if(isset($argv[5]) && (strtolower($argv[5])=='yes' || strtolower($argv[5])=='y')){
    	$license_accepted = true;
	 }
	//////////////////////////////////////////////////////////////////////////////
	//Adding admin user to the silent upgrade

	$current_user = new User();
	if(isset($argv[4])) {
	   //if being used for internal upgrades avoid admin user verification
	   $user_name = $argv[4];
	   $q = "select id from users where user_name = '" . $user_name . "' and is_admin=1";
	   $result = $GLOBALS['db']->query($q, false);
	   $logged_user = $GLOBALS['db']->fetchByAssoc($result);
	   if(isset($logged_user['id']) && $logged_user['id'] != null){
		//do nothing
	    $current_user->retrieve($logged_user['id']);
	   }
	   else{
	   	echo "Not an admin user in users table. Please provide an admin user\n";
		die();
	   }
	}
	else {
		echo "*******************************************************************************\n";
		echo "*** ERROR: 4th parameter must be a valid admin user.\n";
		echo $usage;
		echo "FAILURE\n";
		die();
	}


		/////retrieve admin user
	global $sugar_config;
	$configOptions = $sugar_config['dbconfig'];


///////////////////////////////////////////////////////////////////////////////
////	UPGRADE PREP
if(!is_dir(clean_path("{$cwd}/{$sugar_config['upload_dir']}/upgrades"))) {
	prepSystemForUpgradeSilent();
}

$unzip_dir = clean_path("{$cwd}/{$sugar_config['upload_dir']}upgrades/temp");
$install_file = clean_path("{$cwd}/{$sugar_config['upload_dir']}upgrades/patch/".basename($argv[1]));

$_SESSION['unzip_dir'] = $unzip_dir;
$_SESSION['install_file'] = $install_file;
$_SESSION['zip_from_dir'] = $zip_from_dir;

mkdir_recursive($unzip_dir);
if(!is_dir($unzip_dir)) {
	die("\nFAILURE\n");
}
unzip($argv[1], $unzip_dir);
// mimic standard UW by copy patch zip to appropriate dir
copy($argv[1], $install_file);
////	END UPGRADE PREP
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
////	UPGRADE UPGRADEWIZARD

$zipBasePath = clean_path("{$cwd}/{$sugar_config['upload_dir']}upgrades/temp/{$zip_from_dir}");
$uwFiles = findAllFiles(clean_path("{$zipBasePath}/modules/UpgradeWizard"), array());
$destFiles = array();

foreach($uwFiles as $uwFile) {
	$destFile = clean_path(str_replace($zipBasePath, $cwd, $uwFile));
	copy($uwFile, $destFile);
}
//Bug 19554. copy include/utils/user_utils.php to make sure that vcsmsi() is not redeclared.
if(file_exists("{$zipBasePath}/include/utils/user_utils.php")){
	$destFile = clean_path(str_replace($zipBasePath, $cwd, "{$zipBasePath}/include/utils/user_utils.php"));
	copy("{$zipBasePath}/include/utils/user_utils.php", $destFile);
}
require_once('modules/UpgradeWizard/uw_utils.php'); // must upgrade UW first
logThis("*** SILENT UPGRADE INITIATED.", $path);
logThis("*** UpgradeWizard Upgraded  ", $path);

if(function_exists('set_upgrade_vars')){
	set_upgrade_vars();
}

if($configOptions['db_type'] == 'mysql'){
	//Change the db wait_timeout for this session
	$que ="select @@wait_timeout";
	$result = $db->query($que);
	$tb = $db->fetchByAssoc($result);
	logThis('Wait Timeout before change ***** '.$tb['@@wait_timeout'] , $path);
	$query ="set wait_timeout=28800";
	$db->query($query);
	$result = $db->query($que);
	$ta = $db->fetchByAssoc($result);
	logThis('Wait Timeout after change ***** '.$ta['@@wait_timeout'] , $path);
}

////	END UPGRADE UPGRADEWIZARD
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
////	MAKE SURE PATCH IS COMPATIBLE
if(is_file("{$cwd}/{$sugar_config['upload_dir']}upgrades/temp/manifest.php")) {
	// provides $manifest array
	include("{$cwd}/{$sugar_config['upload_dir']}upgrades/temp/manifest.php");
	if(!isset($manifest)) {
		die("\nThe patch did not contain a proper manifest.php file.  Cannot continue.\n\n");
	} else {
		copy("{$cwd}/{$sugar_config['upload_dir']}upgrades/temp/manifest.php", "{$cwd}/{$sugar_config['upload_dir']}upgrades/patch/{$zip_from_dir}-manifest.php");

		$error = validate_manifest($manifest);
		if(!empty($error)) {
			$error = strip_tags(br2nl($error));
			die("\n{$error}\n\nFAILURE\n");
		}
	}
} else {
	die("\nThe patch did not contain a proper manifest.php file.  Cannot continue.\n\n");
}


global $sugar_config;
global $sugar_version;
global $sugar_flavor;

////	END MAKE SURE PATCH IS COMPATIBLE
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
////	RUN SILENT UPGRADE
ob_start();
set_time_limit(0);
if(file_exists('ModuleInstall/PackageManager/PackageManagerDisplay.php')) {
	require_once('ModuleInstall/PackageManager/PackageManagerDisplay.php');
}

	$parserFiles = array();

if(file_exists(clean_path("{$zipBasePath}/include/SugarFields"))) {
	$parserFiles = findAllFiles(clean_path("{$zipBasePath}/include/SugarFields"), $parserFiles);
}
 //$cwd = clean_path(getcwd());
foreach($parserFiles as $file) {
	$srcFile = clean_path($file);
	//$targetFile = clean_path(getcwd() . '/' . $srcFile);
    if (strpos($srcFile,".svn") !== false) {
	  //do nothing
    }
    else{
    $targetFile = str_replace(clean_path($zipBasePath), $cwd, $srcFile);

	if(!is_dir(dirname($targetFile))) {
		mkdir_recursive(dirname($targetFile)); // make sure the directory exists
	}

	if(!file_exists($targetFile))
	 {
		logThis('Copying file to destination: ' . $targetFile);
		if(!copy($srcFile, $targetFile)) {
			logThis('*** ERROR: could not copy file: ' . $targetFile);
		} else {
			$copiedFiles[] = $targetFile;
		}
	} else {
		logThis('Skipping file: ' . $targetFile);
		//$skippedFiles[] = $targetFile;
	}
   }
 }
	//copy minimum required files including sugar_file_utils.php
	if(file_exists("{$zipBasePath}/include/utils/sugar_file_utils.php")){
		$destFile = clean_path(str_replace($zipBasePath, $cwd, "{$zipBasePath}/include/utils/sugar_file_utils.php"));
		copy("{$zipBasePath}/include/utils/sugar_file_utils.php", $destFile);
	}
	if(file_exists('include/utils/sugar_file_utils.php')){
    	require_once('include/utils/sugar_file_utils.php');
    }

/*
$errors = preflightCheck();
if((count($errors) == 1)) { // only diffs
	logThis('file preflight check passed successfully.');
}
else{
	die("\nThe user doesn't have sufficient permissions to write to database'.\n\n");
}
*/
//If version less than 500 then look for modules to be upgraded
if(function_exists('set_upgrade_vars')){
	set_upgrade_vars();
}
//Initialize the session variables. If upgrade_progress.php is already created
//look for session vars there and restore them
if(function_exists('initialize_session_vars')){
	initialize_session_vars();
}

if(!didThisStepRunBefore('preflight')){
	set_upgrade_progress('preflight','in_progress');
	//Quickcreatedefs on the basis of editviewdefs
    if(substr($sugar_version,0,1) >= 5){
    	updateQuickCreateDefs();
	}
	set_upgrade_progress('preflight','done');
}
////////////////COMMIT PROCESS BEGINS///////////////////////////////////////////////////////////////
////	MAKE BACKUPS OF TARGET FILES

if(!didThisStepRunBefore('commit')){
	set_upgrade_progress('commit','in_progress','commit','in_progress');
	if(!didThisStepRunBefore('commit','commitMakeBackupFiles')){
		set_upgrade_progress('commit','in_progress','commitMakeBackupFiles','in_progress');
		$errors = commitMakeBackupFiles($rest_dir, $install_file, $unzip_dir, $zip_from_dir, array());
		set_upgrade_progress('commit','in_progress','commitMakeBackupFiles','done');
	}

	///////////////////////////////////////////////////////////////////////////////
	////	HANDLE PREINSTALL SCRIPTS
	if(empty($errors)) {
		$file = "{$unzip_dir}/".constant('SUGARCRM_PRE_INSTALL_FILE');

		if(is_file($file)) {
			include($file);
			if(!didThisStepRunBefore('commit','pre_install')){
				set_upgrade_progress('commit','in_progress','pre_install','in_progress');
				pre_install();
				set_upgrade_progress('commit','in_progress','pre_install','done');
			}
		}
	}

	//Clean smarty from cache
	if(is_dir($GLOBALS['sugar_config']['cache_dir'].'smarty')){
		$allModFiles = array();
		$allModFiles = findAllFiles($GLOBALS['sugar_config']['cache_dir'].'smarty',$allModFiles);
	   foreach($allModFiles as $file){
	       	//$file_md5_ref = str_replace(clean_path(getcwd()),'',$file);
	       	if(file_exists($file)){
				unlink($file);
	       	}
	   }
	}

		//Also add the three-way merge here. The idea is after the 451 html files have
		//been converted run the 3-way merge. If 500 then just run the 3-way merge
		if(file_exists('modules/UpgradeWizard/SugarMerge/SugarMerge.php')){
		    set_upgrade_progress('end','in_progress','threewaymerge','in_progress');
		    require_once('modules/UpgradeWizard/SugarMerge/SugarMerge.php');
		    $merger = new SugarMerge($zipBasePath);
		    $merger->mergeAll();
		    set_upgrade_progress('end','in_progress','threewaymerge','done');
		}
	///////////////////////////////////////////////////////////////////////////////
	////	COPY NEW FILES INTO TARGET INSTANCE

     if(!didThisStepRunBefore('commit','commitCopyNewFiles')){
			set_upgrade_progress('commit','in_progress','commitCopyNewFiles','in_progress');
			$split = commitCopyNewFiles($unzip_dir, $zip_from_dir);
	 		$copiedFiles = $split['copiedFiles'];
	 		$skippedFiles = $split['skippedFiles'];
			set_upgrade_progress('commit','in_progress','commitCopyNewFiles','done');
	 }
     require_once('modules/DynamicFields/templates/Fields/TemplateText.php');
	///////////////////////////////////////////////////////////////////////////////
    ///    RELOAD NEW DEFINITIONS
    global $ACLActions, $beanList, $beanFiles;
    include('modules/ACLActions/actiondefs.php');
    include('include/modules.php'); 
	/////////////////////////////////////////////

    if (!function_exists("inDeveloperMode")) { 
        //this function was introduced from tokyo in the file include/utils.php, so when upgrading from 5.1x and 5.2x we should declare the this function 
        function inDeveloperMode()
        {
            return isset($GLOBALS['sugar_config']['developerMode']) && $GLOBALS['sugar_config']['developerMode'];
        }
    }
	///////////////////////////////////////////////////////////////////////////////
	////	HANDLE POSTINSTALL SCRIPTS
	if(empty($errors)) {
		logThis('Starting post_install()...', $path);
		if(!didThisStepRunBefore('commit','post_install')){
			$file = "$unzip_dir/" . constant('SUGARCRM_POST_INSTALL_FILE');
			if(is_file($file)) {
				//set_upgrade_progress('commit','in_progress','post_install','in_progress');
				$progArray['post_install']='in_progress';
				post_install_progress($progArray,'set');
					include($file);
					post_install();
				// cn: only run conversion if admin selects "Sugar runs SQL"
				if(!empty($_SESSION['allTables']) && $_SESSION['schema_change'] == 'sugar')
					executeConvertTablesSql($db->dbType, $_SESSION['allTables']);
				//set process to done
				$progArray['post_install']='done';
				//set_upgrade_progress('commit','in_progress','post_install','done');
				post_install_progress($progArray,'set');
			}
		}
	    //clean vardefs
		logThis('Performing UWrebuild()...', $path);
		ob_start();
			@UWrebuild();
		ob_end_clean();
		logThis('UWrebuild() done.', $path);

		logThis('begin check default permissions .', $path);
	    	checkConfigForPermissions();
	    logThis('end check default permissions .', $path);

	    logThis('begin check logger settings .', $path);
	    	checkLoggerSettings();
	    logThis('begin check logger settings .', $path);

	    logThis('begin check resource settings .', $path);
			checkResourceSettings();
		logThis('begin check resource settings .', $path);

        
		require("sugar_version.php");
		require('config.php');
		global $sugar_config;
		if(!rebuildConfigFile($sugar_config, $sugar_version)) {
			logThis('*** ERROR: could not write config.php! - upgrade will fail!', $path);
			$errors[] = 'Could not write config.php!';
		}
		checkConfigForPermissions();
		logThis('post_install() done.', $path);
	}

	///////////////////////////////////////////////////////////////////////////////
	////	REGISTER UPGRADE
	if(empty($errors)) {
		logThis('Registering upgrade with UpgradeHistory', $path);
		if(!didThisStepRunBefore('commit','upgradeHistory')){
			set_upgrade_progress('commit','in_progress','upgradeHistory','in_progress');
			$file_action = "copied";
			// if error was encountered, script should have died before now
			$new_upgrade = new UpgradeHistory();
			$new_upgrade->filename = $install_file;
			$new_upgrade->md5sum = md5_file($install_file);
			$new_upgrade->name = $zip_from_dir;
			$new_upgrade->description = $manifest['description'];
			$new_upgrade->type = 'patch';
			$new_upgrade->version = $sugar_version;
			$new_upgrade->status = "installed";
			$new_upgrade->manifest = (!empty($_SESSION['install_manifest']) ? $_SESSION['install_manifest'] : '');

			if($new_upgrade->description == null){
				$new_upgrade->description = "Silent Upgrade was used to upgrade the instance";
			}
			else{
				$new_upgrade->description = $new_upgrade->description." Silent Upgrade was used to upgrade the instance.";
			}
		   $new_upgrade->save();
		   set_upgrade_progress('commit','in_progress','upgradeHistory','done');
		   set_upgrade_progress('commit','done','commit','done');
		}
	  }

	//Clean modules from cache
		if(is_dir($GLOBALS['sugar_config']['cache_dir'].'smarty')){
			$allModFiles = array();
			$allModFiles = findAllFiles($GLOBALS['sugar_config']['cache_dir'].'smarty',$allModFiles);
		   foreach($allModFiles as $file){
		       	//$file_md5_ref = str_replace(clean_path(getcwd()),'',$file);
		       	if(file_exists($file)){
					unlink($file);
		       	}
		   }
		}
   //delete cache/modules before rebuilding the relations
   	//Clean modules from cache
		if(is_dir($GLOBALS['sugar_config']['cache_dir'].'modules')){
			$allModFiles = array();
			$allModFiles = findAllFiles($GLOBALS['sugar_config']['cache_dir'].'modules',$allModFiles);
		   foreach($allModFiles as $file){
		       	//$file_md5_ref = str_replace(clean_path(getcwd()),'',$file);
		       	if(file_exists($file)){
					unlink($file);
		       	}
		   }
		}
	ob_start();
	if(!isset($_REQUEST['silent'])){
		$_REQUEST['silent'] = true;
	}
	else if(isset($_REQUEST['silent']) && $_REQUEST['silent'] != true){
		$_REQUEST['silent'] = true;
	}
	logThis('Start rebuild relationships.', $path);
	 	@rebuildRelations();
	logThis('End rebuild relationships.', $path);
	 //logThis('Checking for leads_assigned_user relationship and if not found then create.', $path);
		@createMissingRels();
	 //logThis('Checked for leads_assigned_user relationship.', $path);
	ob_end_clean();
    //clean cache again
	if(function_exists('deleteCache')){
		@deleteCache();
	}
}

set_upgrade_progress('end','in_progress','end','in_progress');
/////////////////////////Old Logger settings///////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

if(function_exists('deleteCache')){
	set_upgrade_progress('end','in_progress','deleteCache','in_progress');
	@deleteCache();
	set_upgrade_progress('end','in_progress','deleteCache','done');
}

///////////////////////////////////////////////////////////////////////////////
////	TAKE OUT TRASH

if(empty($errors)) {
	set_upgrade_progress('end','in_progress','unlinkingfiles','in_progress');
	logThis('Taking out the trash, unlinking temp files.', $path);
	unlinkTempFiles(true);
	set_upgrade_progress('end','in_progress','unlinkingfiles','done');
}

///////////////////////////////////////////////////////////////////////////////
////	HANDLE REMINDERS
if(empty($errors)) {
	commitHandleReminders($skippedFiles, $path);
}

if(file_exists(clean_path(getcwd()).'/original451files')){
	rmdir_recursive(clean_path(getcwd()).'/original451files');
}



























}



//Also set the tracker settings if  flavor conversion ce->pro or ce->ent
if(isset($_SESSION['current_db_version']) && isset($_SESSION['target_db_version'])){
	if($_SESSION['current_db_version'] == $_SESSION['target_db_version']){
	    $_REQUEST['upgradeWizard'] = true;
	    ob_start();
	    	include('modules/Administration/upgradeTeams.php');
			include('include/Smarty/internals/core.write_file.php');
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
			$value = 0;
			$admin->saveSetting($category, 'users', $value);
			$key = array('num_lic_oc','key','expire_date');
			$value = '';
			foreach($key as $k){
				$admin->saveSetting($category, $k, $value);
			}
		}
	}
}

set_upgrade_progress('end','done','end','done');
///////////////////////////////////////////////////////////////////////////////
////	RECORD ERRORS
$phpErrors = ob_get_contents();
ob_end_clean();
logThis("**** Potential PHP generated error messages: {$phpErrors}", $path);

if(count($errors) > 0) {
	foreach($errors as $error) {
		logThis("****** SilentUpgrade ERROR: {$error}", $path);
	}
	echo "FAILED\n";
} else {
	logThis("***** SilentUpgrade completed successfully.", $path);
	echo "********************************************************************\n";
	echo "*************************** SUCCESS*********************************\n";
	echo "********************************************************************\n";

	echo "******** If your pre-upgrade Leads data is not showing  ************\n";
	echo "******** Or you see errors in detailview subpanels  ****************\n";
	echo "************* In order to resolve them  ****************************\n";
	echo "******** Log into application as Administrator  ********************\n";
	echo "******** Go to Admin panel  ****************************************\n";
	echo "******** Run Repair -> Rebuild Relationships  **********************\n";
	echo "********************************************************************\n";
}



























?>
