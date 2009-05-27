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
require_once('modules/ModuleBuilder/MB/MBModule.php');

class MBPackage{
	var $name;
	var $is_uninstallable = true;
	var $description = '';
	var $has_images = true;
	var $modules = array();
	var $date_modified = '';
	var $author = '';
	var $key = '';
	var $readme='';
	function MBPackage($name){
		$this->name = $name;
		$this->load();
		
	}
	function loadModules($force=false){
		if(!file_exists(MB_PACKAGE_PATH . '/' . $this->name .'/modules'))return;
		$d = dir(MB_PACKAGE_PATH . '/' . $this->name .'/modules');
		while($e = $d->read()){
			if(substr($e, 0, 1) != '.' && is_dir(MB_PACKAGE_PATH . '/'. $this->name. '/modules/' . $e)){
				$this->getModule($e, $force);
			}
		}
	}
	
	/**
	 * Loads the translated module titles from the selected language into.
	 * Will override currently loaded string to reflect undeployed label changes.
	 * $app_list_strings
	 * @return 
	 * @param $languge String language identifyer
	 */
	function loadModuleTitles($languge = '') 
	{
		if (empty($language))
		{
			$language = $GLOBALS['current_language'];
		}
		global $app_list_strings;
		$packLangFilePath = $this->getPackageDir() . "/language/application/" . $language . ".lang.php";
		if (file_exists($packLangFilePath))
		{
			
			require($packLangFilePath);
		}
	}
	
	function getModule($name, $force=true){
		if(!$force && !empty($this->modules[$name]))return;
		$path = $this->getPackageDir();
		
		$this->modules[$name] = new MBModule($name, $path, $this->name, $this->key);
	}
	
	function deleteModule($name){
		$this->modules[$name]->delete();
		unset($this->modules[$name]);
	}
	
function getManifest($version_specific = false, $for_export = false){
	//If we are exporting the package, we must ensure a different install key
	$pre = $for_export ? MB_EXPORTPREPEND : "";
	$date = gmdate($GLOBALS['timedate']->get_db_date_time_format());
	$time = time();
	$this->description = to_html($this->description);
	$is_uninstallable = ($this->is_uninstallable ? 'true' : 'false');
	$flavor = "'" . $GLOBALS['sugar_flavor'] . "'";
	if($GLOBALS['sugar_flavor'] == 'CE')$flavor = "'CE', 'PRO','ENT'";
	$version = (!empty($version_specific))?"'" . $GLOBALS['sugar_version'] . "'" : '';
	$header = file_get_contents('modules/ModuleBuilder/MB/header.php');
	return  <<<EOQ
	$header
	\$manifest = array (
		 'acceptable_sugar_versions' => 
		  array (
	     	$version
		  ),
		  'acceptable_sugar_flavors' =>
		  array(
		  	$flavor
		  ),
		  'readme'=>'$this->readme',
		  'key'=>'$this->key',
		  'author' => '$this->author',
		  'description' => '$this->description',
		  'icon' => '',
		  'is_uninstallable' => $is_uninstallable,
		  'name' => '$pre$this->name',
		  'published_date' => '$date',
		  'type' => 'module',
		  'version' => '$time',
		  'remove_tables' => 'prompt',
		  );
EOQ;
}
	
function buildInstall($path){
	$installdefs = array ('id' => $this->name,
		'beans'=>array(),
		'layoutdefs'=>array(),
		'relationships'=>array(),
	);
	if($this->has_images){
		$installdefs['image_dir'] = '<basepath>/icons'; 
	}
	foreach(array_keys($this->modules) as $module){
		$this->modules[$module]->build($path);
		$this->modules[$module]->addInstallDefs($installdefs);
	}
	$this->path = $this->getPackageDir(); 
	if(file_exists($this->path . '/language')){
		$d= dir($this->path . '/language');
		while($e = $d->read()){
			$lang_path = $this->path .'/language/' . $e;
			if(substr($e, 0, 1) != '.' && is_dir($lang_path)){
				$f = dir($lang_path);
				while($g = $f->read()){
					if(substr($g, 0, 1) != '.' && is_file($lang_path.'/'. $g)){
						$lang = substr($g, 0, strpos($g, '.'));
						$installdefs['language'][] = array(
						'from'=> '<basepath>/SugarModules/language/'.$e . '/'. $g,
						'to_module'=> $e,
						'language'=> $lang	
						);
					}
				}
			}
		}
			
		copy_recursive( $this->path . '/language/', $path . '/language/');
		$icon_path = $path . '/../icons/default/images/';
		mkdir_recursive($icon_path);
		copy_recursive($this->path . '/icons/', $icon_path);
	}
	return "\n".'$installdefs = ' . var_export_helper($installdefs). ';';

}
	
	function getPackageDir(){
		return MB_PACKAGE_PATH . '/' . $this->name;
	}
	
	function getBuildDir(){
		return MB_PACKAGE_BUILD . '/' . $this->name;
	}
	
	function getZipDir(){
		return $this->getPackageDir() . '/zips';
	}
	
	
	function load(){
		$path = $this->getPackageDir();
		if(file_exists($path .'/manifest.php')){
			require($path . '/manifest.php');
			if(!empty($manifest)){
				$this->date_modified = $manifest['published_date'];
				$this->is_uninstallable = $manifest['is_uninstallable'];
				$this->author = $manifest['author'];
				$this->key = $manifest['key'];
				$this->description = $manifest['description'];
				if(!empty($manifest['readme']))
					$this->readme = $manifest['readme'];
			}
		}
		$this->loadModules(true);
	}

	function save(){
		$path = $this->getPackageDir();
		if(mkdir_recursive($path)){
			$fp = sugar_fopen($path .'/manifest.php', 'w');
			
			
			//Save all the modules when we save a package
			$this->updateModulesMetaData(true);
			fwrite($fp, $this->getManifest() );
			fclose($fp);
		}
	}
	
	function build($export=true, $clean = false){
		$this->loadModules();
		require_once('include/utils/zip_utils.php');
		$package_path = $this->getPackageDir();
		$path = $this->getBuildDir() . '/SugarModules';
		if($clean && file_exists($path))rmdir_recursive($path);
		if(mkdir_recursive($path)){
			
			$manifest = $this->getManifest().$this->buildInstall($path);
			$fp = sugar_fopen($this->getBuildDir() .'/manifest.php', 'w');
			fwrite($fp, $manifest);
			fclose($fp);
			
		}
		if(file_exists('modules/ModuleBuilder/MB/LICENSE.txt')){
			copy('modules/ModuleBuilder/MB/LICENSE.txt', $this->getBuildDir() . '/LICENSE.txt');
		}else if(file_exists('LICENSE.txt')){
			copy('LICENSE.txt', $this->getBuildDir() . '/LICENSE.txt');
		}
		$package_dir = $this->getPackageDir();
		$date = date('Y_m_d_His');
		$zipDir = $this->getZipDir();
		if(!file_exists($zipDir))mkdir_recursive($zipDir);
		$cwd = getcwd();
		chdir($this->getBuildDir());
		zip_dir('.',$cwd . '/'. $zipDir. '/'. $this->name. $date. '.zip');
 	 	chdir($cwd);
 	 	if($export){
			header('Location:' . $zipDir. '/'. $this->name. $date. '.zip');
 	 	}
 	 	return array(
 	 		'zip'=>$zipDir. '/'. $this->name. $date. '.zip',
 	 		'manifest'=>$this->getBuildDir(). '/manifest.php',
 	 		'name'=>$this->name. $date,
 	 		);
	}
	
	
	function getNodes(){
		$this->loadModules();
		$node = array('name'=>$this->name, 'action'=>'module=ModuleBuilder&action=package&package=' . $this->name, 'children'=>array());
		foreach(array_keys($this->modules) as $module){
			$node['children'][] = $this->modules[$module]->getNodes();
		}
		return $node;
	}
	
	function populateFromPost(){
		$this->description = $_REQUEST['description'];
		$this->author = $_REQUEST['author'];
		$this->key = $_REQUEST['key'];
		$this->readme = $_REQUEST['readme'];
	}
	
	function rename($new_name){
		$old= $this->getPackageDir();
		$this->name = $new_name;
		$new = $this->getPackageDir();
		if(file_exists($new)){
			return false;	
		}
		if(rename($old, $new)){
			return true;
		}
			
		return false;
	}
	
	function updateModulesMetaData($save=false){
			
			foreach(array_keys($this->modules) as $module){
				$this->modules[$module]->key_name = $this->key . '_' . $this->modules[$module]->name;
				$this->modules[$module]->renameMetaData($this->modules[$module]->getModuleDir());
				$this->modules[$module]->renameLanguageFiles($this->modules[$module]->getModuleDir());
				if($save)$this->modules[$module]->save();
			}
		
	}
	
	function copy($new_name){
		$old= $this->getPackageDir();
		
		$count = 0;
		$this->name = $new_name;
		$new= $this->getPackageDir();
		while(file_exists($new)){
			$count++;
			$this->name = $new_name . $count;
			$new= $this->getPackageDir();
		}
		
		$new = $this->getPackageDir();
		if(copy_recursive($old, $new)){
			$this->updateModulesMetaData();
			return true;
		}
		return false;
		
	}
	
	function delete(){
		return rmdir_recursive($this->getPackageDir());
	}
	
	
	    //creation of the installdefs[] array
    function customBuildInstall($modules, $path){
        $columns=$this->getColumnsName();
        $installdefs = array ('id' => $this->name);
        $include_path="$path/SugarModules/include/language";
        if(file_exists($include_path) && is_dir($include_path)){
            $dd= dir($include_path);
            while($gg = $dd->read()){
                if(substr($gg, 0, 1) != '.' && is_file($include_path . '/' . $gg)){
                    $lang = substr($gg, 0, strpos($gg, '.'));
                    $installdefs['language'][] = array(
                    'from'=> '<basepath>/SugarModules/include/language/'. $gg,
                    'to_module'=> 'application',
                    'language'=>$lang    
                    );
                }
            }
        }
        $db = DBManagerFactory::getInstance();
        foreach($modules as $value){
            $custom_module = $this->getCustomModules($value);
            foreach($custom_module as $va){
                if ($va == 'language'){
                    $lang_path = 'custom/modules/' . $value . '/language';
                    $d= dir($lang_path);
                    while($g = $d->read()){
                        if(substr($g, 0, 1) != '.' && is_file($lang_path . '/' . $g)){
                            $lang = substr($g, 0, strpos($g, '.'));
                            $installdefs['language'][] = array(
                            'from'=> '<basepath>/SugarModules/modules/' . $value . '/language/'. $g,
                            'to_module'=> $value,
                            'language'=>$lang    
                            );
                        }
                    }    
                    $result=$db->query("SELECT *  FROM fields_meta_data where custom_module='$value'");
                    while($row = $db->fetchByAssoc($result)){
                        $name = $row['id'];
                    	foreach($row as $col=>$res){
                            switch ($col) {
                                case 'custom_module':
                                    $installdefs['custom_fields'][$name]['module'] = $res;
                                    break;
                                case 'required':
                                    $installdefs['custom_fields'][$name]['require_option'] = $res;
                                    break;
                                case 'vname':
                                    $installdefs['custom_fields'][$name]['label'] = $res;
                                    break;
                                case 'required':
                                    $installdefs['custom_fields'][$name]['require_option'] = $res;
                                    break;
                                case 'massupdate':
                                    $installdefs['custom_fields'][$name]['mass_update'] = $res;
                                    break;
                                case 'comments':
                                    $installdefs['custom_fields'][$name]['comments'] = $res;
                                    break;
                                case 'help':
                                    $installdefs['custom_fields'][$name]['help'] = $res;
                                    break;
                                case 'len':
                                    $installdefs['custom_fields'][$name]['max_size'] = $res;
                                    break;    
                                default:
                                    $installdefs['custom_fields'][$name][$col] = $res;
                            }//switch
                        }//foreach
                    }//while
                }//fi
                if($va == 'metadata'){
                    $meta_path = 'custom/modules/' . $value . '/metadata';
                    $m= dir($meta_path);
                    while($ggg = $m->read()){
                        if(substr($ggg, 0, 1) != '.' && is_file($meta_path . '/' . $ggg)){
                            if($ggg == 'listviewdefs.php'){
                                $installdefs['copy'][] = array(
                                'from'=> '<basepath>/SugarModules/modules/'. $value . '/metadata/'. $ggg,
                                'to'=> 'custom/modules/'. $value . '/metadata/' . $ggg,   
                                );
                            }
                            else{
                                $installdefs['copy'][] = array(
                                'from'=> '<basepath>/SugarModules/modules/'. $value . '/metadata/'. $ggg,
                                'to'=> 'custom/modules/'. $value . '/metadata/' . $ggg,   
                                );
                                $installdefs['copy'][] = array(
                                'from'=> '<basepath>/SugarModules/modules/'. $value . '/metadata/'. $ggg,
                                'to'=> 'custom/working/modules/'. $value . '/metadata/' . $ggg,   
                                );
                            }
                        }
                    }
                }//fi
            }//foreach
        }//foreach
        return "\n".'$installdefs = ' . var_export_helper($installdefs). ';';

    }

    
    //return an array which contain the name of fields_meta_data table's columns 
    function getColumnsName(){
         
        $meta = new FieldsMetaData();
        $arr = array(); 
         foreach($meta->getFieldDefinitions() as $key=>$value) {
            $arr[] = $key;
        }
        return $arr;
    }


    //creation of the custom fields ZIP file (use getmanifest() and customBuildInstall() )  
    function exportCustom($modules, $export=true, $clean = true){
        $path=$this->getBuildDir();
        if($clean && file_exists($path))rmdir_recursive($path);
        foreach($modules as $mod){
            $pathmod="$path/SugarModules/modules/$mod";
            if(mkdir_recursive($pathmod)){
                if(file_exists("custom/modules/$mod")){
                    copy_recursive("custom/modules/$mod", "$pathmod");
                }
                else{
                    print( "ERROR: One module is not a custom module.\n" );
                    return(false);
                }
            }
        }
        if(file_exists("custom/include")){
            if(mkdir_recursive("$path/SugarModules/include")){
                copy_recursive("custom/include", "$path/SugarModules/include");
            }
        }
        if(file_exists($path)){
            $manifest = $this->getManifest(true).$this->customBuildInstall($modules,$path);
            $fp = sugar_fopen($path .'/manifest.php', 'w');
            fwrite($fp, $manifest);
            fclose($fp);
        }
        if(file_exists('modules/ModuleBuilder/MB/LICENSE.txt')){
            copy('modules/ModuleBuilder/MB/LICENSE.txt', $path . '/LICENSE.txt');
        }
        else if(file_exists('LICENSE.txt')){
            copy('LICENSE.txt', $path . '/LICENSE.txt');
        }
        require_once('include/utils/zip_utils.php');
        $date = date('Y_m_d_His');
        $zipDir = $this->getZipDir();
        if(!file_exists($zipDir))mkdir_recursive($zipDir);
        $cwd = getcwd();
        chdir($this->getBuildDir());
        zip_dir('.',$cwd . '/'. $zipDir. '/'. $this->name. $date. '.zip');
        chdir($cwd);
        if($clean && file_exists($this->getBuildDir()))rmdir_recursive($this->getBuildDir());
        if($export){
            header('Location:' . $zipDir. '/'. $this->name. $date. '.zip');
        }
        return $zipDir. '/'. $this->name. $date. '.zip';
    }



    //if $module=false : return an array with custom module and there customizations.
    //if $module=!false : return an array with the directories of custom/module/$module.
    function getCustomModules($module=false){
        global $mod_strings;
        $path='custom/modules/';
        if(!file_exists($path) || !is_dir($path)){
            return array($mod_strings['LBL_EC_NOCUSTOM'] => "");
        }
        else{
            if ($module != false ){
                $path=$path . $module . '/';
            }
            // Bug 15177 : scandir() is a php 5 only function.
            //$scanlisting = scandir($path);
            
            $scandirectory=opendir($path);
            while (false !== ($filename = readdir($scandirectory))) {
                $scanlisting[] = $filename;
            }
            sort($scanlisting);
            $dirlisting = array();
            foreach ($scanlisting as $value){
                if(is_dir($path . $value) == true && $value != '.' && $value != '..') {
                    $dirlisting[] = $value;
                }
            }
            if(empty($dirlisting)){
                return array($mod_strings['LBL_EC_NOCUSTOM'] => "");
            }
            if ($module == false ){
                foreach ($dirlisting as $value){
                    $custommodules[$value]=$this->getCustomModules($value);
                    foreach ($custommodules[$value] as $va){
                        switch ($va) {
                        case 'language':
                                $return[$value][$va] = $mod_strings['LBL_EC_CUSTOMFIELD'];
                            break;
                        case 'metadata':
                            $return[$value][$va] = $mod_strings['LBL_EC_CUSTOMLAYOUT'];
                            break;
                        case 'Ext':
                            //$return[$value][$va] = $mod_strings['LBL_EC_CUSTOMFIELD'];
                            break;
                        case '':
                            $return[$value . " " . $mod_strings['LBL_EC_EMPTYCUSTOM']] = "";
                            break;
                        default:
                            $return[$value][$va] = $mod_strings['LBL_UNDEFINED'];
                        }
                    }
                }
                return $return;
            }
            else{
                return $dirlisting;
            }
        }
    }
	
    /**
     * Returns a set of field defs for fields that will exist when this package is deployed
     * based on the relationships in all of its modules.
     * 
     * @param $moduleName (module must be from whithin this package)
     * @return array Field defs
     */
    function getRelationshipsForModule($moduleName) {
    	$ret = array();
    	if (isset($this->modules[$moduleName])) {
    		$keyName = $this->modules[$moduleName]->key_name;
    		foreach($this->modules as $mName => $module) {
    			$rels = $module->getRelationships();
    			$relList = $rels->getRelationshipList();
    			foreach($relList as $rName ) {
    			    $rel = $rels->get ( $rName ) ;
    			     if ($rel->lhs_module == $keyName || $rel->rhs_module == $keyName) {
                        $ret[$rName] =  $rel;
    			     }
    			}
    		}
    	}
    	return $ret;
    }

    
    function exportProjectInstall($package, $for_export){
    	$pre = $for_export ? MB_EXPORTPREPEND : "";
        $installdefs = array ('id' => $pre . $this->name);
        $installdefs['copy'][] = array(
            'from'=> '<basepath>/' . $this->name,
            'to'=> 'custom/modulebuilder/packages/'. $this->name,   
        );
        return "\n".'$installdefs = ' . var_export_helper($installdefs). ';';

    }
    
    
    
    function exportProject($package, $export=true, $clean = true){
        $tmppath="custom/modulebuilder/projectTMP/";
        if(file_exists($this->getPackageDir())){
            if(mkdir_recursive($tmppath)){
                copy_recursive($this->getPackageDir(), $tmppath ."/". $this->name);
                $manifest = $this->getManifest(true, $export).$this->exportProjectInstall($package, $export);
                $fp = sugar_fopen($tmppath .'/manifest.php', 'w');
                fwrite($fp, $manifest);
                fclose($fp);
                if(file_exists('modules/ModuleBuilder/MB/LICENSE.txt')){
                    copy('modules/ModuleBuilder/MB/LICENSE.txt', $tmppath . '/LICENSE.txt');
                }
                else if(file_exists('LICENSE.txt')){
                    copy('LICENSE.txt', $tmppath . '/LICENSE.txt');
                }
                $readme_contents = $this->readme;
                $readmefp = sugar_fopen($tmppath.'/README.txt','w');
                fwrite($readmefp, $readme_contents);
                fclose($readmefp);
            }
        }
        require_once('include/utils/zip_utils.php');
        $date = date('Y_m_d_His');
        $zipDir = "custom/modulebuilder/packages/ExportProjectZips";
        if(!file_exists($zipDir))mkdir_recursive($zipDir);
        $cwd = getcwd();
        chdir($tmppath);
        zip_dir('.',$cwd . '/'. $zipDir. '/project_'. $this->name. $date. '.zip');
        chdir($cwd);
        if($clean && file_exists($tmppath))rmdir_recursive($tmppath);
        if($export){
            header('Location:' . $zipDir. '/project_'. $this->name. $date. '.zip');
        }
        return $zipDir. '/project_'. $this->name. $date. '.zip';
    }
	
    
}
?>
