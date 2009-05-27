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

 * Description: TODO:  To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

function  hasPasswordExpired($username){
   // if ($syst_generated_pwd == '0'){
    //    $type='syst';    
  //  }else{
        $type='user';
   // }
    $current_user= new user();
    $usr_id=$current_user->retrieve_user_id($username);
	$current_user->retrieve($usr_id);	
    if ($current_user->portal_only=='0'){
	    global $mod_strings;
	    $res=$GLOBALS['sugar_config']['passwordsetting'];
	               
	    if ($res[$type.'expiration'] == '1'){
	    	global $timedate;
	    	if ($current_user->pwd_last_changed == ''){
	    		$current_user->pwd_last_changed= $timedate->to_display_date_time(gmdate($GLOBALS['timedate']->get_db_date_time_format()));
	    		$current_user->save();
	    		
	    		}
	    		
	        $expireday = $res[$type.'expirationtype']*$res[$type.'expirationtime'];
		    $stim = strtotime($current_user->pwd_last_changed);
		    //add day to timestamp
		    $expiretime = date("Y-m-d H:i:s", mktime(date("H",$stim), date("i",$stim), date("s",$stim), date("m",$stim), date("d",$stim)+$expireday,   date("Y",$stim)));
		    $timenow = $timedate->to_display_date_time(gmdate($GLOBALS['timedate']->get_db_date_time_format()));
		    if ($timenow < $expiretime)
		    	return false;
		    else{
		    	$_SESSION['expiration_type']= $mod_strings['LBL_PASSWORD_EXPIRATION_TIME'];
		    	return true;
		    	}
		        
		}
	    if ($res[$type.'expiration'] == '2'){
	    	
	    	$login=$current_user->getPreference('loginexpiration');
	    	$current_user->setPreference('loginexpiration',$login+1);
	        $current_user->save();
	        if ($login >= $res[$type.'expirationlogin']){
	        	$_SESSION['expiration_type']= $mod_strings['LBL_PASSWORD_EXPIRATION_LOGIN'];
	        	return true;    
	        }
	        else
	            {
		    	return false;
		    	}
	                    
	    }
	    
	    if ($res[$type.'expiration'] == '0')       
	        return false;
	
	}
}
?>
