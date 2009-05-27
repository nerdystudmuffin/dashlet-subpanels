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

require_once("include/ytree/Tree.php");
require_once("include/ytree/ExtNode.php");
require_once("include/SugarFolders/SugarFolders.php");



class EmailUI {
	var $db;
	var $folder; // place holder for SugarFolder object
	var $folderStates = array(); // array of folderPath names and their states (1/0)
	var $smarty;
	var $addressSeparators = array(";", ",");
	var $rolloverStyle = "<style>div#rollover {position: relative;float: left;margin: none;text-decoration: none;}div#rollover a:hover {padding: 0;}div#rollover a span {display: none;}div#rollover a:hover span {text-decoration: none;display: block;width: 250px;margin-top: 5px;margin-left: 5px;position: absolute;padding: 10px;color: #333;	border: 1px solid #ccc;	background-color: #fff;	font-size: 12px;z-index: 1000;}</style>\n";
	var $groupCss = "<span class='groupInbox'>";
	var $cacheTimeouts = array(
		'messages'		=> 86400,	// 24 hours
		'folders'		=> 300,		// 5 mins
		'attachments'	=> 86400,	// 24 hours
	);
	var $userCacheDir = '';
	var $coreDynamicFolderQuery = "SELECT emails.id polymorphic_id, 'Emails' polymorphic_module FROM emails
								   JOIN emails_text on emails.id = emails_text.email_id
                                   WHERE (type = '::TYPE::' OR status = '::STATUS::') AND assigned_user_id = '::USER_ID::' AND emails.deleted = '0'";

	/**
	 * Sole constructor
	 */
	function EmailUI() {
		global $sugar_config;
		global $current_user;

		$folderStateSerial = $current_user->getPreference('folderOpenState', 'Emails', $current_user);

		if(!empty($folderStateSerial)) {
			$this->folderStates = unserialize($folderStateSerial);
		}

		$this->smarty = new Sugar_Smarty();
		$this->folder = new SugarFolder();
		$this->userCacheDir = "{$sugar_config['cache_dir']}modules/Emails/{$current_user->id}";
		$this->db = DBManagerFactory::getInstance();
	}

	///////////////////////////////////////////////////////////////////////////
	////	CORE
	/**
	 * Renders the frame for emails
	 */
	function displayEmailFrame() {
		
		require_once("include/OutboundEmail/OutboundEmail.php");

		global $app_strings, $app_list_strings;
		global $mod_strings;
		global $sugar_config;
		global $current_user;
		global $locale;
		global $timedate;
		global $theme;
		global $sugar_version;
		global $sugar_flavor;
		global $current_language;
		global $server_unique_key;

		$this->preflightUserCache();
		$json = getJSONobj();
		$ie = new InboundEmail();

		$out = '<script type="text/javascript" language="Javascript" src="modules/Emails/javascript/vars.js"></script>';

		// focus listView
		$list = array(
			'mbox' => 'Home',
			'ieId' => '',
			'name' => 'Home',
			'unreadChecked' => 0,
			'out' => array(),
		);

		// lang pack
		$lang = "var app_strings = new Object();\n";
		foreach($app_strings as $k => $v) {
			if(strpos($k, 'LBL_EMAIL_') !== false) {
				$lang .= "app_strings.{$k} = '{$v}';\n";
			}
		}
		$modStrings = "var mod_strings = new Object();\n";
		foreach($mod_strings as $k => $v) {
			$v = str_replace("'", "\'", $v);
			$modStrings .= "mod_strings.{$k} = '{$v}';\n";
		}
		$lang .= "\n\n{$modStrings}\n";

		// link drop-down
		$parent_types = $app_list_strings['record_type_display'];
		$disabled_parent_types = ACLController::disabledModuleList($parent_types, false, 'list');

		foreach($disabled_parent_types as $disabled_parent_type) {
		  unset($parent_types[$disabled_parent_type]);
		}
		$linkBeans = $json->encode(get_select_options_with_id($parent_types, ''));

		//TinyMCE Config
		require_once("include/SugarTinyMCE.php");
        $tiny = new SugarTinyMCE();
        $tinyConf = $tiny->getConfig();

        //Check quick create module access
        $QCAvailibleModules = array();
        $QCModules = array('Bugs', 'Cases', 'Contacts', 'Leads', 'Tasks');
        foreach($QCModules as $module) {
        	$class = substr($module, 0, strlen($module) - 1);
            require_once("modules/{$module}/{$class}.php");
            if($class=="Case")
                $class = "aCase";
            $seed = new $class();
        	if ($seed->ACLAccess('edit')) {
        		$QCAvailibleModules[] = $module;
        	}
        }

		///////////////////////////////////////////////////////////////////////
		////	BASIC ASSIGNS
		$charsets = $json->encode($locale->getCharsetSelect());
		$this->smarty->assign('yuiPath', 'modules/Emails/javascript/yui-ext/');
		$this->smarty->assign('app_strings', $app_strings);
		$this->smarty->assign('mod_strings', $mod_strings);
		$this->smarty->assign('theme', $theme);
		$this->smarty->assign('lang', $lang);
		$this->smarty->assign('linkBeans', $linkBeans);
		$this->smarty->assign('sugar_config', $sugar_config);
		$this->smarty->assign('emailCharsets', $charsets);
		$this->smarty->assign('is_admin', $current_user->is_admin);
		$this->smarty->assign('sugar_version', $sugar_version);
		$this->smarty->assign('sugar_flavor', $sugar_flavor);
		$this->smarty->assign('current_language', $current_language);
		$this->smarty->assign('server_unique_key', $server_unique_key);
		$this->smarty->assign('tinyMCE', $tinyConf);
		$this->smarty->assign('qcModules', $json->encode($QCAvailibleModules));
		$extAllDebugValue = "ext-all.js";



		$this->smarty->assign('extFileName', $extAllDebugValue);

		//#20776 jchi
		$peopleTables = array("users", "contacts", "leads", "prospects");
		$filterPeopleTables = array();
		global $app_list_strings, $app_strings;
		$filterPeopleTables['LBL_DROPDOWN_LIST_ALL'] = $app_strings['LBL_DROPDOWN_LIST_ALL'];
		foreach($peopleTables as $table) {
			$module = ucfirst($table);
            $class = substr($module, 0, strlen($module) - 1);
            require_once("modules/{$module}/{$class}.php");
            $person = new $class();

            if (!$person->ACLAccess('list')) continue;
            $filterPeopleTables[$person->table_name] = $app_list_strings['moduleList'][$person->module_dir];
		}
		$this->smarty->assign('listOfPersons' , get_select_options_with_id($filterPeopleTables,''));

		// settings: general
		$e2UserPreferences = $this->getUserPrefsJS();
		$emailSettings = $e2UserPreferences['emailSettings'];

		///////////////////////////////////////////////////////////////////////
		////	USER SETTINGS
		// settings: accounts














































		$cuDatePref = $current_user->getUserDateTimePreferences();
		$this->smarty->assign('dateFormat', $cuDatePref['date']);
		$this->smarty->assign('dateFormatExample', str_replace(array("Y", "m", "d"), array("yyyy", "mm", "dd"), $cuDatePref['date']));
		$this->smarty->assign('calFormat', $timedate->get_cal_date_format());
		$viewRawEmail = 'false';
		if(isset($sugar_config['email_inbound_save_raw']) && $sugar_config['email_inbound_save_raw'] == true) {
			$viewRawEmail = 'true';
		}
		$this->smarty->assign('viewRawSource', $viewRawEmail);

		$ieAccounts = $ie->retrieveByGroupId($current_user->id);
		$ieAccountsOptions = "<option value=''>{$app_strings['LBL_NONE']}</option>\n";

		foreach($ieAccounts as $k => $v) {
			$disabled = ($v->group_id != $current_user->id) ? "DISABLED" : "";
			$group = ($v->group_id != $current_user->id) ? $app_strings['LBL_EMAIL_GROUP']."." : "";
			$ieAccountsOptions .= "<option value='{$v->id}' $disabled>{$group}{$v->name}</option>\n";
		}

		$this->smarty->assign('ieAccounts', $ieAccountsOptions);
		$this->smarty->assign('rollover', $this->rolloverStyle);
		$this->smarty->assign('PROTOCOL', get_select_options_with_id($app_list_strings['dom_email_server_type'], ''));
		$this->smarty->assign('MAIL_SSL_OPTIONS', get_select_options_with_id($app_list_strings['email_settings_for_ssl'], ''));
		$this->smarty->assign('ie_mod_strings', return_module_language($current_language, 'InboundEmail'));

		// outbound opts
		$oe = new OutboundEmail();
		$outbounds = $oe->getUserMailers($current_user);
		$smtpOptions  = "SUGAR.mailers = ";
		$smtpOptions .= $json->encode($outbounds, false);

		$charset = array(
			'options' => $locale->getCharsetSelect(),
			'selected' => $emailSettings['defaultOutboundCharset']
		);
		$this->smarty->assign('charset', $charset);

		$emailCheckInterval = array('options' => $app_strings['LBL_EMAIL_CHECK_INTERVAL_DOM'], 'selected' => $emailSettings['emailCheckInterval']);
		$this->smarty->assign('emailCheckInterval', $emailCheckInterval);
		$emailSettings['layoutStyle'] == '2rows' ? $this->smarty->assign('rowsChecked', 'CHECKED') : $this->smarty->assign('colsChecked', 'CHECKED');
		//$this->smarty->assign('autoImportChecked', ($emailSettings['autoImport'] == 1) ? 'CHECKED' : "");
		$this->smarty->assign('alwaysSaveOutboundChecked', ($emailSettings['alwaysSaveOutbound']) ? 'CHECKED' : "");
		$this->smarty->assign('sendPlainTextChecked', ($emailSettings['sendPlainText'] == 1) ? 'CHECKED' : '');
		$this->smarty->assign('tabPositionChecked', ($emailSettings['tabPosition'] == 'bottom') ? 'CHECKED' : '');
		$this->smarty->assign('showNumInList', get_select_options_with_id($app_strings['LBL_EMAIL_SETTING_NUM_DOM'], $emailSettings['showNumInList']));
		$this->smarty->assign('fullScreenChecked', ($emailSettings['fullScreen'] == 1) ? "CHECKED" : "");

		$this->smarty->assign('userPrefs', $json->encode($this->getUserPrefsJS()));
		////	END USER SETTINGS
		///////////////////////////////////////////////////////////////////////

		///////////////////////////////////////////////////////////////////////
		////	SIGNATURES
		$prependSignature = ($current_user->getPreference('signature_prepend')) ? 'true' : 'false';
		$defsigID = $current_user->getPreference('signature_default');
		$defaultSignature = $current_user->getDefaultSignature();
		$sigJson = !empty($defaultSignature) ? $json->encode(array($defaultSignature['id'] => from_html($defaultSignature['signature_html']))) : "new Object()";
		$this->smarty->assign('defaultSignature', $sigJson);
		$this->smarty->assign('signatures', $current_user->getSignatures(false, $defsigID));
		$this->smarty->assign('signaturesSettings', $current_user->getSignatures(false, $defsigID, false));
		$signatureButtons = $current_user->getSignatureButtons('SUGAR.email2.settings.createSignature');
		$signatureButtons = $signatureButtons . '<span name="delete_sig" id="delete_sig" style="visibility:hidden;"><input class="button" onclick="javascript:SUGAR.email2.settings.deleteSignature();" value="'.$app_strings['LBL_EMAIL_DELETE'].'" type="button" tabindex="392">&nbsp;
					</span>';
		$this->smarty->assign('signatureButtons', $signatureButtons);
		$this->smarty->assign('signaturePrepend', $prependSignature == 'true' ? 'CHECKED' : '');
		$this->smarty->assign('signatureDefaultId', (isset($defaultSignature['id'])) ? $defaultSignature['id'] : "");
		////	END SIGNATURES
		///////////////////////////////////////////////////////////////////////

		///////////////////////////////////////////////////////////////////////
		////	EMAIL TEMPLATES
		$email_templates_arr = $this->getEmailTemplatesArray();
		$this->smarty->assign('EMAIL_TEMPLATE_OPTIONS', get_select_options_with_id($email_templates_arr, ''));
		////	END EMAIL TEMPLATES
		///////////////////////////////////////////////////////////////////////

		///////////////////////////////////////////////////////////////////////
		////	FOLDERS & TreeView
		$this->smarty->assign('groupUserOptions', $ie->getGroupsWithSelectOptions(array('' => $app_strings['LBL_EMAIL_CREATE_NEW'])));

		$tree = $this->getMailboxNodes();

		// preloaded folder
		$preloadFolder = 'lazyLoadFolder = ';
		$focusFolderSerial = $current_user->getPreference('focusFolder', 'Emails');
		if(!empty($focusFolderSerial)) {
			$focusFolder = unserialize($focusFolderSerial);
			//$focusFolder['ieId'], $focusFolder['folder']
			$preloadFolder .= $json->encode($focusFolder).";";
		} else {
			$preloadFolder .= "new Object();";
		}
		////	END FOLDERS
		///////////////////////////////////////////////////////////////////////

		$fullscreen = '';
		if($emailSettings['fullScreen'] == 1) {
			$fullscreen = 'SUGAR.ui.toggleHeader();';
		}

		$out .= $this->smarty->fetch("modules/Emails/templates/_baseEmail.tpl");
		$out .= $tree->generate_header();
		$out .= $tree->generateNodesNoInit(true, 'email2treeinit');
		$out .=<<<eoq
			<script type="text/javascript" language="javascript">
				{$fullscreen}

				var loader = new YAHOO.util.YUILoader({
				    require : ["layout", "element", "tabview", "menu", "cookie", "sugarwidgets"],
				    loadOptional: true,
				    filter: 'debug',
				    skin: "",
				    onSuccess: email2init,
				    allowRollup: true,
				    base: "include/javascript/yui/build/"
				});
				loader.addModule({
				    name :"sugarwidgets",
				    type : "js",
				    fullpath: "include/javascript/sugarwidgets/SugarYUIWidgets.js",
				    varName: "YAHOO.SUGAR",
				    requires: ["datatable", "dragdrop", "treeview", "tabview"]
				});
				loader.insert();

				{$preloadFolder};
				{$smtpOptions};
			</script>
eoq;


		return $out;
	}
	////	END CORE
	///////////////////////////////////////////////////////////////////////////

	///////////////////////////////////////////////////////////////////////////
	////	ADDRESS BOOK
	/**
	 * Retrieves all relationship metadata for a user's address book
	 * @return array
	 */
	function getContacts() {
		global $current_user;

		$q = "SELECT * FROM address_book WHERE assigned_user_id = '{$current_user->id}' ORDER BY bean DESC";
		$r = $this->db->query($q);

		$ret = array();

		while($a = $this->db->fetchByAssoc($r)) {
			$ret[$a['bean_id']] = array(
				'id'		=> $a['bean_id'],
				'module'	=> $a['bean'],
			);
		}

		return $ret;
	}

	/**
	 * Saves changes to a user's address book
	 * @param array contacts
	 */
	function setContacts($contacts) {
		global $current_user;

		$oldContacts = $this->getContacts();

		foreach($contacts as $cid => $contact) {
			if(!in_array($contact['id'], $oldContacts)) {
				$q = "INSERT INTO address_book (assigned_user_id, bean, bean_id) VALUES ('{$current_user->id}', '{$contact['module']}', '{$contact['id']}')";
				$r = $this->db->query($q, true);
			}
		}
	}

	/**
	 * Removes contacts from the user's address book
	 * @param array ids
	 */
	function removeContacts($ids) {
		global $current_user;

		$concat = "";

		foreach($ids as $id) {
			if(!empty($concat))
				$concat .= ", ";

			$concat .= "'{$id}'";
		}

		$q = "DELETE FROM address_book WHERE assigned_user_id = '{$current_user->id}' AND bean_id IN ({$concat})";
		$r = $this->db->query($q);





	}

	/**
	 * saves editted Contact info
	 * @param string $str JSON serialized object
	 */
	function saveContactEdit($str) {
		
		$json = getJSONobj();

		$str = from_html($str);
		$obj = $json->decode($str);

		$contact = new Contact();
		$contact->retrieve($obj['contact_id']);
		$contact->first_name = $obj['contact_first_name'];
		$contact->last_name = $obj['contact_last_name'];
		$contact->save();

		// handle email address changes
		$addresses = array();

		foreach($obj as $k => $req) {
			if(strpos($k, 'emailAddress') !== false) {
				$addresses[$k] = $req;
			}
		}

		// prefill some REQUEST vars for emailAddress save
		$_REQUEST['emailAddressOptOutFlag'] = $obj['optOut'];
		$_REQUEST['emailAddressInvalidFlag'] = $obj['invalid'];
		$contact->emailAddress->save($obj['contact_id'], 'Contacts', $addresses, $obj['primary'], '');
	}

	/**
	 * Prepares the Edit Contact mini-form via template assignment
	 * @param string id ID of contact in question
	 * @param string module Module in focus
	 * @return array
	 */
	function getEditContact($id, $module) {
		global $app_strings;
		

		if(!class_exists("Contact")) {
			
		}

		$contact = new Contact();
		$contact->retrieve($_REQUEST['id']);
		$ret = array();

		if($contact->ACLAccess('edit')) {
			$contactMeta = array();
			$contactMeta['id'] = $contact->id;
			$contactMeta['module'] = $contact->module_dir;
			$contactMeta['first_name'] = $contact->first_name;
			$contactMeta['last_name'] = $contact->last_name;

			$this->smarty->assign("app_strings", $app_strings);
			$this->smarty->assign("contact_strings", return_module_language($_SESSION['authenticated_user_language'], 'Contacts'));
			$this->smarty->assign("contact", $contactMeta);

			$ea = new SugarEmailAddress();
			$newEmail = $ea->getEmailAddressWidgetEditView($id, $module, true);
			$this->smarty->assign("emailWidget", $newEmail['html']);

			$ret['form'] = $this->smarty->fetch("modules/Emails/templates/editContact.tpl");
			$ret['prefillData'] = $newEmail['prefillData'];
		} else {
			$id = "";
			$ret['form'] = $app_strings['LBL_EMAIL_ERROR_NO_ACCESS'];
			$ret['prefillData'] = '{}';
		}

		$ret['id'] = $id;
		$ret['contactName'] = $contact->full_name;

		return $ret;
	}









































































































































	/**
	 * Retrieves a concatenated list of contacts, those with assigned_user_id = user's id and those in the address_book
	 * table
	 * @param array $contacts Array of contact types -> IDs
	 * @param object $user User in focus
	 * @return array
	 */
	function getUserContacts($contacts, $user=null) {
		
		global $current_user;
		global $locale;

		if(empty($user)) {
			$user = $current_user;
		}

		$emailAddress = new SugarEmailAddress();
		$ret = array();

		$union = '';

		$modules = array();
		foreach($contacts as $contact) {
			if(!isset($modules[$contact['module']])) {
				$modules[$contact['module']] = array();
			}
			$modules[$contact['module']][] = $contact;
		}

		foreach($modules as $module => $contacts) {
			if(!empty($union)) {
				$union .= " UNION ALL ";
			}

			$table = strtolower($module);
			$idsSerial = '';

			foreach($contacts as $contact) {
				if(!empty($idsSerial)) {
					$idsSerial .= ",";
				}
				$idsSerial .= "'{$contact['id']}'";
			}

			$union .= "(SELECT id, first_name, last_name, title, '{$module}' module FROM {$table} WHERE id IN({$idsSerial}) AND deleted = 0 )";
		}
		if(!empty($union)) {
			$union .= " ORDER BY last_name";
		}

		$r = $user->db->query($union);

		//_pp($union);

		while($a = $user->db->fetchByAssoc($r)) {
			$c = array();

			$c['name'] = $locale->getLocaleFormattedName($a['first_name'], "<b>{$a['last_name']}</b>", '', $a['title'], '', $user);
			$c['id'] = $a['id'];
			$c['module'] = $a['module'];
			$c['email'] = $emailAddress->getAddressesByGUID($a['id'], $a['module']);
			$ret[$a['id']] = $c;
		}

		return $ret;
	}
	////	END ADDRESS BOOK
	///////////////////////////////////////////////////////////////////////////


	///////////////////////////////////////////////////////////////////////////
	////	EMAIL 2.0 Preferences
	function getUserPrefsJS() {
		global $current_user;
		global $locale;

		// sort order per mailbox view
		$sortSerial = $current_user->getPreference('folderSortOrder', 'Emails');
		$sortArray = array();
		if(!empty($sortSerial)) {
			$sortArray = unserialize($sortSerial);
		}

		// treeview collapsed/open states
		$folderStateSerial = $current_user->getPreference('folderOpenState', 'Emails');
		$folderStates = array();
		if(!empty($folderStateSerial)) {
			$folderStates = unserialize($folderStateSerial);
		}

		// subscribed accounts
		$showFolders = unserialize(base64_decode($current_user->getPreference('showFolders', 'Emails')));

		// general settings
		$emailSettings = $current_user->getPreference('emailSettings', 'Emails');

		if(empty($emailSettings)) {
			$emailSettings = array();
			$emailSettings['emailCheckInterval'] = -1;
			$emailSettings['layoutStyle'] = '2rows';
			$emailSettings['autoImport'] = '';
			$emailSettings['alwaysSaveOutbound'] = '1';
			$emailSettings['sendPlainText'] = '';
			$emailSettings['defaultOutboundCharset'] = $locale->default_email_charset;
			$emailSettings['tabPosition'] = 'top';
			$emailSettings['showNumInList'] = 20;
			$emailSettings['fullScreen'] = 0;
		}

		// focus folder
		$focusFolder = $current_user->getPreference('focusFolder', 'Emails');
		$focusFolder = !empty($focusFolder) ? unserialize($focusFolder) : array();

		// unread only flag
		$showUnreadOnly = $current_user->getPreference('showUnreadOnly', 'Emails');

		$listViewSort = array(
			"sortBy" => 'date',
			"sortDirection" => 'DESC',
		);

		// signature prefs
		$signaturePrepend = $current_user->getPreference('signature_prepend') ? 'true' : 'false';
		$signatureDefault = $current_user->getPreference('signature_default');
		$signatures = array(
			'signature_prepend' => $signaturePrepend,
			'signature_default' => $signatureDefault
		);







		// current_user
		$user = array(
			'emailAddresses' => $current_user->emailAddress->getAddressesByGUID($current_user->id, 'Users'),
			'full_name' => from_html($current_user->full_name),
		);

		$userPreferences = array();
		$userPreferences['sort'] = $sortArray;
		$userPreferences['folderStates'] = $folderStates;
		$userPreferences['showFolders'] = $showFolders;
		$userPreferences['emailSettings'] = $emailSettings;
		$userPreferences['focusFolder'] = $focusFolder;
		$userPreferences['showUnreadOnly'] = $showUnreadOnly;
		$userPreferences['listViewSort'] = $listViewSort;
		$userPreferences['signatures'] = $signatures;



		$userPreferences['current_user'] = $user;
		return $userPreferences;
	}



	///////////////////////////////////////////////////////////////////////////
	////	FOLDER FUNCTIONS
	/**
	 * Retrieves folders available to the current user and returns threaded versions
	 * @params string type Default 'user', else 'group'
	 * @return string <options> list of folders
	 */
/*
	function getFoldersAvailable($type='user') {
		global $current_user;
		global $app_strings;

		$subscriptions = array();
		$qSub = "SELECT folder_id FROM folders_subscriptions WHERE assigned_user_id = '{$current_user->id}'";
		$rSub = $this->folder->db->query($qSub);
		while($aSub = $this->folder->db->fetchByAssoc($rSub)) {
			$subscriptions[] = $aSub['folder_id'];
		}

		$return = "<option value=''>{$app_strings['LBL_NONE']}</option>";

		$group = ($type == 'user') ? 0 : 1;
		$q = "SELECT * FROM folders WHERE parent_folder = '' AND is_group = {$group} AND deleted = 0";
		$r = $this->folder->db->query($q);

		while($a = $this->folder->db->fetchByAssoc($r)) {
			$selected = (in_array($a['id'], $subscriptions)) ? " SELECTED" : "";
			$return .= "<option value='{$a['id']}'{$selected}>{$a['name']}</option>";

			if($a['has_child'] == 1) {
				$return .= $this->getFoldersChild($a['id'], 1, $subscriptions);
			}
		}

		return $return;
	}
*/
	/**
	 * Retrieves the options for children folders of a given foldf
	 * @param string id ID of parent folder
	 * @param int depth how deep are we?
	 * @param array subscriptions array of subscribed folder's ids
	 * @returns string <option> of child folder
	 */
/*
	function getFoldersChild($id, $depth, $subscriptions) {
		$q = "SELECT * FROM folders WHERE parent_folder = '{$id}' AND deleted = 0";
		$r = $this->folder->db->query($q);

		$ret = "";

		$depthMarker = "";
		for($i=0; $i<$depth; $i++) {
			$depthMarker .= "-";
		}

		while($a = $this->folder->db->fetchByAssoc($r)) {
			$selected = (in_array($a['id'], $subscriptions)) ? " SELECTED" : "";
			$ret .= "<option value='{$a['id']}'{$subscriptions}>{$depthMarker} {$a['name']}</option>";

			if($a['has_child'] == 1) {
				$ret .= $this->getFoldersChild($a['id'], $depth+1, $subscriptions);
			}
		}
		return $ret;
	}
*/
	/**
	 * Creates a new Sugar folder
	 * @param string $nodeLabel New sugar folder name
	 * @param string $parentLabel Parent folder name
	 */
	function saveNewFolder($nodeLabel, $parentId, $isGroup=0) {
		global $current_user;

		$this->folder->name = $nodeLabel;
		$this->folder->is_group = $isGroup;
		$this->folder->parent_folder = ($parentId == 'Home') ? "" : $parentId;
		$this->folder->has_child = 0;
		$this->folder->created_by = $current_user->id;
		$this->folder->modified_by = $current_user->id;
		$this->folder->date_created = date($GLOBALS['timedate']->get_db_date_time_format(), gmmktime());
		$this->folder->date_modified = date($GLOBALS['timedate']->get_db_date_time_format(), gmmktime());




		$this->folder->save();
		return array(
			'action' => 'newFolderSave',
			'id' => $this->folder->id,
			'name' => $this->folder->name,
			'is_group' => $this->folder->is_group,
			'is_dynamic' => $this->folder->is_dynamic
		);
	}

	/**
	 * Saves user sort prefernces
	 */
	function saveListViewSortOrder($ieId, $focusFolder, $sortBy, $sortDir) {
		global $current_user;

		$sortArray = array();

		$sortSerial = $current_user->getPreference('folderSortOrder', 'Emails');
		if(!empty($sortSerial)) {
			$sortArray = unserialize($sortSerial);
		}

		$sortArray[$ieId][$focusFolder]['current']['sort'] = $sortBy;
		$sortArray[$ieId][$focusFolder]['current']['direction'] = $sortDir;
		$sortSerial = serialize($sortArray);
		$current_user->setPreference('folderSortOrder', $sortSerial, '', 'Emails');
	}

	/**
	 * Stickies folder collapse/open state
	 */
	function saveFolderOpenState($focusFolder, $focusFolderOpen) {
		global $current_user;

		$folderStateSerial = $current_user->getPreference('folderOpenState', 'Emails');
		$folderStates = array();

		if(!empty($folderStateSerial)) {
			$folderStates = unserialize($folderStateSerial);
		}

		$folderStates[$focusFolder] = $focusFolderOpen;
		$newFolderStateSerial = serialize($folderStates);
		$current_user->setPreference('folderOpenState', $newFolderStateSerial, '', 'Emails');
	}

	/**
	 * saves a folder's view state
	 */
	function saveListView($ieId, $folder) {
		global $current_user;

		$saveState = array();
		$saveState['ieId'] = $ieId;
		$saveState['folder'] = $folder;
		$saveStateSerial = serialize($saveState);
		$current_user->setPreference('focusFolder', $saveStateSerial, '', 'Emails');
	}

	/**
	 * Generates cache folder structure
	 */
	function preflightEmailCache($cacheRoot) {
		// base
		if(!file_exists($cacheRoot))
			mkdir_recursive(clean_path($cacheRoot));

		// folders
		if(!file_exists($cacheRoot."/folders"))
			mkdir_recursive(clean_path("{$cacheRoot}/folders"));

		// messages
		if(!file_exists($cacheRoot."/messages"))
			mkdir_recursive(clean_path("{$cacheRoot}/messages"));

		// attachments
		if(!file_exists($cacheRoot."/attachments"))
			mkdir_recursive(clean_path("{$cacheRoot}/attachments"));
	}

	function deleteEmailCacheForFolders($cacheRoot) {
		$filePath = $cacheRoot."/folders/folders.php";
		if (file_exists($filePath)) {
			unlink($filePath);
		}
	}
	///////////////////////////////////////////////////////////////////////////
	////	IMAP FUNCTIONS
	/**
	 * Identifies subscribed mailboxes and empties the trash
	 * @param object $ie InboundEmail
	 */
	function emptyTrash(&$ie) {
		global $current_user;

		$showFolders = unserialize(base64_decode($current_user->getPreference('showFolders', 'Emails')));

		if(is_array($showFolders)) {
			foreach($showFolders as $ieId) {
				if(!empty($ieId)) {
					$ie->retrieve($ieId);
					$ie->emptyTrash();
				}
			}
		}
	}

	/**
	 * returns an array of nodes that correspond to IMAP mailboxes.
	 * @param bool $forceRefresh
	 * @return object TreeView object
	 */
	function getMailboxNodes() {
		global $sugar_config;
		global $current_user;
		global $app_strings;

		$tree = new Tree("frameFolders");
		$tree->tree_style= 'include/ytree/TreeView/css/check/tree.css';

		$nodes = array();
		$ie = new InboundEmail();
		$refreshOffset = $this->cacheTimeouts['folders']; // 5 mins.  this will be set via user prefs

		$rootNode = new ExtNode($app_strings['LBL_EMAIL_HOME_FOLDER'], $app_strings['LBL_EMAIL_HOME_FOLDER']);
		$rootNode->dynamicloadfunction = '';
		$rootNode->expanded = true;
		$rootNode->dynamic_load = true;
		$showFolders = unserialize(base64_decode($current_user->getPreference('showFolders', 'Emails')));

		if(empty($showFolders)) {
			$showFolders = array();
		}

		// INBOX NODES
		if($current_user->hasPersonalEmail()) {
			$personals = $ie->retrieveByGroupId($current_user->id);

			foreach($personals as $k => $personalAccount) {
				if(in_array($personalAccount->id, $showFolders)) {
					// check for cache value
					$cacheRoot = "{$sugar_config['cache_dir']}modules/Emails/{$personalAccount->id}";
					$this->preflightEmailCache($cacheRoot);

					if($this->validCacheFileExists($personalAccount->id, 'folders', "folders.php")) {
						$mailboxes = $this->getMailBoxesFromCacheValue($personalAccount);
						/*
						$foldersCache = $this->getCacheValue($personalAccount->id, 'folders', "folders.php", 'foldersCache');
						$mailboxes = $foldersCache['mailboxes'];
						$mailboxesArray = $personalAccount->generateFlatArrayFromMultiDimArray($mailboxes, $personalAccount->retrieveDelimiter());
						$personalAccount->insertMailBoxFolders($mailboxesArray);
						//_ppd($mailboxesArray);
						//$this->deleteEmailCacheForFolders($cacheRoot);
						*/
					} else {
						//$personalAccount->connectMailserver();
						$mailboxes = $personalAccount->getMailboxes();
						//$foldersCache = array('mailboxes' => $mailboxes);
						//$this->writeCacheFile("foldersCache", $foldersCache, $personalAccount->id, 'folders', 'folders.php');
					}

					$acctNode = new ExtNode('Home::' . $personalAccount->name, $personalAccount->name);
					$acctNode->dynamicloadfunction = '';
					$acctNode->expanded = false;
					$acctNode->set_property('cls', 'ieFolder');
					$acctNode->set_property('ieId', $personalAccount->id);
		        	$acctNode->set_property('protocol', $personalAccount->protocol);

					if(array_key_exists('Home::'.$personalAccount->name, $this->folderStates)) {
						if($this->folderStates['Home::'.$personalAccount->name] == 'open') {
							$acctNode->expanded = true;
						}
					}
					$acctNode->dynamic_load = true;

					$nodePath = $acctNode->_properties['id'];

					foreach($mailboxes as $k => $mbox) {
						$acctNode->add_node($this->buildTreeNode($k, $k, $mbox, $personalAccount->id,
						    $nodePath, false, $personalAccount));
					}

					$rootNode->add_node($acctNode);
				}
			}
		}

		// GROUP INBOX NODES
		$beans = $ie->retrieveAllByGroupId($current_user->id, false);
		foreach($beans as $k => $groupAccount) {
			if(in_array($groupAccount->id, $showFolders)) {
				// check for cache value
				$cacheRoot = "{$sugar_config['cache_dir']}modules/Emails/{$groupAccount->id}";
				$this->preflightEmailCache($cacheRoot);
				//$groupAccount->connectMailserver();

				if($this->validCacheFileExists($groupAccount->id, 'folders', "folders.php")) {
				//if(false) {
					$mailboxes = $this->getMailBoxesFromCacheValue($groupAccount);
					/*
					$foldersCache = $this->getCacheValue($groupAccount->id, 'folders', "folders.php", 'foldersCache');
					$mailboxes = $foldersCache['mailboxes'];
					$mailboxesArray = $groupAccount->generateFlatArrayFromMultiDimArray($mailboxes, $groupAccount->retrieveDelimiter());
					$groupAccount->insertMailBoxFolders($mailboxesArray);
					*/
					//$this->deleteEmailCacheForFolders($cacheRoot);
				} else {
					$mailboxes = $groupAccount->getMailBoxesForGroupAccount();
					/*
					if ($groupAccount->mailbox != $email_user) {
						$mailboxes = $groupAccount->sortMailboxes($groupAccount->mailbox, $groupAccount->retrieveDelimiter());
						$mailboxesArray = $groupAccount->generateFlatArrayFromMultiDimArray($mailboxes, $groupAccount->retrieveDelimiter());
						$groupAccount->insertMailBoxFolders($mailboxesArray);
						// save mailbox value of an inbound email account to email user
						$groupAccount->saveMailBoxValueOfInboundEmail();
					} else {
						$mailboxes = $groupAccount->getMailboxes();
					}*/
					//$foldersCache = array('mailboxes' => $mailboxes);
					//$this->writeCacheFile("foldersCache", $foldersCache, $groupAccount->id, 'folders', 'folders.php');
				}

				$acctNode = new ExtNode($groupAccount->name, "group.{$groupAccount->name}");
				$acctNode->dynamicloadfunction = '';
				$acctNode->expanded = false;
		        $acctNode->set_property('isGroup', 'true');
		        $acctNode->set_property('ieId', $groupAccount->id);
		        $acctNode->set_property('protocol', $groupAccount->protocol);

				if(array_key_exists('Home::'.$groupAccount->name, $this->folderStates)) {
					if($this->folderStates['Home::'.$groupAccount->name] == 'open') {
						$acctNode->expanded = true;
					}
				}
				$acctNode->dynamic_load = true;
				$nodePath = $rootNode->_properties['id']."::".$acctNode->_properties['id'];

				foreach($mailboxes as $k => $mbox) {
					$acctNode->add_node($this->buildTreeNode($k, $k, $mbox, $groupAccount->id,
					   $nodePath, true, $groupAccount));
				}

				$rootNode->add_node($acctNode);
			}
		}

		// SugarFolder nodes
		/* SugarFolders are built at onload when the UI renders */

		$tree->add_node($rootNode);
		return $tree;
	}

	function getMailBoxesFromCacheValue($mailAccount) {
		$foldersCache = $this->getCacheValue($mailAccount->id, 'folders', "folders.php", 'foldersCache');
		$mailboxes = $foldersCache['mailboxes'];
		$mailboxesArray = $mailAccount->generateFlatArrayFromMultiDimArray($mailboxes, $mailAccount->retrieveDelimiter());
		$mailAccount->saveMailBoxFolders($mailboxesArray);
		$this->deleteEmailCacheForFolders($cacheRoot);
		return $mailboxes;
	} // fn

	/**
	 * Builds up a TreeView Node object
	 * @param mixed
	 * @param mixed
	 * @param string
	 * @param string ID of InboundEmail instance
	 * @param string nodePath Serialized path from root node to current node
	 * @param bool isGroup
	 * @param bool forceRefresh
	 * @return mixed
	 */
	function buildTreeNode($key, $label, $mbox, $ieId, $nodePath, $isGroup, $ie) {
		global $sugar_config;

		// get unread counts
		$exMbox = explode("::", $nodePath);
		$unseen = 0;
		$GLOBALS['log']->debug("$key --- $nodePath::$label");

		if(count($exMbox) >= 2) {
			$mailbox = "";
			for($i=2; $i<count($exMbox); $i++) {
				if($mailbox != "") {
					$mailbox .= ".";
				}
				$mailbox .= "{$exMbox[$i]}";
			}

		    $mailbox = substr($key, strpos($key, '.'));

			$unseen = $this->getUnreadCount($ie, $mailbox);

			if($unseen > 0) {
				//$label = " <span id='span{$ie->id}{$ie->mailbox}' style='font-weight:bold'>{$label} (<span id='span{$ie->id}{$ie->mailbox}nums'>{$unseen}</span>)</span>";
			}
		}

		$nodePath = $nodePath."::".$label;
        $node = new ExtNode($nodePath, $label);
        $node->dynamicloadfunction = '';
        $node->expanded = false;
        $node->set_property('labelStyle', "remoteFolder");


        if(array_key_exists($nodePath, $this->folderStates)) {
        	if($this->folderStates[$nodePath] == 'open') {
        		$node->expanded = true;
        	}
        }

		$group = ($isGroup) ? 'true' : 'false';
        $node->dynamic_load = true;
        //$node->set_property('href', " SUGAR.email2.listView.populateListFrame(YAHOO.namespace('frameFolders').selectednode, '{$ieId}', 'false');");
        $node->set_property('isGroup', $group);
        $node->set_property('isDynamic', 'false');
        $node->set_property('ieId', $ieId);
        $node->set_property('mbox', $key);
        $node->set_property('unseen', $unseen);
        $node->set_property('cls', 'ieFolder');

        if(is_array($mbox)) {
        	foreach($mbox as $k => $v) {
        		$node->add_node($this->buildTreeNode("$key.$k", $k, $v, $ieId, $nodePath, $isGroup, $ie));
        	}
        }

        return $node;
	}

	/**
	 * Totals the unread emails
	 */
	function getUnreadCount(&$ie, $mailbox) {
		global $sugar_config;
		$unseen = 0;

		/*
		if(!$ie->isPop3Protocol() && $fromMailserver) {
			$connectString = $ie->getConnectString('', $mailbox);
			$unseen = imap_status($ie->conn, $connectString, SA_UNSEEN);
			return $unseen->unseen;

		}*/

		// use cache
		return $ie->getCacheUnreadCount($mailbox);
	}

	///////////////////////////////////////////////////////////////////////////
	////	DISPLAY CODE
	/**
	 * Used exclusively by draft code.  Returns Notes and Documents as attachments.
	 * @param array $ret
	 * @return array
	 */
	function getDraftAttachments($ret) {
		global $db;

		// $ret['uid'] is the draft Email object's GUID
		$ret['attachments'] = array();

		$q = "SELECT id, filename FROM notes WHERE parent_id = '{$ret['uid']}' AND deleted = 0";
		$r = $db->query($q);

		while($a = $db->fetchByAssoc($r)) {
			$ret['attachments'][$a['id']] = array(
				'id'		=> $a['id'],
				'filename'	=> $a['filename'],
			);
		}

		return $ret;
	}

	function createCopyOfInboundAttachment($ie, $ret, $uid) {
		global $sugar_config;
		if ($ie->isPop3Protocol()) {
			// get the UIDL from database;
			$cachedUIDL = md5($uid);
			$cache = "{$sugar_config['cache_dir']}modules/Emails/{$ie->id}/messages/{$ie->mailbox}{$cachedUIDL}.php";
		} else {
			$cache = "{$sugar_config['cache_dir']}modules/Emails/{$ie->id}/messages/{$ie->mailbox}{$uid}.php";
		}
		if(file_exists($cache)) {
			include($cache); // profides $cacheFile
			$metaOut = unserialize($cacheFile['out']);
			$meta = $metaOut['meta']['email'];
			if (isset($meta['attachments'])) {
				$attachmentHtmlData = $meta['attachments'];
				$actualAttachmentInfo = array();
				$this->parseAttachmentInfo($actualAttachmentInfo, $attachmentHtmlData);
				if (sizeof($actualAttachmentInfo) > 0) {
					foreach($actualAttachmentInfo as $key => $value) {
						$attachmentid;
						$fileName;
						$datasplit = split("&", $value);
						$attachmentIdArray = split("=", $datasplit[0]);
						$attachmentid = $attachmentIdArray[1];

						$fileNameArray = split("=", $datasplit[4]);
						$fileName = $fileNameArray[1];
						$guid = create_guid();
						$destination = clean_path("{$this->userCacheDir}/{$guid}{$fileName}");

						$attachmentFilePath = "{$sugar_config['cache_dir']}modules/Emails/{$ie->id}/attachments/{$attachmentid}";
						copy($attachmentFilePath, $destination);
						$ret['attachments'][$guid] = array();
						$ret['attachments'][$guid]['id'] = $guid . $fileName;
						$ret['attachments'][$guid]['filename'] = $fileName;
					} // for
				} // if
			} // if

		} // if
		return $ret;

	} // fn

	function parseAttachmentInfo(&$actualAttachmentInfo, $attachmentHtmlData) {
	 	$downLoadPHP = strpos($attachmentHtmlData, "index.php?entryPoint=download&");
		while ($downLoadPHP) {
		 	$attachmentHtmlData = substr($attachmentHtmlData, $downLoadPHP+30);
		 	$final = strpos($attachmentHtmlData, "\">");
		 	$actualAttachmentInfo[] = substr($attachmentHtmlData, 0, $final);
		 	$attachmentHtmlData = substr($attachmentHtmlData, $final);
		 	$downLoadPHP = strpos($attachmentHtmlData, "index.php?entryPoint=download&");
		} // while
	}
	/**
	 * Renders the QuickCreate form from Smarty and returns HTML
	 * @param array $vars request variable global
	 * @param object $email Fetched email object
	 * @param bool $addToAddressBook
	 * @return array
	 */
	function getQuickCreateForm($vars, $email, $addToAddressBookButton=false) {
		require_once("include/EditView/EditView2.php");
		global $app_strings;
		global $mod_strings;
		global $current_user;
		global $beanList;
		global $beanFiles;
		global $current_language;

		//Setup the current module languge
		$mod_strings = return_module_language($current_language, $_REQUEST['qc_module']);

		$bean = $beanList[$_REQUEST['qc_module']];
		$class = $beanFiles[$bean];
		require_once($class);

		$focus = new $bean();

		$people = array('Contact', 'Lead');
		$emailAddress = array();

		// people
		if(in_array($bean, $people)) {
			// lead specific
			$focus->lead_source = 'Email';
			$focus->lead_source_description = trim($email->name);

			$from = (isset($email->from_name) && !empty($email->from_name)) ? $email->from_name : $email->from_addr;

			$name = explode(" ", trim($from));

			$address = trim(array_pop($name));
			$address = str_replace(array("<",">","&lt;","&gt;"), "", $address);

			$emailAddress[] = array(
				'email_address'		=> $address,
				'primary_address'	=> 1,
				'invalid_email'		=> 0,
				'opt_out'			=> 0,
				'reply_to_address'	=> 1
			);

			if(!empty($name)) {
				$focus->last_name = trim(array_pop($name));

				foreach($name as $first) {
					if(!empty($focus->first_name)) {
						$focus->first_name .= " ";
					}
					$focus->first_name .= trim($first);
				}
			}
		} else {
			// bugs, cases, tasks
			$focus->name = trim($email->name);
		}

		$focus->description = trim(strip_tags($email->description));
		$focus->assigned_user_id = $current_user->id;





		$EditView = new EditView();
		$EditView->ss = new Sugar_Smarty();
		//MFH BUG#20283 - checks for custom quickcreate fields
		$EditView->setup($_REQUEST['qc_module'], $focus, 'custom/modules/'.$focus->module_dir.'/metadata/editviewdefs.php', 'include/EditView/EditView.tpl');
		$EditView->process();
		$EditView->render();
		$EditView->defs['templateMeta']['form']['buttons'] = array(
			'email2save' => array(
				'id' => 'e2AjaxSave',
				'customCode' => '<input type="button" class="button" value="   '.$app_strings['LBL_SAVE_BUTTON_LABEL']
				              . '   " onclick="SUGAR.email2.detailView.saveQuickCreate(false);" />'
			),
			'email2saveandreply' => array(
			    'id' => 'e2SaveAndReply',
			    'customCode' => '<input type="button" class="button" value="   '.$app_strings['LBL_EMAIL_SAVE_AND_REPLY']
			                  . '   " onclick="SUGAR.email2.detailView.saveQuickCreate(\'reply\');" />'
			),
			'email2cancel' => array(
			     'id' => 'e2cancel',
			     'customCode' => '<input type="button" class="button" value="   '.$app_strings['LBL_EMAIL_CANCEL']
                              . '   " onclick="SUGAR.email2.detailView.quickCreateDialog.hide();" />'
			)
		);


		if($addToAddressBookButton) {
			$EditView->defs['templateMeta']['form']['buttons']['email2saveAddToAddressBook'] = array(
				'id' => 'e2addToAddressBook',
				'customCode' => '<input type="button" class="button" value="   '.$app_strings['LBL_EMAIL_ADDRESS_BOOK_SAVE_AND_ADD']
				              . '   " onclick="SUGAR.email2.detailView.saveQuickCreate(true);" />'
			);
		}

		//Get the module language for javascript
	    if(!is_file($GLOBALS['sugar_config']['cache_dir'] . 'jsLanguage/' . $_REQUEST['qc_module'] . '/' . $GLOBALS['current_language'] . '.js')) {
            require_once('include/language/jsLanguage.php');
            jsLanguage::createModuleStringsCache($_REQUEST['qc_module'], $GLOBALS['current_language']);
        }
		$jsLanguage = '<script type="text/javascript" src="' . $GLOBALS['sugar_config']['cache_dir'] . 'jsLanguage/'
		            . $_REQUEST['qc_module'] . '/' . $GLOBALS['current_language'] . '.js?s=' . $GLOBALS['sugar_version'] . '&c='
		            . $GLOBALS['sugar_config']['js_custom_version'] . '&j=' . $GLOBALS['sugar_config']['js_lang_version'] . '"></script>';

		$EditView->view = 'EmailQCView';
		$EditView->defs['templateMeta']['form']['headerTpl'] = 'include/EditView/header.tpl';
		$EditView->defs['templateMeta']['form']['footerTpl'] = 'include/EditView/footer.tpl';
		$meta = array();
		$meta['html'] = $jsLanguage . $EditView->display(false, true);
		$meta['html'] = str_replace("src='include/SugarEmailAddress/SugarEmailAddress.js?s={$GLOBALS['js_version_key']}&c={$GLOBALS['sugar_config']['js_custom_version']}'", '', $meta['html']);
		$meta['emailAddress'] = $emailAddress;

		$mod_strings = return_module_language($current_language, 'Emails');

		return $meta;
	}

	/**
     * Renders the Import form from Smarty and returns HTML
     * @param array $vars request variable global
     * @param object $email Fetched email object
     * @param bool $addToAddressBook
     * @return array
     */
    function getImportForm($vars, $email) {
        require_once("include/EditView/EditView2.php");
        require_once("include/TemplateHandler/TemplateHandler.php");
        global $app_strings;
        global $current_user;
        global $app_list_strings;
        $sqsdefs = array();
        $sqsdefs['parent_name'] = array('name' => 'parent_name', 'type' => 'parent',
                 'label' => 'LBL_EMAIL_RELATE_TO', 'relate' => 'parent_id');

        $smarty = new Sugar_Smarty();
        $smarty->assign("APP",$app_strings);













        $showAssignTo = false;
        if (!isset($vars['showAssignTo']) || $vars['showAssignTo'] == true) {
        	$showAssignTo = true;
		} // if
		if ($showAssignTo) {
	        if(empty($email->assigned_user_id) && empty($email->id))
	            $email->assigned_user_id = $current_user->id;
	        if(empty($email->assigned_name) && empty($email->id))
	            $email->assigned_user_name = $current_user->user_name;
	        $sqsdefs['assigned_user_name'] = array('name' => 'assigned_user_name', 'type' => 'relate',
	                 'module' => 'Users', 'label' => 'LBL_ASSIGNED_TO', 'relate' => 'assigned_user_id');
		}
		$smarty->assign("showAssignedTo",$showAssignTo);

        $showDelete = false;
        if (!isset($vars['showDelete']) || $vars['showDelete'] == true) {
            $showDelete = true;
        }
        $smarty->assign("showDelete",$showDelete);

        $smarty->assign("userId",$email->assigned_user_id);
        $smarty->assign("userName",$email->assigned_user_name);
        $parent_types = $app_list_strings['record_type_display'];
        $smarty->assign('parentOptions', get_select_options_with_id($parent_types, $email->parent_type));

        $sqs = TemplateHandler::createQuickSearchCode($sqsdefs,$sqsdefs);
        $sqs .= '<script type="text/javascript" language="Javascript">';
        foreach($sqsdefs as $field =>$def) {
        	$sqs .= "\n addToValidateBinaryDependency('ImportEditView', '$field', 'alpha', false,"
        		  . "'{$app_strings['ERR_SQS_NO_MATCH_FIELD']} {$app_strings[$def['label']]}', '{$def['relate']}');";
        }
        $sqs .= '</script>';
        $smarty->assign('SQS', $sqs);

        $meta = array();
        $meta['html'] = $smarty->fetch("modules/Emails/templates/importRelate.tpl");
        return $meta;
    }

    /**
     * This function returns the detail view for email in new 2.0 interface
     *
     */
    function getDetailViewForEmail2($emailId) {
		
		require_once('modules/Emails/Forms.php');
		
		require_once('include/DetailView/DetailView.php');
		global $app_strings, $app_list_strings;
		global $mod_strings;

        $smarty = new Sugar_Smarty();

		// SETTING DEFAULTS
		$focus		= new Email();
		$focus->retrieve($emailId);
		$detailView->ss = new Sugar_Smarty();
		$detailView	= new DetailView();
		$title = "";
		$offset		= 0;
		if($focus->type == 'out') {
			$title = get_module_title('Emails', $mod_strings['LBL_SENT_MODULE_NAME'].": ".$focus->name, true);
		} elseif ($focus->type == 'draft') {
			$title = get_module_title('Emails', $mod_strings['LBL_LIST_FORM_DRAFTS_TITLE'].": ".$focus->name, true);
		} elseif($focus->type == 'inbound') {
			$title = get_module_title('Emails', $mod_strings['LBL_INBOUND_TITLE'].": ".$focus->name, true);
		}
		$smarty->assign("emailTitle", $title);

		// DEFAULT TO TEXT IF NO HTML CONTENT:
		$html = trim(from_html($focus->description_html));
		if(empty($html)) {
			$smarty->assign('SHOW_PLAINTEXT', 'true');
			$description = nl2br($focus->description);
		} else {
			$smarty->assign('SHOW_PLAINTEXT', 'false');
			$description = from_html($focus->description_html);
		}

		//if not empty or set to test (from test campaigns)
		if (!empty($focus->parent_type) && $focus->parent_type !='test') {
			$smarty->assign('PARENT_MODULE', $focus->parent_type);
			$smarty->assign('PARENT_TYPE', $app_list_strings['record_type_display'][$focus->parent_type]);
		}

        global $gridline;
		$smarty->assign('MOD', $mod_strings);
		$smarty->assign('APP', $app_strings);
		$smarty->assign('GRIDLINE', $gridline);
		$smarty->assign('PRINT_URL', 'index.php?'.$GLOBALS['request_string']);
		$smarty->assign('ID', $focus->id);
		$smarty->assign('TYPE', $focus->type);
		$smarty->assign('PARENT_NAME', $focus->parent_name);
		$smarty->assign('PARENT_ID', $focus->parent_id);
		$smarty->assign('NAME', $focus->name);
		$smarty->assign('ASSIGNED_TO', $focus->assigned_user_name);
		$smarty->assign('DATE_MODIFIED', $focus->date_modified);
		$smarty->assign('DATE_ENTERED', $focus->date_entered);
		$smarty->assign('DATE_START', $focus->date_start);
		$smarty->assign('TIME_START', $focus->time_start);
		$smarty->assign('FROM', $focus->from_addr);
		$smarty->assign('TO', nl2br($focus->to_addrs));
		$smarty->assign('CC', nl2br($focus->cc_addrs));
		$smarty->assign('BCC', nl2br($focus->bcc_addrs));
		$smarty->assign('CREATED_BY', $focus->created_by_name);
		$smarty->assign('MODIFIED_BY', $focus->modified_by_name);
		$smarty->assign('DESCRIPTION', nl2br($focus->description));
		$smarty->assign('DESCRIPTION_HTML', from_html($focus->description_html));
		$smarty->assign('DATE_SENT', $focus->date_entered);
		$smarty->assign('EMAIL_NAME', 'RE: '.$focus->name);
		$smarty->assign("TAG", $focus->listviewACLHelper());
		$smarty->assign("SUGAR_VERSION", $GLOBALS['sugar_version']);
		$smarty->assign("JS_CUSTOM_VERSION", $GLOBALS['sugar_config']['js_custom_version']);



		if(!empty($focus->reply_to_email)) {
			$replyTo = "
				<tr>
		        <td class=\"tabDetailViewDL\"><slot>".$mod_strings['LBL_REPLY_TO_NAME']."</slot></td>
		        <td colspan=3 class=\"tabDetailViewDF\"><slot>".$focus->reply_to_addr."</slot></td>
		        </tr>";
		 	$smarty->assign("REPLY_TO", $replyTo);
		}
		///////////////////////////////////////////////////////////////////////////////
		////	JAVASCRIPT VARS
		$jsVars  = '';
		$jsVars .= "var showRaw = '{$mod_strings['LBL_BUTTON_RAW_LABEL']}';";
		$jsVars .= "var hideRaw = '{$mod_strings['LBL_BUTTON_RAW_LABEL_HIDE']}';";
		$smarty->assign("JS_VARS", $jsVars);
		///////////////////////////////////////////////////////////////////////////////
		////	NOTES (attachements, etc.)
		///////////////////////////////////////////////////////////////////////////////

		$note = new Note();
		$where = "notes.parent_id='{$focus->id}'";
		//take in account if this is from campaign and the template id is stored in the macros.

		if(isset($macro_values) && isset($macro_values['email_template_id'])){
		    $where = "notes.parent_id='{$macro_values['email_template_id']}'";
		}
		$notes_list = $note->get_full_list("notes.name", $where, true);

		if(! isset($notes_list)) {
			$notes_list = array();
		}

		$attachments = '';
		for($i=0; $i<count($notes_list); $i++) {
			$the_note = $notes_list[$i];
			//$attachments .= "<a href=\"".UploadFile::get_url($the_note->filename,$the_note->id)."\" target=\"_blank\">".$the_note->name.$the_note->description ."</a><br>";
			$attachments .= "<a href=\"index.php?entryPoint=download&id=".$the_note->id."&type=Notes\">".$the_note->name."</a><br />";
		}
		$smarty->assign("ATTACHMENTS", $attachments);
		///////////////////////////////////////////////////////////////////////////////
		////    SUBPANELS
		///////////////////////////////////////////////////////////////////////////////
		$show_subpanels = true;
		if ($show_subpanels) {
		    require_once('include/SubPanel/SubPanelTiles.php');
		    $subpanel = new SubPanelTiles($focus, 'Emails');
		    $smarty->assign("SUBPANEL", $subpanel->display());
		}
        $meta['html'] = $smarty->fetch("modules/Emails/templates/emailDetailView.tpl");
        return $meta;

    } // fn

	/**
	 * Sets the "read" flag in the overview cache
	 */
	function setReadFlag($ieId, $mbox, $uid) {
		$this->markEmails('read', $ieId, $mbox, $uid);
	}

	/**
	 * Marks emails with the passed flag type.  This will be applied to local
	 * cache files as well as remote emails.
	 * @param string $type Flag type
	 * @param string $ieId
	 * @param string $folder IMAP folder structure or SugarFolder GUID
	 * @param string $uids Comma sep list of UIDs or GUIDs
	 */
	function markEmails($type, $ieId, $folder, $uids) {

		global $app_strings;
		$uids = $this->_cleanUIDList($uids);
		$exUids = explode($app_strings['LBL_EMAIL_DELIMITER'], $uids);

		if(strpos($folder, 'sugar::') !== false) {
			// dealing with a sugar email object, uids are GUIDs
			foreach($exUids as $id) {
				$email = new Email();
				$email->retrieve($id);

				switch($type) {
					case "unread":
						$email->status = 'unread';
						$email->save();
					break;

					case "read":
						$email->status = 'read';
						$email->save();
					break;

					case "deleted":
						$email->delete();
					break;

					case "flagged":
						$email->flagged = 1;
						$email->save();
					break;

					case "unflagged":
						$email->flagged = 0;
						$email->save();
					break;

				}
			}
		} else {
			/* dealing with IMAP email, uids are IMAP uids */
			global $ie; // provided by EmailUIAjax.php
			if(empty($ie)) {
				
				$ie = new InboundEmail();
			}
			$ie->retrieve($ieId);
			$ie->mailbox = $folder;
			$ie->connectMailserver();
			// mark cache files
			if($type == 'deleted') {
				$ie->deleteMessageOnMailServer($uids);
				$ie->deleteMessageFromCache($uids);
			} else {
				$overviews = $ie->getCacheValueForUIDs($ie->mailbox, $exUids);
				$manipulated = array();

				foreach($overviews['retArr'] as $k => $overview) {
					if(in_array($overview->uid, $exUids)) {
						switch($type) {
							case "unread":
								$overview->seen = 0;
							break;

							case "read":
								$overview->seen = 1;
							break;

							case "flagged":
								$overview->flagged = 1;
							break;

							case "unflagged":
								$overview->flagged = 0;
							break;
						}
						$manipulated[] = $overview;
					}
				}

				if(!empty($manipulated)) {
					$ie->setCacheValue($ie->mailbox, array(), $manipulated);
					/* now mark emails on email server */
					$ie->markEmails(implode(",", explode($app_strings['LBL_EMAIL_DELIMITER'], $uids)), $type);
				}
			} // end not type == deleted
		}
	}

function doAssignment($distributeMethod, $ieid, $folder, $uids, $users) {
	global $app_strings;
	$users = explode(",", $users);
	$emailIds = explode($app_strings['LBL_EMAIL_DELIMITER'], $uids);
	$out = "";
	if($folder != 'sugar::Emails') {
		$emailIds = array();
		$uids = explode($app_strings['LBL_EMAIL_DELIMITER'], $uids);
		$ie = new InboundEmail();
		$ie->retrieve($ieid);
		//$ie->mailbox = $folder;
		$messageIndex = 1;
		//$ie->connectMailserver();
		// dealing with an inbound email data so we need to import an email and then
		foreach($uids as $uid) {
			$ie->mailbox = $folder;
			$ie->connectMailserver();
			$msgNo = $uid;
			if (!$ie->isPop3Protocol()) {
				$msgNo = imap_msgno($ie->conn, $uid);
			} else {
				$msgNo = $ie->getCorrectMessageNoForPop3($uid);
			}

			if(!empty($msgNo)) {
				if ($ie->importOneEmail($msgNo, $uid)) {
					$emailIds[] = $ie->email->id;
					$ie->deleteMessageOnMailServer($uid);
					//$ie->retrieve($ieid);
					//$ie->connectMailserver();
					$ie->mailbox = $folder;
					$ie->deleteMessageFromCache(($uids[] = $uid));
				} else {
					$out = $out . "Message No : " . $messageIndex . " failed. Reason : Message already imported \r\n";
				}
			}
			$messageIndex++;
		} // for
	} // if

	if (count($emailIds) > 0) {
		$this->doDistributionWithMethod($users, $emailIds, $distributeMethod);
	} // if
	return $out;
} // fn

function doDistributionWithMethod($users, $emailIds, $distributionMethod) {
	// we have users and the items to distribute
	if($distributionMethod == 'roundRobin') {
		$this->distRoundRobin($users, $emailIds);
	} elseif($distributionMethod == 'leastBusy') {
		$this->distLeastBusy($users, $emailIds);
	} elseif($distributionMethod == 'direct') {
		if(count($users) > 1) {
			// only 1 user allowed in direct assignment
			$error = 1;
		} else {
			$user = $users[0];
			$this->distDirect($user, $emailIds);
		} // else
	} // elseif

} // fn

/**
 * distributes emails to users on Round Robin basis
 * @param	$userIds	array of users to dist to
 * @param	$mailIds	array of email ids to push on those users
 * @return  boolean		true on success
 */
function distRoundRobin($userIds, $mailIds) {
	// check if we have a 'lastRobin'
	/*
	if(!file_exists($this->cachePath.'/'.$this->cacheFile)) {
		$this->writeToCache('robin', array($userIds[0]));
		$lastRobin = $userIds[0];
	} else {
		require_once($this->cachePath.'/'.$this->cacheFile);
		$lastRobin = $robin[0];
	}*/

	$lastRobin = $userIds[0];
	foreach($mailIds as $k => $mailId) {
		$userIdsKeys = array_flip($userIds); // now keys are values
		$thisRobinKey = $userIdsKeys[$lastRobin] + 1;
		if(!empty($userIds[$thisRobinKey])) {
			$thisRobin = $userIds[$thisRobinKey];
			$lastRobin = $userIds[$thisRobinKey];
		} else {
			$thisRobin = $userIds[0];
			$lastRobin = $userIds[0];
		}

		$email = new Email();
		$email->retrieve($mailId);
		$email->assigned_user_id = $thisRobin;
		$email->status = 'unread';
		$email->save();
	}
	//$this->writeToCache('robin', array($lastRobin));
	return true;
}

/**
 * distributes emails to users on Least Busy basis
 * @param	$userIds	array of users to dist to
 * @param	$mailIds	array of email ids to push on those users
 * @return  boolean		true on success
 */
function distLeastBusy($userIds, $mailIds) {
	foreach($mailIds as $k => $mailId) {
		$email = new Email();
		$email->retrieve($mailId);
		foreach($userIds as $k => $id) {
			$r = $this->db->query("SELECT count(*) AS c FROM emails WHERE assigned_user_id = '.$id.' AND status = 'unread'");
			$a = $this->db->fetchByAssoc($r);
			$counts[$id] = $a['c'];
		}
		asort($counts); // lowest to highest
		$countsKeys = array_flip($counts); // keys now the 'count of items'
		$leastBusy = array_shift($countsKeys); // user id of lowest item count
		$email->assigned_user_id = $leastBusy;
		$email->status = 'unread';
		$email->save();
	}
	return true;
}

/**
 * distributes emails to 1 user
 * @param	$user		users to dist to
 * @param	$mailIds	array of email ids to push
 * @return  boolean		true on success
 */
function distDirect($user, $mailIds) {
	foreach($mailIds as $k => $mailId) {
		$email = new Email();
		$email->retrieve($mailId);
		$email->assigned_user_id = $user;
		$email->status = 'unread';
		$email->save();
	}
	return true;
}

function getAssignedEmailsCountForUsers($userIds) {
	$counts = array();
	foreach($userIds as $id) {
		$r = $this->db->query("SELECT count(*) AS c FROM emails WHERE assigned_user_id = '.$id.' AND status = 'unread'");
		$a = $this->db->fetchByAssoc($r);
		$counts[$id] = $a['c'];
	} // foreach
	return $counts;
} // fn

function getLastRobin($ie) {
	$lastRobin = "";
	if($this->validCacheFileExists($ie->id, 'folders', "robin.cache.php")) {
		$lastRobin = $this->getCacheValue($ie->id, 'folders', "robin.cache.php", 'robin');
	} // if
	return $lastRobin;
} // fn

function setLastRobin($ie, $lastRobin) {
    global $sugar_config;
    $cacheFolderPath = clean_path("{$sugar_config['cache_dir']}modules/Emails/{$ie->id}/folders");
    if (!file_exists($cacheFolderPath)) {
    	mkdir_recursive($cacheFolderPath);
    }
	$this->writeCacheFile('robin', $lastRobin, $ie->id, 'folders', "robin.cache.php");
} // fn

	/**
	 * returns the metadata defining a single email message for display.  Uses cache file if it exists
	 * @return array
	 */
function getSingleMessage($ie) {
		
		global $timedate;
		$ie->retrieve($_REQUEST['ieId']);
		$noCache = true;

		$ie->mailbox = $_REQUEST['mbox'];
		$filename = $_REQUEST['mbox'].$_REQUEST['uid'].".php";
		$md5uidl = "";
		if ($ie->isPop3Protocol()) {
			$md5uidl = md5($_REQUEST['uid']);
			$filename = $_REQUEST['mbox'].$md5uidl.".php";
		} // if

		if($this->validCacheFileExists($_REQUEST['ieId'], 'messages', $filename)) {
			$out = $this->getCacheValue($_REQUEST['ieId'], 'messages', $filename, 'out');
			$noCache = false;

			// something fubar'd the cache?
			if(empty($out['meta']['email']['name']) && empty($out['meta']['email']['description'])) {
				$noCache = true;
			} else {
				// When sending data from cache, convert date into users preffered format
				$dateTimeInGMTFormat = $out['meta']['email']['date_start'];
				$out['meta']['email']['date_start'] = $timedate->to_display_date_time($dateTimeInGMTFormat);
			} // else
		}

		if($noCache) {
			$writeToCacheFile = true;
			if ($ie->isPop3Protocol()) {
				$status = $ie->setEmailForDisplay($_REQUEST['uid'], true, true, true);
			} else {
				$status = $ie->setEmailForDisplay($_REQUEST['uid'], false, true, true);
			}
			$out = $ie->displayOneEmail($_REQUEST['uid'], $_REQUEST['mbox']);
			// modify the out object to store date in GMT format on the local cache file
			$dateTimeInUserFormat = $out['meta']['email']['date_start'];
			$out['meta']['email']['date_start'] = $timedate->to_db_date($dateTimeInUserFormat) . " " . $timedate->to_db_time($dateTimeInUserFormat);
			if ($status == 'error') {
				$writeToCacheFile = false;
			}
			if ($writeToCacheFile) {
				if ($ie->isPop3Protocol()) {
					$this->writeCacheFile('out', $out, $_REQUEST['ieId'], 'messages', "{$_REQUEST['mbox']}{$md5uidl}.php");
				} else {
					$this->writeCacheFile('out', $out, $_REQUEST['ieId'], 'messages', "{$_REQUEST['mbox']}{$_REQUEST['uid']}.php");
				} // else
			// restore date in the users preferred format to be send on to UI for diaply
			$out['meta']['email']['date_start'] = $dateTimeInUserFormat;
			} // if
		}

		if($noCache) {
			$GLOBALS['log']->debug("EMAILUI: getSingleMessage() NOT using cache file");
		} else {
			$GLOBALS['log']->debug("EMAILUI: getSingleMessage() using cache file [ ".$_REQUEST['mbox'].$_REQUEST['uid'].".php ]");
		}

		$this->setReadFlag($_REQUEST['ieId'], $_REQUEST['mbox'], $_REQUEST['uid']);
		return $out;
	}


	/**
	 * Returns the HTML for a list of emails in a given folder
	 * @param GUID $ieId GUID to InboundEmail instance
	 * @param string $mbox Mailbox path name in dot notation
	 * @param int $folderListCacheOffset Seconds for valid cache file
	 * @return string HTML render of list.
	 */
	function getListEmails($ieId, $mbox, $folderListCacheOffset, $forceRefresh='false') {
		global $sugar_config;
		

		$ie = new InboundEmail();
		$ie->retrieve($ieId);
		$list = $ie->displayFolderContents($mbox, $forceRefresh);

		return $list;
	}

	/**
	 * Returns the templatized compose screen.  Used by reply, forwards and draft status messages.
	 * @param object email Email bean in focus
	 */
	function displayComposeEmail($email) {
		global $locale;
		global $current_user;

		
		$ea = new SugarEmailAddress();

		if(!empty($email)) {
			$description = (empty($email->description_html)) ? $email->description : from_html($email->description_html);
		}

		$toaddresses = from_html((isset($email->toaddrs)) ? $email->toaddrs : $email->to_addrs);
		$ccAddresses = from_html((isset($email->ccAddrs)) ? $email->ccAddrs : $email->cc_addrs);
		$bccAddresses = from_html((isset($email->bccAddrs)) ? $email->bccAddrs : $email->bcc_addrs);

		$ret = array();
		$ret['type'] = $email->type;
		$ret['name'] = $email->name;
		$ret['description'] = $description;
		$ret['from'] = (isset($_REQUEST['composeType']) && $_REQUEST['composeType'] == 'forward') ? "" : $email->from_addr;
		$ret['to'] = $toaddresses;
		$ret['cc'] = $ccAddresses;
		$ret['bcc'] = $bccAddresses;
		$ret['uid'] = $email->id;
		$ret['parent_name'] = $email->parent_name;
		$ret['parent_type'] = $email->parent_type;
		$ret['parent_id'] = $email->parent_id;

		// reply all
		if(isset($_REQUEST['composeType']) && $_REQUEST['composeType'] == 'replyAll') {
			$userEmails = array();
			$userEmailsMeta = $ea->getAddressesByGUID($current_user->id, 'Users');
			foreach($userEmailsMeta as $emailMeta) {
				$userEmails[] = strtolower(trim($emailMeta['email_address']));
			}
			$userEmails[] = strtolower(trim($email->from_addr));

			$ret['cc'] = from_html($email->cc_addrs);

			$to = str_replace($this->addressSeparators, "::", $toaddresses);
			$exTo = explode("::", $to);

			if(is_array($exTo)) {
				foreach($exTo as $addr) {
					$addr = strtolower(trim($addr));
					if(!in_array($addr, $userEmails)) {
						if(!empty($ret['cc'])) {
							$ret['cc'] = $ret['cc'].", ";
						}
						$ret['cc'] = $ret['cc'].trim($addr);
					}
				}
			} elseif(!empty($exTo)) {
				$exTo = trim($exTo);
				if(!in_array($exTo, $userEmails)) {
					$ret['cc'] = $ret['cc'].", ".$exTo;
				}
			}
		}
		return $ret;
	}
	/**
	 * Formats email body on reply/forward
	 * @param object email Email object in focus
	 * @param string type
	 * @return object email
	 */
	function handleReplyType($email, $type) {
		global $mod_strings;
		 $GLOBALS['log']->debug("****At Handle Reply Type: $type");
		switch($type) {
			case "reply":
			case "replyAll":
				$header = $email->getReplyHeader();
                if(!preg_match('/^(re:)+/i', $email->name)) {
                    $email->name = "{$mod_strings['LBL_RE']} {$email->name}";
                }
				if ($type == "reply") {
					$email->cc_addrs = "";
					if (!empty($email->reply_to_addr)) {
						$email->from_addr = $email->reply_to_addr;
					} // if
				} else {
					if (!empty($email->reply_to_addr)) {
						$email->to_addrs = $email->to_addrs . "," . $email->reply_to_addr;
					} // if
				} // else
			break;

			case "forward":
				$header = $email->getForwardHeader();
				if(!preg_match('/^(fw:)+/i', $email->name)) {
                    $email->name = "{$mod_strings['LBL_FW']} {$email->name}";
                }
				$email->cc_addrs = "";
			break;

			case "replyCase":
				$GLOBALS['log']->debug("EMAILUI: At reply case");
				$header = $email->getReplyHeader();
				
                $myCase = new aCase();
                $myCase->retrieve($email->parent_id);
                $myCaseMacro = $myCase->getEmailSubjectMacro();
                $GLOBALS['log']->debug("****Case # : {$myCase->case_number} macro: $myCaseMacro");
				if(!strpos($email->name, str_replace('%1',$myCase->case_number,$myCaseMacro))) {
		        	$GLOBALS['log']->debug("Replacing");
		            $email->name = str_replace('%1',$myCase->case_number,$myCaseMacro) . ' '. $email->name;
		        }
                $email->name = "{$mod_strings['LBL_RE']} {$email->name}";
            break;
		}

		$html = trim($email->description_html);
		$plain = trim($email->description);

		$desc = (!empty($html)) ? $html : $plain;

		$email->description = $header.$email->quoteHtmlEmailForNewEmailUI($desc);
		return $email;

/*		if(trim($email->description) != "") {
			$GLOBALS['log']->fatal("EMAILUI: Got a [ plain-text ] email to reply/forward quote");
			$description		= $email->quotePlainTextEmail($email->description);
			$email->description = $header.$description;
		} else {
			$email->description = '';
		}

		if(trim($email->description_html) != "") {
			$GLOBALS['log']->fatal("EMAILUI: Got an [ HTML ] email to reply/forward quote");
			$description_html	= $email->quoteHtmlEmail($email->description_html);
			$email->description_html = $header.$description_html;
		} else {
			$email->description_html = '';
		}
		return $email;
*/	}

	///////////////////////////////////////////////////////////////////////////
	////	PRIVATE HELPERS
	/**
	 * Generates a UNION query to get one list of users, contacts, leads, and
	 * prospects; used specifically for the addressBook
	 */
	function _getPeopleUnionQuery($whereArr , $person) {
		global $current_user , $app_strings;
		global $db;
		if(!isset($person) || $person === 'LBL_DROPDOWN_LIST_ALL'){
			$peopleTables = array("users", "contacts", "leads", "prospects");
		}else{
			$peopleTables = array($person);
		}
		$q = '';

		$whereAdd = "";

		foreach($whereArr as $column => $clause) {
			if(!empty($whereAdd)) {
				$whereAdd .= " AND ";
			}
			$clause = $current_user->db->helper->escape_quote($clause);
			$whereAdd .= "{$column} LIKE '{$clause}%'";
		}


		foreach($peopleTables as $table) {
			$module = ucfirst($table);
            $class = substr($module, 0, strlen($module) - 1);
            require_once("modules/{$module}/{$class}.php");
            $person = new $class();
			if (!$person->ACLAccess('list')) {
				continue;
			} // if
			$where = "({$table}.deleted = 0 AND eabr.primary_address = 1 AND {$table}.id <> '{$current_user->id}')";

            if (ACLController::requireOwner($module, 'list')) {
            	$where = $where . " AND ({$table}.assigned_user_id = '{$current_user->id}')";
            } // if
			if(!empty($whereAdd)) {
				$where .= " AND ({$whereAdd})";
			}

			$t = "SELECT {$table}.id, {$table}.first_name, {$table}.last_name, eabr.primary_address, ea.email_address, '{$module}' module ";
			$t .= "FROM {$table} ";
			$t .= "JOIN email_addr_bean_rel eabr ON ({$table}.id = eabr.bean_id and eabr.deleted=0) ";
			$t .= "JOIN email_addresses ea ON (eabr.email_address_id = ea.id) ";



			$t .= " WHERE {$where}";
			//_pp($t);
			//$t .= " LIMIT 10";

			if(!empty($q)) {
				$q .= "\n UNION ALL \n";
			}

			$q .= "({$t})";
		}
		$countq = "SELECT count(people.id) c from ($q) as people";
		$q .= "ORDER BY last_name";
		//_ppd($q);
		return array('query' => $q, 'countQuery' => $countq);
    }





































































	/**
	 * Cleans UID lists
	 * @param mixed $uids
	 * @param bool $returnString False will return an array
	 * @return mixed
	 */
	function _cleanUIDList($uids, $returnString=false) {
		global $app_strings;
		$GLOBALS['log']->debug("_cleanUIDList: before - [ {$uids} ]");

		if(!is_array($uids)) {
			$returnString = true;

			$exUids = explode($app_strings['LBL_EMAIL_DELIMITER'], $uids);
			$uids = $exUids;
		}

		$cleanUids = array();
		foreach($uids as $uid) {
			$cleanUids[$uid] = $uid;
		}

		sort($cleanUids);

		if($returnString) {
			$cleanImplode = implode($app_strings['LBL_EMAIL_DELIMITER'], $cleanUids);
			$GLOBALS['log']->debug("_cleanUIDList: after - [ {$cleanImplode} ]");
			return $cleanImplode;
		}

		return $cleanUids;
	}

	/**
	 * Creates defaults for the User
	 * @param object $user User in focus
	 */
	function preflightUser(&$user) {
		global $mod_strings;

		$goodToGo = $user->getPreference("email2Preflight", "Emails");
			$q = "SELECT count(*) count FROM folders f where f.created_by = '{$user->id}' AND f.folder_type = 'inbound' AND f.deleted = 0";
			$r = $user->db->query($q);
			$a = $user->db->fetchByAssoc($r);

			if($a['count'] < 1) {
				require_once("include/SugarFolders/SugarFolders.php");



				// My Emails
				$folder = new SugarFolder();
				$folder->new_with_id = true;
				$folder->id = create_guid();
				$folder->name = $mod_strings['LNK_MY_INBOX'];
				$folder->has_child = 1;
				$folder->created_by = $user->id;
				$folder->modified_by = $user->id;
				$folder->is_dynamic = 1;
				$folder->folder_type = "inbound";
				$folder->dynamic_query = $this->generateDynamicFolderQuery('inbound', $user->id);



				$folder->save();

				// My Drafts
				$drafts = new SugarFolder();
				$drafts->name = $mod_strings['LNK_MY_DRAFTS'];
				$drafts->has_child = 0;
				$drafts->parent_folder = $folder->id;
				$drafts->created_by = $user->id;
				$drafts->modified_by = $user->id;
				$drafts->is_dynamic = 1;
				$drafts->folder_type = "draft";
				$drafts->dynamic_query = $this->generateDynamicFolderQuery('draft', $user->id);



				$drafts->save();

				// My Archived
				//$archived = new SugarFolder();
				//$archived->name = $mod_strings['LNK_MY_ARCHIVED_LIST'];
				//$archived->has_child = 0;
				//$archived->parent_folder = $folder->id;
				//$archived->created_by = $user->id;
				//$archived->modified_by = $user->id;
				//$archived->is_dynamic = 1;
				//$archived->dynamic_query = $this->generateDynamicFolderQuery('archived', $user->id);



				//$archived->save();

				// Sent Emails
				$archived = new SugarFolder();
				$archived->name = $mod_strings['LNK_SENT_EMAIL_LIST'];
				$archived->has_child = 0;
				$archived->parent_folder = $folder->id;
				$archived->created_by = $user->id;
				$archived->modified_by = $user->id;
				$archived->is_dynamic = 1;
				$archived->folder_type = "sent";
				$archived->dynamic_query = $this->generateDynamicFolderQuery('sent', $user->id);



				$archived->save();

			// set flag to show that this was run
			$user->setPreference("email2Preflight", true, 1, "Emails");
		}
	}

	/**
	 * Parses the core dynamic folder query
	 * @param string $type 'inbound', 'draft', etc.
	 * @param string $userId
	 * @return string
	 */
	function generateDynamicFolderQuery($type, $userId) {
		$q = $this->coreDynamicFolderQuery;

		$status = $type;

		if($type == "sent") {
			$type = "out";
		}

		$replacee = array("::TYPE::", "::STATUS::", "::USER_ID::");
		$replacer = array($type, $status, $userId);

		$ret = str_replace($replacee, $replacer, $q);

		if($type == 'inbound') {
			$ret .= " AND status NOT IN ('sent', 'archived', 'draft') AND type NOT IN ('out', 'archived', 'draft')";
		} else {
			$ret .= " AND status NOT IN ('archived') AND type NOT IN ('archived')";
		}

		return $ret;
	}

	/**
	 * Preps the User's cache dir
	 */
	function preflightUserCache() {
		$path = clean_path($this->userCacheDir);
		if(!file_exists($this->userCacheDir))
			mkdir_recursive($path);

		$files = findAllFiles($path, array());

		foreach($files as $file) {
			unlink($file);
		}
	}

	function clearInboundAccountCache($ieId) {
		global $sugar_config;
		$cacheRoot = getcwd()."/{$sugar_config['cache_dir']}modules/Emails/{$ieId}";
		$files = findAllFiles($cacheRoot."/messages/", array());
		foreach($files as $file) {
			unlink($file);
		} // fn
		$files = findAllFiles($cacheRoot."/attachments/", array());
		foreach($files as $file) {
			unlink($file);
		} // for
	} // fn

	/**
	 * returns an array of EmailTemplates that the user has access to for the compose email screen
	 * @return array
	 */
	function getEmailTemplatesArray() {
		
		global $app_strings;

		if(ACLController::checkAccess('EmailTemplates', 'list', true) && ACLController::checkAccess('EmailTemplates', 'view', true)) {
			$et = new EmailTemplate();
			$etResult = $et->db->query($et->create_new_list_query('','',array(),array(),''));
			$email_templates_arr = array('' => $app_strings['LBL_NONE']);
			while($etA = $et->db->fetchByAssoc($etResult)) {
				$email_templates_arr[$etA['id']] = $etA['name'];
			}
		} else {
			$email_templates_arr = array('' => $app_strings['LBL_NONE']);
		}

		return $email_templates_arr;
	}

	function getFromAccountsArray($ie) {
        global $current_user;
        global $app_strings;

        $ieAccountsFull = $ie->retrieveAllByGroupIdWithGroupAccounts($current_user->id);
        $ieAccountsFrom= array();

        $oe = new OutboundEmail();
        $system = $oe->getSystemMailerSettings();
        $ret = $current_user->getUsersNameAndEmail();
		$ret['name'] = from_html($ret['name']);
		$useMyAccountString = true;

        if(empty($ret['email'])) {
        	$systemReturn = $current_user->getSystemDefaultNameAndEmail();
        	$ret['email'] = $systemReturn['email'];
        	$ret['name'] = from_html($systemReturn['name']);
        	$useMyAccountString = false;
		} // if

		$myAccountString = '';
		if ($useMyAccountString) {
			$myAccountString = " - {$app_strings['LBL_MY_ACCOUNT']}";
		} // if
        foreach($ieAccountsFull as $k => $v) {
        	$name = $v->get_stored_options('from_name');
        	$addr = $v->get_stored_options('from_addr');
        	if ($name != null && $addr != null) {
        		$name = from_html($name);
        		if (!$v->is_personal) {
                	$ieAccountsFrom[] = array("value" => $v->id, "text" => "{$name} ({$addr}) - {$app_strings['LBL_EMAIL_UPPER_CASE_GROUP']}");
        		} else {
                	$ieAccountsFrom[] = array("value" => $v->id, "text" => "{$name} ({$addr})");
        		} // else
        	} // if
        } // foreach

        if(!empty($system->id)) {
            
            $admin = new Administration();
            $admin->retrieveSettings(); //retrieve all admin settings.
            $ieAccountsFrom[] = array("value" => $system->id, "text" =>
                "{$ret['name']} ({$ret['email']}){$myAccountString}");
        } // if

        return $ieAccountsFrom;
    } // fn

    /**
     * This function will return all the accounts this user has access to based on the
     * match of the emailId passed in as a parameter
     *
     * @param unknown_type $ie
     * @return unknown
     */
	function getFromAllAccountsArray($ie, $ret) {
        global $current_user;
        global $app_strings;

        $ret['fromAccounts'] = array();
        if (!isset($ret['to']) && !empty($ret['from'])) {
	        $ret['fromAccounts']['status'] = false;
	        return $ret;
        }
        $ieAccountsFull = $ie->retrieveAllByGroupIdWithGroupAccounts($current_user->id);
		$foundInPersonalAccounts = false;
		$foundInGroupAccounts = false;
		$foundInSystemAccounts = false;

		//$toArray = array();
		if ($ret['type'] == "draft") {
			$toArray = explode(",", $ret['from']);
		} else {
			$toArray = $ie->email->email2ParseAddressesForAddressesOnly($ret['to']);
		} // else
        foreach($ieAccountsFull as $k => $v) {
        	$storedOptions = unserialize(base64_decode($v->stored_options));
			if (in_array($storedOptions['from_addr'], $toArray)) {
        		if ($v->is_personal) {
					$foundInPersonalAccounts = true;
					break;
				} else  {
					$foundInGroupAccounts = true;
				} // else
			} // if
        } // foreach

	    $oe = new OutboundEmail();
        $system = $oe->getSystemMailerSettings();

        $return = $current_user->getUsersNameAndEmail();
		$return['name'] = from_html($return['name']);
		$useMyAccountString = true;

        if(empty($return['email'])) {
        	$systemReturn = $current_user->getSystemDefaultNameAndEmail();
        	$return['email'] = $systemReturn['email'];
        	$return['name'] = from_html($systemReturn['name']);
        	$useMyAccountString = false;
		} // if

		$myAccountString = '';
		if ($useMyAccountString) {
			$myAccountString = " - {$app_strings['LBL_MY_ACCOUNT']}";
		} // if

        if(!empty($system->id)) {
            
            $admin = new Administration();
            $admin->retrieveSettings(); //retrieve all admin settings.
            if (in_array(trim($return['email']), $toArray)) {
            	$foundInSystemAccounts = true;
            } // if
        } // if

        if (!$foundInPersonalAccounts && !$foundInGroupAccounts && !$foundInSystemAccounts) {
	        $ret['fromAccounts']['status'] = false;
	        return $ret;
        } // if

        $ieAccountsFrom= array();
        foreach($ieAccountsFull as $k => $v) {
        	$storedOptions = unserialize(base64_decode($v->stored_options));
        	$storedOptionsName = from_html($storedOptions['from_name']);

        	$selected = false;
			if (in_array($storedOptions['from_addr'], $toArray)) {
        	//if ($ret['to'] == $storedOptions['from_addr']) {
        		$selected = true;
			} // if
        	if ($foundInPersonalAccounts) {
        		if ($v->is_personal) {
            		$ieAccountsFrom[] = array("value" => $v->id, "selected" => $selected, "text" => "{$storedOptionsName} ({$storedOptions['from_addr']})");
        		} // if
        	} else {
            	$ieAccountsFrom[] = array("value" => $v->id, "selected" => $selected, "text" => "{$storedOptionsName} ({$storedOptions['from_addr']}) - {$app_strings['LBL_EMAIL_UPPER_CASE_GROUP']}");
        	} // else
        } // foreach

        if(!empty($system->id)) {
            //require_once('modules/Administration/Administration.php');
            //$admin = new Administration();
            //$admin->retrieveSettings(); //retrieve all admin settings.
            if (!$foundInPersonalAccounts && !$foundInGroupAccounts && $foundInSystemAccounts) {
            $ieAccountsFrom[] = array("value" => $system->id, "selected" => true, "text" =>
                "{$return['name']} ({$return['email']}){$myAccountString}");
            } else {
            $ieAccountsFrom[] = array("value" => $system->id, "text" =>
                "{$return['name']} ({$return['email']}){$myAccountString}");
            } // else
        } // if

        $ret['fromAccounts']['status'] = ($foundInPersonalAccounts || $foundInGroupAccounts || $foundInSystemAccounts) ? true : false;
		$ret['fromAccounts']['data'] = $ieAccountsFrom;
        return $ret;
    } // fn


	/**
	 * takes an array and creates XML
	 * @param array Array to convert
	 * @param string Name to wrap highest level items in array
	 * @return string XML
	 */
	function arrayToXML($a, $paramName) {
		if(!is_array($a))
			return '';

		$bad = array("<",">","'",'"',"&");
		$good = array("&lt;", "&gt;", "&#39;", "&quot;","&amp;");

		$ret = "";

		for($i=0; $i<count($a); $i++) {
			$email = $a[$i];
			$ret .= "\n<{$paramName}>";

			foreach($email as $k => $v) {
				$ret .= "\n\t<{$k}>".str_replace($bad, $good, $v)."</{$k}>";
			}
			$ret .= "\n</{$paramName}>";
		}
		return $ret;
	}

	/**
	 * Re-used option getter for Show Accounts multiselect pane
	 */
	function getShowAccountsOptions(&$ie) {
		global $current_user;
		global $app_strings;

		$ieAccountsFull = $ie->retrieveAllByGroupId($current_user->id);
		//$ieAccountsShowOptions = "<option value=''>{$app_strings['LBL_NONE']}</option>\n";
		$ieAccountsShowOptionsMeta = array();
		$ieAccountsShowOptionsMeta[] = array("value" => "", "text" => $app_strings['LBL_NONE'], 'selected' => '');
		$showFolders = unserialize(base64_decode($current_user->getPreference('showFolders', 'Emails')));

		foreach($ieAccountsFull as $k => $v) {
			//$selected = (!empty($showFolders) && in_array($v->id, $showFolders)) ? "SELECTED" : "";
			$selected = (!empty($showFolders) && in_array($v->id, $showFolders)) ? true : false;
			$group = ($v->group_id != $current_user->id) ? $app_strings['LBL_EMAIL_GROUP']."." : "";
			//$ieAccountsShowOptions .= "<option {$selected} value='{$v->id}'>{$group}{$v->name}</option>\n";
			$ieAccountsShowOptionsMeta[] = array("value" => $v->id, "text" => $group.$v->name, 'selected' => $selected);
		}

		return $ieAccountsShowOptionsMeta;
	}

	function getShowAccountsOptionsForSearch(&$ie) {
		global $current_user;
		global $app_strings;

		$ieAccountsFull = $ie->retrieveAllByGroupId($current_user->id);
		//$ieAccountsShowOptions = "<option value=''>{$app_strings['LBL_NONE']}</option>\n";
		$ieAccountsShowOptionsMeta = array();
		$ieAccountsShowOptionsMeta[] = array("value" => "", "text" => $app_strings['LBL_NONE'], 'selected' => '');
		$showFolders = unserialize(base64_decode($current_user->getPreference('showFolders', 'Emails')));

		foreach($ieAccountsFull as $k => $v) {
			if(!in_array($v->id, $showFolders)) {
				continue;
			}
			$group = ($v->group_id != $current_user->id) ? $app_strings['LBL_EMAIL_GROUP']."." : "";
			//$ieAccountsShowOptions .= "<option {$selected} value='{$v->id}'>{$group}{$v->name}</option>\n";
			$ieAccountsShowOptionsMeta[] = array("value" => $v->id, "text" => $group.$v->name, 'protocol' => $v->protocol);
		}

		return $ieAccountsShowOptionsMeta;
	}
	/**
	 * Formats a display message on successful async call
	 * @param string $type Type of message to display
	 */
	function displaySuccessMessage($type) {
		global $app_strings;

		switch($type) {
			case "delete":
				$message = $app_strings['LBL_EMAIL_DELETE_SUCCESS'];
			break;

			default:
				$message = "NOOP: invalid type";
			break;
		}

		$this->smarty->assign('app_strings', $app_strings);
		$this->smarty->assign('message', $message);
		echo $this->smarty->fetch("modules/Emails/templates/successMessage.tpl");
	}

	/**
	 * Validates existence and expiration of a cache file
	 * @param string $ieId
	 * @param string $type Type of cache file: folders, messages, etc.
	 * @param string $file The cachefile name
	 * @param int refreshOffset Refresh time in secs.
	 * @return mixed.
	 */
	function validCacheFileExists($ieId, $type, $file, $refreshOffset=-1) {
		global $sugar_config;

		if($refreshOffset == -1) {
			$refreshOffset = $this->cacheTimeouts[$type]; // use defaults
		}

		$cacheFilePath = getcwd()."/{$sugar_config['cache_dir']}modules/Emails/{$ieId}/{$type}/{$file}";
		if(file_exists($cacheFilePath)) {
			return true;
			/*
			 * cache files are valid until "CheckMail" or "ClearCache" is triggered
			include($cacheFilePath); // provides $type array

			if($cacheFile['timestamp'] + $refreshOffset > strtotime('now')) {
				return true;
			}
			*/
		}

		return false;
	}

	/**
	 * retrieves the cached value
	 * @param string $ieId
	 * @param string $type Type of cache file: folders, messages, etc.
	 * @param string $file The cachefile name
	 * @param string $key name of cache value
	 * @return mixed
	 */
	function getCacheValue($ieId, $type, $file, $key) {
		global $sugar_config;

		$cacheFilePath = "{$sugar_config['cache_dir']}modules/Emails/{$ieId}/{$type}/{$file}";
		$cacheFile = array();

		if(file_exists($cacheFilePath)) {
			include($cacheFilePath); // provides $cacheFile

			if(isset($cacheFile[$key])) {
				$ret = unserialize($cacheFile[$key]);
				return $ret;
			}
		} else {
			$GLOBALS['log']->debug("EMAILUI: cache file not found [ {$cacheFilePath} ] - creating blank cache file");
			$this->writeCacheFile('retArr', array(), $ieId, $type, $file);
		}

		return null;
	}

	/**
	 * retrieves the cache file last touched time
	 * @param string $ieId
	 * @param string $type Type of cache file: folders, messages, etc.
	 * @param string $file The cachefile name
	 * @return string
	 */
	function getCacheTimestamp($ieId, $type, $file) {
		global $sugar_config;

		$cacheFilePath = "{$sugar_config['cache_dir']}modules/Emails/{$ieId}/{$type}/{$file}";
		$cacheFile = array();

		if(file_exists($cacheFilePath)) {
			include($cacheFilePath); // provides $cacheFile['timestamp']

			if(isset($cacheFile['timestamp'])) {
				$GLOBALS['log']->debug("EMAILUI: found timestamp [ {$cacheFile['timestamp']} ]");
				return $cacheFile['timestamp'];
			}
		}

		return '';
	}

	/**
	 * Updates the timestamp for a cache file - usually to mark a "check email"
	 * process
	 * @param string $ieId
	 * @param string $type Type of cache file: folders, messages, etc.
	 * @param string $file The cachefile name
	 */
	function setCacheTimestamp($ieId, $type, $file) {
		global $sugar_config;

		$cacheFilePath = "{$sugar_config['cache_dir']}modules/Emails/{$ieId}/{$type}/{$file}";
		$cacheFile = array();

		if(file_exists($cacheFilePath)) {
			include($cacheFilePath); // provides $cacheFile['timestamp']

			if(isset($cacheFile['timestamp'])) {
				$cacheFile['timestamp'] = strtotime('now');
				$GLOBALS['log']->debug("EMAILUI: setting updated timestamp [ {$cacheFile['timestamp']} ]");
				return $this->_writeCacheFile($cacheFile, $cacheFilePath);
			}
		}
	}


	/**
	 * Writes caches to flat file in cache dir.
	 * @param string $key Key to the main cache entry (not timestamp)
	 * @param mixed $var Variable to be cached
	 * @param string $ieId I-E focus ID
	 * @param string $type Folder in cache
	 * @param string $file Cache file name
	 */
	function writeCacheFile($key, $var, $ieId, $type, $file) {
		global $sugar_config;

		$the_file = clean_path("{$sugar_config['cache_dir']}/modules/Emails/{$ieId}/{$type}/{$file}");
		$timestamp = strtotime('now');
		$array = array();
		$array['timestamp'] = $timestamp;
		$array[$key] = serialize($var); // serialized since varexport_helper() can't handle PHP objects

		return $this->_writeCacheFile($array, $the_file);
	}

	/**
	 * Performs the actual file write.  Abstracted from writeCacheFile() for
	 * flexibility
	 * @param array $array The array to write to the cache
	 * @param string $file Full path (relative) with cache file name
	 * @return bool
	 */
	function _writeCacheFile($array, $file) {
		global $sugar_config;

		$arrayString = var_export_helper($array);

		$date = date("r");
	    $the_string =<<<eoq
<?php // created: {$date}
	\$cacheFile = {$arrayString};
?>
eoq;
	    if($fh = @sugar_fopen($file, "w")) {
	        fputs($fh, $the_string);
	        fclose($fh);
	        return true;
	    } else {
	    	$GLOBALS['log']->debug("EMAILUI: Could not write cache file [ {$file} ]");
	        return false;
	    }
	}

	/**
	 * generates XML output from an array
	 * @param array
	 * @param string master list Item
	 * @return string
	 */
	function xmlOutput($a, $paramName, $count=0, $fromCache=true, $unread=-1) {
		global $app_strings;
		$count = ($count > 0) ? $count : 0;

		if(isset($a['fromCache'])) {
			$cached = ($a['fromCache'] == 1) ? 1 : 0;
		} else {
			$cached = ($fromCache) ? 1 : 0;
		}

		if($a['mbox'] == 'undefined' || empty($a['mbox'])) {
			$a['mbox'] = $app_strings['LBL_NONE'];
		}

		$xml = $this->arrayToXML($a['out'], $paramName);

		$ret =<<<eoq
<?xml version="1.0" encoding="UTF-8"?>
<EmailPage>
<TotalCount>{$count}</TotalCount>
<UnreadCount>{$unread}</UnreadCount>
<FromCache> {$cached} </FromCache>
<{$paramName}s>
{$xml}
</{$paramName}s>
</EmailPage>
eoq;
		return $ret;
	}
} // end class def
