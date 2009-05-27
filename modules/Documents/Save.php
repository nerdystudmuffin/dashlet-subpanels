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

 * Description:  Base Form For Notes
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/


require_once('include/formbase.php');
require_once('include/upload_file.php');


global $mod_strings;
$mod_strings = return_module_language($current_language, 'Documents');

$prefix='';

$do_final_move = 0;

$Revision = new DocumentRevision();
$Document = new Document();
if (isset($_REQUEST['record'])) {
	$Document->retrieve($_REQUEST['record']);
}
if(!$Document->ACLAccess('Save')){
		ACLController::displayNoAccess(true);
		sugar_cleanup(true);
}
	
$Document = populateFromPost($prefix, $Document);


//if (!isset($_POST[$prefix.'is_template'])) $Document->is_template = 0;
//else $Document->is_template = 1;



$upload_file = new UploadFile('uploadfile');

$do_final_move = 0;

//$_FILES['uploadfile']['name'] = $_REQUEST['escaped_document_name'];
if (isset($_FILES['uploadfile']) && $upload_file->confirm_upload())
{
    $Revision->filename = $upload_file->get_stored_file_name();
    $Revision->file_mime_type = $upload_file->mime_type;
	$Revision->file_ext = $upload_file->file_ext;
 	$do_final_move = 1;
} else {
	if (!empty($_REQUEST['old_id'])) {
		
		//populate the document revision based on the old_id
		$old_revision = new DocumentRevision();
		$old_revision->retrieve($_REQUEST['old_id']);

    	$Revision->filename = $old_revision->filename;
    	$Revision->file_mime_type = $old_revision->file_mime_type;
		$Revision->file_ext = $old_revision->file_ext;
	}
}

if (isset($Document->id)) {
	//save document
	$return_id = $Document->save();
} else {
	//save document
	$return_id = $Document->save();

	//save revision.
	$Revision->change_log = $mod_strings['DEF_CREATE_LOG'];
	$Revision->revision = $Document->revision;
	$Revision->document_id = $Document->id;
	$Revision->save();
	
	//update document with latest revision id
	$Document->process_save_dates=false; //make sure that conversion does not happen again.
	$Document->document_revision_id = $Revision->id;
	$Document->save();
	
	//set relationship field values if contract_id is passed (via subpanel create)
	if (!empty($_POST['contract_id'])) {
		$save_revision['document_revision_id']=$Document->document_revision_id;	
		$Document->load_relationship('contracts');
		$Document->contracts->add($_POST['contract_id'],$save_revision);
	}
    
	if ((isset($_POST['load_signed_id']) and !empty($_POST['load_signed_id']))) {
		$query="update linked_documents set deleted=1 where id='".$_POST['load_signed_id']."'";
		$Document->db->query($query);
	}
}

$return_id = $Document->id;

if ($do_final_move) {
	$upload_file->final_move($Revision->id);
}
else if ( ! empty($_REQUEST['old_id'])) {
   	$upload_file->duplicate_file($_REQUEST['old_id'], $Revision->id, $Revision->filename);
}

$GLOBALS['log']->debug("Saved record with id of ".$return_id);
handleRedirect($return_id, "Documents");
?>
