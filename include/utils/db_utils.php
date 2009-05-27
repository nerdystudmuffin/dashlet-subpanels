<?php
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



if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*
 * The last parmeter should be used to specify parameters for oracle. it also acts has a complete override
 * for the additional_parameters array.
 */
function db_convert($string, $type, $additional_parameters=array(),$additional_parameters_oracle_only=array()){
	global $sugar_config;
	
	//converts the paramters array into a comma delimited string.
	$additional_parameters_string='';
	foreach ($additional_parameters as $value) {
		$additional_parameters_string.=",".$value;
	}
	$additional_parameters_string_oracle_only='';
	foreach ($additional_parameters_oracle_only as $value) {
		$additional_parameters_string_oracle_only.=",".$value;
	}
	
	if($sugar_config['dbconfig']['db_type']== "mysql"){
		switch($type){
			case 'today': return "CURDATE()";	
			case 'left': return "LEFT($string".$additional_parameters_string.")";
			case 'date_format': return "DATE_FORMAT($string".$additional_parameters_string.")";
			case 'datetime': return "DATE_FORMAT($string, '%Y-%m-%d %H:%i:%s')";
			case 'IFNULL': return "IFNULL($string".$additional_parameters_string.")";
            case 'CONCAT': return "CONCAT($string,".implode(",",$additional_parameters).")";
			
		}
		return "$string";
	}else if($sugar_config['dbconfig']['db_type']== "oci8"){
















	}elseif($sugar_config['dbconfig']['db_type']== "mssql")
	{
		switch($type){
			case 'today': return "GETDATE()";	
			case 'left': return "LEFT($string".$additional_parameters_string.")";			
			case 'date_format': 
            if(!empty($additional_parameters) && in_array("'%Y-%m'", $additional_parameters)) {
               return "CONVERT(varchar(7),". $string . ",120)";
            } else {
               return "CONVERT(varchar(10),". $string . ",120)";
            }
			case 'datetime': 
                if(!($GLOBALS['db'] instanceOf SqlsrvManager)) {
                    return "CONVERT(varchar(20)," . $string . ",120)";	
                }
                break;
			case 'IFNULL': return "ISNULL($string".$additional_parameters_string.")";		
	        case 'CONCAT': return "$string+".implode("+",$additional_parameters);
    
    	}
		return "$string";
	}
	
	return "$string";
}

function db_concat($table, $fields){
	global $sugar_config;
	$ret = '';
	if($sugar_config['dbconfig']['db_type']== "mysql"){
		foreach($fields as $index=>$field){
			if(empty($ret))$ret = "CONCAT(". db_convert($table.".".$field,'IFNULL', array("''"));	
			else $ret.=	",' ',".db_convert($table.".".$field,'IFNULL', array("''"));
		}	
		if (!empty($ret)) $ret.=')';

	} else if($sugar_config['dbconfig']['db_type']== "oci8"){






	}else if($sugar_config['dbconfig']['db_type']== "mssql")
	{
		foreach($fields as $index=>$field)
		{
			if(empty($ret))$ret =  db_convert($table.".".$field,'IFNULL', array("''"));	
			else $ret.=	" + ' ' + ".db_convert($table.".".$field,'IFNULL', array("''"));
		}	
		if (!empty($ret)) $ret.='';

	}
	return $ret;
}
	

function from_db_convert($string, $type){

	global $sugar_config;
	if($sugar_config['dbconfig']['db_type']== "mysql"){
		return $string;
	}else if($sugar_config['dbconfig']['db_type']== "oci8"){






	}
	else if($sugar_config['dbconfig']['db_type']== "mssql")
	{
			switch($type){
			case 'date': return substr($string, 0,11);
			case 'time': return substr($string, 11);
		}
		return $string;
	}
	return $string;
	
	
}

$toHTML = array(
	'"' => '&quot;',
	'<' => '&lt;',
	'>' => '&gt;',
	"'" => '&#039;',
);
$GLOBALS['toHTML_keys'] = array_keys($toHTML);
$GLOBALS['toHTML_values'] = array_values($toHTML);

/**
 * Replaces specific characters with their HTML entity values
 * @param string $string String to check/replace
 * @param bool $encode Default true
 * @return string
 *
 * @todo Make this utilize the external caching mechanism after re-testing (see
 *       log on r25320).
 */
function to_html($string, $encode=true){
	if (empty($string)) {
		return $string;
	}
	static $cache = array();
	global $toHTML;
	if (isset($cache['c'.$string])) {
	    return $cache['c'.$string];
	}
	
	$cache_key = 'c'.$string;
	
	if($encode && is_string($string)){//$string = htmlentities($string, ENT_QUOTES);
		/*
		 * cn: bug 13376 - handle ampersands separately 
		 * credit: ashimamura via bug portal
		 */ 
		//$string = str_replace("&", "&amp;", $string);

		if(is_array($toHTML)) { // cn: causing errors in i18n test suite ($toHTML is non-array)
			$string = str_replace(
				$GLOBALS['toHTML_keys'],
				$GLOBALS['toHTML_values'],
				$string
			);
		}
	}
	$cache[$cache_key] = $string;
	return $cache[$cache_key];
}

/**
 * Replaces specific HTML entity values with the true characters
 * @param string $string String to check/replace
 * @param bool $encode Default true
 * @return string
 */
function from_html($string, $encode=true) {
    if (!is_string($string) || !$encode) {
        return $string;
    }

	global $toHTML;
    static $toHTML_values = null;
    static $toHTML_keys = null;
    static $cache = array();
    if (!isset($toHTML_values) || !empty($GLOBALS['from_html_cache_clear'])) {
        $toHTML_values = array_values($toHTML);
        $toHTML_keys = array_keys($toHTML);
    }
	
    if (!isset($cache[$string])) {
        $cache[$string] = str_replace($toHTML_values, $toHTML_keys, $string);
    }
    return $cache[$string];
}

function run_sql_file( $filename ){
    if( !is_file( $filename ) ){
        print( "Could not find file: $filename <br>" );
        return( false );
    }

    

    $fh         = sugar_fopen( $filename,'r' );
    $contents   = fread( $fh, filesize($filename) );
    fclose( $fh );

    $lastsemi   = strrpos( $contents, ';') ;
    $contents   = substr( $contents, 0, $lastsemi );
    $queries    = split( ';', $contents );
    $db         = DBManagerFactory::getInstance();

    foreach( $queries as $query ){
        if( !empty($query) ){




			if($db->dbType == 'oci8')
			{



			}
			else
			{
				$db->query( $query.';', true, "An error has occured while running.<br>" );
			}
        }
    }
    return( true );
}

function isTypeBoolean($type) {

	switch ($type){
  		case 'bool':
			return true;
			break;
	}
	return false;
}

function getBooleanValue($val) {
	
	if (empty($val) or $val=='off') {
		return false;
	}
	return true;
}
function isTypeNumber($type) {

	switch ($type){
  		case 'decimal':
  		case 'int':
  		case 'double':
  		case 'float':
  		case 'uint':
  		case 'ulong':
  		case 'long':
  		case 'short':
			return true;
			break;
	}
	return false;
}

/* return true if the value if empty*/
function emptyValue($val, $type){


	if (empty($val)) return true;

	switch ($type){

  		case 'decimal':
  		case 'int':
  		case 'double':
  		case 'float':
  		case 'uint':
  		case 'ulong':
  		case 'long':
  		case 'short':

			if ($val == 0) {		
				return true;
			} else {
				return false;
			}		  
			break;
        case 'date':
        	if ($val == '0000-00-00')
				return true;
			else
				return false;
			break;

	}	
	
	return false;
	
	/* other dbtypes
	  	  case 'bool':
		  case 'varchar':
		  case 'enum':
          case 'char':
          case 'id':
          case 'date':
          case 'text':        
          case 'blob':
          case 'clob':
          case 'date':
		  case 'datetime':
		  case 'time':
		*/
}	


/**
 * Used in OracleHelper to generate SEQUENCE names. This could also be used
 * by an upgrade script to upgrading sequences.  It will take in a name
 * and md5 the name and only return $length characters.
 *
 * @param string $name - name of the orignal sequence
 * @param int $length - length of the desired md5 sequence.
 * @return string
 */
function generateMD5Name($name, $length = 6){
	$md5_name = md5($name);
	//this should generate a 32 character string
	//now that we have this md5 representation, let's
	//cut it so we only have $length number of chars
	return substr($md5_name, 0, $length);
}

/**
 * Generate an Oracle SEQUENCE name. If the length of the sequence names exceeds a certain amount
 * we will use an md5 of the field name to shorten.
 *
 * @param string $table
 * @param string $field_name
 * @param boolean $upper_case
 * @return string
 */
function getSequenceName($table, $field_name, $upper_case = true){
	$sequence_name = $table. '_' .$field_name . '_seq';
	if(strlen($sequence_name) > 30)
		$sequence_name = $table. '_' .generateMD5Name($field_name) . '_seq';
	if($upper_case)
		$sequence_name = strtoupper($sequence_name);
	return $sequence_name;
}
?>
