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

class OutboundEmail {
	/**
	 * Necessary
	 */
	var $db;
	var $field_defs = array(
		'id',
		'name',
		'type',
		'user_id',
		'mail_sendtype',
		'mail_smtpserver',
		'mail_smtpport',
		'mail_smtpuser',
		'mail_smtppass',
		'mail_smtpauth_req',
		'mail_smtpssl',
	);

	/**
	 * Columns
	 */
	var $id;
	var $name;
	var $type; // user or system
	var $user_id; // owner
	var $mail_sendtype; // smtp or sendmail
	var $mail_smtpserver;
	var $mail_smtpport;
	var $mail_smtpuser;
	var $mail_smtppass;
	var $mail_smtpauth_req; // bool
	var $mail_smtpssl; // bool

	/**
	 * Sole constructor
	 */
	function OutboundEmail() {
		$this->db = DBManagerFactory::getInstance();
	}

	/**
	 * Retrieves name value pairs for opts lists
	 */
	function getUserMailers($user) {
		global $app_strings;

		$q = "SELECT * FROM outbound_email WHERE user_id = '{$user->id}' AND type = 'user' ORDER BY name";
		$r = $this->db->query($q);

		/*$ret = array(
			'none' => array(
				'name' => $app_strings['LBL_NONE'],
			),
			'sendmail' => array(
				'name' => 'Sendmail',
				'mail_sendtype' => 'sendmail',
			),
		);*/

		$ret = array();

		$system = $this->getSystemMailerSettings();

		if(!empty($system->id) ) {
			if ($system->mail_sendtype == 'SMTP') {
				$ret[$system->id] = array('name' => "{$system->name} - {$system->mail_smtpserver}");
			} else {
				$ret[$system->id] = array('name' => "{$system->name} - sendmail");
			}
		}

		while($a = $this->db->fetchByAssoc($r)) {
			$oe = array();

			$name = $a['name'];

			if($a['mail_sendtype'] == 'SMTP' && !empty($a['mail_smtpserver'])) {
				$name = "{$a['name']} - {$a['mail_smtpserver']}";

				if($a['mail_smtpauth_req'] && !empty($a['mail_smtpuser'])) {
					$name .= ":{$a['mail_smtpuser']}";
				}
			}

			$oe['name'] = $name;

			$ret[$a['id']] = $oe;
		}

		//$ret['line'] = array('name' => '---');
		//$ret['add'] = array('name' => $app_strings['LBL_OUTBOUND_EMAIL_ADD_SERVER']);

		return $ret;
	}

	/**
	 * Retrieves a cascading mailer set
	 * @param object user
	 * @param string mailer_id
	 * @return object
	 */
	function getUserMailerSettings(&$user, $mailer_id='', $ieId='') {
		$mailer = '';

		if(!empty($mailer_id)) {
			$mailer = "AND id = '{$mailer_id}'";
		} elseif(!empty($ieId)) {
			$q = "SELECT stored_options FROM inbound_email WHERE id = '{$ieId}'";
			$r = $this->db->query($q);
			$a = $this->db->fetchByAssoc($r);

			if(!empty($a)) {
				$opts = unserialize(base64_decode($a['stored_options']));

				if(isset($opts['outbound_email'])) {
					$mailer = "AND id = '{$opts['outbound_email']}'";
				}
			}
		}

		$q = "SELECT id FROM outbound_email WHERE user_id = '{$user->id}' {$mailer}";
		$r = $this->db->query($q);
		$a = $this->db->fetchByAssoc($r);

		if(empty($a)) {
			$ret = $this->getSystemMailerSettings();
			/*
			$sendType = $user->getPreference('mail_sendtype');

			$this->id = '';
			$this->name = $user->user_name;
			$this->type = 'user';
			$this->user_id = $user->id;
			$this->mail_sendtype = empty($sendType) ? "sendmail" : "SMTP";
			$this->mail_smtpserver = $user->getPreference('mail_smtpserver');
			$this->mail_smtpport = $user->getPreference('mail_smtpport');
			$this->mail_smtpuser = $user->getPreference('mail_smtpuser');
			$this->mail_smtppass = $user->getPreference('mail_smtppass');
			$this->mail_smtpauth_req = $user->getPreference('mail_smtpauth_req');
			$this->mail_smtpssl = $user->getPreference('mail_smtpssl');
			$this->save();

			$ret = $this;
			*/
		} else {
			$ret = $this->retrieve($a['id']);
		}
		return $ret;
	}

	/**
	 * Retrieves a cascading mailer set
	 * @param object user
	 * @param string mailer_id
	 * @return object
	 */
	function getInboundMailerSettings(&$user, $mailer_id='', $ieId='') {
		$mailer = '';

		if(!empty($mailer_id)) {
			$mailer = "id = '{$mailer_id}'";
		} elseif(!empty($ieId)) {
			$q = "SELECT stored_options FROM inbound_email WHERE id = '{$ieId}'";
			$r = $this->db->query($q);
			$a = $this->db->fetchByAssoc($r);

			if(!empty($a)) {
				$opts = unserialize(base64_decode($a['stored_options']));

				if(isset($opts['outbound_email'])) {
					$mailer = "id = '{$opts['outbound_email']}'";
				} else {
					$mailer = "id = '{$ieId}'";
				}
			} else {
				// its possible that its an system account
				$mailer = "id = '{$ieId}'";
			}
		}

		if (empty($mailer)) {
			$mailer = "type = 'system'";
		} // if
		
		$q = "SELECT id FROM outbound_email WHERE {$mailer}";
		$r = $this->db->query($q);
		$a = $this->db->fetchByAssoc($r);

		if(empty($a)) {
			$ret = $this->getSystemMailerSettings();
			/*
			$sendType = $user->getPreference('mail_sendtype');

			$this->id = '';
			$this->name = $user->user_name;
			$this->type = 'user';
			$this->user_id = $user->id;
			$this->mail_sendtype = empty($sendType) ? "sendmail" : "SMTP";
			$this->mail_smtpserver = $user->getPreference('mail_smtpserver');
			$this->mail_smtpport = $user->getPreference('mail_smtpport');
			$this->mail_smtpuser = $user->getPreference('mail_smtpuser');
			$this->mail_smtppass = $user->getPreference('mail_smtppass');
			$this->mail_smtpauth_req = $user->getPreference('mail_smtpauth_req');
			$this->mail_smtpssl = $user->getPreference('mail_smtpssl');
			$this->save();

			$ret = $this;
			*/
		} else {
			$ret = $this->retrieve($a['id']);
		}
		return $ret;
	}

	/**
	 * Retrieves the system's Outbound options
	 */
	function getSystemMailerSettings() {
		$q = "SELECT id FROM outbound_email WHERE type = 'system'";
		$r = $this->db->query($q);
		$a = $this->db->fetchByAssoc($r);

		if(empty($a)) {
			$this->id = "";
			$this->name = 'system';
			$this->type = 'system';
			$this->user_id = '1';
			$this->mail_sendtype = 'SMTP';
			$this->mail_smtpserver = '';
			$this->mail_smtpport = 25;
			$this->mail_smtpuser = '';
			$this->mail_smtppass = '';
			$this->mail_smtpauth_req = 0;
			$this->mail_smtpssl = 0;

			$this->save();
			$ret = $this;
		} else {
			$ret = $this->retrieve($a['id']);
		}

		return $ret;
	}

	/**
	 * Populates this instance
	 * @param string $id
	 * @return object $this
	 */
	function retrieve($id) {
		require_once('include/utils/encryption_utils.php');
		$q = "SELECT * FROM outbound_email WHERE id = '{$id}'";
		$r = $this->db->query($q);
		$a = $this->db->fetchByAssoc($r);

		if(!empty($a)) {
			foreach($a as $k => $v) {
				if ($k == 'mail_smtppass' && !empty($v)) {
					$this->$k = blowfishDecode(blowfishGetKey('OutBoundEmail'), $v);
				} else {
					$this->$k = $v;
				} // else
			}
		}

		return $this;
	}

	function populateFromPost() {
		foreach($this->field_defs as $def) {
			if(isset($_POST[$def])) {
				$this->$def = $_POST[$def];
			} else {
				$this->$def = "";
			}
		}
	}

	/**
	 * saves an instance
	 */
	function save() {
		require_once('include/utils/encryption_utils.php');
		if(empty($this->id)) {
			$this->id = create_guid();

			$cols = '';
			$values = '';

			foreach($this->field_defs as $def) {
				if(!empty($cols)) {
					$cols .= ", ";
				}
				if(!empty($values)) {
					$values .= ", ";
				}
				$cols .= $def;
				if ($def == 'mail_smtppass' && !empty($this->mail_smtppass)) {
					$this->mail_smtppass = blowfishEncode(blowfishGetKey('OutBoundEmail'), $this->mail_smtppass);
				} // if
				if($def == 'mail_smtpauth_req' || $def == 'mail_smtpssl'){
					if(empty($this->$def)){
						$this->$def = 0;	
					}
					$values .= "{$this->$def}";
				}else{
					$values .= "'{$this->$def}'";
				}
			}

			$q  = "INSERT INTO outbound_email ($cols) VALUES ({$values})";
		} else {
			$values = "";
			foreach($this->field_defs as $def) {
				if(!empty($values)) {
					$values .= ", ";
				}

				if ($def == 'mail_smtppass' && !empty($this->$def)) {
					$this->$def = blowfishEncode(blowfishGetKey('OutBoundEmail'), $this->$def);
				} // if
				if($def == 'mail_smtpauth_req' || $def == 'mail_smtpssl'){
					if(empty($this->$def)){
						$this->$def = 0;	
					}
					$values .= "{$def} = {$this->$def}";
				}else{
					$values .= "{$def} = '{$this->$def}'";
				}
			}

			$q = "UPDATE outbound_email SET {$values} WHERE id = '{$this->id}'";
		}

		$this->db->query($q, true);
		return $this;
	}

	/**
	 * Saves system mailer.  Presumes all values are filled.
	 */
	function saveSystem() {
		$q = "SELECT id FROM outbound_email WHERE type = 'system'";
		$r = $this->db->query($q);
		$a = $this->db->fetchByAssoc($r);

		if(empty($a)) {
			$a['id'] = ''; // trigger insert
		}

		$this->id = $a['id'];
		$this->name = 'system';
		$this->type = 'system';
		$this->user_id = '1';
		$this->save();
	}

	/**
	 * Deletes an instance
	 */
	function delete() {
		if(empty($this->id)) {
			return false;
		}

		$q = "DELETE FROM outbound_email WHERE id = '{$this->id}'";
		return $this->db->query($q);
	}
}
