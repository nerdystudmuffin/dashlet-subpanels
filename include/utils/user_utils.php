<?php
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

/**
 * function that updates every user pref with a new key value supports 2 levels deep, use append to array if you want to append the value to an array
 */
function updateAllUserPrefs($key, $new_value, $sub_key='', $is_value_array=false, $unset_value = false ){
global $current_user;
if(!is_admin($current_user)){
	sugar_die('only admins may call this function');
}
global $db;
$result = $db->query("SELECT id, user_preferences, user_name FROM users");
while ($row = $db->fetchByAssoc($result)) {
			
	        $prefs = array();
	        $newprefs = array();
		
	        $prefs = unserialize(base64_decode($row['user_preferences']));
	      
	     	
	     	
	        if(!empty($sub_key)){
	        	
	        	if($is_value_array ){
	        		if(!isset($prefs[$key][$sub_key])){
	        			continue;
	        		}
	        			
	        		if(empty($prefs[$key][$sub_key])){
	        			$prefs[$key][$sub_key] = array();	
	        		}
	        		$already_exists = false;
	        		foreach($prefs[$key][$sub_key] as $k=>$value){
	        			if($value == $new_value){
	        				
	        				$already_exists = true;	
	        				if($unset_value){
	        					unset($prefs[$key][$sub_key][$k]);
	        				}
	        			}	
	        		}
	        		if(!$already_exists && !$unset_value){
	        			$prefs[$key][$sub_key][] = $new_value;	
	        		}
	        	}
	        	else{
	        		if(!$unset_value)$prefs[$key][$sub_key] = $new_value;
	        	}
	        	
	        }else{
	        	
	        		if($is_value_array ){
	        		if(!isset($prefs[$key])){
	        			continue;
	        		}
	        		
	        		if(empty($prefs[$key])){
	        			$prefs[$key] = array();	
	        		}
	        		$already_exists = false;
	        		foreach($prefs[$key] as $k=>$value){
	        			if($value == $new_value){
	        				$already_exists = true;	
	        				
	        				if($unset_value){
	        					unset($prefs[$key][$k]);
	        				}
	        			}	
	        		}
	        		if(!$already_exists && !$unset_value){
	        			
	        			$prefs[$key][] = $new_value;	
	        		}
	        	}else{
	        		if(!$unset_value)$prefs[$key] = $new_value;
	        	}
	        }	
	  		
        	$newstr = $GLOBALS['db']->quote(base64_encode(serialize($prefs)));
       		$db->query("UPDATE users SET user_preferences = '{$newstr}' WHERE id = '{$row['id']}'");
		
}
	       
	
        unset($prefs);
        unset($newprefs);
        unset($newstr);
}








?>
