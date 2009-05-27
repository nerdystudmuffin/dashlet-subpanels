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


require_once("include/JSON.php");


class SugarEmailAddress extends SugarBean {
	var $table_name = 'email_addresses';
	var $module_name = "EmailAddresses";
	var $module_dir = 'EmailAddresses';
	var $object_name = 'EmailAddress';
	var $regex = "/^\w+(['\.\-\+]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,})+\$/";

	var $db;
	var $smarty;

	var $addresses = array(); // array of emails
	var $view = '';

	/**
	 * Sole constructor
	 */
	function SugarEmailAddress() {
		parent::SugarBean();
		$this->smarty = new Sugar_Smarty();
	}

	/**
	 * Legacy email address handling.  This is to allow support for SOAP or customizations
	 * @param string $id
	 * @param string $module
	 */
	function handleLegacySave($bean, $prefix = "") {
			if(!isset($_REQUEST) || !isset($_REQUEST['useEmailWidget'])) {
            if (empty($this->addresses)) {
			$this->addresses = array();
			$optOut = (isset($bean->email_opt_out) && $bean->email_opt_out == "1") ? true : false;
			$invalid = (isset($bean->invalid_email) && $bean->invalid_email == "1") ? true : false;
	
			$isPrimary = true;
			for($i = 1; $i <= 10; $i++){
				$email = 'email'.$i;
				if(isset($bean->$email) && !empty($bean->$email)){
					$opt_out_field = $email.'_opt_out';
					$invalid_field = $email.'_invalid';
					$field_optOut = (isset($bean->$opt_out_field)) ? $bean->$opt_out_field : $optOut;
					$field_invalid = (isset($bean->$invalid_field)) ? $bean->$invalid_field : $invalid;
					$this->addAddress($bean->$email, $isPrimary, false, $field_invalid, $field_optOut);
					$isPrimary = false;
                    }
				}
			}
		}
		$this->populateAddresses($bean->id, $bean->module_dir, array(),'');
		if(isset($_REQUEST) && isset($_REQUEST['useEmailWidget'])) {
		  	$this->populateLegacyFields($bean);
		}
	}

	/**
	 * Fills standard email1 legacy fields
	 * @param string id
	 * @param string module
	 * @return object
	 */
	function handleLegacyRetrieve(&$bean) {
        $module_dir = $this->getCorrectedModule($bean->module_dir);
		$this->addresses = $this->getAddressesByGUID($bean->id, $module_dir);
		$this->populateLegacyFields($bean);

		return;
	}
	
	function populateLegacyFields(&$bean){
		$primary_found = false;
		$alternate_found = false;
		$alternate2_found = false;
		foreach($this->addresses as $k=>$address) {
			if ($primary_found && $alternate_found)
				break;
			if ($address['primary_address'] == 1 && !$primary_found) {
				$primary_index = $k;
				$primary_found = true;
			} elseif (!$alternate_found) {
				$alternate_index = $k;
				$alternate_found = true;
			} elseif (!$alternate2_found){
				$alternate2_index = $k;
				$alternate2_found = true;
			}
		}

		if ($primary_found) {
			$bean->email1 = $this->addresses[$primary_index]['email_address'];
			$bean->email_opt_out = $this->addresses[$primary_index]['opt_out'];
			$bean->invalid_email = $this->addresses[$primary_index]['invalid_email'];
			if ($alternate_found) {
				$bean->email2 = $this->addresses[$alternate_index]['email_address'];
			}
		} elseif ($alternate_found) {
			// Use the first found alternate as email1.
			$bean->email1 = $this->addresses[$alternate_index]['email_address'];
			$bean->email_opt_out = $this->addresses[$alternate_index]['opt_out'];
			$bean->invalid_email = $this->addresses[$alternate_index]['invalid_email'];
			if ($alternate2_found) {
				$bean->email2 = $this->addresses[$alternate2_index]['email_address'];
			}
		}
	}
	
	/**
	 * Saves email addresses for a parent bean
	 * @param string $id Parent bean ID
	 * @param string $module Parent bean's module
	 * @param array $addresses Override of $_REQUEST vars, used to handle non-standard bean saves
	 * @param string $primary GUID of primary address
	 * @param string $replyTo GUID of reply-to address
	 * @param string $invalid GUID of invalid address
	 */
	function save($id, $module, $new_addrs=array(), $primary='', $replyTo='', $invalid='', $optOut='', $in_workflow=false) {
		if(empty($this->addresses) || $in_workflow){
			$this->populateAddresses($id, $module, $new_addrs,$primary);
		}
		//find all email addresses..
		$current_links=array();
        // Need to correct this to handle the Employee/User split
        $module = $this->getCorrectedModule($module);
		$q2="select *  from email_addr_bean_rel eabr WHERE eabr.bean_id = '{$id}' AND eabr.bean_module = '{$module}' and eabr.deleted=0";
		$r2 = $this->db->query($q2);
		while(($row2=$this->db->fetchByAssoc($r2)) != null ) {
			$current_links[$row2['email_address_id']]=$row2;
		}

		if (!empty($this->addresses)) {
			// insert new relationships and create email address record, if they don't exist
			foreach($this->addresses as $address) {
				if(!empty($address['email_address'])) {
					$guid = create_guid();
					$emailId = $this->AddUpdateEmailAddress($address['email_address'],$address['invalid_email'],$address['opt_out']);// this will save the email address if not found

					//verify linkage and flags.
					$upd_eabr="";
					if (isset($current_links[$emailId])) {
						if ($address['primary_address'] != $current_links[$emailId]['primary_address'] or $address['reply_to_address'] != $current_links[$emailId]['reply_to_address'] ) {
							$upd_eabr="UPDATE email_addr_bean_rel SET primary_address='{$address['primary_address']}', reply_to_address='{$address['reply_to_address']}' WHERE id='{$current_links[$emailId]['id']}'";
						}

						unset($current_links[$emailId]);
					} else {
						$upd_eabr = "INSERT INTO email_addr_bean_rel (id, email_address_id,bean_id, bean_module,primary_address,reply_to_address,date_created,date_modified,deleted) VALUES('{$guid}', '{$emailId}', '{$id}', '{$module}', {$address['primary_address']}, {$address['reply_to_address']}, '".gmdate($GLOBALS['timedate']->get_db_date_time_format())."', '".gmdate($GLOBALS['timedate']->get_db_date_time_format())."', 0)";
					}

					if (!empty($upd_eabr)) {
						$r2 = $this->db->query($upd_eabr);
					}
				}
			}
		}

		//delete link to dropped email address.
		if (!empty($current_links)) {

			$delete="";
			foreach ($current_links as $eabr) {

				$delete.=empty($delete) ? "'".$eabr['id'] . "' " : ",'" . $eabr['id'] . "'";
			}

			$eabr_unlink="update email_addr_bean_rel set deleted=1 where id in ({$delete})";
			$this->db->query($eabr_unlink);
		}
		return;
	}

    /**
	 * returns the number of email addresses found for a specifed bean
	 *
	 * @param  string $email       Address to match
	 * @param  object $bean        Bean to query against
	 * @param  string $addresstype Optional, pass a 1 to query against the primary address, 0 for the other addresses
	 * @return int                 Count of records found
	 */
	function getCountEmailAddressByBean(
        $email,
        $bean,
        $addresstype
        )
    {
		$emailCaps = strtoupper(trim($email));
		if(empty($emailCaps))
			return 0;

		$q = "SELECT *
                FROM email_addr_bean_rel eabl JOIN email_addresses ea
                        ON (ea.id = eabl.email_address_id)
                    JOIN {$bean->table_name} bean
                        ON (eabl.bean_id = bean.id)
                WHERE ea.email_address_caps = '{$emailCaps}'
                    and eabl.bean_module = '{$bean->module_dir}'
                    and eabl.primary_address = '{$addresstype}'
                    and eabl.deleted=0 ";

        $r = $this->db->query($q);

        // do it this way to make the count accurate in oracle
        $i = 0;
        while ($this->db->fetchByAssoc($r)) ++$i;

        return $i;
    }

	/**
	 * This function returns a contact or user ID if a matching email is found
	 * @param	$email		the email address to match
	 * @param	$table		which table to query
	 */
	function getRelatedId($email, $module) {
		$email = trim(strtoupper($email));
		$module = ucfirst($module);

		$q = "SELECT bean_id FROM email_addr_bean_rel eabr
				JOIN email_addresses ea ON (eabr.email_address_id = ea.id)
				WHERE bean_module = '{$module}' AND ea.email_address_caps = '{$email}' AND eabr.deleted=0";

		$r = $this->db->query($q, true);

		$retArr = array();
		while($a = $this->db->fetchByAssoc($r)) {
			$retArr[] = $a['bean_id'];
		}
		if(count($retArr) > 0) {
			return $retArr;
		} else {
			return false;
		}
	}

	/**
	 * returns a collection of beans matching the email address
	 * @param string $email Address to match
	 * @return array
	 */
	function getBeansByEmailAddress($email) {
		global $beanList;
		global $beanFiles;

		$ret = array();

		$email = trim($email);

		if(empty($email)) {
			return array();
		}

		$emailCaps = strtoupper($email);
		$q = "SELECT * FROM email_addr_bean_rel eabl JOIN email_addresses ea ON (ea.id = eabl.email_address_id)
				WHERE ea.email_address_caps = '{$emailCaps}' and eabl.deleted=0 ";
		$r = $this->db->query($q);

		while($a = $this->db->fetchByAssoc($r)) {
			if(isset($beanList[$a['bean_module']]) && !empty($beanList[$a['bean_module']])) {
				$className = $beanList[$a['bean_module']];

				if(isset($beanFiles[$className]) && !empty($beanFiles[$className])) {
					if(!class_exists($className)) {
						require_once($beanFiles[$className]);
					}

					$bean = new $className();
					$bean->retrieve($a['bean_id']);

					$ret[] = $bean;
				} else {
					$GLOBALS['log']->fatal("SUGAREMAILADDRESS: could not find valid class file for [ {$className} ]");
				}
			} else {
				$GLOBALS['log']->fatal("SUGAREMAILADDRESS: could not find valid class [ {$a['bean_module']} ]");
			}
		}

		return $ret;
	}

	/**
	 * Saves email addresses for a parent bean
	 * @param string $id Parent bean ID
	 * @param string $module Parent bean's module
	 * @param array $addresses Override of $_REQUEST vars, used to handle non-standard bean saves
	 * @param string $primary GUID of primary address
	 * @param string $replyTo GUID of reply-to address
	 * @param string $invalid GUID of invalid address
	 */
	function populateAddresses($id, $module, $new_addrs=array(), $primary='', $replyTo='', $invalid='', $optOut='') {
		$module = $this->getCorrectedModule($module);
		$post_from_email_address_widget = !empty($_REQUEST['emailAddressWidget']) ? true : false;
		$primaryValue = $primary;
		if(isset($_REQUEST['emailAddressPrimaryFlag'])) {
		   $primaryValue = $_REQUEST['emailAddressPrimaryFlag'];
		} else if(isset($_REQUEST[$module . 'emailAddressPrimaryFlag'])) {
		   $primaryValue = $_REQUEST[$module . 'emailAddressPrimaryFlag'];
		}

		$optOutValues = array();
		if(isset($_REQUEST['emailAddressOptOutFlag'])) {
		   $optOutValues = $_REQUEST['emailAddressOptOutFlag'];
		} else if(isset($_REQUEST[$module . 'emailAddressOptOutFlag'])) {
		   $optOutValues = $_REQUEST[$module . 'emailAddressOptOutFlag'];
		}

		$invalidValues = array();
		if(isset($_REQUEST['emailAddressInvalidFlag'])) {
		   $invalidValues = $_REQUEST['emailAddressInvalidFlag'];
		} else if(isset($_REQUEST[$module . 'emailAddressInvalidFlag'])) {
		   $invalidValues = $_REQUEST[$module . 'emailAddressInvalidFlag'];
		}

		$deleteValues = array();
		if(isset($_REQUEST['emailAddressDeleteFlag'])) {
		   $deleteValues = $_REQUEST['emailAddressDeleteFlag'];
		} else if(isset($_REQUEST[$module . 'emailAddressDeleteFlag'])) {
		   $deleteValues = $_REQUEST[$module . 'emailAddressDeleteFlag'];
		}
        $fromRequest = false;
		// determine which array to process
		foreach($_REQUEST as $k => $v) {
			if(strpos($k, 'emailAddress') !== false) {
				$fromRequest = true;
				break;
			}
		}
		// prep from form save
		$primaryField = $primary;
		$replyToField = '';
		$invalidField = '';
		$optOutField = '';
		if($fromRequest && empty($primary) && isset($primaryValue)) {
			$primaryField = $primaryValue;
		}
		if($fromRequest && empty($replyTo) && isset($_REQUEST['emailAddressReplyToFlag'])) {
			$replyToField = $_REQUEST['emailAddressReplyToFlag'];
		}
		if($fromRequest && empty($new_addrs)) {
			foreach($_REQUEST as $k => $v) {
				if(preg_match("/emailAddress[0-9]+$/i", $k) && !empty($v)) {
					$new_addrs[$k] = $v;
				}
			}
		}
		
		if($fromRequest && empty($new_addrs)) {
		    foreach($_REQUEST as $k => $v) {
		        if(preg_match("/emailAddressVerifiedValue[0-9]+$/i", $k) && !empty($v)) {
		            $validateFlag = str_replace("Value", "Flag", $k);
		            if (isset($_REQUEST[$validateFlag]) && $_REQUEST[$validateFlag] == "true")
		              $new_addrs[$k] = $v;
		        }
		    }
		}

		//empty the addresses array is the post happened from email address widget.
		if($post_from_email_address_widget) {
			$this->addresses=array();  //this gets populated during retrieve of the contact bean.
		} else {
			$optOutValues = array();
			$invalidValues = array();
			foreach($new_addrs as $k=>$email) {
			   preg_match('/emailAddress([0-9])+$/', $k, $matches);
			   $count = $matches[1];
			   $result = $this->db->query("SELECT opt_out, invalid_email from email_addresses where email_address_caps = '" . strtoupper($email) . "'");
			   if(!empty($result)) {
			      $row=$this->db->fetchByAssoc($result);
			   	  if(!empty($row['opt_out'])) {
	                 $optOutValues[$k] = "emailAddress$count";
			   	  }
			   	  if(!empty($row['invalid_email'])) {
			   	  	 $invalidValues[$k] = "emailAddress$count";
			   	  }
			   }
			}
		}

		// Re-populate the addresses class variable if we have new address(es).
		if (!empty($new_addrs)) {
			foreach($new_addrs as $k => $reqVar) {
				$key = preg_match("/^$module/s", $k) ? substr($k, strlen($module)) : $k;
				$reqVar = trim($reqVar);
				if(strpos($key, 'emailAddress') !== false) {
                    if(!empty($reqVar) && !in_array($key, $deleteValues)) {
						$primary	= ($key == $primaryValue) ? true : false;
						$replyTo	= ($key == $replyToField)	? true : false;
						$invalid	= (in_array($key, $invalidValues)) ? true : false;
						$optOut		= (in_array($key, $optOutValues)) ? true : false;
						$this->addAddress(trim($new_addrs[$k]), $primary, $replyTo, $invalid, $optOut);
					}
				}
			} //foreach
		}

		
	}

	/**
	 * Preps internal array structure for email addresses
	 * @param string $addr Email address
	 * @param bool $primary Default false
	 * @param bool $replyTo Default false
	 */
	function addAddress($addr, $primary=false, $replyTo=false, $invalid=false, $optOut=false) {
        $addr = html_entity_decode($addr, ENT_QUOTES);
		if(preg_match($this->regex, $addr)) {
			$primaryFlag = ($primary) ? '1' : '0';
			$replyToFlag = ($replyTo) ? '1' : '0';
			$invalidFlag = ($invalid) ? '1' : '0';
			$optOutFlag = ($optOut) ? '1' : '0';

			$addr = trim($addr);

			// If we have such address already, remove it and add new one in.
			foreach ($this->addresses as $k=>$address) {
				if ($address['email_address'] == $addr) {
					unset($this->addresses[$k]);
				} elseif ($primary && $address['primary_address'] == '1') {
					// We should only have one primary. If we are adding a primary but
					// we find an existing primary, reset this one's primary flag.
					$address['primary_address'] = '0';
				}
			}

			$this->addresses[] = array(
				'email_address' => $addr,
				'primary_address' => $primaryFlag,
				'reply_to_address' => $replyToFlag,
				'invalid_email' => $invalidFlag,
				'opt_out' => $optOutFlag,
			);
		} else {
			$GLOBALS['log']->fatal("SUGAREMAILADDRESS: address did not validate [ {$addr} ]");
		}
	}

	/**
	 * Updates invalid_email and opt_out flags for each address
	 */
	function updateFlags() {
		if(!empty($this->addresses)) {
			foreach($this->addresses as $addressMeta) {
				if(isset($addressMeta['email_address']) && !empty($addressMeta['email_address'])) {
					$address = $this->_cleanAddress($addressMeta['email_address']);

					$q = "SELECT * FROM email_addresses WHERE email_address = '{$address}'";
					$r = $this->db->query($q);
					$a = $this->db->fetchByAssoc($r);

					if(!empty($a)) {
						if(isset($a['invalid_email']) && isset($addressMeta['invalid_email']) && isset($addressMeta['opt_out']) && $a['invalid_email'] != $addressMeta['invalid_email'] || $a['opt_out'] != $addressMeta['opt_out']) {
							$qUpdate = "UPDATE email_addresses SET invalid_email = {$addressMeta['invalid_email']}, opt_out = {$addressMeta['opt_out']}, date_modified = '".gmdate($GLOBALS['timedate']->get_db_date_time_format())."' WHERE id = '{$a['id']}'";
							$rUpdate = $this->db->query($qUpdate);
						}
					}
				}
			}
		}
	}

	/**
	 * PRIVATE UTIL
	 * Normalizes an RFC-clean email address, returns a string that is the email address only
	 * @param string $addr Dirty email address
	 * @return string clean email address
	 */
	function _cleanAddress($addr) {
		$addr = trim(from_html($addr));

		if(strpos($addr, "<") !== false && strpos($addr, ">") !== false) {
			$address = trim(substr($addr, strpos($addr, "<") +1, strpos($addr, ">") - strpos($addr, "<") -1));
		} else {
			$address = trim($addr);
		}
		
		return $address;
	}

	/**
	 * preps a passed email address for email address storage
	 * @param array $addr Address in focus, must be RFC compliant
	 * @return string $id email_addresses ID
	 */
	function getEmailGUID($addr) {
		$address = $this->_cleanAddress($addr);
		$addressCaps = strtoupper($address);

		$q = "SELECT id FROM email_addresses WHERE email_address_caps = '{$addressCaps}'";
		$r = $this->db->query($q);
		$a = $this->db->fetchByAssoc($r);

		if(!empty($a) && !empty($a['id'])) {
			return $a['id'];
		} else {
            $guid = '';
            if(!empty($address)){
                $guid = create_guid();
                $address = $GLOBALS['db']->quote($address);
                $addressCaps = $GLOBALS['db']->quote($addressCaps);
                $qa = "INSERT INTO email_addresses (id, email_address, email_address_caps, date_created, date_modified, deleted)
                        VALUES('{$guid}', '{$address}', '{$addressCaps}', '".gmdate($GLOBALS['timedate']->get_db_date_time_format())."', '".gmdate($GLOBALS['timedate']->get_db_date_time_format())."', 0)";
                $ra = $this->db->query($qa);
            }
            return $guid;
		}
	}

	function AddUpdateEmailAddress($addr,$invalid=0,$opt_out=0) {

		$address = $this->_cleanAddress($addr);
		$addressCaps = strtoupper($address);

		$q = "SELECT * FROM email_addresses WHERE email_address_caps = '{$addressCaps}' and deleted=0";
		$r = $this->db->query($q);
		$a = $this->db->fetchByAssoc($r);
		if(!empty($a) && !empty($a['id'])) {
			//verify the opt out and invalid flags.
			if ($a['invalid_email'] != $invalid or $a['opt_out'] != $opt_out) {
				$upd_q="update email_addresses set invalid_email={$invalid}, opt_out={$opt_out},date_modified = '".gmdate($GLOBALS['timedate']->get_db_date_time_format())."' where id='{$a['id']}'";
				$upd_r= $this->db->query($upd_q);
			}
			return $a['id'];
		} else {
            $guid = '';
            if(!empty($address)){
                $guid = create_guid();
                $address = $GLOBALS['db']->quote($address);
                $addressCaps = $GLOBALS['db']->quote($addressCaps);
                $qa = "INSERT INTO email_addresses (id, email_address, email_address_caps, date_created, date_modified, deleted, invalid_email, opt_out)
                        VALUES('{$guid}', '{$address}', '{$addressCaps}', '".gmdate($GLOBALS['timedate']->get_db_date_time_format())."', '".gmdate($GLOBALS['timedate']->get_db_date_time_format())."', 0 , $invalid, $opt_out)";
                $this->db->query($qa);
            }
            return $guid;
		}
	}

	/**
	 * Returns Primary or newest email address
	 * @param object $focus Object in focus
	 * @return string email
	 */
	function getPrimaryAddress($focus,$parent_id=null,$parent_type=null) {

		$parent_type=empty($parent_type) ? $focus->module_dir : $parent_type;
		$parent_id=empty($parent_id) ? $focus->id : $parent_id;

		$q = "SELECT ea.email_address FROM email_addresses ea
				LEFT JOIN email_addr_bean_rel ear ON ea.id = ear.email_address_id
				WHERE ear.bean_module = '{$parent_type}'
				AND ear.bean_id = '{$parent_id}'
				AND ear.deleted = 0
				ORDER BY ear.primary_address DESC";
		$r = $this->db->limitQuery($q, 0, 1);
		$a = $this->db->fetchByAssoc($r);

		if(isset($a['email_address'])) {
			return $a['email_address'];
		}
		return '';
	}

	function getReplyToAddress($focus) {
		$q = "SELECT ea.email_address FROM email_addresses ea
				LEFT JOIN email_addr_bean_rel ear ON ea.id = ear.email_address_id
				WHERE ear.bean_module = '{$focus->module_dir}'
				AND ear.bean_id = '{$focus->id}'
				AND ear.deleted = 0
				ORDER BY ear.reply_to_address DESC";
		$r = $this->db->query($q);
		$a = $this->db->fetchByAssoc($r);

		if(isset($a['email_address'])) {
			return $a['email_address'];
		}
		return '';
	}

	/**
	 * Returns all email addresses by parent's GUID
	 * @param string $id Parent's GUID
	 * @param string $module Parent's module
	 * @return array
	 */
	function getAddressesByGUID($id, $module) {
		$return = array();
		$module = $this->getCorrectedModule($module);

		$q = "SELECT ea.*, ear.* FROM email_addresses ea
				LEFT JOIN email_addr_bean_rel ear ON ea.id = ear.email_address_id
				WHERE ear.bean_module = '{$module}'
				AND ear.bean_id = '{$id}'
				AND ear.deleted = 0
				ORDER BY ear.reply_to_address, ear.primary_address DESC";
		$r = $this->db->query($q);

		while($a = $this->db->fetchByAssoc($r)) {
			$return[] = $a;
		}

		return $return;
	}

	/**
	 * Returns the HTML/JS for the EmailAddress widget
	 * @param string $parent_id ID of parent bean, generally $focus
	 * @param string $module $focus' module
	 * @param bool asMetadata Default false
	 * @return string HTML/JS for widget
	 */
	function getEmailAddressWidgetEditView($id, $module, $asMetadata=false, $tpl='') {
		global $app_strings;

		$prefill = 'false';
		$prefillData = 'new Object()';
	    if(isset($_POST['is_converted']) && $_POST['is_converted']==true){
            $id=$_POST['return_id'];
            $module=$_POST['return_module'];
        }

        $prefillDataArr = array();
		if(!empty($id)) {
			$prefillDataArr = $this->getAddressesByGUID($id, $module);
		} else if(isset($_REQUEST['full_form']) && !empty($_REQUEST['emailAddress0'])){
			$count = 0;
			$key = 'emailAddress'.$count;
            while(isset($_REQUEST[$key])) {
            	   $email = $_REQUEST[$key];
            	   $prefillDataArr[] =  array('email_address'=>$email,
            	                   			 'primary_address'=>isset($_REQUEST['emailAddressPrimaryFlag']) && $_REQUEST['emailAddressPrimaryFlag'] == $key,
            	                   			 'invalid_email'=>isset($_REQUEST['emailAddressInvalidFlag']) && in_array($key, $_REQUEST['emailAddressInvalidFlag']),
            	                   			 'opt_out'=>isset($_REQUEST['emailAddressOptOutFlag']) && in_array($key, $_REQUEST['emailAddressOptOutFlag']),
            	                   			 'reply_to_address'=>false
            	                   		);
            	   $key = 'emailAddress' . ++$count;
            } //while
		}

		if(!empty($prefillDataArr)) {
			$json = new JSON(JSON_LOOSE_TYPE);
			$prefillData = $json->encode($prefillDataArr);
			$prefill = !empty($prefillDataArr) ? 'true' : 'false';
		}

		$this->smarty->assign('module', $module);
		$this->smarty->assign('app_strings', $app_strings);
		$this->smarty->assign('prefillEmailAddresses', $prefill);
		$this->smarty->assign('prefillData', $prefillData);
		//Set addDefaultAddress flag (do not add if it's from the Email module)
		$this->smarty->assign('addDefaultAddress', (isset($_REQUEST['module']) && $_REQUEST['module'] == 'Emails') ? 'false' : 'true');
		$this->smarty->assign('emailView', $this->view);

		if($module == 'Users') {
			$this->smarty->assign('useReplyTo', true);
		} else {
			$this->smarty->assign('useOptOut', true);
			$this->smarty->assign('useInvalid', true);
		}

		$template = empty($tpl) ? "include/SugarEmailAddress/templates/forEditView.tpl" : $tpl;
		$newEmail = $this->smarty->fetch($template);

		if($asMetadata) {
			// used by Email 2.0
			$ret = array();
			$ret['prefillData'] = $prefillDataArr;
			$ret['html'] = $newEmail;

			return $ret;
		}

		return $newEmail;
	}














































	/**
	 * Returns the HTML/JS for the EmailAddress widget
	 * @param object $focus Bean in focus
	 * @return string HTML/JS for widget
	 */
	function getEmailAddressWidgetDetailView($focus, $tpl='') {
		global $app_strings;
		global $current_user;
		$assign = array();
		if(empty($focus->id))return '';
		$prefillData = $this->getAddressesByGUID($focus->id, $focus->module_dir);

        foreach($prefillData as $addressItem) {
			$key = ($addressItem['primary_address'] == 1) ? 'primary' : "";
			$key = ($addressItem['reply_to_address'] == 1) ? 'reply_to' : $key;
			$key = ($addressItem['opt_out'] == 1) ? 'opt_out' : $key;
			$key = ($addressItem['invalid_email'] == 1) ? 'invalid' : $key;

			$assign[] = array('key' => $key, 'address' => $current_user->getEmailLink2($addressItem['email_address'], $focus).$addressItem['email_address']."</a>");
		}


		$this->smarty->assign('app_strings', $app_strings);
		$this->smarty->assign('emailAddresses', $assign);
		$templateFile = empty($tpl) ? "include/SugarEmailAddress/templates/forDetailView.tpl" : $tpl;
		$return = $this->smarty->fetch($templateFile);
		return $return;
	}


    /**
     * getEmailAddressWidgetDuplicatesView($focus)
	 * @param object $focus Bean in focus
	 * @return string HTML that contains hidden input values based off of HTML request
     */
    function getEmailAddressWidgetDuplicatesView($focus) {

		$count = 0;
		$emails = array();
		$primary = null;
		$optOut = array();
		$invalid = array();
		$mod = isset($focus) ? $focus->module_dir : "";

		while(isset($_POST[$mod . 'emailAddress' . $count])) {
              $emails[] = $_POST[$mod . 'emailAddress' . $count];
			  $count++;
		} //while

        if($count == 0) {
           return "";
        }

        if(isset($_POST[$mod . 'emailAddressPrimaryFlag'])) {
           $primary = $_POST[$mod . 'emailAddressPrimaryFlag'];
        }

        if(isset($_POST[$mod . 'emailAddressOptOutFlag'])) {
           foreach($_POST[$mod . 'emailAddressOptOutFlag'] as $v) {
              $optOut[] = $v;
           }
        }

        if(isset($_POST[$mod . 'emailAddressInvalidFlag'])) {
           foreach($_POST[$mod . 'emailAddressInvalidFlag'] as $v) {
              $invalid[] = $v;
           }
        }

		$this->smarty->assign('emails', $emails);
		$this->smarty->assign('primary', $primary);
		$this->smarty->assign('optOut', $optOut);
		$this->smarty->assign('invalid', $invalid);
		$this->smarty->assign('moduleDir', $mod);

		return $this->smarty->fetch("include/SugarEmailAddress/templates/forDuplicatesView.tpl");
    }

    /**
     * getFormBaseURL
     *
     */
    function getFormBaseURL($focus) {
    	$get = "";
		$count = 0;
		$mod = isset($focus) ? $focus->module_dir : "";
		while(isset($_REQUEST['emailAddress' . $count])) {
			  $get .= "&" . $mod . "emailAddress" . $count . "=" . urlencode($_REQUEST['emailAddress' . $count]);
      		  $count++;
		} //while

        $options = array('emailAddressPrimaryFlag', 'emailAddressOptOutFlag', 'emailAddressInvalidFlag');

        foreach($options as $option) {
	        $count = 0;
	        if(isset($_REQUEST[$option])) {
	           if(is_array($_REQUEST[$option])) {
		           foreach($_REQUEST[$option] as $optOut) {
		           	  $get .= "&" . $mod . $option . "[" . $count . "]=" . $optOut;
		           	  $count++;
		           } //foreach
	           } else {
	           	   $get .= "&" . $mod . $option . "=" . $_REQUEST[$option];
	           }
	        } //if
        } //foreach
        return $get;

    }

	function setView($view) {
	   $this->view = $view;
	}

/**
 * This function is here so the Employees/Users division can be handled cleanly in one place
 * @param object $focus SugarBean
 * @return string The value for the bean_module column in the email_addr_bean_rel table
 */
    function getCorrectedModule(&$module) {
        return ($module == "Employees")? "Users" : $module;
    }
} // end class def


/**
 * Convenience function for MVC (Mystique)
 * @param object $focus SugarBean
 * @param string $field unused
 * @param string $value unused
 * @param string $view DetailView or EditView
 * @return string
 */
function getEmailAddressWidget($focus, $field, $value, $view) {
	$sea = new SugarEmailAddress();
	$sea->setView($view);





		if($view == 'EditView' || $view == 'QuickCreate') {
			return $sea->getEmailAddressWidgetEditView($focus->id, $focus->module_dir, false);
		}









	return $sea->getEmailAddressWidgetDetailView($focus);
}

?>
