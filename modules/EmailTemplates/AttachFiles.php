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
 //Request object must have these property values:
 //		Module: module name, this module should have a file called TreeData.php
 //		Function: name of the function to be called in TreeData.php, the function will be called statically.
 //		PARAM prefixed properties: array of these property/values will be passed to the function as parameter.


require_once('include/JSON.php');
require_once('include/upload_file.php');


//process request parameters. consider following parameters.
//function, and all parameters prefixed with PARAM.
//PARAMT_ are tree level parameters.
//PARAMN_ are node level parameters.
//module  name and function name parameters are the only ones consumed
//by this file..
//foreach ($_FILES['uploadfile'] as $key=>$value) {

//$GLOBALS['log']->fatal("AttachFiles: KEY ".$key);
//$GLOBALS['log']->fatal("AttachFiles: Value".$value);

$GLOBALS['log']->fatal($_FILES);

        /*
         $origfilename = $_FILES['email_attachment0']['name'];
         $origfilename1 = $_FILES["email_attachment1"]["name"];
         $filetype = $_FILES["uploadfile"]["type"];
         $filetempname = $_FILES["uploadfile"]["tmp_name"];
         $file_error   = $_FILES["uploadfile"]["error"];
         $filename = explode(".", $_FILES["uploadfile"]["name"]);         
         $filesize =$_FILES["uploadfile"]["size"];
        */
 //$GLOBALS['log']->fatal("Sugar path: config ".$_FILES);
         
         //$filenameext = $filename[count($filename)-1];
         //unset($filename[count($filename)-1]);
         //$filename = implode(".", $filename);
         //$filename = substr($filename, 0, 15).".".$filenameext;
         $file_ext_allow = FALSE;	
	//$GLOBALS['log']->fatal("AttachFiles: FILE1 ".$origfilename." ".$filename);
	//$GLOBALS['log']->fatal("AttachFiles: FILE2 ".$filetempname." ".$filesize);
 /*
         $fp = fopen($filetempname, 'r');
         $content = fread($fp, $filesize);
         $content = addslashes($content);
         fclose($fp); 
*/


// cn: bug 11012 - fixed some MIME types not getting picked up.  Also changed array iterator.
$imgType = array('image/gif', 'image/png', 'image/bmp', 'image/jpeg', 'image/jpg', 'image/pjpeg');
foreach($_FILES as $k => $file) {
	if(in_array(strtolower($_FILES[$k]['type']), $imgType)) {
		$dest = $GLOBALS['sugar_config']['cache_dir'].'images/'.$_FILES[$k]['name'];
		if(is_uploaded_file($_FILES[$k]['tmp_name'])) {
			move_uploaded_file($_FILES[$k]['tmp_name'], $dest);
		}
	}
}

  //if( copy($filetempname,$dest)){
  	//$GLOBALS['log']->fatal($sugar_config['upload_dir']);
  //}
	$ret[0]=$origfilename;
	$ret[1]=$origfilename1;
	//$ret[1]=$filetype;
	//$ret[2]=$filetempname;
	//$ret[3]=$file_error; 
	//$ret[4]= $filesize;
	
	
//$GLOBALS['log']->fatal($ret);
	//$ret[2]=$content;
	
	//$ret[5] = $_FILES["uploadfile"];
	

if (!empty($ret)) {	
	$json = getJSONobj();
	print $json->encode($ret);	
	//return the parameters
}
sugar_cleanup();
exit();
?>
