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
 * Portions created by SugarCRM are Copyright(C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
global $mod_strings;
$res = $GLOBALS['sugar_config']['passwordsetting'];
$usr= new user();
$usr_id=$usr->retrieve_user_id($_POST['user_name']);
$usr->retrieve($usr_id);
$_SESSION['login_error']='';
$_SESSION['waiting_error']='';
$_SESSION['hasExpiredPassword']='0';
// if there is too many login attempts
if (!empty($usr_id) && $res['lockoutexpiration'] > 0 && $usr->getPreference('loginfailed')>=($res['lockoutexpirationlogin']) && !($usr->portal_only)){
    // if there is a lockout time set
    if ($res['lockoutexpiration'] == '2'){
    	// lockout date is now if not set
    	if (($logout_time=$usr->getPreference('logout_time'))==''){
	        $usr->setPreference('logout_time',date("Y-m-d H:i:s"));
	        $logout_time=$usr->getPreference('logout_time');
	        }    
		$stim = strtotime($logout_time);
		$expiretime = date("Y-m-d H:i:s", mktime(date("H",$stim), date("i",$stim)+($res['lockoutexpirationtime']*$res['lockoutexpirationtype']), date("s",$stim), date("m",$stim), date("d",$stim),   date("Y",$stim)));
	    // Test if the user is still locked out and return a error message
	    if (date("Y-m-d H:i:s") < $expiretime){
	        $_SESSION['login_error']=$mod_strings['LBL_LOGIN_ATTEMPTS_OVERRUN'];
	        $_SESSION['waiting_error']=$mod_strings['LBL_LOGIN_LOGIN_TIME_ALLOWED'].' ';
	        $lol= strtotime($expiretime)-strtotime(date("Y-m-d H:i:s"));
			        switch (true) {
		    case (floor($lol/86400) !=0):
		        $_SESSION['waiting_error'].=floor($lol/86400).$mod_strings['LBL_LOGIN_LOGIN_TIME_DAYS'];
		        break;
		    case (floor($lol/3600)!=0):
		        $_SESSION['waiting_error'].=floor($lol/3600).$mod_strings['LBL_LOGIN_LOGIN_TIME_HOURS'];
		        break;
		    case (floor($lol/60)!=0):
		        $_SESSION['waiting_error'].=floor($lol/60).$mod_strings['LBL_LOGIN_LOGIN_TIME_MINUTES'];
		        break;
	        case (floor($lol)!=0):
		        $_SESSION['waiting_error'].=floor($lol).$mod_strings['LBL_LOGIN_LOGIN_TIME_SECONDS'];
		        break;
			}
	    }
	    else{
	    	$usr->setPreference('loginfailed','0');
	        $usr->setPreference('logout_time','');
	        $usr->savePreferencesToDB();
	        $authController->login($_REQUEST['user_name'], $_REQUEST['user_password']);           
	    }
    }
    else{
    	$_SESSION['login_error']=$mod_strings['LBL_LOGIN_ATTEMPTS_OVERRUN'];
        $_SESSION['waiting_error']=$mod_strings['LBL_LOGIN_ADMIN_CALL'];
	}
}
else {
    $authController->login($_REQUEST['user_name'], $_REQUEST['user_password']);
}
// authController will set the authenticated_user_id session variable
if(isset($_SESSION['authenticated_user_id'])) {
	// Login is successful
    global $record;
    global $current_user;
    











    
    $GLOBALS['module'] = !empty($_REQUEST['login_module']) ? '?module='.$_REQUEST['login_module'] : '?module=Home';
   	$GLOBALS['action'] = !empty($_REQUEST['login_action']) ? '&action='.$_REQUEST['login_action'] : '&action=index';
    $GLOBALS['record']= !empty($_REQUEST['login_record']) ? '&record='.$_REQUEST['login_record'] : '';

	// awu: $module is somehow undefined even though the super globals is set, so we set the local variable here
	$module = $GLOBALS['module'];
	$action = $GLOBALS['action'];
	$record = $GLOBALS['record'];
     
    //C.L. Added $hasHistory check to respect the login_XXX settings if they are set
    $hasHistory = (!empty($_REQUEST['login_module']) || !empty($_REQUEST['login_action']) || !empty($_REQUEST['login_record']));
    if(isset($current_user) && !$hasHistory){
	    $modListHeader = query_module_access_list($current_user);
	    //try to get the user's tabs
	    $tempList = $modListHeader;
	    $idx = array_shift($tempList);
	    if(!empty($modListHeader[$idx])){
	    	$module = '?module='.$modListHeader[$idx];
	    	$action = '&action=index';
	    	$record = '';
	    }
    }

} else {
	// Login has failed
	if(!empty($usr_id)){
		if (($logout=$usr->getPreference('loginfailed'))=='')
	        $usr->setPreference('loginfailed','1');
	    else 
	        $usr->setPreference('loginfailed',$logout+1);
	    $usr->savePreferencesToDB();
    }
	$module ="?module=Users";
    $action="&action=Login";
    $record="";
}

// construct redirect url
$url = 'Location: index.php'.$module.$action.$record;







//adding this for bug: 21712.
$GLOBALS['app']->headerDisplayed = true;
sugar_cleanup();
header($url);
?>
