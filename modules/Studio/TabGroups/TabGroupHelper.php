<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
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



require_once('modules/Administration/Common.php');
class TabGroupHelper{
    var $modules = array();
    function getAvailableModules(){
       static $availableModules = array();
       if(!empty($availableModules))return $availableModules;
       foreach($GLOBALS['moduleList'] as $value){
           $availableModules[$value] = array('label'=>$GLOBALS['app_list_strings']['moduleList'][$value], 'value'=>$value);
       }
       foreach($GLOBALS['modInvisListActivities'] as $value){
           $availableModules[$value] = array('label'=>$GLOBALS['app_list_strings']['moduleList'][$value], 'value'=>$value);
       }
       return $availableModules;
    }
    
    /**
     * Takes in the request params from a save request and processes 
     * them for the save.
     *
     * @param REQUEST params  $params
     */
    function saveTabGroups($params){
    	
    	$tabGroups = array();
		 $selected_lang = (!empty($params['dropdown_lang'])?$params['dropdown_lang']:$_SESSION['authenticated_user_language']);    	
        for($count = 0; isset($params['slot_' . $count]); $count++){
        	
        	if($params['delete_' . $count] == 1){
        		continue;	
        	}
        	
        	
        	$index = $params['slot_' . $count];
        	$labelID = (!empty($params['tablabelid_' . $index]))?$params['tablabelid_' . $index]: 'LBL_GROUPTAB' . $count . '_'. time();
        	$labelValue = $params['tablabel_' . $index];
        	if(empty($GLOBALS['app_strings'][$labelID]) || $GLOBALS['app_strings'][$labelID] != $labelValue){
        		$contents = return_custom_app_list_strings_file_contents($selected_lang);
        		$new_contents = replace_or_add_app_string($labelID,$labelValue, $contents);
        		save_custom_app_list_strings_contents($new_contents, $selected_lang);
        		$app_strings[$labelID] = $labelValue;
        		
        	}
        	$tabGroups[$labelID] = array('label'=>$labelID);
        	$tabGroups[$labelID]['modules']= array();
        	for($subcount = 0; isset($params[$index.'_' . $subcount]); $subcount++){
        		$tabGroups[$labelID]['modules'][] = $params[$index.'_' . $subcount];
        	}
        	
    	} 
    	sugar_cache_put('app_strings', $GLOBALS['app_strings']);
     	$newFile = create_custom_directory('include/tabConfig.php');
     	write_array_to_file("GLOBALS['tabStructure']", $tabGroups, $newFile);
   		$GLOBALS['tabStructure'] = $tabGroups; 
   }
    
}


?>
