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

 * Description:  Base form for contact
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

class FeedFormBase  {

function checkForDuplicates($prefix){
	require_once('include/formbase.php');
	
	$focus = new Feed();
	if(!checkRequired($prefix, array_keys($focus->required_fields))){
		return null;
	}
	$query = '';
	$baseQuery = 'select id,url  from feeds where deleted!=1 and (';

	if(isset($_POST[$prefix.'url']) && !empty($_POST[$prefix.'url'])){
	$query = $baseQuery ."  url = '". $_POST[$prefix.'url'] ."'";
	}

	if(!empty($query)){
		$rows = array();
		global $db;
		$result = $db->query($query.');');
		if($db->getRowCount($result) == 0){
			return null;
		}
		for($i = 0; $i < $db->getRowCount($result); $i++){
			$rows[$i] = $db->fetchByAssoc($result, $i);
		}
		return $rows;
	}
	return null;
}


function buildTableForm($rows, $mod=''){
	if(!empty($mod)){
	global $current_language;
	$mod_strings = return_module_language($current_language, $mod);
	}else global $mod_strings;
	global $app_strings;
	$cols = sizeof($rows[0]) * 2 + 1;
	$form = '<table width="100%"><tr><td>'.$mod_strings['MSG_DUPLICATE']. '</td></tr><tr><td height="20"></td></tr></table>';
	$form .= "<form action='index.php' method='post' name='dupFeed'><input type='hidden' name='selectedFeed' value=''>";
	 $form .= get_form_header($mod_strings['LBL_DUPLICATE'],"", '');
	$form .= "<table width='100%' cellpadding='0' cellspacing='0'>	<tr >	<td > &nbsp;</td>";


	require_once('include/formbase.php');
	$form .= getPostToForm();

	if(isset($rows[0])){
		foreach ($rows[0] as $key=>$value){
			if($key != 'id'){
					$form .= "<td scope='col' >". $mod_strings[$mod_strings['db_'.$key]]. "</td>";
			}
		}
		$form .= "</tr>";
	}
	$rowColor = 'oddListRowS1';
	foreach($rows as $row) {
		$form .= "<tr class='$rowColor'>";
		$form .= "<td width='1%' nowrap='nowrap'><a href='#' onClick=\"document.dupFeeds.selectedFeed.value='${row['id']}';document.dupFeeds.submit() \">[${app_strings['LBL_SELECT_BUTTON_LABEL']}]</a>&nbsp;&nbsp;</td>";
		$wasSet = false;

		foreach ($row as $key=>$value) {
            if($key != 'id') {
                if(!$wasSet) {
                    $form .= "<td scope='row'><a target='_blank' href='index.php?module=Feeds&action=DetailView&record=${row['id']}'>$value</a></td>";
                    $wasSet = true;
                }
                else {
                    $form .= "<td><a target='_blank' href='index.php?module=Feeds&action=DetailView&record=${row['id']}'>$value</a></td>";			
                }
            }
		}

		if($rowColor == 'evenListRowS1'){
			$rowColor = 'oddListRowS1';
		}else{
			 $rowColor = 'evenListRowS1';
		}
		$form .= "</tr>";
	}
		$form .= "<tr ><td colspan='$cols' class='blackline'></td></tr>";
	$form .= "</table><br><input type='submit' class='button' name='ContinueFeed' value='${app_strings['LBL_CREATE_BUTTON_LABEL']} ${mod_strings['LNK_NEW_CONTACT']}'></form>";
	return $form;





}
function getWideFormBody($prefix, $mod='',$formname='',  $contact = ''){
	
	if(empty($contact)){
		$contact = new Feed();
	}
	global $mod_strings;
$temp_strings = $mod_strings;
if(!empty($mod)){
	global $current_language;
	$mod_strings = return_module_language($current_language, $mod);
}
		global $app_strings;
		global $app_list_strings;
		$primary_address_country_options = get_select_options_with_id($app_list_strings['countries_dom'], $contact->primary_address_country);
		global $current_user;
		$lbl_required_symbol = $app_strings['LBL_REQUIRED_SYMBOL'];
		$lbl_first_name = $mod_strings['LBL_FIRST_NAME'];
		$lbl_last_name = $mod_strings['LBL_LAST_NAME'];
		$lbl_phone = $mod_strings['LBL_OFFICE_PHONE'];
		$lbl_address =  $mod_strings['LBL_PRIMARY_ADDRESS'];
		$user_id = $current_user->id;
		$lbl_email_address = $mod_strings['LBL_EMAIL_ADDRESS'];
		$form = <<<EOQ
		<input type="hidden" name="${prefix}record" value="">
		<input type="hidden" name="${prefix}assigned_user_id" value='${user_id}'>
		<table border='0' celpadding="0" cellspacing="0" width='100%'>
		<tr>
		<td nowrap class='dataLabel'>$lbl_first_name</td>
		<td class='dataLabel'>$lbl_last_name&nbsp;<span class="required">$lbl_required_symbol</span></td>
		<td nowrap class='dataLabel'>&nbsp;</td>
		<td class='dataLabel'>&nbsp;</td>
		</tr>
		<tr>
		<td nowrap  class='dataField'><input name="${prefix}first_name" type="text" value="{$contact->first_name}"></td>
		<td class='dataField'><input name='${prefix}last_name' type="text" value="{$contact->last_name}"></td>
		<td class='dataField' nowrap>&nbsp;</td>
		<td class='dataField'>&nbsp;</td>
		</tr>
		
		<tr>
		<td class='dataLabel' nowrap>${mod_strings['LBL_TITLE']}</td>
		<td class='dataLabel' nowrap>${mod_strings['LBL_DEPARTMENT']}</td>
		<td class='dataLabel' nowrap>&nbsp;</td>
		<td class='dataLabel' nowrap>&nbsp;</td>
		</tr>
		<tr>
		<td class='dataField' nowrap><input name='${prefix}title' type="text" value="{$contact->title}"></td>
		<td class='dataField' nowrap><input name='${prefix}department' type="text" value="{$contact->department}"></td>
		<td class='dataField' nowra>&nbsp;</td>
		<td class='dataField' nowrap>&nbsp;</td>
		</tr>

		<tr>
		<td nowrap colspan='4' class='dataLabel'>$lbl_address</td>
		</tr>
		
		<tr>
		<td nowrap colspan='4' class='dataField'><input type='text' name='${prefix}primary_address_street' size='80' value='{$contact->primary_address_street}'></td>
		</tr>
		
		<tr>
		<td class='dataLabel'>${mod_strings['LBL_CITY']}</td>
		<td class='dataLabel'>${mod_strings['LBL_STATE']}</td>
		<td class='dataLabel'>${mod_strings['LBL_POSTAL_CODE']}</td>
		<td class='dataLabel'>${mod_strings['LBL_COUNTRY']}</td>
		</tr>
		
		<tr>
		<td class='dataField'><input name='${prefix}primary_address_city'  maxlength='100' value='{$contact->primary_address_city}'></td>
		<td class='dataField'><input name='${prefix}primary_address_state'  maxlength='100' value='{$contact->primary_address_state}'></td>
		<td class='dataField'><input name='${prefix}primary_address_postalcode'  maxlength='100' value='{$contact->primary_address_postalcode}'></td>
		<td class='dataField'><select name='${prefix}primary_address_country' size='1' selected='{$contact->primary_address_country}'>{$primary_address_country_options}</select></td>
		</tr>
		
		
		<tr>
		<td nowrap class='dataLabel'>$lbl_phone</td>
		<td nowrap class='dataLabel'>${mod_strings['LBL_MOBILE_PHONE']}</td>
		<td nowrap class='dataLabel'>${mod_strings['LBL_FAX_PHONE']}</td>
		<td nowrap class='dataLabel'>${mod_strings['LBL_HOME_PHONE']}</td>
		</tr>

		<tr>
		<td nowrap class='dataField'><input name='${prefix}phone_work' type="text" value="{$contact->phone_work}"></td>
		<td nowrap class='dataField'><input name='${prefix}phone_mobile' type="text" value="{$contact->phone_mobile}"></td>
		<td nowrap class='dataField'><input name='${prefix}phone_fax' type="text" value="{$contact->phone_fax}"></td>
		<td nowrap class='dataField'><input name='${prefix}phone_home' type="text" value="{$contact->phone_home}"></td>
		</tr>

		<tr>
		<td class='dataLabel' nowrap>$lbl_email_address</td>
		<td class='dataLabel' nowrap>${mod_strings['LBL_OTHER_EMAIL_ADDRESS']}</td>
		<td class='dataLabel' nowrap>&nbsp;</td>
		<td class='dataLabel' nowrap>&nbsp;</td>
		</tr>
		
		<tr>
		<td class='dataField' nowrap><input name='${prefix}email1' type="text" value="{$contact->email1}"></td>
		<td class='dataField' nowrap><input name='${prefix}email2' type="text" value="{$contact->email2}"></td>
		<td class='dataField' nowrap>&nbsp;</td>
		<td class='dataField' nowrap>&nbsp;</td>
		</tr>


		<tr>
		<td nowrap colspan='4' class='dataLabel'>${mod_strings['LBL_DESCRIPTION']}</td>
		</tr>
		<tr>
		<td nowrap colspan='4' class='dataField'><textarea cols='80' rows='4' name='${prefix}description' >{$contact->description}</textarea></td>
		</tr>
		</table>
		<input type='hidden' name='${prefix}alt_address_city' value='{$contact->alt_address_city}'><input type='hidden' name='${prefix}alt_address_state'   value='{$contact->alt_address_state}'><input type='hidden' name='${prefix}alt_address_postalcode'   value='{$contact->alt_address_postalcode}'><input type='hidden' name='${prefix}alt_address_country'  value='{$contact->alt_address_country}'>
		<input type='hidden' name='${prefix}do_not_call'  value='{$contact->do_not_call}'><input type='hidden' name='${prefix}email_opt_out'  value='{$contact->email_opt_out}'>
EOQ;


$javascript = new javascript();
$javascript->setFormName($formname);
$javascript->setSugarBean(new Feed());
//$javascript->addField('email1','false',$prefix);
//$javascript->addField('email2','false',$prefix);
$javascript->addRequiredFields($prefix);
$form .=$javascript->getScript();
$mod_strings = $temp_strings;
return $form;
}

function getFormBody($prefix, $mod='', $formname=''){
global $mod_strings;
$temp_strings = $mod_strings;
if(!empty($mod)){
	global $current_language;
	$mod_strings = return_module_language($current_language, $mod);
}
		global $app_strings;
		global $current_user;
		$lbl_required_symbol = $app_strings['LBL_REQUIRED_SYMBOL'];
		$lbl_url = $mod_strings['LBL_RSS_URL'] .":";
		$user_id = $current_user->id;
		$form = <<<EOQ
		<input type="hidden" name="${prefix}record" value="">
		<input type="hidden" name="${prefix}assigned_user_id" value='${user_id}'>
		$lbl_url<span class="required"> $lbl_required_symbol</span> <input name="${prefix}url" type="text" value="">&nbsp;<br>

EOQ;


$javascript = new javascript();
$javascript->setFormName($formname);
$javascript->setSugarBean(new Feed());
//$javascript->addField('email1','false',$prefix);
$javascript->addRequiredFields($prefix);

$form .=$javascript->getScript();
$mod_strings = $temp_strings;
return $form;

}
function getForm($prefix, $mod=''){
if(!empty($mod)){
	global $current_language;
	$mod_strings = return_module_language($current_language, $mod);
}else global $mod_strings;
global $app_strings;

$lbl_save_button_title = $app_strings['LBL_SAVE_BUTTON_TITLE'];
$lbl_save_button_key = $app_strings['LBL_SAVE_BUTTON_KEY'];
$lbl_save_button_label = $app_strings['LBL_SAVE_BUTTON_LABEL'];


$the_form = get_left_form_header($mod_strings['LBL_NEW_FORM_TITLE']);
$the_form .= <<<EOQ

		<form name="${prefix}FeedSave" onSubmit="return check_form('${prefix}FeedSave')" method="POST" action="index.php">
			<input type="hidden" name="${prefix}module" value="Feeds">
			<input type="hidden" name="${prefix}action" value="Save">
			<input type="hidden" name="${prefix}return_action" value="{$_REQUEST['action']}">
			<input type="hidden" name="${prefix}return_module" value="Feeds">
EOQ;
$the_form .= $this->getFormBody($prefix,'Feeds', "${prefix}FeedSave");
$the_form .= <<<EOQ
		<input title="$lbl_save_button_title" accessKey="$lbl_save_button_key" class="button" type="submit" name="${prefix}button" value="  $lbl_save_button_label  " >
		</form>

EOQ;
$the_form .= get_left_form_footer();
$the_form .= get_validate_record_js();

return $the_form;


}


function handleSave($prefix,$redirect=true, $useRequired=false){
	 
	
	require_once('include/formbase.php');
	global $timedate;
	

	$focus = new Feed();
	if($useRequired &&  !checkRequired($prefix, array_keys($focus->required_fields))){
		return null;
	}
	$focus = populateFromPost($prefix, $focus);
 	//if (!isset($_POST[$prefix.'email_opt_out'])) $focus->email_opt_out = 'off';
	//if (!isset($_POST[$prefix.'do_not_call'])) $focus->do_not_call = 'off';

/*
 if (!defined('DOMIT_RSS_INCLUDE_PATH')) {
                define('DOMIT_RSS_INCLUDE_PATH', "include/domit_rss/");
        }
        require_once(DOMIT_RSS_INCLUDE_PATH . 'xml_domit_rss.php');


print $focus->url;
        $rssdoc = new xml_domit_rss_document($focus->url,'cache/feeds/',3600);
	if (  $rssdoc == null)
{ 
return;
}
	$currChannel = $rssdoc->getChannel(0);

        $focus->title = $currChannel->getTitle();
*/


	$focus->save();
	$return_id = $focus->id;
	$GLOBALS['log']->debug("Saved record with id of ".$return_id);
	if($redirect){
		$this->handleRedirect($return_id);
	}else{
		return $focus;
	}
}

function handleRedirect($return_id){
	if(isset($_POST['return_module']) && $_POST['return_module'] != "") $return_module = $_POST['return_module'];
	else $return_module = "Feeds";
	if(isset($_POST['return_action']) && $_POST['return_action'] != "") $return_action = $_POST['return_action'];
	else $return_action = "DetailView";
	if(isset($_POST['return_id']) && $_POST['return_id'] != "") $return_id = $_POST['return_id'];
	header("Location: index.php?action=$return_action&module=$return_module&record=$return_id");

}

}


?>
