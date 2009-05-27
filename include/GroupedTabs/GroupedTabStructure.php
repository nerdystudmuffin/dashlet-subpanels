<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
 * SubPanelTiles
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



require_once('include/tabConfig.php');

class GroupedTabStructure
{
	/** 
     * Prepare the tabs structure.
     * Uses 'Other' tab functionality.
     * If $modList is not specified, $modListHeader is used as default.
     * 
     * @param   array   optional list of modules considered valid
     * @param   array   optional array to temporarily union into the root of the tab structure 
     * 
     * @return  array   the complete tab-group structure
	 */
    function get_tab_structure($modList = '', $patch = '')
    {
    	global $modListHeader, $app_strings, $modInvisListActivities;
        
        /* Use default if not provided */
        if(!$modList)
        {
        	$modList =& $modListHeader;
        }
        
        /* Apply patch, use a reference if we can */
        if($patch)
        {
        	$tabStructure = $GLOBALS['tabStructure'];
        	
            foreach($patch as $mainTab => $subModules)
            {
                $tabStructure[$mainTab]['modules'] = array_merge($tabStructure[$mainTab]['modules'], $subModules);
            }
        }
        else
        {
        	$tabStructure =& $GLOBALS['tabStructure'];
        }
        
        $retStruct = array();
        $mlhUsed = array();
        
        $modList = array_merge($modList,$modInvisListActivities);
        /* Only return modules which exists in the modList */
        foreach($tabStructure as $mainTab => $subModules)
        {
            foreach($subModules['modules'] as $key => $subModule)
            {
               /* Perform a case-insensitive in_array check
                * and mark whichever module matched as used.
                */ 
                foreach($modList as $module)
                {
                    if(strcasecmp($subModule, $module) === 0)
                    {
                        $retStruct[$app_strings[$subModules['label']]]['modules'][$key] = $subModule;
                        $mlhUsed[$module] = true;
                        break;
                    }
                }
            }
        }
        
        /* Put all the unused modules in modList
         * into the 'Other' tab.
         */
        foreach($modList as $module)
        {
            if(!isset($mlhUsed[$module]))
            {
            	$retStruct[$app_strings['LBL_TABGROUP_OTHER']]['modules'] []= $module;
            }
        }
        
        return $retStruct;
    }
}

?>
