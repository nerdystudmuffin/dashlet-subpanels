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
 * $Id$
 * Description:  Defines the English language pack for the base application.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('modules/UpgradeWizard/SugarMerge/EditViewMerge.php');
require_once('modules/UpgradeWizard/SugarMerge/DetailViewMerge.php');
require_once('modules/UpgradeWizard/SugarMerge/SearchMerge.php');
require_once('modules/UpgradeWizard/SugarMerge/ListViewMerge.php');
require_once('modules/UpgradeWizard/SugarMerge/QuickCreateMerge.php');
require_once('modules/UpgradeWizard/SugarMerge/SideQuickCreateMerge.php');


/**
 * SugarMerge wraps around all the merge functionality of Sugar given a module name and the path to an unzipped patch
 *
 */
class SugarMerge {
	private $mergeMapping = array();
	private $new_path = '';
	private $custom_path = 'custom';
	private $original_path = '';
	private $merged = array();
	private $fp = NULL;
	
	function SugarMerge($new_path='', $original_path='', $custom_path='custom') {
		
		$this->new_path = empty($new_path) || preg_match('/[\/]$/', $new_path) ? $new_path : $new_path . '/';
		$this->original_path = empty($original_path) || preg_match('/[\/]$/', $original_path) ? $original_path : $original_path . '/';
		$this->custom_path = empty($custom_path) || preg_match('/[\/]$/', $custom_path) ? $custom_path : $custom_path . '/';

		$this->mergeMapping = array(
			'editviewdefs.php'=> new EditViewMerge(),
			'detailviewdefs.php'=>new DetailViewMerge(),
			'listviewdefs.php'=>new ListViewMerge(),
			'searchdefs.php'=>new SearchMerge(),
			'quickcreatedefs.php'=>new QuickCreateMerge(),
			'sidecreatedefs.php'=>new SideQuickCreateMerge(),
		);		
	}

	function setLogFilePointer($fp){
		$this->fp = $fp;
	}



	/**
	 * This will run through all the modules that may need merging and determine if there is anything to merge
	 * if $merge is set to true it will perform the merge
	 * if $merge and $save are set to true it will perform the merge and save the results in the custom directory
	 *
	 * @param BOOLEAN $merge - do we wish to perform the merge if false it will just return a list of files that can be merged
	 * @param BOOLEAN $save - do we wish to save the merged files to true - $merge must be true for this to apply - otherwise it will simulate merging so you can view the log files of the merge
	 * @return ARRAY - an associative array of module names to files that were either merged or have the potential to be merged depeneding if $merge and $save  are set to true
	 */
	function mergeAll($merge=true, $save=true){
		$this->merged = array();
		$searchDirectory = $this->custom_path;
		if(!preg_match('/[\/]modules$/si', $searchDirectory)) {
		   $searchDirectory .= preg_match('/[\/]$/', $this->custom_path) ? 'modules' : '/modules';
		}
		
		if(file_exists($searchDirectory)){
			$dir = dir($searchDirectory);
			while($e = $dir->read()){
				if(substr($e , 0, 1) != '.') {
					if(is_dir("{$searchDirectory}/{$e}/metadata")){
						$this->merged[$e] = $this->mergeModule($e, $merge, $save );
					}
				}
			}
		}
		return $this->merged;
	}

	
	

	/**
	 * This will merge any files that need merging for a given module
	 * if $merge is set to true it will perform the merge
	 * if $merge and $save are set to true it will perform the merge and save the results in the custom directory
	 *
	 * @param STRING $module - the name of the module to merge files for
	 * @param BOOLEAN $merge - do we wish to perform the merge if false it will just return a list of files that can be merged
	 * @param BOOLEAN $save - do we wish to save the merged files to true - $merge must be true for this to apply - otherwise it will simulate merging so you can view the log files of the merge
	 * @return ARRAY - an associative array of files that were either merged or have the potential to be merged depeneding if $merge and $save  are set to true
	 */
	function mergeModule($module, $merge = true, $save=true){
		$merged = array();
		$path = $this->original_path . 'modules/' . $module . '/metadata/';
		$custom_path = $this->custom_path . 'modules/' . $module . '/metadata/';
		$new_path = $this->new_path . 'modules/' . $module . '/metadata/';
		foreach($this->mergeMapping as $file=>&$object){
			if(file_exists("{$custom_path}{$file}") && file_exists("{$new_path}{$file}")){
				if($merge){
					$merged[$file] = $this->mergeFile($module, $file, $save);
				}else{
					$merged[$file] = true;
				}
			}
		}

		return $merged;

	}

	/**
	 * This function will merge a single file for a module
	 *
	 * @param STRING $module - name of the module
	 * @param STRING $file - name of the file
	 * @param STRING $save - should the merged file be saved to the custom directory
	 * @return BOOLEAN - success or failure of the merge
	 */
	function mergeFile($module, $file, $save=true){
		$path = $this->original_path . 'modules/' . $module . '/metadata/';
		$custom_path = $this->custom_path . 'modules/' . $module . '/metadata/';
		$new_path = $this->new_path . 'modules/' . $module . '/metadata/';
		if($this->fp) $this->mergeMapping[$file]->setLogFilePointer($this->fp);
		if(isset($this->mergeMapping[$file]) && file_exists("{$custom_path}{$file}") && file_exists("{$new_path}{$file}")){
			return $this->mergeMapping[$file]->merge($module, "{$path}{$file}", "{$new_path}{$file}", "{$custom_path}{$file}", $save);
		}
		return false;

	}
	
	
	/**
	 * Return the custom modules path
	 *
	 * @return STRING directory where custom module files are located
	 */
	function getCustomPath() {
		return $this->custom_path;
	}
	
	
	/**
	 * Return the new upgrade modules path
	 *
	 * @return STRING directory where new module files are located
	 */
	function getNewPath() {
		return $this->new_path;
	}	


	/**
	 * Return the original modules path
	 *
	 * @return STRING directory where new module files are located
	 */
	function getOriginalPath() {
		return $this->original_path;
	}		
	
}
?>
