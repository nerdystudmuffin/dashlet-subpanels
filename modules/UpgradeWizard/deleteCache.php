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
 //Request object must have these property values:
 //		Module: module name, this module should have a file called TreeData.php
 //		Function: name of the function to be called in TreeData.php, the function will be called statically.
 //		PARAM prefixed properties: array of these property/values will be passed to the function as parameter.

require_once('include/JSON.php');

//require_once('modules/UpgradeWizard/uw_utils.php');

$json = getJSONobj();

//Clean modules from cache
if(is_dir($GLOBALS['sugar_config']['cache_dir'].'modules')){
	$allModFiles = array();
	$allModFiles = findAllFiles($GLOBALS['sugar_config']['cache_dir'].'modules',$allModFiles);
   foreach($allModFiles as $file){
       	if(file_exists($file)){
			unlink($file);
       	}
   }
}
//Clean jsLanguage from cache
if(is_dir($GLOBALS['sugar_config']['cache_dir'].'jsLanguage')){
	$allModFiles = array();
	$allModFiles = findAllFiles($GLOBALS['sugar_config']['cache_dir'].'jsLanguage',$allModFiles);
   foreach($allModFiles as $file){
	   	if(file_exists($file)){
			unlink($file);
	   	}
	}
}
//Clean smarty from cache
if(is_dir($GLOBALS['sugar_config']['cache_dir'].'smarty')){
	$allModFiles = array();
	$allModFiles = findAllFiles($GLOBALS['sugar_config']['cache_dir'].'smarty',$allModFiles);
   foreach($allModFiles as $file){
       	if(file_exists($file)){
			unlink($file);
       	}
   }
}

$response = '';
//$GLOBALS['log']->fatal('file name '.$file_name);
//$GLOBALS['log']->fatal('file size loaded '.filesize($file_name));
/*
if($allModFiles != null){
	foreach($allModFiles as $f){
		$GLOBALS['log']->fatal('file name '.$f);
		$response .= $f;
	}
}
*/
if (!empty($response)) {
    echo $response;
}
sugar_cleanup();
exit();
?>