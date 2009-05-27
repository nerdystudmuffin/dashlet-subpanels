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

 * Description:  Creates the runtime database connection.
 ********************************************************************************/
class javascript{
	var $formname = 'form';
	var $script = '';
	var $sugarbean = null;
	function setFormName($name){
		$this->formname = $name;
	}

	function javascript(){
		global $app_strings, $current_user, $sugar_config;

		static $dec_sep = null;
		static $num_grp_sep = null;
		
		if($dec_sep == null) {
			$user_dec_sep = $current_user->getPreference('dec_sep');
			$dec_sep = (empty($user_dec_sep) ? $sugar_config['default_decimal_seperator'] : $user_dec_sep);
		}
		if($num_grp_sep == null) {
	 		$user_num_grp_sep = $current_user->getPreference('num_grp_sep');
			$num_grp_sep = (empty($user_num_grp_sep) ? $sugar_config['default_number_grouping_seperator'] : $user_num_grp_sep);
		}
		$this->script .= "num_grp_sep = '$num_grp_sep';\n
						 dec_sep = '$dec_sep';\n";
        // Bug 24730 - default initialize the bean object in case we never set it to the current bean object
		$this->sugarbean = new stdClass;
		$this->sugarbean->field_name_map = array();
		$this->sugarbean->module_dir = '';
	}
	
	function setSugarBean($sugar){
		$this->sugarbean = $sugar;
	}

	function addRequiredFields($prefix=''){
			if(isset($this->sugarbean->required_fields)){
				foreach($this->sugarbean->required_fields as $field=>$value){
					$this->addField($field,'true', $prefix);
				}
			}
	}

    function addSpecialField($dispField, $realField, $type, $required, $prefix = '') {
    	$this->addFieldGeneric($dispField, 'date', $this->sugarbean->field_name_map[$realField]['vname'], $required, $prefix );
    }
    
	function addField($field,$required, $prefix='', $displayField='', $translate = false){
		if(isset($this->sugarbean->field_name_map[$field]['vname'])){
            $vname = $this->sugarbean->field_name_map[$field]['vname'];
            if ( $translate )
                $vname = $this->buildStringToTranslateInSmarty($this->sugarbean->field_name_map[$field]['vname']);
			if(empty($required)){
				if(isset($this->sugarbean->field_name_map[$field]['required']) && $this->sugarbean->field_name_map[$field]['required']){
					$required = 'true';
				}else{
					$required = 'false';	
				}
				if(isset($this->sugarbean->required_fields[$field]) && $this->sugarbean->required_fields[$field]){
					$required = 'true';
				}
				if($field == 'id'){
					$required = 'false';	
				}	
						
			}
			if(isset($this->sugarbean->field_name_map[$field]['validation'])){
				switch($this->sugarbean->field_name_map[$field]['validation']['type']){
					case 'range': 
						$min = 0;
						$max = 100;
						if(isset($this->sugarbean->field_name_map[$field]['validation']['min'])){
							$min = $this->sugarbean->field_name_map[$field]['validation']['min'];
						}
						if(isset($this->sugarbean->field_name_map[$field]['validation']['max'])){
							$max = $this->sugarbean->field_name_map[$field]['validation']['max'];
						}
						if($min > $max){
							$max = $min;
						}
						if(!empty($displayField)){
							$dispField = $displayField;
						}
						else{
							$dispField = $field;
						}
						$this->addFieldRange($dispField,$this->sugarbean->field_name_map[$field]['type'],$vname,$required,$prefix, $min, $max );	
						break;
					case 'isbefore':
						$compareTo = $this->sugarbean->field_name_map[$field]['validation']['compareto'];
						if(!empty($displayField)){
							$dispField = $displayField;
						}
						else{
							$dispField = $field;
						}
						if(!empty($this->sugarbean->field_name_map[$field]['validation']['blank']) && $this->sugarbean->field_name_map[$field]['validation']['blank']) 
						$this->addFieldDateBeforeAllowBlank($dispField,$this->sugarbean->field_name_map[$field]['type'],$vname,$required,$prefix, $compareTo );
						else $this->addFieldDateBefore($dispField,$this->sugarbean->field_name_map[$field]['type'],$vname,$required,$prefix, $compareTo );
						break;
					default: 
						if(!empty($displayField)){
							$dispField = $displayField;
						}
						else{
							$dispField = $field;
						}
						
						$type = (!empty($this->sugarbean->field_name_map[$field]['custom_type']))?$this->sugarbean->field_name_map[$field]['custom_type']:$this->sugarbean->field_name_map[$field]['type'];
						
						$this->addFieldGeneric($dispField,$type,$vname,$required,$prefix );	
						break;
				}
			}else{
				if(!empty($displayField)){
							$dispField = $displayField;
						}
						else{
							$dispField = $field;
						}
					$type = (!empty($this->sugarbean->field_name_map[$field]['custom_type']))?$this->sugarbean->field_name_map[$field]['custom_type']:$this->sugarbean->field_name_map[$field]['type'];
					if(!empty($this->sugarbean->field_name_map[$dispField]['isMultiSelect']))$dispField .='[]';
					$this->addFieldGeneric($dispField,$type,$vname,$required,$prefix );
			}
		}else{
			$GLOBALS['log']->debug('No VarDef Label For ' . $field . ' in module ' . $this->sugarbean->module_dir ); 	
		}

	}


	function stripEndColon($modString)
	{
		if(substr($modString, -1, 1) == ":")
			$modString = substr($modString, 0, (strlen($modString) - 1));
		if(substr($modString, -2, 2) == ": ")
			$modString = substr($modString, 0, (strlen($modString) - 2));
		return $modString;
		
	}
	
	function addFieldGeneric($field, $type,$displayName, $required, $prefix=''){
		$this->script .= "addToValidate('".$this->formname."', '".$prefix.$field."', '".$type . "', $required,'". $this->stripEndColon(translate($displayName,$this->sugarbean->module_dir)) . "' );\n";
	}

	function addFieldRange($field, $type,$displayName, $required, $prefix='',$min, $max){
		$this->script .= "addToValidateRange('".$this->formname."', '".$prefix.$field."', '".$type . "', $required,'".$this->stripEndColon(translate($displayName,$this->sugarbean->module_dir)) . "', $min, $max );\n";
	}
	
	function addFieldIsValidDate($field, $type, $displayName, $msg, $required, $prefix='') {
		$name = $prefix.$field;
		$req = ($required) ? 'true' : 'false';
		$this->script .= "addToValidateIsValidDate('{$this->formname}', '{$name}', '{$type}', {$req}, '{$msg}');\n";
	}

	function addFieldIsValidTime($field, $type, $displayName, $msg, $required, $prefix='') {
		$name = $prefix.$field;
		$req = ($required) ? 'true' : 'false';
		$this->script .= "addToValidateIsValidTime('{$this->formname}', '{$name}', '{$type}', {$req}, '{$msg}');\n";
	}

	function addFieldDateBefore($field, $type,$displayName, $required, $prefix='',$compareTo){
		$this->script .= "addToValidateDateBefore('".$this->formname."', '".$prefix.$field."', '".$type . "', $required,'".$this->stripEndColon(translate($displayName,$this->sugarbean->module_dir)) . "', '$compareTo' );\n";
	}

	function addFieldDateBeforeAllowBlank($field, $type, $displayName, $required, $prefix='', $compareTo, $allowBlank='true'){
		$this->script .= "addToValidateDateBeforeAllowBlank('".$this->formname."', '".$prefix.$field."', '".$type . "', $required,'".$this->stripEndColon(translate($displayName,$this->sugarbean->module_dir)) . "', '$compareTo', '$allowBlank' );\n";
	}
	
	function addToValidateBinaryDependency($field, $type, $displayName, $required, $prefix='',$compareTo){
		$this->script .= "addToValidateBinaryDependency('".$this->formname."', '".$prefix.$field."', '".$type . "', $required,'".$this->stripEndColon(translate($displayName,$this->sugarbean->module_dir)) . "', '$compareTo' );\n";
	}
    
    function addToValidateComparison($field, $type, $displayName, $required, $prefix='',$compareTo){
        $this->script .= "addToValidateComparison('".$this->formname."', '".$prefix.$field."', '".$type . "', $required,'".$this->stripEndColon(translate($displayName,$this->sugarbean->module_dir)) . "', '$compareTo' );\n";
    }
    
    function addFieldIsInArray($field, $type, $displayName, $required, $prefix, $arr, $operator){
    	$name = $prefix.$field;
		$req = ($required) ? 'true' : 'false';
		$json = getJSONobj();
		$arr = $json->encode($arr);
		$this->script .= "addToValidateIsInArray('{$this->formname}', '{$name}', '{$type}', {$req}, '".$this->stripEndColon(translate($displayName,$this->sugarbean->module_dir))."', '{$arr}', '{$operator}');\n";
    }

	function addAllFields($prefix,$skip_fields=null, $translate = false){
		if (!isset($skip_fields))
		{
			$skip_fields = array();
		}
		foreach($this->sugarbean->field_name_map as $field=>$value){
			if (!isset($skip_fields[$field]))
			{
			    if(isset($value['type']) && ($value['type'] == 'datetimecombo' || $value['type'] == 'datetime')) {
			    	$isRequired = (isset($value['required']) && $value['required']) ? 'true' : 'false';
			        $this->addSpecialField($value['name'] . '_date', $value['name'], 'datetime', $isRequired);
			    } else if (isset($value['type'])) {
					if ($value['type'] != 'link') {						
			  			$this->addField($field, '', $prefix,'',$translate);
					}
				}
			}
		}
	}

	function getScript($showScriptTag = true){
		$tempScript = $this->script;
		$this->script = "";
		if($showScriptTag){
			$this->script = "<script type=\"text/javascript\">\n";
		}
		
		$this->script .= $tempScript;

		if($showScriptTag){
			$this->script .= "</script>";
		}
		return $this->script;
	}
    
    function buildStringToTranslateInSmarty(
        $string
        )
    {
        if ( is_array($string) ) {
            $returnstring = '';
            foreach ( $string as $astring )
                $returnstring .= $this->buildStringToTranslateInSmarty($astring);
            return $returnstring;
        }
            
        return "{/literal}{sugar_translate label='$string' module='{$this->sugarbean->module_dir}'}{literal}";
    }
}
?>
