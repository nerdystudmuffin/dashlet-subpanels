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
 * Description:
 ********************************************************************************/
 // hack to allow "&", "%" and "+" through a $_GET var
// set by ie_test_open_popup() javascript call
foreach($_REQUEST as $k => $v) {
	$v = str_replace('::amp::', '&', $v);
	$v = str_replace('::plus::', '+', $v);
	$v = str_replace('::percent::', '%', $v);
	$_REQUEST[$k] = $v;
}

if(ob_get_level() > 0) {
	ob_end_clean();
}

if(ob_get_level() < 1) {
	ob_start();
}



require_once('modules/InboundEmail/language/en_us.lang.php');
global $theme;




// GLOBALS
global $mod_strings;
global $app_strings;
global $app_list_strings;
global $current_user;
global $sugar_config;

$jsCustomVersion = $sugar_config['js_custom_version'];
$sugar_version = $sugar_version;
$title				= '';
$msg				= '';
$tls				= '';
$cert				= '';
$ssl				= '';
$notls				= '';
$novalidate_cert	= '';
$useSsl				= false;
$deletedFoldersList = "";

///////////////////////////////////////////////////////////////////////////////
////	TITLES

$popupBoolean = false;
if (isset($_REQUEST['target']) && $_REQUEST['target'] == 'Popup') {
	$popupBoolean = true;
}
if (isset($_REQUEST['target1']) && $_REQUEST['target1'] == 'Popup') {
	$popupBoolean = true;
}

if($popupBoolean) {
	$title = $mod_strings['LBL_SELECT_SUBSCRIBED_FOLDERS'];
	$msg = $mod_strings['LBL_TEST_WAIT_MESSAGE'];
}

$subdcriptionFolderHelp = $app_strings['LBL_EMAIL_SUBSCRIPTION_FOLDER_HELP'];

if(isset($_REQUEST['ssl']) && ($_REQUEST['ssl'] == "true" || $_REQUEST['ssl'] == 1)) {
	$useSsl = true;
}

$searchField = $_REQUEST['searchField'];
$multipleString = "multiple=\"true\"";
if (!empty($searchField)) {
	$subdcriptionFolderHelp = "";
	$multipleString = "";
	if ($searchField == 'trash') {
		$title = $mod_strings['LBL_SELECT_TRASH_FOLDERS'];
	} else {
		$title = $mod_strings['LBL_SELECT_SENT_FOLDERS'];
	} // else

} // else

////	END TITLES
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
////	COMMON CODE
echo "
<HTML>
	<HEAD>
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=ISO-8859-1\">
		<title>SugarCRM - Commercial Open Source CRM</title>
		".SugarThemeRegistry::current()->getCSS()."
		<script type=\"text/javascript\">
				function setMailbox(box) {
					var inboundmailboxes = document.getElementById('inboundmailboxes');
					var selectedmbox = '';
					var j = 0;
					for (var i = 0 ; i < inboundmailboxes.options.length ; i++) {
						if (inboundmailboxes.options[i].selected) {
							if (j != 0) {
								selectedmbox = selectedmbox + ',';
							} // if
							selectedmbox = selectedmbox + inboundmailboxes.options[i].value;
							j++;
						} // if
					} // for
					var searchFieldValue = document.getElementById('searchField').value;
					if (searchFieldValue.length > 0) {
						if (searchFieldValue == 'trash') {
							var tf = opener.document.getElementById(\"trashFolder\");
							tf.value = selectedmbox;
						} else if(searchFieldValue == 'sent') {
							var sf = opener.document.getElementById(\"sentFolder\");
							sf.value = selectedmbox;
						} // else
					} else {
						var mb = opener.document.getElementById(\"mailbox\");
						mb.value = selectedmbox;
					}
					window.close();
				}
		</script>

	</HEAD>
	<body onload='switchMsg();'>";
echo '<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td>&nbsp;
				</td>
				<td>
					<h3>&nbsp;'.$title.'</h3>
				</td>
			</tr>
			<tr>
				<td>&nbsp;
				</td>
				<td valign="top">
					<div id="msg">
					'.$msg.'
					</div>
				</td>
			</tr>';
if (!empty($subdcriptionFolderHelp)) {
echo '<tr>
				<td>&nbsp;
				</td>
				<td>&nbsp;
				</td>
			</tr>
			<tr align="center">
				<td>&nbsp;
				</td>
				<td>'.$subdcriptionFolderHelp.'
				</td>
			</tr>';
} // if
echo '<tr>
				<td>&nbsp;
				</td>
				<td valign="top">
					<div id="deletedFoldersList" style="display:none;">
					'.$deletedFoldersList.'
					</div>
				</td>
			</tr>
			<tr align="center">
				<td>&nbsp;
				</td>
				<td  valign="top">
					<select '.$multipleString.' size="12" name="inboundmailboxes" id="inboundmailboxes">
					</select>
				</td>
			</tr>
			<tr>
				<td>&nbsp;
				</td>
				<td>&nbsp;
				</td>
			</tr>
			<tr align="center">
				<td>&nbsp;
				</td>
				<td>
					<input type="button" style="" class="button" value="'.$app_strings['LBL_EMAIL_OK'].'" onclick="setMailbox();">
					<input type="button" class="button" value="'.$app_strings['LBL_EMAIL_CANCEL'].'" onclick="window.close();">
				</td>
			</tr>
			<tr>
				<td>&nbsp;
				</td>
				<td>&nbsp;
				</td>
			</tr>';
echo '	</table>';

sleep(1);
ob_flush();
flush();
ob_start();

ob_flush();
flush();
ob_start();


$ie					= new InboundEmail();
$ie->email_user		= $_REQUEST['email_user'];
$ie->server_url		= $_REQUEST['server_url'];
$ie->port			= $_REQUEST['port'];
$ie->protocol		= $_REQUEST['protocol'];
$ie->email_password	= str_rot13($_REQUEST['email_password']);
//$ie->mailbox		= $_REQUEST['mailbox'];

$ie->mailbox		= 'INBOX';

if($popupBoolean) {
	$returnArray = $ie->getFoldersListForMailBox();
	$foldersList = $returnArray['foldersList'];
	if ($returnArray['status']) {
		$msg = $returnArray['statusMessage'];
		$requestMailBox = explode(",", $_REQUEST['mailbox']);
		$foldersListArray = explode(",", $foldersList);
		$deletedFoldersString = "";
		$count = 0;
		if (!empty($requestMailBox) && !empty($foldersListArray)) {
			foreach($requestMailBox as $mailbox) {
				if (!in_array($mailbox, $foldersListArray)) {
					if ($count != 0) {
						$deletedFoldersString = $deletedFoldersString . " ,";
					}
					$deletedFoldersString = $deletedFoldersString . $mailbox;
					$count++;
				}
			} // foreach
		} // if
		if (!empty($deletedFoldersString)) {
			$deletedFoldersList = $mod_strings['LBL_DELETED_FOLDERS_LIST'];
			$deletedFoldersList = sprintf($deletedFoldersList, $deletedFoldersString);
		}
	} else {
		$msg = $returnArray['statusMessage'];
	}
}
////	END COMMON CODE
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
////	COMPLETE RENDERING OF THE POPUP
echo '<input type="hidden" id="returnstatus" name="returnstatus" value="'. $returnArray['status'] .'">';
echo '<input type="hidden" id="foldersList" name="foldersList" value="'. $foldersList .'">';
echo '<input type="hidden" id="selectedfoldersList" name="selectedfoldersList" value="'. implode(",", $requestMailBox) .'">';
echo '<input type="hidden" id="searchField" name="searchField" value="'. $searchField .'">';

echo '
<script type="text/javascript">
	function switchMsg() {
		if(typeof(document.getElementById("msg")) != "undefined") {
			document.getElementById("msg").innerHTML = "'.$msg.'";
			var deletedFoldersList = document.getElementById("deletedFoldersList");
			deletedFoldersList.innerHTML = "'. $deletedFoldersList .'";
			if (deletedFoldersList.innerHTML.length > 0) {
				deletedFoldersList.style.display = "";
			} // if
			var selectedFoldersListObject = new Object();
			var selectedFoldersListArray = document.getElementById("selectedfoldersList").value.split(",");
			for (var j = 0 ; j < selectedFoldersListArray.length ; j++) {
				selectedFoldersListObject[selectedFoldersListArray[j]] = selectedFoldersListArray[j];
			} // for
			if (document.getElementById("returnstatus").value == "1") {
				var foldersList = document.getElementById("foldersList").value;
				var foldersArray = foldersList.split(",");
				var inboundmailboxes = document.getElementById("inboundmailboxes");
				for (var i = 0 ; i < foldersArray.length ; i++) {
					var opt = new Option(foldersArray[i], foldersArray[i]);
					if (selectedFoldersListObject[foldersArray[i]] != null) {
						opt.selected = true;
					}
					inboundmailboxes.options.add(opt);
				} // for
			} // if
			var selectdFoldersValue = document.getElementById("selectedfoldersList").value;
			var searchFieldValue = document.getElementById("searchField").value;
			if (selectdFoldersValue.length <= 0) {
				if (searchFieldValue.length > 0) {
					if (searchFieldValue == "trash") {
						var inboundmailboxes = document.getElementById("inboundmailboxes");
						for (var i = 0 ; i < inboundmailboxes.options.length ; i++) {
							if ((inboundmailboxes.options[i].text.search(/trash/i) != -1) ||
								(inboundmailboxes.options[i].text.search(/delete/i) != -1)) {

								inboundmailboxes.options[i].selected = true;
								break;
							}
						} // for
					} // if
					if (searchFieldValue == "sent") {
						var inboundmailboxes = document.getElementById("inboundmailboxes");
						for (var i = 0 ; i < inboundmailboxes.options.length ; i++) {
							if (inboundmailboxes.options[i].text.search(/sent/i) != -1) {

								inboundmailboxes.options[i].selected = true;
								break;
							}
						} // for
					} // if
				} // if
			} // if
		}
	}
</script>
	</body>
</html>';
ob_end_flush();
?>
