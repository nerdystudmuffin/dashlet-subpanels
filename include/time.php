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
 ********************************************************************************/

function to_display_time($string, $displayMeridiem=false){
	global $current_user, $timeMeridiem;
	global $sugar_config;
	if($current_user->getPreference('time')){
		$time = $current_user->getPreference('time');
	}else $time = $sugar_config['default_time_format'];
	$hours = getHours($string);
	$minutes = getMinutes($string);
	if(substr_count($time, 'HH') > 0){
		if($hours >= 12){
				if($hours > 12){
					$hours = $hours - 12;
					if($hours < 10){
						$hours = '0'.$hours;	
					}
				}
				if($displayMeridiem)	
				$minutes.=$timeMeridiem[1];
		}else{
			
			if($hours == 0){
				$hours = 12;	
			}
			if($displayMeridiem)
			$minutes.=$timeMeridiem[0];
		}
	}
	if(substr_count($time, ':') > 0){
		return $hours.':'.$minutes;
	}
	
	return $hours.$minutes;
	
	
}

function to_db_time($string, $mer=''){
		global  $timeMeridiem;
		$hours = getHours($string);
		$minutes = getMinutes($string);
		if(!empty($mer)){
			$meridiem = $mer;	
		}else $meridiem = getMeridiem($string);
		
		if(!empty($meridiem)){
			$hours = $hours % 12;	
		}
		if($meridiem == $timeMeridiem[1] ){
			$hours += 12;	
		}
		if($hours > 24){
			$hours = $hours % 24;	
		}
		if($hours < 10 && strlen($hours) == 1){
				$hours = '0'.$hours;	
		}
		return $hours.':'.$minutes;
		
}
function getHours($string){
			return substr($string ,0, 2);
		
}
function getMinutes($string){
	if(substr_count($string, ':') > 0){
			return substr($string ,3, 2);	
		}
		else{
			return substr($string ,2, 2);	
		}
}
function getMeridiem($string){
	global $current_user;
	global $sugar_config;
	if($current_user->getPreference('time')){
		$time = $current_user->getPreference('time');
	}else $time = $sugar_config['default_time_format'];
	if(substr_count($time, 'HH')){
		if(substr_count($string, ':') > 0){
			return substr($string ,5, 2);	
		}
	}
	return '';
}
function AMPMMenu($prefix, $string){
	global $current_user,  $timeMeridiem;
	global $sugar_config;
	if($current_user->getPreference('time')){
		$time = $current_user->getPreference('time');
	}else $time = $sugar_config['default_time_format'];
	if(substr_count($time, 'HH')){
		$menu = "<select name='".$prefix."meridiem'>";
		$mer = $timeMeridiem[0];
		if(getHours($string) < 12 && getHours($string) >23){
			$menu .="<option value='$mer' selected>$mer";
		}else $menu .="<option value='$mer'>$mer";
		$mer = $timeMeridiem[1];
		if(getHours($string) > 11 && getHours($string) < 24){
			$menu .="<option value='$mer' selected>$mer";
		}else $menu .="<option value='$mer'>$mer";
		return $menu. "</select>";
	}
	return '';
}

function getDisplayTimeFormat(){
	global $current_user, $timeMeridiem;
	global $sugar_config;
	
	if($current_user->getPreference('time')){
			$time = $current_user->getPreference('time');
			if(substr_count($time, 'HH'))
				return $sugar_config['time_formats'][$time]. $timeMeridiem[1];
			return $sugar_config['time_formats'][$time];
	}
	if(substr_count($sugar_config['default_time_format'], 'HH')){
		return $sugar_config['time_formats'][$sugar_config['default_time_format']]. $timeMeridiem[1];
	}
	return $sugar_config['time_formats'][$sugar_config['default_time_format']];
	
}

?>
