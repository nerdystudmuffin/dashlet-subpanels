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
require_once('include/SugarPHPMailer.php');

require_once('include/Pear/HTML_Safe/Safe.php');

class Email extends SugarBean {
	/* SugarBean schema */
	var $id;
	var $date_entered;
	var $date_modified;
	var $assigned_user_id;
	var $assigned_user_name;
	var $modified_user_id;
	var $created_by;



	var $deleted;
	var $from_addr;
	var $reply_to_addr;
	var $to_addrs;
    var $cc_addrs;
    var $bcc_addrs;
	var $message_id;

	/* Bean Attributes */
	var $name;
    var $type = 'archived';
    var $date_sent;
	var $status;
	var $intent;
	var $mailbox_id;
	var $from_name;

	var $reply_to_status;
	var $reply_to_name;
	var $reply_to_email;
	var $description;
	var $description_html;
	var $raw_source;
	var $parent_id;
	var $parent_type;

	/* link attributes */
	var $parent_name;


	/* legacy */
	var $date_start; // legacy
	var $time_start; // legacy
	var $from_addr_name;
	var $to_addrs_arr;
    var $cc_addrs_arr;
    var $bcc_addrs_arr;
	var $to_addrs_ids;
	var $to_addrs_names;
	var $to_addrs_emails;
	var $cc_addrs_ids;
	var $cc_addrs_names;
	var $cc_addrs_emails;
	var $bcc_addrs_ids;
	var $bcc_addrs_names;
	var $bcc_addrs_emails;
	var $contact_id;
	var $contact_name;

	/* Archive Email attrs */
	var $duration_hours;



	var $new_schema = true;
	var $table_name = 'emails';
	var $module_dir = 'Emails';
	var $object_name = 'Email';
	var $db;

	/* private attributes */
	var $rolloverStyle		= "<style>div#rollover {position: relative;float: left;margin: none;text-decoration: none;}div#rollover a:hover {padding: 0;text-decoration: none;}div#rollover a span {display: none;}div#rollover a:hover span {text-decoration: none;display: block;width: 250px;margin-top: 5px;margin-left: 5px;position: absolute;padding: 10px;color: #333;	border: 1px solid #ccc;	background-color: #fff;	font-size: 12px;z-index: 1000;}</style>\n";
	var $default_email_subject_values = array('Follow-up on proposal', 'Initial discussion', 'Review needs', 'Discuss pricing', 'Demo', 'Introduce all players', );
	var $cachePath;
	var $cacheFile			= 'robin.cache.php';
	var $replyDelimiter	= "> ";
	var $emailDescription;
	var $emailDescriptionHTML;
	var $emailRawSource;
	var $link_action;
	var $emailAddress;
	var $attachments = array();

	/* to support Email 2.0 */
	var $isDuplicate;
	var $uid;
	var $to;
	var $flagged;
	var $answered;
	var $seen;
	var $draft;
	var $relationshipMap = array(
		'Contacts'	=> 'emails_contacts_rel',
		'Accounts'	=> 'emails_accounts_rel',
		'Leads'		=> 'emails_leads_rel',
		'Users'		=> 'emails_users_rel',
		'Prospects'	=> 'emails_prospects_rel',
	);

	/* public */
	var $et;		// EmailUI object



	/**
	 * sole constructor
	 */
	function Email() {
	    $this->cachePath = $GLOBALS['sugar_config']['cache_dir'].'modules/Emails';
		parent::SugarBean();




		$this->safe = new HTML_Safe();
		$this->safe->clear();
		$this->emailAddress = new SugarEmailAddress();
	}

	function email2init() {
		require_once('modules/Emails/EmailUI.php');
		$this->et = new EmailUI();
	}
	function bean_implements($interface){
		switch($interface){
			case 'ACL': return true;
			default: return false;
		}

	}

	/**
	 * Presaves one attachment for new email 2.0 spec
	 * DOES NOT CREATE A NOTE
	 * @return string ID of note associated with the attachment
	 */
	function email2saveAttachment() {
		global $sugar_config;

		$filesError = array(
			0 => 'UPLOAD_ERR_OK - There is no error, the file uploaded with success.',
			1 => 'UPLOAD_ERR_INI_SIZE - The uploaded file exceeds the upload_max_filesize directive in php.ini.',
			2 => 'UPLOAD_ERR_FORM_SIZE - The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
			3 => 'UPLOAD_ERR_PARTIAL - The uploaded file was only partially uploaded.',
			4 => 'UPLOAD_ERR_NO_FILE - No file was uploaded.',
			5 => 'UNKNOWN ERROR',
			6 => 'UPLOAD_ERR_NO_TMP_DIR - Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.',
			7 => 'UPLOAD_ERR_CANT_WRITE - Failed to write file to disk. Introduced in PHP 5.1.0.',
		);

		// cn: Bug 5995 - rudimentary error checking
		if($_FILES['email_attachment']['error'] != 0 && $_FILES['email_attachment']['error'] != 4) {
			$GLOBALS['log']->debug('Email Attachment could not be attach due to error: '.$filesError[$_FILES['email_attachment']['error']]);
			return array();
		}

		if(isset($_FILES['email_attachment']) && is_uploaded_file($_FILES['email_attachment']['tmp_name'])) {
			$guid = create_guid();
			$cleanAttachmentFileName = from_html($_FILES['email_attachment']['name']);
			$GLOBALS['log']->debug("Email Attachment [ {$cleanAttachmentFileName} ] ");
			$cleanAttachmentFileName = str_replace("\\", "", $cleanAttachmentFileName);
			$GLOBALS['log']->debug("Email Attachment [ {$cleanAttachmentFileName} ] ");
			$destination = clean_path("{$this->et->userCacheDir}/{$guid}{$cleanAttachmentFileName}");
			$badExt = $this->safeAttachmentName($cleanAttachmentFileName);
			if ($badExt) {
				$destination = $destination . ".txt";
			} // if
			$fileName = $badExt ? $cleanAttachmentFileName . ".txt" : $cleanAttachmentFileName;
			if(move_uploaded_file($_FILES['email_attachment']['tmp_name'], $destination)) {
				return array(
					'guid' => $guid,
					'name' => $GLOBALS['db']->helper->escape_quote($fileName),
					'nameForDisplay' => $fileName
				);
			} else {
				$GLOBALS['log']->debug("Email Attachment [ {$cleanAttachmentFileName} ] could not be moved to cache dir");
				return array();
			}
		}
	}

	function safeAttachmentName($filename) {
		global $sugar_config;
		$badExtension = false;
		//get position of last "." in file name
		$file_ext_beg = strrpos($filename, ".");
		$file_ext = "";

		//get file extension
		if($file_ext_beg > 0) {
			$file_ext = substr($filename, $file_ext_beg + 1);
		}

		//check to see if this is a file with extension located in "badext"
		foreach($sugar_config['upload_badext'] as $badExt) {
			if(strtolower($file_ext) == strtolower($badExt)) {
				//if found, then append with .txt and break out of lookup
				$filename = $filename . ".txt";
				$badExtension = true;
				break; // no need to look for more
			} // if
		} // foreach

		return $badExtension;
	} // fn

	/**
	 * takes output from email 2.0 to/cc/bcc fields and returns appropriate arrays for usage by PHPMailer
	 * @param string addresses
	 * @return array
	 */
	function email2ParseAddresses($addresses) {
		$addresses = from_html($addresses);
		$addresses = str_replace(",", ";", $addresses);
		$exAddr = explode(";", $addresses);

		$ret = array();
		$clean = array("<", ">");
		$dirty = array("&lt;", "&gt;");

		foreach($exAddr as $addr) {
			$name = '';

			$addr = str_replace($dirty, $clean, $addr);

			if((strpos($addr, "<") === false) && (strpos($addr, ">") === false)) {
				$address = $addr;
			} else {
				$address = substr($addr, strpos($addr, "<") + 1, strpos($addr, ">") - 1 - strpos($addr, "<"));
				$name = substr($addr, 0, strpos($addr, "<"));
			}

			$addrTemp = array();
			$addrTemp['email'] = trim($address);
			$addrTemp['display'] = trim($name);
			$ret[] = $addrTemp;
		}

		return $ret;
	}

	/**
	 * takes output from email 2.0 to/cc/bcc fields and returns appropriate arrays for usage by PHPMailer
	 * @param string addresses
	 * @return array
	 */
	function email2ParseAddressesForAddressesOnly($addresses) {
		$addresses = from_html($addresses);
		$addresses = str_replace(",", ";", $addresses);
		$exAddr = explode(";", $addresses);

		$ret = array();
		$clean = array("<", ">");
		$dirty = array("&lt;", "&gt;");

		foreach($exAddr as $addr) {
			$name = '';

			$addr = str_replace($dirty, $clean, $addr);

			if(strpos($addr, "<") && strpos($addr, ">")) {
				$address = substr($addr, strpos($addr, "<") + 1, strpos($addr, ">") - 1 - strpos($addr, "<"));
			} else {
				$address = $addr;
			}

			$ret[] = trim($address);
		}

		return $ret;
	}

	/**
	 * Determines MIME-type encoding as possible.
	 * @param string $fileLocation relative path to file
	 * @return string MIME-type
	 */
	function email2GetMime($fileLocation) {
		if(function_exists('mime_content_type')) {
			$mime = mime_content_type($fileLocation);
		} elseif(function_exists('ext2mime')) {
			$mime = ext2mime($fileLocation);
		} else {
			$mime = 'application/octet-stream';
		}
		return $mime;
	}


	function decodeDuringSend($htmlData) {
	    $htmlData = str_replace("sugarLessThan", "&lt;", $htmlData);
	    $htmlData = str_replace("sugarGreaterThan", "&gt;", $htmlData);
		return $htmlData;
	}
	/**
	 * Sends Email for Email 2.0
	 */
	function email2Send($request) {
		
		
		
		global $mod_strings;
		global $current_user;
		global $sugar_config;
		global $locale;
		global $timedate;
		global $beanList;
		global $beanFiles;
        $OBCharset = $locale->getPrecedentPreference('default_email_charset');

		/**********************************************************************
		 * Sugar Email PREP
		 */
		/* preset GUID */

		$orignialId = "";
		if(!empty($this->id)) {
			$orignialId = 	$this->id;
		} // if

		if(empty($this->id)) {
			$this->id = create_guid();
			$this->new_with_id = true;
		}

		/* satisfy basic HTML email requirements */
		$this->name = $request['sendSubject'];
		$this->description_html = '&lt;html&gt;&lt;body&gt;'.$request['sendDescription'].'&lt;/body&gt;&lt;/html&gt;';

		/**********************************************************************
		 * PHPMAILER PREP
		 */
		$mail = new SugarPHPMailer();
		$mail = $this->setMailer($mail, '', $_REQUEST['fromAccount']);

		$subject = $this->name;
		$mail->Subject = from_html($this->name);

		// work-around legacy code in SugarPHPMailer
		if($_REQUEST['setEditor'] == 1) {
			$_REQUEST['description_html'] = $_REQUEST['sendDescription'];
			$this->description_html = $_REQUEST['description_html'];
		} else {
			$this->description_html = '';
			$this->description = $_REQUEST['sendDescription'];
		}
		// end work-around

		if (isset($request['saveDraft']) || ($this->type == 'draft' && $this->status == 'draft')) {
			if($this->type != 'draft' && $this->status != 'draft') {
	        	$this->id = create_guid();
	        	$this->new_with_id = true;
			} // if
			$q1 = "update emails_email_addr_rel set deleted = 1 WHERE email_id = '{$this->id}'";
			$r1 = $this->db->query($q1);
		} // if

		if (isset($request['saveDraft'])) {
			$this->type = 'draft';
			$this->status = 'draft';
			$forceSave = true;
		} else {
			/* Apply Email Templates */
			// do not parse email templates if the email is being saved as draft....
		    $toAddresses = $this->email2ParseAddresses($_REQUEST['sendTo']);
	        $sea = new SugarEmailAddress();
	        $object_arr = array();

	        foreach($toAddresses as $addrMeta) {
	            $addr = $addrMeta['email'];
	            $beans = $sea->getBeansByEmailAddress($addr);
	            foreach($beans as $bean) {
	                $object_arr[$bean->module_dir] = $bean->id;
	            }
	        }

			if( isset($_REQUEST['parent_type']) && !empty($_REQUEST['parent_type']) &&
				isset($_REQUEST['parent_id']) && !empty($_REQUEST['parent_id']) &&
				($_REQUEST['parent_type'] == 'Accounts' ||
				$_REQUEST['parent_type'] == 'Contacts' ||
				$_REQUEST['parent_type'] == 'Leads' ||
				$_REQUEST['parent_type'] == 'Users' ||
				$_REQUEST['parent_type'] == 'Prospects')) {
					if(isset($beanList[$_REQUEST['parent_type']]) && !empty($beanList[$_REQUEST['parent_type']])) {
						$className = $beanList[$_REQUEST['parent_type']];
						if(isset($beanFiles[$className]) && !empty($beanFiles[$className])) {
							if(!class_exists($className)) {
								require_once($beanFiles[$className]);
							}
							$bean = new $className();
							$bean->retrieve($_REQUEST['parent_id']);
	                		$object_arr[$bean->module_dir] = $bean->id;
						} // if
					} // if
			} // if

			if (!empty($object_arr)) {
				ksort($object_arr);
			} // if

	        /* template parsing */
	        if (empty($object_arr)) {
	          $object_arr= array('Contacts' => '123');
	        }
	        $object_arr['Users'] = $current_user->id;
	        $this->description_html = EmailTemplate::parse_template($this->description_html, $object_arr);
	        $this->name = EmailTemplate::parse_template($this->name, $object_arr);
	        $this->description = EmailTemplate::parse_template($this->description, $object_arr);
	        //$OBCharset = empty($request['sendCharset'])?$locale->getPrecedentPreference('default_email_charset'):$request['sendCharset'];
	        $this->description = html_entity_decode($this->description,ENT_COMPAT,'UTF-8');
			if($this->type != 'draft' && $this->status != 'draft') {
	        	$this->id = create_guid();
	        	$this->date_entered = "";
	        	$this->new_with_id = true;
		        $this->type = 'out';
		        $this->status = 'sent';
			}
        }

        if(isset($_REQUEST['parent_type']) && empty($_REQUEST['parent_type']) &&
			isset($_REQUEST['parent_id']) && empty($_REQUEST['parent_id']) ) {
				$this->parent_id = "";
				$this->parent_type = "";
		} // if


        $mail->Subject = $this->name;
        $mail = $this->handleBody($mail);
        $mail->Subject = $this->name;
        $this->description_html = from_html($this->description_html);
        $this->description_html = $this->decodeDuringSend($this->description_html);
		$this->description = $this->decodeDuringSend($this->description);

		/* from account */
		$replyToAddress = $current_user->emailAddress->getReplyToAddress($current_user);
		$replyToName = "";
		if(empty($request['fromAccount'])) {
			$defaults = $current_user->getPreferredEmail();
			$mail->From = $defaults['email'];
			$mail->FromName = $defaults['name'];
			$replyToName = $mail->FromName;
			//$replyToAddress = $current_user->emailAddress->getReplyToAddress($current_user);
		} else {
			// passed -> user -> system default
			$ie = new InboundEmail();
			$ie->retrieve($request['fromAccount']);
			$storedOptions = unserialize(base64_decode($ie->stored_options));
			$fromName = "";
			$fromAddress = "";
			$replyToName = "";
			$replyToAddress = "";
			if (!empty($storedOptions)) {
				$fromAddress = $storedOptions['from_addr'];
				$fromName = from_html($storedOptions['from_name']);
				$replyToAddress = (isset($storedOptions['reply_to_addr']) ? $storedOptions['reply_to_addr'] : "");
				$replyToName = (isset($storedOptions['reply_to_name']) ? from_html($storedOptions['reply_to_name']) : "");
			} // if
			$defaults = $current_user->getPreferredEmail();
			// Personal Account doesn't have reply To Name and Reply To Address. So add those columns on UI
			// After adding remove below code

			// code to remove
			if ($ie->is_personal) {
				if (empty($replyToAddress)) {
					$replyToAddress = $current_user->emailAddress->getReplyToAddress($current_user);
				} // if
				if (empty($replyToName)) {
					$replyToName = $defaults['name'];
				} // if
			}
			// end of code to remove
			$mail->From = (!empty($fromAddress)) ? $fromAddress : $defaults['email'];
			$mail->FromName = (!empty($fromName)) ? $fromName : $defaults['name'];
			$replyToName = (!empty($replyToName)) ? $replyToName : $mail->FromName;
		}

		$mail->Sender = $mail->From; /* set Return-Path field in header to reduce spam score in emails sent via Sugar's Email module */
		if (!empty($replyToAddress)) {
			$mail->AddReplyTo($replyToAddress,$locale->translateCharsetMIME(trim( $replyToName), 'UTF-8', $OBCharset));
		} else {
			$mail->AddReplyTo($mail->From,$locale->translateCharsetMIME(trim( $mail->FromName), 'UTF-8', $OBCharset));
		} // else
        $emailAddressCollection = array(); // used in linking to beans below
		// handle to/cc/bcc
		foreach($this->email2ParseAddresses($request['sendTo']) as $addr_arr) {
			if(empty($addr_arr['email'])) continue;

			if(empty($addr_arr['display'])) {
				$mail->AddAddress($addr_arr['email'], "");
			} else {
				$mail->AddAddress($addr_arr['email'],$locale->translateCharsetMIME(trim( $addr_arr['display']), 'UTF-8', $OBCharset));
			}
			$emailAddressCollection[] = $addr_arr['email'];
		}
		foreach($this->email2ParseAddresses($request['sendCc']) as $addr_arr) {
			if(empty($addr_arr['email'])) continue;

			if(empty($addr_arr['display'])) {
				$mail->AddCC($addr_arr['email'], "");
			} else {
				$mail->AddCC($addr_arr['email'],$locale->translateCharsetMIME(trim( $addr_arr['display']), 'UTF-8', $OBCharset));
			}
			$emailAddressCollection[] = $addr_arr['email'];
		}

		foreach($this->email2ParseAddresses($request['sendBcc']) as $addr_arr) {
			if(empty($addr_arr['email'])) continue;

			if(empty($addr_arr['display'])) {
				$mail->AddBCC($addr_arr['email'], "");
			} else {
				$mail->AddBCC($addr_arr['email'],$locale->translateCharsetMIME(trim( $addr_arr['display']), 'UTF-8', $OBCharset));
			}
			$emailAddressCollection[] = $addr_arr['email'];
		}


		/* parse remove attachments array */
		$removeAttachments = array();
		if(!empty($request['templateAttachmentsRemove'])) {
			$exRemove = explode("::", $request['templateAttachmentsRemove']);

			foreach($exRemove as $file) {
				$removeAttachments = substr($file, 0, 36);
			}
		}

		/* handle attachments */
		if(!empty($request['attachments'])) {
			$exAttachments = explode("::", $request['attachments']);

			foreach($exAttachments as $file) {
				$file = trim(from_html($file));
				$file = str_replace("\\", "", $file);
				if(!empty($file)) {
					$fileLocation = $this->et->userCacheDir."/{$file}";
					$filename = substr($file, 36, strlen($file)); // strip GUID	for PHPMailer class to name outbound file

					$mail->AddAttachment($fileLocation,$locale->translateCharsetMIME(trim($filename), 'UTF-8', $OBCharset), 'base64', $this->email2GetMime($fileLocation));
					//$mail->AddAttachment($fileLocation, $filename, 'base64');

					// only save attachments if we're archiving or drafting
					if((($this->type == 'draft') && !empty($this->id)) || (isset($request['saveToSugar']) && $request['saveToSugar'] == 1)) {
						$note = new Note();
						$note->id = create_guid();
						$note->new_with_id = true; // duplicating the note with files
						$note->parent_id = $this->id;
						$note->parent_type = $this->module_dir;
						$note->name = $filename;
						$note->filename = $filename;
						$noteFile = "{$sugar_config['upload_dir']}{$note->id}";
						$note->file_mime_type = $this->email2GetMime($noteFile);




						if(!copy($fileLocation, $noteFile)) {
							$GLOBALS['log']->debug("EMAIL 2.0: could not copy attachment file to cache/upload [ {$fileLocation} ]");
						}

						$note->save();
					}
				}
			}
		}

		/* handle sugar documents */
		if(!empty($request['documents'])) {
			$exDocs = explode("::", $request['documents']);

			
			

			foreach($exDocs as $docId) {
				$docId = trim($docId);
				if(!empty($docId)) {
					$doc = new Document();
					$docRev = new DocumentRevision();
					$doc->retrieve($docId);
					$docRev->retrieve($doc->document_revision_id);

					$filename = $docRev->filename;
					$fileLocation = "{$sugar_config['upload_dir']}{$docRev->id}";
					$mime_type = $docRev->file_mime_type;
					$mail->AddAttachment($fileLocation,$locale->translateCharsetMIME(trim($filename), 'UTF-8', $OBCharset), 'base64', $mime_type);

					// only save attachments if we're archiving or drafting
					if((($this->type == 'draft') && !empty($this->id)) || (isset($request['saveToSugar']) && $request['saveToSugar'] == 1)) {
						$note = new Note();
						$note->id = create_guid();
						$note->new_with_id = true; // duplicating the note with files
						$note->parent_id = $this->id;
						$note->parent_type = $this->module_dir;
						$note->name = $filename;
						$note->filename = $filename;
						$note->file_mime_type = $mime_type;



						$noteFile = "{$sugar_config['upload_dir']}{$note->id}";

						if(!copy($fileLocation, $noteFile)) {
							$GLOBALS['log']->debug("EMAIL 2.0: could not copy SugarDocument revision file to {$sugar_config['upload_dir']} [ {$fileLocation} ]");
						}

						$note->save();
					}
				}
			}
		}

		/* handle template attachments */
		if(!empty($request['templateAttachments'])) {
			
			$exNotes = explode("::", $request['templateAttachments']);
			foreach($exNotes as $noteId) {
				$noteId = trim($noteId);
				if(!empty($noteId)) {
					$note = new Note();
					$note->retrieve($noteId);
					if (!empty($note->id)) {
						$filename = $note->filename;
						$fileLocation = "{$sugar_config['upload_dir']}{$note->id}";
						$mime_type = $note->file_mime_type;
						if (!$note->embed_flag) {
							$mail->AddAttachment($fileLocation,$locale->translateCharsetMIME(trim($filename), 'UTF-8', $OBCharset), 'base64', $mime_type);
							// only save attachments if we're archiving or drafting
							if((($this->type == 'draft') && !empty($this->id)) || (isset($request['saveToSugar']) && $request['saveToSugar'] == 1)) {

								if ($note->parent_id != $this->id) {
									$note1 = new Note();
									$note1->id = create_guid();
									$note1->new_with_id = true; // duplicating the note with files
									$note1->parent_id = $this->id;
									$note1->parent_type = $this->module_dir;
									$note1->name = $filename;
									$note1->filename = $filename;
									$note1->file_mime_type = $mime_type;



									$noteFile = "{$sugar_config['upload_dir']}{$note1->id}";
									if(!copy($fileLocation, $noteFile)) {
										$GLOBALS['log']->debug("EMAIL 2.0: could not copy SugarDocument revision file to {$sugar_config['upload_dir']} [ {$fileLocation} ]");
									}
									$note1->save();
								} // if
							} // if

						} // if
					} else {
						$fileLocation = $this->et->userCacheDir."/{$noteId}";
						$filename = substr($noteId, 36, strlen($noteId)); // strip GUID	for PHPMailer class to name outbound file

						$mail->AddAttachment($fileLocation,$locale->translateCharsetMIME(trim($filename), 'UTF-8', $OBCharset), 'base64', $this->email2GetMime($fileLocation));


					}
				}
			}
		}



		/**********************************************************************
		 * Final Touches
		 */
		/* save email to sugar? */
		$forceSave = false;

		if($this->type == 'draft' && !isset($request['saveDraft'])) {
			// sending a draft email
			$this->type = 'out';
			$this->status = 'sent';
			$forceSave = true;
		} elseif(isset($request['saveDraft'])) {
			$this->type = 'draft';
			$this->status = 'draft';
			$forceSave = true;
		}

		      /**********************************************************************
         * SEND EMAIL (finally!)
         */
        $mailSent = false;
        if ($this->type != 'draft') {
            $mail->prepForOutbound($request['sendCharset']);
            if (!$mail->Send()) {
                $this->status = 'send_error';
                echo("Error emailing:".$mail->ErrorInfo);
                return false;
            }
        }

		if ((!(empty($orignialId) || isset($request['saveDraft']) || ($this->type == 'draft' && $this->status == 'draft'))) &&
			(($_REQUEST['composeType'] == 'reply') || ($_REQUEST['composeType'] == 'replyCase')) && ($orignialId != $this->id)) {
			$originalEmail = new Email();
			$originalEmail->retrieve($orignialId);
			$originalEmail->reply_to_status = 1;
			$originalEmail->save();
		} // if


		if(	$forceSave ||
			$this->type == 'draft' ||
			(isset($request['saveToSugar']) && $request['saveToSugar'] == 1)) {

			// saving a draft OR saving a sent email
			$decodedFromName = mb_decode_mimeheader($mail->FromName);
			$this->from_addr = "{$decodedFromName} <{$mail->From}>";
			$this->from_addr_name = $this->from_addr;
			$this->to_addrs = $_REQUEST['sendTo'];
			$this->to_addrs_names = $_REQUEST['sendTo'];
			$this->cc_addrs = $_REQUEST['sendCc'];
			$this->cc_addrs_names = $_REQUEST['sendCc'];
			$this->bcc_addrs = $_REQUEST['sendBcc'];
			$this->bcc_addrs_names = $_REQUEST['sendBcc'];



			$this->assigned_user_id = $current_user->id;

	        ///////////////////////////////////////////////////////////////////
	        ////    SAVE RAW MESSAGE
	        // cn: bug 10250 - MySQL requires certain settings to get the SQL buffer > 1Mb
	        if(isset($sugar_config['email_outbound_save_raw']) && $sugar_config['email_outbound_save_raw'] == true) {
		        $mail->SetMessageType();
		        $raw  = $mail->CreateHeader();
		        $raw .= $mail->CreateBody();
		        $this->raw_source = to_html($raw);
	        }
	        ////    END SAVE RAW MESSAGE
	        ///////////////////////////////////////////////////////////////////

	        //require_once("modules/InboundEmail/InboundEmail.php");
	        //$conv = InboundEmail::getUnixHeaderDate('');

			$this->date_sent = $timedate->convert_to_gmt_datetime('now');
	        $this->date_sent = $timedate->to_display_date_time($this->date_sent);
			///////////////////////////////////////////////////////////////////
			////	LINK EMAIL TO SUGARBEANS BASED ON EMAIL ADDY

			if( isset($_REQUEST['parent_type']) && !empty($_REQUEST['parent_type']) &&
				isset($_REQUEST['parent_id']) && !empty($_REQUEST['parent_id']) ) {
	                $this->parent_id = $_REQUEST['parent_id'];
	                $this->parent_type = $_REQUEST['parent_type'];
					$q = "SELECT count(*) c FROM emails_beans WHERE  email_id = '{$this->id}' AND bean_id = '{$_REQUEST['parent_id']}' AND bean_module = '{$_REQUEST['parent_type']}'";
					$r = $this->db->query($q);
					$a = $this->db->fetchByAssoc($r);
					if($a['c'] <= 0) {
						if(isset($beanList[$_REQUEST['parent_type']]) && !empty($beanList[$_REQUEST['parent_type']])) {
							$className = $beanList[$_REQUEST['parent_type']];
							if(isset($beanFiles[$className]) && !empty($beanFiles[$className])) {
								if(!class_exists($className)) {
									require_once($beanFiles[$className]);
								}
								$bean = new $className();
								$bean->retrieve($_REQUEST['parent_id']);
								if($bean->load_relationship('emails')) {
									$bean->emails->add($this->id);
								} // if

							} // if

						} // if

					} // if

				} else {
					if(!class_exists('aCase')) {
						
					}
					$c = new aCase();
					if($caseId = InboundEmail::getCaseIdFromCaseNumber($mail->Subject, $c)) {
						$c->retrieve($caseId);
						$c->load_relationship('emails');
						$c->emails->add($this->id);
						$this->parent_type = "Cases";
						$this->parent_id = $caseId;
					} // if

				} // else

			/*
			if(!empty($emailAddressCollection) && $this->type != 'draft') {
				foreach($emailAddressCollection as $emailAddress) {
					if(empty($emailAddress)) continue;

					$beansCollection = $this->emailAddress->getBeansByEmailAddress($emailAddress);

					foreach($beansCollection as $bean) {
						if($bean->load_relationship('emails')) {
							$bean->emails->add($this->id);
						} else {




						} // else
					} // for
				} // for

			}*/
			////	LINK EMAIL TO SUGARBEANS BASED ON EMAIL ADDY
			///////////////////////////////////////////////////////////////////
			$this->save();
		}

		if(!empty($request['fromAccount'])) {
			if (isset($ie->id) && !$ie->isPop3Protocol()) {
				$sentFolder = $ie->get_stored_options("sentFolder");
				if (!empty($sentFolder)) {
					$data = $mail->CreateHeader() . "\r\n" . $mail->CreateBody() . "\r\n";
					$ie->mailbox = $sentFolder;
					if ($ie->connectMailserver() == 'true') {
						$connectString = $ie->getConnectString($ie->getServiceString(), $ie->mailbox);
						$returnData = imap_append($ie->conn,$connectString, $data, "\\Seen");
						if (!$returnData) {
							$GLOBALS['log']->debug("could not copy email to {$ie->mailbox} for {$ie->name}");
						} // if
					} else {
						$GLOBALS['log']->debug("could not connect to mail serve for folder {$ie->mailbox} for {$ie->name}");
					} // else
				} else {
					$GLOBALS['log']->debug("could not copy email to {$ie->mailbox} sent folder as its empty");
				} // else
			} // if
		} // if
		return true;
	} // end email2send

	/**
	 * Generates a comma sperated name and addresses to be used in compose email screen for contacts or leads
	 * from listview
	 */
	function getNamePlusEmailAddressesForCompose($table, $idsArray) {
		global $locale;
		global $db;
		$table = strtolower($table);
		$returndata = array();
		$idsString = "";
		foreach($idsArray as $id) {
			if ($idsString != "") {
				$idsString = $idsString . ",";
			} // if
			$idsString = $idsString . "'" . $id . "'";
		} // foreach
		$where = "({$table}.deleted = 0 AND {$table}.id in ({$idsString}))";

		$selectColumn = "{$table}.first_name, {$table}.last_name, {$table}.salutation, {$table}.title";
		if ($table == 'accounts') {
			$selectColumn = "{$table}.name";
		}
		$query = "SELECT {$table}.id, {$selectColumn}, eabr.primary_address, ea.email_address";
		$query .= " FROM {$table} ";
		$query .= "JOIN email_addr_bean_rel eabr ON ({$table}.id = eabr.bean_id and eabr.deleted=0) ";
		$query .= "JOIN email_addresses ea ON (eabr.email_address_id = ea.id) ";



		$query .= " WHERE ({$where}) ORDER BY eabr.primary_address DESC";
		$r = $this->db->query($query);

		while($a = $this->db->fetchByAssoc($r)) {
			if (!isset($returndata[$a['id']])) {
				if ($table == 'accounts') {
					$returndata[$a['id']] = "{$a['name']} <".$a['email_address'].">";
				} else {
					$full_name = $locale->getLocaleFormattedName($a['first_name'], $a['last_name'], $a['salutation'], $a['title']);
					$returndata[$a['id']] = "{$full_name} <".$a['email_address'].">";
				} // else
			}
		}

		return join(",", array_values($returndata));
    }

	/**
	 * Overrides
	 */
	///////////////////////////////////////////////////////////////////////////
	////	SAVERS
	function save($check_notify = false) {
		if($this->isDuplicate) {
			$GLOBALS['log']->debug("EMAIL - tried to save a duplicate Email record");
		} else {

			if(empty($this->id)) {
				$this->id = create_guid();
				$this->new_with_id = true;
			}
			$this->saveEmailText();
			$this->saveEmailAddresses();

			$GLOBALS['log']->debug('-------------------------------> Email called save()');

			// handle legacy concatenation of date and time fields
			if(empty($this->date_sent)) $this->date_sent = $this->date_start." ".$this->time_start;
			parent::save($check_notify);
		}
	}

	/**
	 * Handles normalization of Email Addressess
	 */
	function saveEmailAddresses() {
		// from, single address
		$fromId = $this->emailAddress->getEmailGUID(from_html($this->from_addr));
        if(!empty($fromId)){
		  $this->linkEmailToAddress($fromId, 'from');
        }

		// to, multiple
		$replace = array(",",";");
		$toaddrs = str_replace($replace, "::", from_html($this->to_addrs));
		$exToAddrs = explode("::", $toaddrs);

		if(!empty($exToAddrs)) {
			foreach($exToAddrs as $toaddr) {
				$toaddr = trim($toaddr);
				if(!empty($toaddr)) {
					$toId = $this->emailAddress->getEmailGUID($toaddr);
					$this->linkEmailToAddress($toId, 'to');
				}
			}
		}

		// cc, multiple
		$ccAddrs = str_replace($replace, "::", from_html($this->cc_addrs));
		$exccAddrs = explode("::", $ccAddrs);

		if(!empty($exccAddrs)) {
			foreach($exccAddrs as $ccAddr) {
				$ccAddr = trim($ccAddr);
				if(!empty($ccAddr)) {
					$ccId = $this->emailAddress->getEmailGUID($ccAddr);
					$this->linkEmailToAddress($ccId, 'cc');
				}
			}
		}

		// bcc, multiple
		$bccAddrs = str_replace($replace, "::", from_html($this->bcc_addrs));
		$exbccAddrs = explode("::", $bccAddrs);
		if(!empty($exbccAddrs)) {
			foreach($exbccAddrs as $bccAddr) {
				$bccAddr = trim($bccAddr);
				if(!empty($bccAddr)) {
					$bccId = $this->emailAddress->getEmailGUID($bccAddr);
					$this->linkEmailToAddress($bccId, 'bcc');
				}
			}
		}
	}

	function linkEmailToAddress($id, $type) {
		// TODO: make this update?
		$q1 = "SELECT * FROM emails_email_addr_rel WHERE email_id = '{$this->id}' AND email_address_id = '{$id}' AND address_type = '{$type}' AND deleted = 0";
		$r1 = $this->db->query($q1);
		$a1 = $this->db->fetchByAssoc($r1);

		if(!empty($a1) && !empty($a1['id'])) {
			return $a1['id'];
		} else {
			$guid = create_guid();
			$q2 = "INSERT INTO emails_email_addr_rel VALUES('{$guid}', '{$this->id}', '{$type}', '{$id}', 0)";
			$r2 = $this->db->query($q2);
		}

		return $guid;
	}


	function saveEmailText() {
		$isOracle = ($this->db->dbType == "oci8") ? true : false;
		if ($isOracle) {



		} else {
			$description = $this->db->quote(trim($this->description));
			$description_html = $this->db->quoteForEmail(trim($this->description_html));
			$raw_source = $this->db->quote(trim($this->raw_source));
			$fromAddressName = $this->db->helper->escape_quote($this->from_addr_name);
			$toAddressName = $this->db->helper->escape_quote($this->to_addrs_names);
			$ccAddressName = $this->db->helper->escape_quote($this->cc_addrs_names);
			$bccAddressName = $this->db->helper->escape_quote($this->bcc_addrs_names);
			$replyToAddrName = $this->db->helper->escape_quote($this->reply_to_addr);

			if(!$this->new_with_id) {
				$q = "UPDATE emails_text SET from_addr = '{$fromAddressName}', to_addrs = '{$toAddressName}', cc_addrs = '{$ccAddressName}', bcc_addrs = '{$bccAddressName}', reply_to_addr = '{$replyToAddrName}', description = '{$description}', description_html = '{$description_html}', raw_source = '{$raw_source}' WHERE email_id = '{$this->id}'";
			} else {
				$q = "INSERT INTO emails_text (email_id, from_addr, to_addrs, cc_addrs, bcc_addrs, reply_to_addr, description, description_html, raw_source, deleted) VALUES('{$this->id}', '{$fromAddressName}', '{$toAddressName}', '{$ccAddressName}', '{$bccAddressName}', '{$replyToAddrName}', '{$description}', '{$description_html}', '{$raw_source}', 0)";
			}
			$this->db->query($q);

		} // else
	}













































	///////////////////////////////////////////////////////////////////////////
	////	RETRIEVERS
	function retrieve($id, $encoded=true, $deleted=true) {
		// cn: bug 11915, return SugarBean's retrieve() call bean instead of $this
		$ret = parent::retrieve($id, $encoded, $deleted);

		if($ret) {
			$ret->raw_source = to_html($ret->safeText(from_html($ret->raw_source)));
			$ret->description = to_html($ret->safeText(from_html($ret->description)));
			$ret->description_html = $ret->safeText($ret->description_html);
			$ret->retrieveEmailText();
			$ret->retrieveEmailAddresses();

			$dateSent = explode(' ', $ret->date_sent);
			if (!empty($dateSent)) {
			$ret->date_start = $dateSent[0];
			$ret->time_start = $dateSent[1];
			} else {
				$ret->date_start = '';
				$ret->time_start = '';
			} // else
			// for Email 2.0
			foreach($ret as $k => $v) {
				$this->$k = $v;
			}
		}
		return $ret;
	}


	/**
	 * Retrieves email addresses from GUIDs
	 */
	function retrieveEmailAddresses() {
		$return = array();

		$q = "SELECT email_address, address_type
				FROM emails_email_addr_rel eam
				JOIN email_addresses ea ON ea.id = eam.email_address_id
				WHERE eam.email_id = '{$this->id}' AND eam.deleted=0";
		$r = $this->db->query($q);

		while($a = $this->db->fetchByAssoc($r)) {
			if(!isset($return[$a['address_type']])) {
				$return[$a['address_type']] = array();
			}
			$return[$a['address_type']][] = $a['email_address'];
		}

		if(count($return) > 0) {
			if(isset($return['from'])) {
				$this->from_addr = implode(", ", $return['from']);
			}
			if(isset($return['to'])) {
				$this->to_addrs = implode(", ", $return['to']);
			}
			if(isset($return['cc'])) {
				$this->cc_addrs = implode(", ", $return['cc']);
			}
			if(isset($return['bcc'])) {
				$this->bcc_addrs = implode(", ", $return['bcc']);
			}
		}
	}

	/**
	 * Handles longtext fields
	 */
	function retrieveEmailText() {
		$q = "SELECT from_addr, reply_to_addr, to_addrs, cc_addrs, bcc_addrs, description, description_html, raw_source FROM emails_text WHERE email_id = '{$this->id}'";
		$r = $this->db->query($q);
		$a = $this->db->fetchByAssoc($r, -1, false);

		$this->description = $a['description'];
		$this->description_html = $a['description_html'];
		$this->raw_source = $a['raw_source'];
		$this->from_addr_name = $a['from_addr'];
		$this->reply_to_addr  = $a['reply_to_addr'];
		$this->to_addrs_names = $a['to_addrs'];
		$this->cc_addrs_names = $a['cc_addrs'];
		$this->bcc_addrs_names = $a['bcc_addrs'];
	}

	function delete($id='') {
		if(empty($id))
			$id = $this->id;

		$q  = "UPDATE emails SET deleted = 1 WHERE id = '{$id}'";
		$qt = "UPDATE emails_text SET deleted = 1 WHERE email_id = '{$id}'";
		$r  = $this->db->query($q);
		$rt = $this->db->query($qt);
	}

	/**
	 * creates the standard "Forward" info at the top of the forwarded message
	 * @return string
	 */
	function getForwardHeader() {
		global $mod_strings;
		global $current_user;

		//$from = str_replace(array("&gt;","&lt;"), array(")","("), $this->from_name);
		$from = to_html($this->from_name);
		$subject = to_html($this->name);
		$ret  = "<br /><br />";
		$ret .= $this->replyDelimiter."{$mod_strings['LBL_FROM']} {$from}<br />";
		$ret .= $this->replyDelimiter."{$mod_strings['LBL_DATE_SENT']} {$this->date_sent}<br />";
		$ret .= $this->replyDelimiter."{$mod_strings['LBL_TO']} {$this->to_addrs}<br />";
		$ret .= $this->replyDelimiter."{$mod_strings['LBL_CC']} {$this->cc_addrs}<br />";
		$ret .= $this->replyDelimiter."{$mod_strings['LBL_SUBJECT']} {$subject}<br />";
		$ret .= $this->replyDelimiter."<br />";

		return $ret;
		//return from_html($ret);
	}

    /**
     * retrieves Notes that belong to this Email and stuffs them into the "attachments" attribute
     */
    function getNotes($id, $duplicate=false) {
        if(!class_exists('Note')) {
            
        }

        $exRemoved = array();
		if(isset($_REQUEST['removeAttachment'])) {
			$exRemoved = explode('::', $_REQUEST['removeAttachment']);
		}

        $noteArray = array();
        $q = "SELECT id FROM notes WHERE parent_id = '".$id."'";
        $r = $this->db->query($q);

        while($a = $this->db->fetchByAssoc($r)) {
        	if(!in_array($a['id'], $exRemoved)) {
	            $note = new Note();
	            $note->retrieve($a['id']);

	            // duplicate actual file when creating forwards
		        if($duplicate) {
		        	if(!class_exists('UploadFile')) {
		        		require_once('include/upload_file.php');
		        	}
		        	// save a brand new Note
		        	$noteDupe->id = create_guid();
		        	$noteDupe->new_with_id = true;
					$noteDupe->parent_id = $this->id;
					$noteDupe->parent_type = $this->module_dir;

					$noteFile = new UploadFile('none');
					$noteFile->duplicate_file($a['id'], $note->id, $note->filename);

					$note->save();
		        }
		        // add Note to attachments array
	            $this->attachments[] = $note;
        	}
        }
    }

	/**
	 * creates the standard "Reply" info at the top of the forwarded message
	 * @return string
	 */
	function getReplyHeader() {
		global $mod_strings;
		global $current_user;

		$from = str_replace(array("&gt;","&lt;", ">","<"), array(")","(",")","("), $this->from_name);
		$ret  = "<br>{$mod_strings['LBL_REPLY_HEADER_1']} {$this->date_start}, {$this->time_start}, {$from} {$mod_strings['LBL_REPLY_HEADER_2']}";

		return from_html($ret);
	}

	/**
	 * Quotes plain-text email text
	 * @param string $text
	 * @return string
	 */
	function quotePlainTextEmail($text) {
		$quoted = "\n";

		// plain-text
		$desc = nl2br(trim($text));
		$exDesc = explode('<br />', $desc);

		foreach($exDesc as $k => $line) {
			$quoted .= '> '.trim($line)."\r";
		}

		return $quoted;
	}

	/**
	 * "quotes" (i.e., "> my text yadda" the HTML part of an email
	 * @param string $text HTML text to quote
	 * @return string
	 */
	function quoteHtmlEmail($text) {
		$text = trim(from_html($text));

		if(empty($text)) {
			return '';
		}
		$out = "<div style='border-left:1px solid #00c; padding:5px; margin-left:10px;'>{$text}</div>";

		return $out;
	}

	/**
	 * "quotes" (i.e., "> my text yadda" the HTML part of an email
	 * @param string $text HTML text to quote
	 * @return string
	 */
	function quoteHtmlEmailForNewEmailUI($text) {
		$text = trim($text);

		if(empty($text)) {
			return '';
		}
		$out = "<div style='border-left:1px solid #00c; padding:5px; margin-left:10px;'>{$text}</div>";

		return $out;
	}



	///////////////////////////////////////////////////////////////////////////
	////	LEGACY CODE
	/**
	 * Safes description text (both HTML and Plain Text) for display
	 * @param string str The text to safe
	 * @return string Safed text
	 */
	function safeText($str) {
		// Safe_HTML
		$this->safe->clear();
		$ret = $this->safe->parse($str);

		// Julian's XSS cleaner
		$potentials = clean_xss($str, false);

		if(is_array($potentials) && !empty($potentials)) {
			//_ppl($potentials);
			foreach($potentials as $bad) {
				$ret = str_replace($bad, "", $ret);
			}
		}

		// clean <HTML> and <BODY> tags
		$html = '#<\\\\\?HTML[\w =\'\"\&]*>#sim';
		$body = '#<\\\\\?BODY[\w =\'\"\&]*>#sim';

		$ret = preg_replace($html, "", $ret);
		$ret = preg_replace($body, "", $ret);

		return $ret;
	}

	/**
	 * Ensures that the user is able to send outbound emails
	 */
	function check_email_settings() {
		global $current_user;

		$mail_fromaddress = $current_user->emailAddress->getPrimaryAddress($current_user);
		$replyToName = $current_user->getPreference('mail_fromname');
		$mail_fromname = (!empty($replyToName)) ? $current_user->getPreference('mail_fromname') : $current_user->full_name;

		if(empty($mail_fromaddress)) {
			return false;
		}
		if(empty($mail_fromname)) {
	  		return false;
		}

    	$send_type = $current_user->getPreference('mail_sendtype') ;
		if (!empty($send_type) && $send_type == "SMTP") {
			$mail_smtpserver = $current_user->getPreference('mail_smtpserver');
			$mail_smtpport = $current_user->getPreference('mail_smtpport');
			$mail_smtpauth_req = $current_user->getPreference('mail_smtpauth_req');
			$mail_smtpuser = $current_user->getPreference('mail_smtpuser');
			$mail_smtppass = $current_user->getPreference('mail_smtppass');
			if (empty($mail_smtpserver) ||
				empty($mail_smtpport) ||
                (!empty($mail_smtpauth_req) && ( empty($mail_smtpuser) || empty($mail_smtppass)))
			) {
				return false;
			}
		}
		return true;
	}

	/**
	 * outputs JS to set fields in the MassUpdate form in the "My Inbox" view
	 */
	function js_set_archived() {
		global $mod_strings;
		$script = '
		<script type="text/javascript" language="JavaScript"><!-- Begin
			function setArchived() {
				var form = document.getElementById("MassUpdate");
				var status = document.getElementById("mass_status");
				var ok = false;

				for(var i=0; i < form.elements.length; i++) {
					if(form.elements[i].name == "mass[]") {
						if(form.elements[i].checked == true) {
							ok = true;
						}
					}
				}

				if(ok == true) {
					var user = document.getElementById("mass_assigned_user_name");
					var team = document.getElementById("team");

					user.value = "";
					for(var j=0; j<status.length; j++) {
						if(status.options[j].value == "archived") {
							status.options[j].selected = true;
							status.selectedIndex = j; // for IE
						}
					}

					form.submit();
				} else {
					alert("'.$mod_strings['ERR_ARCHIVE_EMAIL'].'");
				}

			}
		//  End --></script>';
		return $script;
	}

	/**
	 * replaces the javascript in utils.php - more specialized
	 */
	function u_get_clear_form_js($type='', $group='', $assigned_user_id='') {
		$uType				= '';
		$uGroup				= '';
		$uAssigned_user_id	= '';

		if(!empty($type)) { $uType = '&type='.$type; }
		if(!empty($group)) { $uGroup = '&group='.$group; }
		if(!empty($assigned_user_id)) { $uAssigned_user_id = '&assigned_user_id='.$assigned_user_id; }

		$the_script = '
		<script type="text/javascript" language="JavaScript"><!-- Begin
			function clear_form(form) {
				var newLoc = "index.php?action=" + form.action.value + "&module=" + form.module.value + "&query=true&clear_query=true'.$uType.$uGroup.$uAssigned_user_id.'";
				if(typeof(form.advanced) != "undefined"){
					newLoc += "&advanced=" + form.advanced.value;
				}
				document.location.href= newLoc;
			}
		//  End --></script>';
		return $the_script;
	}

	function pickOneButton() {
		global $theme;
		global $mod_strings;
		$out = '<div><input	title="'.$mod_strings['LBL_BUTTON_GRAB_TITLE'].'"
						accessKey="'.$mod_strings['LBL_BUTTON_GRAB_KEY'].'"
						class="button"
						type="button" name="button"
						onClick="window.location=\'index.php?module=Emails&action=Grab\';"
						style="margin-bottom:2px"
						value="  '.$mod_strings['LBL_BUTTON_GRAB'].'  "></div>';
		return $out;
	}

	/**
	 * Determines what Editor (HTML or Plain-text) the current_user uses;
	 * @return string Editor type
	 */
	function getUserEditorPreference() {
		global $sugar_config;
		global $current_user;

		$editor = '';

		if(!isset($sugar_config['email_default_editor'])) {
			$sugar_config = $current_user->setDefaultsInConfig();
		}

		$userEditor = $current_user->getPreference('email_editor_option');
		$systemEditor = $sugar_config['email_default_editor'];

		if($userEditor != '') {
			$editor = $userEditor;
		} else {
			$editor = $systemEditor;
		}

		return $editor;
	}

	/**
	 * takes the mess we pass from EditView and tries to create some kind of order
	 * @param array addrs
	 * @param array addrs_ids (from contacts)
	 * @param array addrs_names (from contacts);
	 * @param array addrs_emails (from contacts);
	 * @return array Parsed assoc array to feed to PHPMailer
	 */
	function parse_addrs($addrs, $addrs_ids, $addrs_names, $addrs_emails) {
		// cn: bug 9406 - enable commas to separate email addresses
		$addrs = str_replace(",", ";", $addrs);

		$ltgt = array('&lt;','&gt;');
		$gtlt = array('<','>');

		$return				= array();
		$addrs				= str_replace($ltgt, '', $addrs);
		$addrs_arr			= explode(";",$addrs);
		$addrs_arr			= $this->remove_empty_fields($addrs_arr);
		$addrs_ids_arr		= explode(";",$addrs_ids);
		$addrs_ids_arr		= $this->remove_empty_fields($addrs_ids_arr);
		$addrs_emails_arr	= explode(";",$addrs_emails);
		$addrs_emails_arr	= $this->remove_empty_fields($addrs_emails_arr);
		$addrs_names_arr	= explode(";",$addrs_names);
		$addrs_names_arr	= $this->remove_empty_fields($addrs_names_arr);

		///////////////////////////////////////////////////////////////////////
		////	HANDLE EMAILS HAND-WRITTEN
		$contactRecipients = array();
		$knownEmails = array();

		foreach($addrs_arr as $i => $v) {
			if(trim($v) == "")
				continue; // skip any "blanks" - will always have 1

			$recipient = array();

			//// get the email to see if we're dealing with a dupe
			//// what crappy coding
			preg_match("/[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i",$v, $match);

			if(!empty($match[0]) && !in_array(trim($match[0]), $knownEmails)) {
				$knownEmails[] = $match[0];
				$recipient['email'] = $match[0];

				//// handle the Display name
				$display = trim(str_replace($match[0], '', $v));

				//// only trigger a "displayName" <email@address> when necessary
				if(isset($addrs_names_arr[$i])){
						$recipient['display'] = $addrs_names_arr[$i];
				}
				else if(!empty($display)) {
					$recipient['display'] = $display;
				}
				if(isset($addrs_ids_arr[$i]) && $addrs_emails_arr[$i] == $match[0]){
					$recipient['contact_id'] = $addrs_ids_arr[$i];
				}
				$return[] = $recipient;
			}
		}

		return $return;
	}

	function remove_empty_fields(&$arr) {
		$newarr = array();

		foreach($arr as $field) {
			$field = trim($field);
			if(empty($field)) {
				continue;
			}
			array_push($newarr,$field);
		}
		return $newarr;
	}

	/**
	 * handles attachments of various kinds when sending email
	 */
	function handleAttachments() {
		
		
		

		global $mod_strings;

        ///////////////////////////////////////////////////////////////////////////
        ////    ATTACHMENTS FROM DRAFTS
        if(($this->type == 'out' || $this->type == 'draft') && $this->status == 'draft' && isset($_REQUEST['record'])) {
            $this->getNotes($_REQUEST['record']); // cn: get notes from OLD email for use in new email
        }
        ////    END ATTACHMENTS FROM DRAFTS
        ///////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////
        ////    ATTACHMENTS FROM FORWARDS
        // Bug 8034 Jenny - Need the check for type 'draft' here to handle cases where we want to save
        // forwarded messages as drafts.  We still need to save the original message's attachments.
        if(($this->type == 'out' || $this->type == 'draft') &&
        	isset($_REQUEST['origType']) && $_REQUEST['origType'] == 'forward' &&
        	isset($_REQUEST['return_id']) && !empty($_REQUEST['return_id'])
        ) {
            $this->getNotes($_REQUEST['return_id'], true);
        }

        // cn: bug 8034 - attachments from forward/replies lost when saving in draft
        if(isset($_REQUEST['prior_attachments']) && !empty($_REQUEST['prior_attachments']) && $this->new_with_id == true) {
        	$exIds = explode(",", $_REQUEST['prior_attachments']);
        	if(!isset($_REQUEST['template_attachment'])) {
        		$_REQUEST['template_attachment'] = array();
        	}
        	$_REQUEST['template_attachment'] = array_merge($_REQUEST['template_attachment'], $exIds);
        }
        ////    END ATTACHMENTS FROM FORWARDS
        ///////////////////////////////////////////////////////////////////////////

		///////////////////////////////////////////////////////////////////////////
		////	ATTACHMENTS FROM TEMPLATES
		// to preserve individual email integrity, we must dupe Notes and associated files
		// for each outbound email - good for integrity, bad for filespace
		if(isset($_REQUEST['template_attachment']) && !empty($_REQUEST['template_attachment'])) {
			$removeArr = array();
			$noteArray = array();

			if(isset($_REQUEST['temp_remove_attachment']) && !empty($_REQUEST['temp_remove_attachment'])) {
				$removeArr = $_REQUEST['temp_remove_attachment'];
			}


			foreach($_REQUEST['template_attachment'] as $noteId) {
				if(in_array($noteId, $removeArr)) {
					continue;
				}
				$noteTemplate = new Note();
				$noteTemplate->retrieve($noteId);
				$noteTemplate->id = create_guid();
				$noteTemplate->new_with_id = true; // duplicating the note with files
				$noteTemplate->parent_id = $this->id;
				$noteTemplate->parent_type = $this->module_dir;
				$noteTemplate->save();




				$noteFile = new UploadFile('none');
				$noteFile->duplicate_file($noteId, $noteTemplate->id, $noteTemplate->filename);
				$noteArray[] = $noteTemplate;
			}
			$this->attachments = array_merge($this->attachments, $noteArray);
		}
		////	END ATTACHMENTS FROM TEMPLATES
		///////////////////////////////////////////////////////////////////////////

		///////////////////////////////////////////////////////////////////////////
		////	ADDING NEW ATTACHMENTS
		$max_files_upload = 10;
        // Jenny - Bug 8211 Since attachments for drafts have already been processed,
        // we don't need to re-process them.
        if($this->status != "draft") {
    		$notes_list = array();
    		if(!empty($this->id) && !$this->new_with_id) {
    			$note = new Note();
    			$where = "notes.parent_id='{$this->id}'";
    			$notes_list = $note->get_full_list("", $where, true);
    		}
    		$this->attachments = array_merge($this->attachments, $notes_list);
        }
		// cn: Bug 5995 - rudimentary error checking
		$filesError = array(
			0 => 'UPLOAD_ERR_OK - There is no error, the file uploaded with success.',
			1 => 'UPLOAD_ERR_INI_SIZE - The uploaded file exceeds the upload_max_filesize directive in php.ini.',
			2 => 'UPLOAD_ERR_FORM_SIZE - The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
			3 => 'UPLOAD_ERR_PARTIAL - The uploaded file was only partially uploaded.',
			4 => 'UPLOAD_ERR_NO_FILE - No file was uploaded.',
			5 => 'UNKNOWN ERROR',
			6 => 'UPLOAD_ERR_NO_TMP_DIR - Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.',
			7 => 'UPLOAD_ERR_CANT_WRITE - Failed to write file to disk. Introduced in PHP 5.1.0.',
		);

		for($i = 0; $i < $max_files_upload; $i++) {
			// cn: Bug 5995 - rudimentary error checking
			if (!isset($_FILES["email_attachment{$i}"])) {
				$GLOBALS['log']->debug("Email Attachment {$i} does not exist.");
				continue;
			}
			if($_FILES['email_attachment'.$i]['error'] != 0 && $_FILES['email_attachment'.$i]['error'] != 4) {
				$GLOBALS['log']->debug('Email Attachment could not be attach due to error: '.$filesError[$_FILES['email_attachment'.$i]['error']]);
				continue;
			}

			$note = new Note();
			$note->parent_id = $this->id;
			$note->parent_type = $this->module_dir;
			$upload_file = new UploadFile('email_attachment'.$i);

			if(empty($upload_file)) {
				continue;
			}

			if(isset($_FILES['email_attachment'.$i]) && $upload_file->confirm_upload()) {
				$note->filename = $upload_file->get_stored_file_name();
				$note->file = $upload_file;
				$note->name = $mod_strings['LBL_EMAIL_ATTACHMENT'].': '.$note->file->original_file_name;




				$this->attachments[] = $note;
			}
		}

		$this->saved_attachments = array();
		foreach($this->attachments as $note) {
			if(!empty($note->id)) {
				array_push($this->saved_attachments, $note);
				continue;
			}
			$note->parent_id = $this->id;
			$note->parent_type = 'Emails';
			$note->file_mime_type = $note->file->mime_type;
			$note_id = $note->save();

			$this->saved_attachments[] = $note;

			$note->id = $note_id;
			$note->file->final_move($note->id);
		}
		////	END NEW ATTACHMENTS
		///////////////////////////////////////////////////////////////////////////

		///////////////////////////////////////////////////////////////////////////
		////	ATTACHMENTS FROM DOCUMENTS
		for($i=0; $i<10; $i++) {
			if(isset($_REQUEST['documentId'.$i]) && !empty($_REQUEST['documentId'.$i])) {
				$doc = new Document();
				$docRev = new DocumentRevision();
				$docNote = new Note();
				$noteFile = new UploadFile('none');

				$doc->retrieve($_REQUEST['documentId'.$i]);
				$docRev->retrieve($doc->document_revision_id);

				$this->saved_attachments[] = $docRev;

				// cn: bug 9723 - Emails with documents send GUID instead of Doc name
				$docNote->name = $docRev->getDocumentRevisionNameForDisplay();
				$docNote->filename = $docRev->filename;
				$docNote->description = $doc->description;
				$docNote->parent_id = $this->id;
				$docNote->parent_type = 'Emails';
				$docNote->file_mime_type = $docRev->file_mime_type;
				$docId = $docNote = $docNote->save();

				$noteFile->duplicate_file($docRev->id, $docId, $docRev->filename);
			}
		}

		////	END ATTACHMENTS FROM DOCUMENTS
		///////////////////////////////////////////////////////////////////////////

		///////////////////////////////////////////////////////////////////////////
		////	REMOVE ATTACHMENTS
        if(isset($_REQUEST['remove_attachment']) && !empty($_REQUEST['remove_attachment'])) {
            foreach($_REQUEST['remove_attachment'] as $noteId) {
                $q = 'UPDATE notes SET deleted = 1 WHERE id = \''.$noteId.'\'';
                $this->db->query($q);
            }
        }

        //this will remove attachments that have been selected to be removed from drafts.
        if(isset($_REQUEST['removeAttachment']) && !empty($_REQUEST['removeAttachment'])) {
            $exRemoved = explode('::', $_REQUEST['removeAttachment']);
            foreach($exRemoved as $noteId) {
                $q = 'UPDATE notes SET deleted = 1 WHERE id = \''.$noteId.'\'';
                $this->db->query($q);
            }
        }
		////	END REMOVE ATTACHMENTS
		///////////////////////////////////////////////////////////////////////////
	}


	/**
	 * Determines if an email body (HTML or Plain) has a User signature already in the content
	 * @param array Array of signatures
	 * @return bool
	 */
	function hasSignatureInBody($sig) {
		// strpos can't handle line breaks - normalize
		$html = $this->removeAllNewlines($this->description_html);
		$htmlSig = $this->removeAllNewlines($sig['signature_html']);
		$plain = $this->removeAllNewlines($this->description);
		$plainSig = $this->removeAllNewlines($sig['signature']);

		// cn: bug 11621 - empty sig triggers notice error
		if(!empty($htmlSig) && false !== strpos($html, $htmlSig)) {
			return true;
		} elseif(!empty($plainSig) && false !== strpos($plain, $plainSig)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * internal helper
	 * @param string String to be normalized
	 * @return string
	 */
	function removeAllNewlines($str) {
		$bad = array("\r\n", "\n\r", "\n", "\r");
		$good = array('', '', '', '');

		return str_replace($bad, $good, strip_tags(br2nl(from_html($str))));
	}



	/**
	 * Set navigation anchors to aid DetailView record navigation (VCR buttons)
	 * @param string uri The URI from the referring page (always ListView)
	 * @return array start Array of the URI broken down with a special "current_view" for My Inbox Navs
	 */
	function getStartPage($uri) {
		if(strpos($uri, '&')) { // "&" to ensure that we can explode the GET vars - else we're gonna trigger a Notice error
			$serial = substr($uri, (strpos($uri, '?')+1), strlen($uri));
			$exUri = explode('&', $serial);
			$start = array('module' => '', 'action' => '', 'group' => '', 'record' => '', 'type' => '');

			foreach($exUri as $k => $pair) {
				$exPair = explode('=', $pair);
				$start[$exPair[0]] = $exPair[1];
			}

			// specific views for current_user
			if(isset($start['assigned_user_id'])) {
				$start['current_view'] = "{$start['action']}&module={$start['module']}&assigned_user_id={$start['assigned_user_id']}&type={$start['type']}";
			}

			return $start;
		} else {
			return array();
		}
	}

	/**
	 * preps SMTP info for email transmission
	 * @param object mail SugarPHPMailer object
	 * @param string mailer_id
	 * @param string ieId
	 * @return object mail SugarPHPMailer object
	 */
	function setMailer($mail, $mailer_id='', $ieId='') {
		global $current_user;

		require_once("include/OutboundEmail/OutboundEmail.php");
		$oe = new OutboundEmail();
		$oe = $oe->getInboundMailerSettings($current_user, $mailer_id, $ieId);

		// ssl or tcp - keeping outside isSMTP b/c a default may inadvertantly set ssl://
		$mail->protocol = ($oe->mail_smtpssl) ? "ssl://" : "tcp://";

		if($oe->mail_sendtype == "SMTP") {
			$mail->Mailer = "smtp";
			$mail->Host = $oe->mail_smtpserver;
			$mail->Port = $oe->mail_smtpport;
            if ($oe->mail_smtpssl == 1) {
                $mail->SMTPSecure = 'ssl';
            } // if
            if ($oe->mail_smtpssl == 2) {
                $mail->SMTPSecure = 'tls';
            } // if

			if($oe->mail_smtpauth_req) {
				$mail->SMTPAuth = TRUE;
				$mail->Username = $oe->mail_smtpuser;
				$mail->Password = $oe->mail_smtppass;
			}
		} else {
			$mail->Mailer = "sendmail";
		}

		return $mail;
	}

	/**
	 * preps SugarPHPMailer object for HTML or Plain text sends
	 * @param object SugarPHPMailer instance
	 */
	function handleBody($mail) {
		global $current_user;
		global $sugar_config;
		///////////////////////////////////////////////////////////////////////
		////	HANDLE EMAIL FORMAT PREFERENCE
		// the if() below is HIGHLY dependent on the Javascript unchecking the Send HTML Email box
		// HTML email
		if( (isset($_REQUEST['setEditor']) /* from Email EditView navigation */
			&& $_REQUEST['setEditor'] == 1
			&& trim($_REQUEST['description_html']) != '')
			|| trim($this->description_html) != '' /* from email templates */
            && $current_user->getPreference('email_editor_option', 'global') !== 'plain' //user preference is not set to plain text
		) {
			// wp: if body is html, then insert new lines at 996 characters. no effect on client side
			// due to RFC 2822 which limits email lines to 998
			$mail->IsHTML(true);
			$body = from_html(wordwrap($this->description_html, 996));
			$mail->Body = $body;

			// cn: bug 9725
			// new plan is to use the selected type (html or plain) to fill the other
			$plainText = from_html($this->description_html);
			$plainText = strip_tags(br2nl($plainText));
			$mail->AltBody = $plainText;
			$this->description = $plainText;

			// cn: bug 9709 - html email sent accidentally
			// handle signatures fubar'ing the type
			$sigs = $current_user->getDefaultSignature();
			if(!empty($sigs)) {
				$htmlSig = trim(str_replace(" ", "", strip_tags(from_html($sigs['signature_html']))));
				$htmlBody = trim(str_replace(" ", "", strip_tags(from_html($this->description_html))));

				if($htmlSig == $htmlBody) {
					// found just a sig. ignore it.
					$this->description_html = '';
					$mail->IsHTML(false);
					$mail->Body = wordwrap(from_html($this->description, 996));
				}
			}

			$fileBasePath = "{$sugar_config['cache_dir']}images/";
			$filePatternSearch = "{$sugar_config['cache_dir']}";
			$filePatternSearch = str_replace("/", "\/", $filePatternSearch);
			$filePatternSearch = $filePatternSearch . "images\/";
			if(strpos($mail->Body, "\"{$fileBasePath}") !== 'false') {
				$matches = array();
				preg_match_all("/{$filePatternSearch}.+?\"/i", $mail->Body, $matches);
				foreach($matches[0] as $match) {
					$filename = str_replace($fileBasePath, '', $match);
					$filename = urldecode(substr($filename, 0, -1));
					$cid = $filename;
					$file_location = clean_path(getcwd()."/{$sugar_config['cache_dir']}images/{$filename}");
					$mime_type = "image/".strtolower(substr($filename, strrpos($filename, ".")+1, strlen($filename)));

					if(file_exists($file_location)) {
						$mail->AddEmbeddedImage($file_location, $cid, $filename, 'base64', $mime_type);
					}
				}

	            //replace references to cache with cid tag
	            $mail->Body = str_replace("/" . $fileBasePath,'cid:',$mail->Body);
	            $mail->Body = str_replace($fileBasePath,'cid:',$mail->Body);
				// remove bad img line from outbound email
				$regex = '#<img[^>]+src[^=]*=\"\/([^>]*?[^>]*)>#sim';
				$mail->Body = preg_replace($regex, '', $mail->Body);
			}
			$fileBasePath = "{$sugar_config['upload_dir']}";
			$filePatternSearch = "{$sugar_config['upload_dir']}";
			$filePatternSearch = str_replace("/", "\/", $filePatternSearch);
			if(strpos($mail->Body, "\"{$fileBasePath}") !== 'false') {
				$matches = array();
				preg_match_all("/{$filePatternSearch}.+?\"/i", $mail->Body, $matches);
				foreach($matches[0] as $match) {
					$filename = str_replace($fileBasePath, '', $match);
					$filename = urldecode(substr($filename, 0, -1));
					$cid = $filename;
					$file_location = clean_path(getcwd()."/{$sugar_config['upload_dir']}{$filename}");
					$mime_type = "image/".strtolower(substr($filename, strrpos($filename, ".")+1, strlen($filename)));

					if(file_exists($file_location)) {
						$mail->AddEmbeddedImage($file_location, $cid, $filename, 'base64', $mime_type);
					}
				}

	            //replace references to cache with cid tag
	            $mail->Body = str_replace("/" . $fileBasePath,'cid:',$mail->Body);
	            $mail->Body = str_replace($fileBasePath,'cid:',$mail->Body);

				// remove bad img line from outbound email
				$regex = '#<img[^>]+src[^=]*=\"\/([^>]*?[^>]*)>#sim';
				$mail->Body = preg_replace($regex, '', $mail->Body);
			}
			$mail->Body = $this->decodeDuringSend($mail->Body);
			$mail->Body = from_html($mail->Body);
		} else {
			// plain text only
			$this->description_html = '';
			$mail->IsHTML(false);
			$plainText = from_html($this->description);
			$plainText = str_replace("&nbsp;", " ", $plainText);
			$plainText = str_replace("</p>", "</p><br />", $plainText);
			$plainText = strip_tags(br2nl($plainText));
			$plainText = str_replace("&amp;", "&", $plainText);
            $plainText = str_replace("&#39;", "'", $plainText);
			$mail->Body = wordwrap($plainText, 996);
			$this->description = $mail->Body;
		}

		// wp: if plain text version has lines greater than 998, use base64 encoding
		foreach(explode("\n", ($mail->ContentType == "text/html") ? $mail->AltBody : $mail->Body) as $line) {
			if(strlen($line) > 998) {
				$mail->Encoding = 'base64';
				break;
			}
		}
		////	HANDLE EMAIL FORMAT PREFERENCE
		///////////////////////////////////////////////////////////////////////

		return $mail;
	}

	/**
	 * Sends Email
	 * @return bool True on success
	 */
	function send() {
		global $mod_strings;
		global $current_user;
		global $sugar_config;
		global $locale;
        $OBCharset = $locale->getPrecedentPreference('default_email_charset');
		$mail = new SugarPHPMailer();

		foreach ($this->to_addrs_arr as $addr_arr) {
			if ( empty($addr_arr['display'])) {
				$mail->AddAddress($addr_arr['email'], "");
			} else {
				$mail->AddAddress($addr_arr['email'],$locale->translateCharsetMIME(trim( $addr_arr['display']), 'UTF-8', $OBCharset));
			}
		}
		foreach ($this->cc_addrs_arr as $addr_arr) {
			if ( empty($addr_arr['display'])) {
				$mail->AddCC($addr_arr['email'], "");
			} else {
				$mail->AddCC($addr_arr['email'],$locale->translateCharsetMIME(trim($addr_arr['display']), 'UTF-8', $OBCharset));
			}
		}

		foreach ($this->bcc_addrs_arr as $addr_arr) {
			if ( empty($addr_arr['display'])) {
				$mail->AddBCC($addr_arr['email'], "");
			} else {
				$mail->AddBCC($addr_arr['email'],$locale->translateCharsetMIME(trim($addr_arr['display']), 'UTF-8', $OBCharset));
			}
		}

		$mail = $this->setMailer($mail);

		// FROM ADDRESS
		if(!empty($this->from_addr)) {
			$mail->From = $this->from_addr;
		} else {
			$mail->From = $current_user->getPreference('mail_fromaddress');
			$this->from_addr = $mail->From;
		}
		// FROM NAME
		if(!empty($this->from_name)) {
			$mail->FromName = $this->from_name;
		} else {
			$mail->FromName =  $current_user->getPreference('mail_fromname');
			$this->from_name = $mail->FromName;
		}

		//Reply to information for case create and autoreply.
		if(!empty($this->reply_to_name)) {
			$ReplyToName = $this->reply_to_name;
		} else {
			$ReplyToName = $mail->FromName;
		}
		if(!empty($this->reply_to_addr)) {
			$ReplyToAddr = $this->reply_to_addr;
		} else {
			$ReplyToAddr = $mail->From;
		}
		$mail->Sender = $mail->From; /* set Return-Path field in header to reduce spam score in emails sent via Sugar's Email module */
		$mail->AddReplyTo($ReplyToAddr,$locale->translateCharsetMIME(trim(ReplyToName), 'UTF-8', $OBCharset));

		//$mail->Subject = html_entity_decode($this->name, ENT_QUOTES, 'UTF-8');
		$mail->Subject = $this->name;

		///////////////////////////////////////////////////////////////////////
		////	ATTACHMENTS
		foreach($this->saved_attachments as $note) {
			$mime_type = 'text/plain';
			if($note->object_name == 'Note') {
				if(!empty($note->file->temp_file_location) && is_file($note->file->temp_file_location)) { // brandy-new file upload/attachment
					$file_location = $sugar_config['upload_dir'].$note->id;
					$filename = $note->file->original_file_name;
					$mime_type = $note->file->mime_type;
				} else { // attachment coming from template/forward
					$file_location = rawurldecode(UploadFile::get_file_path($note->filename,$note->id));
					// cn: bug 9723 - documents from EmailTemplates sent with Doc Name, not file name.
					$filename = !empty($note->filename) ? $note->filename : $note->name;
					$mime_type = $note->file_mime_type;
				}
			} elseif($note->object_name == 'DocumentRevision') { // from Documents
				$filePathName = $note->id;
				// cn: bug 9723 - Emails with documents send GUID instead of Doc name
				$filename = $note->getDocumentRevisionNameForDisplay();
				$file_location = getcwd().'/'.$GLOBALS['sugar_config']['upload_dir'].$filePathName;
				$mime_type = $note->file_mime_type;
			}

			// strip out the "Email attachment label if exists
			$filename = str_replace($mod_strings['LBL_EMAIL_ATTACHMENT'].': ', '', $filename);

			//is attachment in our list of bad files extensions?  If so, append .txt to file location
			//get position of last "." in file name
			$file_ext_beg = strrpos($file_location,".");
			$file_ext = "";
			//get file extension
			if($file_ext_beg >0){
				$file_ext = substr($file_location, $file_ext_beg+1 );
			}
			//check to see if this is a file with extension located in "badext"
			foreach($sugar_config['upload_badext'] as $badExt) {
		       	if(strtolower($file_ext) == strtolower($badExt)) {
			       	//if found, then append with .txt to filename and break out of lookup
			       	//this will make sure that the file goes out with right extension, but is stored
			       	//as a text in db.
			        $file_location = $file_location . ".txt";
			        break; // no need to look for more
		       	}
	        }
			$mail->AddAttachment($file_location,$locale->translateCharsetMIME(trim($filename), 'UTF-8', $OBCharset), 'base64', $mime_type);

			// embedded Images
			if($note->embed_flag == true) {
				$cid = $filename;
				$mail->AddEmbeddedImage($file_location, $cid, $filename, 'base64',$mime_type);
			}
		}
		////	END ATTACHMENTS
		///////////////////////////////////////////////////////////////////////

		$mail = $this->handleBody($mail);

        ///////////////////////////////////////////////////////////////////////
        ////    SAVE RAW MESSAGE
        // cn: bug 10250 - MySQL requires certain settings to get the SQL buffer > 1Mb
        if(isset($sugar_config['email_outbound_save_raw']) && $sugar_config['email_outbound_save_raw'] == true) {
	        $mail->SetMessageType();
	        $raw  = $mail->CreateHeader();
	        $raw .= $mail->CreateBody();
	        $this->raw_source = to_html($raw);
        }
        ////    END SAVE RAW MESSAGE
        ///////////////////////////////////////////////////////////////////////

		$GLOBALS['log']->debug('Email sending --------------------- ');

		///////////////////////////////////////////////////////////////////////
		////	I18N TRANSLATION
		$mail->prepForOutbound();
		////	END I18N TRANSLATION
		///////////////////////////////////////////////////////////////////////

		if($mail->Send()) {
			///////////////////////////////////////////////////////////////////
			////	INBOUND EMAIL HANDLING
			// mark replied
			if(!empty($_REQUEST['inbound_email_id'])) {
				$ieMail = new Email();
				$ieMail->retrieve($_REQUEST['inbound_email_id']);
				$ieMail->status = 'replied';
				$ieMail->save();
			}
			$GLOBALS['log']->debug(' --------------------- buh bye -- sent successful');
			////	END INBOUND EMAIL HANDLING
			///////////////////////////////////////////////////////////////////
  			return true;
		}
	    $GLOBALS['log']->debug("Error emailing:".$mail->ErrorInfo);
		return false;
	}


	function listviewACLHelper(){
		$array_assign = parent::listviewACLHelper();
		$is_owner = false;
		if(!empty($this->parent_name)){

			if(!empty($this->parent_name_owner)){
				global $current_user;
				$is_owner = $current_user->id == $this->parent_name_owner;
			}
		}
		if(!ACLController::moduleSupportsACL($this->parent_type) || ACLController::checkAccess($this->parent_type, 'view', $is_owner)){
			$array_assign['PARENT'] = 'a';
		} else {
			$array_assign['PARENT'] = 'span';
		}
		$is_owner = false;
		if(!empty($this->contact_name)) {
			if(!empty($this->contact_name_owner)) {
				global $current_user;
				$is_owner = $current_user->id == $this->contact_name_owner;
			}
		}
		if(ACLController::checkAccess('Contacts', 'view', $is_owner)) {
			$array_assign['CONTACT'] = 'a';
		} else {
			$array_assign['CONTACT'] = 'span';
		}

		return $array_assign;
	}

	function getSystemDefaultEmail() {
		$email = array();

		$r1 = $this->db->query('SELECT config.value FROM config WHERE name=\'fromaddress\'');
		$r2 = $this->db->query('SELECT config.value FROM config WHERE name=\'fromname\'');
		$a1 = $this->db->fetchByAssoc($r1);
		$a2 = $this->db->fetchByAssoc($r2);

		$email['email'] = $a1['value'];
		$email['name']  = $a2['value'];

		return $email;
	}







	function fill_in_additional_list_fields() {
		global $timedate;
		$this->fill_in_additional_detail_fields();

		$this->link_action = 'DetailView';
		///////////////////////////////////////////////////////////////////////
		//populate attachment_image, used to display attachment icon.
		$query =  "select 1 from notes where notes.parent_id = '$this->id' and notes.deleted = 0";
		$result =$this->db->query($query,true," Error filling in additional list fields: ");

		$row = $this->db->fetchByAssoc($result);

		if ($row !=null) {
			$this->attachment_image = SugarThemeRegistry::current()->getImage('attachment',"","","");
		} else {
			$this->attachment_image = SugarThemeRegistry::current()->getImage('blank',"","","");
		}



		///////////////////////////////////////////////////////////////////////
	}

	function fill_in_additional_detail_fields() {
		global $app_list_strings,$mod_strings;
		// Fill in the assigned_user_name
		$this->assigned_user_name = get_assigned_user_name($this->assigned_user_id, '');




		$query  = "SELECT contacts.first_name, contacts.last_name, contacts.phone_work, contacts.id, contacts.assigned_user_id contact_name_owner, 'Contacts' contact_name_mod FROM contacts, emails_beans ";
		$query .= "WHERE emails_beans.email_id='$this->id' AND emails_beans.bean_id=contacts.id  AND emails_beans.deleted=0 AND contacts.deleted=0";
		if(!empty($this->parent_id)){
			$query .= " AND contacts.id= '".$this->parent_id."' ";
		}else if(!empty($_REQUEST['record'])){
			$query .= " AND contacts.id= '".$_REQUEST['record']."' ";
		}
		$result =$this->db->query($query,true," Error filling in additional detail fields: ");

		// Get the id and the name.
		$row = $this->db->fetchByAssoc($result);
		$GLOBALS['log']->info($row);

		if($row != null)
		{
			
			$contact = new Contact();
			$contact->retrieve($row['id']);
			$this->contact_name = $contact->full_name;
			$this->contact_phone = $row['phone_work'];
			$this->contact_id = $row['id'];
			$this->contact_email = $contact->emailAddress->getPrimaryAddress($contact);
			$this->contact_name_owner = $row['contact_name_owner'];
			$this->contact_name_mod = $row['contact_name_mod'];
			$GLOBALS['log']->debug("Call($this->id): contact_name = $this->contact_name");
			$GLOBALS['log']->debug("Call($this->id): contact_phone = $this->contact_phone");
			$GLOBALS['log']->debug("Call($this->id): contact_id = $this->contact_id");
			$GLOBALS['log']->debug("Call($this->id): contact_email1 = $this->contact_email");
		}
		else {
			$this->contact_name = '';
			$this->contact_phone = '';
			$this->contact_id = '';
			$this->contact_email = '';
			$this->contact_name_owner = '';
			$this->contact_name_mod = '';
			$GLOBALS['log']->debug("Call($this->id): contact_name = $this->contact_name");
			$GLOBALS['log']->debug("Call($this->id): contact_phone = $this->contact_phone");
			$GLOBALS['log']->debug("Call($this->id): contact_id = $this->contact_id");
			$GLOBALS['log']->debug("Call($this->id): contact_email1 = $this->contact_email");
		}

		$this->created_by_name = get_assigned_user_name($this->created_by);
		$this->modified_by_name = get_assigned_user_name($this->modified_user_id);

		$this->link_action = 'DetailView';

		if(!empty($this->type)) {
			if($this->type == 'out' && $this->status == 'send_error') {
				$this->type_name = $mod_strings['LBL_NOT_SENT'];
			} else {
				$this->type_name = $app_list_strings['dom_email_types'][$this->type];
			}

			if(($this->type == 'out' && $this->status == 'send_error') || $this->type == 'draft') {
				$this->link_action = 'EditView';
			}
		}

		//todo this  isset( $app_list_strings['dom_email_status'][$this->status]) is hack for 3261.
		if(!empty($this->status) && isset( $app_list_strings['dom_email_status'][$this->status])) {
			$this->status_name = $app_list_strings['dom_email_status'][$this->status];
		}

		if ( empty($this->name ) &&  empty($_REQUEST['record'])) {
			$this->name = '(no subject)';
		}

		$this->fill_in_additional_parent_fields();
	}



	function create_export_query(&$order_by, &$where) {
		$contact_required = ereg("contacts", $where);
		$custom_join = $this->custom_fields->getJOIN(true, true,$where);

		if($contact_required) {
			$query = "SELECT emails.*, contacts.first_name, contacts.last_name";



			if($custom_join) {
				$query .= $custom_join['select'];
			}

			$query .= " FROM contacts, emails, emails_contacts ";
			$where_auto = "emails_contacts.contact_id = contacts.id AND emails_contacts.email_id = emails.id AND emails.deleted=0 AND contacts.deleted=0";
		} else {
			$query = 'SELECT emails.*';



			if($custom_join) {
				$query .= $custom_join['select'];
			}

            $query .= ' FROM emails ';
            $where_auto = "emails.deleted=0";
		}






		if($custom_join){
			$query .= $custom_join['join'];
		}

		if($where != "")
			$query .= "where $where AND ".$where_auto;
        else
			$query .= "where ".$where_auto;

        if($order_by != "")
			$query .= " ORDER BY $order_by";
        else
			$query .= " ORDER BY emails.name";
        return $query;
    }

	function get_list_view_data() {
		global $app_list_strings;
		global $theme;
		global $current_user;
		global $timedate;
		global $mod_strings;

		$email_fields = $this->get_list_view_array();
		$this->retrieveEmailText();
		$email_fields['FROM_ADDR'] = $this->from_addr_name;
		$mod_strings = return_module_language($GLOBALS['current_language'], 'Emails'); // hard-coding for Home screen ListView

		if($this->status != 'replied') {
			$email_fields['QUICK_REPLY'] = '<a  href="index.php?module=Emails&action=Compose&replyForward=true&reply=reply&record='.$this->id.'&inbound_email_id='.$this->id.'">'.$mod_strings['LNK_QUICK_REPLY'].'</a>';
			$email_fields['STATUS'] = ($email_fields['REPLY_TO_STATUS'] == 1 ? $mod_strings['LBL_REPLIED'] : $email_fields['STATUS']);
		} else {
			$email_fields['QUICK_REPLY'] = $mod_strings['LBL_REPLIED'];
		}
		if(!empty($this->parent_type)) {
			$email_fields['PARENT_MODULE'] = $this->parent_type;
		} else {
			switch($this->intent) {
				case 'support':
					$email_fields['CREATE_RELATED'] = '<a href="index.php?module=Cases&action=EditView&inbound_email_id='.$this->id.'" ><img border="0" src="'.SugarThemeRegistry::current()->getImageURL('CreateCases.gif').'">'.$mod_strings['LBL_CREATE_CASE'].'</a>';
				break;

				case 'sales':
					$email_fields['CREATE_RELATED'] = '<a href="index.php?module=Leads&action=EditView&inbound_email_id='.$this->id.'" ><img border="0" src="'.SugarThemeRegistry::current()->getImageURL('CreateLeads.gif').'">'.$mod_strings['LBL_CREATE_LEAD'].'</a>';
				break;

				case 'contact':
					$email_fields['CREATE_RELATED'] = '<a href="index.php?module=Contacts&action=EditView&inbound_email_id='.$this->id.'" ><img border="0" src="'.SugarThemeRegistry::current()->getImageURL('CreateContacts.gif').'">'.$mod_strings['LBL_CREATE_CONTACT'].'</a>';
				break;

				case 'bug':
					$email_fields['CREATE_RELATED'] = '<a href="index.php?module=Bugs&action=EditView&inbound_email_id='.$this->id.'" ><img border="0" src="'.SugarThemeRegistry::current()->getImageURL('CreateBugs.gif').'">'.$mod_strings['LBL_CREATE_BUG'].'</a>';
				break;

				case 'task':
					$email_fields['CREATE_RELATED'] = '<a href="index.php?module=Tasks&action=EditView&inbound_email_id='.$this->id.'" ><img border="0" src="'.SugarThemeRegistry::current()->getImageURL('CreateTasks.gif').'">'.$mod_strings['LBL_CREATE_TASK'].'</a>';
				break;

				case 'bounce':
				break;

				case 'pick':
				// break;

				case 'info':
				//break;

				default:
					$email_fields['CREATE_RELATED'] = $this->quickCreateForm();
				break;
			}

		}

		//BUG 17098 - MFH changed $this->from_addr to $this->to_addrs
		$email_fields['CONTACT_NAME']		= empty($this->contact_name) ? '</a>'.$this->trimLongTo($this->to_addrs).'<a>' : $this->contact_name;
		$email_fields['CONTACT_ID']		= empty($this->contact_id) ? '' : $this->contact_id;
		$email_fields['ATTACHMENT_IMAGE']	= $this->attachment_image;
		$email_fields['LINK_ACTION']		= $this->link_action;

    	if(isset($this->type_name))
	      	$email_fields['TYPE_NAME'] = $this->type_name;

		return $email_fields;
	}

    function quickCreateForm() {
        global $mod_strings, $app_strings, $currentModule, $current_language;

        // Coming from the home page via Dashlets
        if($currentModule != 'Email')
        	$mod_strings = return_module_language($current_language, 'Emails');
        return "<a id='$this->id' onclick='return quick_create_overlib(\"{$this->id}\", \"".SugarThemeRegistry::current()->__toString()."\");' href=\"#\" >".SugarThemeRegistry::current()->getImage("advanced_search","alt='".$mod_strings['LBL_QUICK_CREATE']."'  border='0' align='absmiddle'")."&nbsp;".$mod_strings['LBL_QUICK_CREATE']."</a>";
    }


	/**
	 * takes a long TO: string of emails and returns the first appended by an
	 * elipse
	 */
	function trimLongTo($str) {
		if(strpos($str, ',')) {
			$exStr = explode(',', $str);
			return $exStr[0].'...';
		} elseif(strpos($str, ';')) {
			$exStr = explode(';', $str);
			return $exStr[0].'...';
		} else {
			return $str;
		}
	}

	function get_summary_text() {
		return $this->name;
	}



	function distributionForm($where) {
		global $app_list_strings;
		global $app_strings;
		global $mod_strings;
		global $theme;
		global $current_user;

		$distribution	= get_select_options_with_id($app_list_strings['dom_email_distribution'], '');
		$_SESSION['distribute_where'] = $where;

		$out = '
		<form name="Distribute" id="Distribute">';
		$out .= get_form_header($mod_strings['LBL_DIST_TITLE'], '', false);
		$out .= '
		<table cellpadding="0" cellspacing="0" width="100%" border="0">
			<tr>
				<td>
					<script type="text/javascript">


						function checkDeps(form) {
							return;
						}

						function mySubmit() {
							var assform = document.getElementById("Distribute");
							var select = document.getElementById("userSelect");
							var assign1 = assform.r1.checked;
							var assign2 = assform.r2.checked;
							var dist = assform.dm.value;
							var assign = false;
							var users = false;
							var rules = false;
							var warn1 = "'.$mod_strings['LBL_WARN_NO_USERS'].'";
							var warn2 = "";

							if(assign1 || assign2) {
								assign = true;

							}

							for(i=0; i<select.options.length; i++) {
								if(select.options[i].selected == true) {
									users = true;
									warn1 = "";
								}
							}

							if(dist != "") {
								rules = true;
							} else {
								warn2 = "'.$mod_strings['LBL_WARN_NO_DIST'].'";
							}

							if(assign && users && rules) {

								if(document.getElementById("r1").checked) {
									var mu = document.getElementById("MassUpdate");
									var grabbed = "";

									for(i=0; i<mu.elements.length; i++) {
										if(mu.elements[i].type == "checkbox" && mu.elements[i].checked && mu.elements[i].name.value != "massall") {
											if(grabbed != "") { grabbed += "::"; }
											grabbed += mu.elements[i].value;
										}
									}
									var formgrab = document.getElementById("grabbed");
									formgrab.value = grabbed;
								}
								assform.submit();
							} else {
								alert("'.$mod_strings['LBL_ASSIGN_WARN'].'" + "\n" + warn1 + "\n" + warn2);
							}
						}

						function submitDelete() {
							if(document.getElementById("r1").checked) {
								var mu = document.getElementById("MassUpdate");
								var grabbed = "";

								for(i=0; i<mu.elements.length; i++) {
									if(mu.elements[i].type == "checkbox" && mu.elements[i].checked && mu.elements[i].name != "massall") {
										if(grabbed != "") { grabbed += "::"; }
										grabbed += mu.elements[i].value;
									}
								}
								var formgrab = document.getElementById("grabbed");
								formgrab.value = grabbed;
							}
							if(grabbed == "") {
								alert("'.$mod_strings['LBL_MASS_DELETE_ERROR'].'");
							} else {
								document.getElementById("Distribute").submit();
							}
						}

					</script>
						<input type="hidden" name="module" value="Emails">
						<input type="hidden" name="action" id="action">
						<input type="hidden" name="grabbed" id="grabbed">

					<table cellpadding="1" cellspacing="0" width="100%" border="0" class="edit view">
						<tr height="20">
							<td scope="col" width="40%" scope="row" NOWRAP align="center">
								&nbsp;'.$mod_strings['LBL_ASSIGN_SELECTED_RESULTS_TO'].'&nbsp;';
					$out .= $this->userSelectTable();
					$out .=	'</td>
							<td scope="col" width="15%" scope="row" NOWRAP align="left">
								&nbsp;'.$mod_strings['LBL_USING_RULES'].'&nbsp;
								<select name="distribute_method" id="dm" onChange="checkDeps(this.form);">'.$distribution.'</select>
							</td>

							<td scope="col" width="50%" scope="row" NOWRAP align="right">
								<input title="'.$mod_strings['LBL_BUTTON_DISTRIBUTE_TITLE'].'"
									id="dist_button"
									accessKey="'.$mod_strings['LBL_BUTTON_DISTRIBUTE_KEY'].'"
									class="button" onClick="AjaxObject.detailView.handleAssignmentDialogAssignAction();"
									type="button" name="button"
									value="  '.$mod_strings['LBL_BUTTON_DISTRIBUTE'].'  ">';

					$out .= '
							</td>
						</tr>
					</table>

				</td>
			</tr>
		</table>
		</form>';
	return $out;
	}

	function userSelectTable() {
		global $theme;
		global $mod_strings;

		$colspan = 1;
		$setTeamUserFunction = '';










































		// get users
		$r = $this->db->query("SELECT users.id, users.user_name, users.first_name, users.last_name FROM users WHERE deleted=0 AND status = 'Active' AND is_group=0 ORDER BY users.last_name, users.first_name");

		$userTable = '<table cellpadding="0" cellspacing="0" border="0">';
		$userTable .= '<tr><td colspan="2"><b>'.$mod_strings['LBL_USER_SELECT'].'</b></td></tr>';
		$userTable .= '<tr><td><input type="checkbox" style="border:0px solid #000000" onClick="toggleAll(this); setCheckMark(); checkDeps(this.form);"></td> <td>'.$mod_strings['LBL_TOGGLE_ALL'].'</td></tr>';
		$userTable .= '<tr><td colspan="2"><select style="visibility:hidden;" name="users[]" id="userSelect" multiple size="12">';

		while($a = $this->db->fetchByAssoc($r)) {
			$userTable .= '<option value="'.$a['id'].'" id="'.$a['id'].'">'.$a['first_name'].' '.$a['last_name'].'</option>';
		}
		$userTable .= '</select></td></tr>';
		$userTable .= '</table>';

		$out  = '<script type="text/javascript">';
		$out .= $setTeamUserFunction;
		$out .= '
					function setCheckMark() {
						var select = document.getElementById("userSelect");

						for(i=0 ; i<select.options.length; i++) {
							if(select.options[i].selected == true) {
								document.getElementById("checkMark").style.display="";
								return;
							}
						}

						document.getElementById("checkMark").style.display="none";
						return;
					}

					function showUserSelect() {
						var targetTable = document.getElementById("user_select");
						targetTable.style.visibility="visible";
						var userSelectTable = document.getElementById("userSelect");
						userSelectTable.style.visibility="visible";
						return;
					}
					function hideUserSelect() {
						var targetTable = document.getElementById("user_select");
						targetTable.style.visibility="hidden";
						var userSelectTable = document.getElementById("userSelect");
						userSelectTable.style.visibility="hidden";
						return;
					}
					function toggleAll(toggle) {
						if(toggle.checked) {
							var stat = true;
						} else {
							var stat = false;
						}
						var form = document.getElementById("userSelect");
						for(i=0; i<form.options.length; i++) {
							form.options[i].selected = stat;
						}
					}


				</script>
			<span id="showUsersDiv" style="position:relative;">
				<a href="#" id="showUsers" onClick="javascript:showUserSelect();">
					<img border="0" src="'.SugarThemeRegistry::current()->getImageURL('Users.gif').'"></a>&nbsp;
				<a href="#" id="showUsers" onClick="javascript:showUserSelect();">
					<span style="display:none;" id="checkMark"><img border="0" src="'.SugarThemeRegistry::current()->getImageURL('check_inline.gif').'"></span>
				</a>


				<div id="user_select" style="width:200px;position:absolute;left:2;top:2;visibility:hidden;z-index:1000;">
				<table cellpadding="0" cellspacing="0" border="0" class="list view">
					<tr height="20">
						<td  colspan="'.$colspan.'" id="hiddenhead" onClick="hideUserSelect();" onMouseOver="this.style.border = \'outset red 1px\';" onMouseOut="this.style.border = \'inset white 0px\';this.style.borderBottom = \'inset red 1px\';">
							<a href="#" onClick="javascript:hideUserSelect();"><img border="0" src="'.SugarThemeRegistry::current()->getImageURL('close.gif').'"></a>
							'.$mod_strings['LBL_USER_SELECT'].'
						</td>
					</tr>
					<tr>';
//<td valign="middle" height="30"  colspan="'.$colspan.'" id="hiddenhead" onClick="hideUserSelect();" onMouseOver="this.style.border = \'outset red 1px\';" onMouseOut="this.style.border = \'inset white 0px\';this.style.borderBottom = \'inset red 1px\';">





		$out .=	'		<td style="padding:5px" class="oddListRowS1" bgcolor="#fdfdfd" valign="top" align="left" style="left:0;top:0;">
							'.$userTable.'
						</td>
					</tr>
				</table></div>
			</span>';
		return $out;
	}

	function checkInbox($type) {
		global $theme;
		global $mod_strings;
		$out = '<div><input	title="'.$mod_strings['LBL_BUTTON_CHECK_TITLE'].'"
						accessKey="'.$mod_strings['LBL_BUTTON_CHECK_KEY'].'"
						class="button"
						type="button" name="button"
						onClick="window.location=\'index.php?module=Emails&action=Check&type='.$type.'\';"
						style="margin-bottom:2px"
						value="  '.$mod_strings['LBL_BUTTON_CHECK'].'  "></div>';
		return $out;
	}

        /**
         * Guesses Primary Parent id from From: email address.  Cascades guesses from Accounts to Contacts to Leads to
         * Users.  This will not affect the many-to-many relationships already constructed as this is, at best,
         * informational linking.
         */
        function fillPrimaryParentFields() {
                if(empty($this->from_addr))
                        return;

                $GLOBALS['log']->debug("*** Email trying to guess Primary Parent from address [ {$this->from_addr} ]");

                $tables = array('accounts');
                $ret = array();
                // loop through types to get hits
                foreach($tables as $table) {
                        $q = "SELECT name, id FROM {$table} WHERE email1 = '{$this->from_addr}' OR email2 = '{$this->from_addr}' AND deleted = 0";
                        $r = $this->db->query($q);
                        while($a = $this->db->fetchByAssoc($r)) {
                                if(!empty($a['name']) && !empty($a['id'])) {
                                        $this->parent_type      = ucwords($table);
                                        $this->parent_id        = $a['id'];
                                        $this->parent_name      = $a['name'];
                                        return;
                                }
                        }
                }
        }



} // end class def
