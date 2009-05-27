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

 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/


require_once('include/OutboundEmail/OutboundEmail.php');

// save Outbound (SMTP/Sendmail) settings  #Bug 20033 Ensure data for Outbound email exists before trying to update the system mailer.
if(isset($_REQUEST['mail_sendtype'])) {
	$oe = new OutboundEmail();
	$oe->populateFromPost();
	$oe->saveSystem();
}

$focus = new Administration();

if(isset($_POST['tracking_entities_location_type'])) {
	if ($_POST['tracking_entities_location_type'] != '2') {
		unset($_POST['tracking_entities_location']);
		unset($_POST['tracking_entities_location_type']);
	}
}
// cn: handle mail_smtpauth_req checkbox on/off (removing double reference in the form itself
if(!isset($_POST['mail_smtpauth_req'])) { $_POST['mail_smtpauth_req'] = 0; }
$focus->saveConfig();

// save User defaults for emails
if(isset($_REQUEST['email_default_client'])) $sugar_config['email_default_client'] = $_REQUEST['email_default_client'];
if(isset($_REQUEST['email_default_editor'])) $sugar_config['email_default_editor'] = $_REQUEST['email_default_editor'];
if(isset($_REQUEST['default_email_charset'])) $sugar_config['default_email_charset'] = $_REQUEST['default_email_charset'];
$sugar_config['email_default_delete_attachments'] = (isset($_REQUEST['email_default_delete_attachments'])) ? true : false;
// save outbound raw-save setting
$sugar_config['email_outbound_save_raw'] = ($_REQUEST['email_outbound_save_raw'] == 1) ? true : false; 

///////////////////////////////////////////////////////////////////////////////
////	SECURITY
$security = array();
if(isset($_REQUEST['applet'])) $security['applet'] = 'applet'; 
if(isset($_REQUEST['base'])) $security['base'] = 'base';
if(isset($_REQUEST['embed'])) $security['embed'] = 'embed'; 
if(isset($_REQUEST['form'])) $security['form'] = 'form'; 
if(isset($_REQUEST['frame'])) $security['frame'] = 'frame'; 
if(isset($_REQUEST['frameset'])) $security['frameset'] = 'frameset'; 
if(isset($_REQUEST['iframe'])) $security['iframe'] = 'iframe'; 
if(isset($_REQUEST['import'])) $security['import'] = '\?import';
if(isset($_REQUEST['layer'])) $security['layer'] = 'layer'; 
if(isset($_REQUEST['link'])) $security['link'] = 'link'; 
if(isset($_REQUEST['object'])) $security['object'] = 'object'; 
if(isset($_REQUEST['script'])) $security['script'] = 'script'; 
if(isset($_REQUEST['style'])) $security['style'] = 'style'; 
if(isset($_REQUEST['xmp'])) $security['xmp'] = 'xmp'; 

$sugar_config['email_xss'] = base64_encode(serialize($security));

if(isset($_REQUEST['preserve_raw']) && !empty($_REQUEST['preserve_raw'])) {
	$sugar_config['email_preserve_raw'] = 1;
}
////	SECURITY
///////////////////////////////////////////////////////////////////////////////

ksort($sugar_config);
write_array_to_file('sugar_config', $sugar_config, 'config.php');

header("Location: index.php?action={$_POST['return_action']}&module={$_POST['return_module']}");
?>
