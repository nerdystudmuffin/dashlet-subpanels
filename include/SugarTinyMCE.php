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
 * *******************************************************************************/
/*********************************************************************************

 * Description:
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc. All Rights
 * Reserved. Contributor(s): ______________________________________..
 *********************************************************************************/


/**
 * PHP wrapper class for Javascript driven TinyMCE WYSIWYG HTML editor
 */
class SugarTinyMCE {
	var $jsroot = "include/javascript/tiny_mce/";
	var $buttonConfig = "code,help,separator,bold,italic,underline,strikethrough,separator,justifyleft,justifycenter,justifyright,
	                     justifyfull,separator,forecolor,backcolor,separator,styleselect,formatselect,fontselect,fontsizeselect,
	                     ";
	var $buttonConfig2 = "cut,copy,paste,pastetext,pasteword,selectall,separator,search,replace,separator,bullist,numlist,separator,
	                      outdent,indent,separator,ltr,rtl,separator,undo,redo,separator,
	                      link,unlink,anchor,image,separator,sub,sup,separator,charmap,visualaid";
	var $buttonConfig3 = "tablecontrols,separator,advhr,hr,removeformat,separator,insertdate,inserttime,separator,preview";
	var $defaultConfig = array(
	    'convert_urls' => false,
	    'height' => 300,
		'width'	=> '100%',
		'theme'	=> 'advanced',
		'theme_advanced_toolbar_align' => "left",
		'theme_advanced_toolbar_location'	=> "top",
		'theme_advanced_buttons1'	=> "",
		'theme_advanced_buttons2'	=> "",
		'theme_advanced_buttons3'	=> "",
		'strict_loading_mode'	=> true,
		'mode'	=> 'exact',
	    'plugins' => 'advhr,insertdatetime,table,preview,paste,searchreplace,directionality',
		'elements'	=> '',
        'extended_valid_elements' => 'style,hr[class|width|size|noshade]',
	);
	
	
	/**
	 * Sole constructor
	 */
	function SugarTinyMCE() {
		
	}
	
	/**
	 * Returns the Javascript necessary to initialize a TinyMCE instance for a given <textarea> or <div>
	 * @param string target Comma delimited list of DOM ID's, <textarea id='someTarget'>
	 * @return string 
	 */
	function getInstance($targets = "") {
		global $json;
		
		if(empty($json)) {
			$json = getJSONobj();
		}
		
		$config = $this->defaultConfig;
		$config['elements'] = $targets;
		$config['theme_advanced_buttons1'] = $this->buttonConfig;
		$config['theme_advanced_buttons2'] = $this->buttonConfig2;
		$config['theme_advanced_buttons3'] = $this->buttonConfig3;
		$jsConfig = $json->encode($config);
		
		$instantiateCall = '';
		if (!empty($targets)) {
			$exTargets = explode(",", $targets);
			foreach($exTargets as $instance) {
				//$instantiateCall .= "tinyMCE.execCommand('mceAddControl', false, document.getElementById('{$instance}'));\n";
			} 
		}
		$path = getJSPath('include/javascript/tiny_mce/tiny_mce.js');
		$ret =<<<eoq
<script type="text/javascript" language="Javascript" src="$path"></script>
<script type="text/javascript" language="Javascript">
	tinyMCE.init({$jsConfig});
	{$instantiateCall}	
</script>

eoq;
		return $ret;
	}
	
    function getConfig() {
        global $json;
        
        if(empty($json)) {
            $json = getJSONobj();
        }
        
        $config = $this->defaultConfig;
        $config['theme_advanced_buttons1'] = $this->buttonConfig;
        $config['theme_advanced_buttons2'] = $this->buttonConfig2;
        $config['theme_advanced_buttons3'] = $this->buttonConfig3;
        $jsConfig = $json->encode($config);
        return "var tinyConfig = ".$jsConfig.";";
        
    }
} // end class def
