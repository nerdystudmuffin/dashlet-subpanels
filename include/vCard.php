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

 * Description:  
 ********************************************************************************/

class vCard{
	var $properties = array();
	var $name = 'no_name';
	function clear(){
		$this->properties = array();	
	}
	
	function loadContact($contactid, $module='Contacts') {
		global $app_list_strings;

		require_once($GLOBALS['beanFiles'][$GLOBALS['beanList'][$module]]);
		$contact = new $GLOBALS['beanList'][$module]();
		$contact->retrieve($contactid);
		
		// cn: bug 8504 - CF/LB break Outlook's vCard import
		$bad = array("\n", "\r");
		$good = array("=0A", "=0D");
		$encoding = '';
		if(strpos($contact->primary_address_street, "\n") || strpos($contact->primary_address_street, "\r")) {
			$contact->primary_address_street = str_replace($bad, $good, $contact->primary_address_street);
			$encoding = 'QUOTED-PRINTABLE';
		}
		
		$this->setName(from_html($contact->first_name), from_html($contact->last_name), $app_list_strings['salutation_dom'][from_html($contact->salutation)]);
		$this->setBirthDate(from_html($contact->birthdate));
		$this->setPhoneNumber(from_html($contact->phone_fax), 'FAX');
		$this->setPhoneNumber(from_html($contact->phone_home), 'HOME');
		$this->setPhoneNumber(from_html($contact->phone_mobile), 'CELL');
		$this->setPhoneNumber(from_html($contact->phone_work), 'WORK');
		$this->setEmail(from_html($contact->email1));
		$this->setAddress(from_html($contact->primary_address_street), from_html($contact->primary_address_city), from_html($contact->primary_address_state), from_html($contact->primary_address_postalcode), from_html($contact->primary_address_country), 'WORK', $encoding);
		$this->setORG(from_html($contact->account_name), from_html($contact->department));
		$this->setTitle($contact->title);
	}
	
	function setTitle($title){
		$this->setProperty("TITLE",$title );	
	}
	function setORG($org, $dep){
		$this->setProperty("ORG","$org;$dep" );	
	}
	function setAddress($address, $city, $state,$postal, $country, $type, $encoding=''){
		if(!empty($encoding)) {
			$encoding = ";ENCODING={$encoding}";
		}
		$this->setProperty("ADR;$type$encoding",";;$address;$city;$state;$postal;$country" );	
	}
	
	function setName($first_name, $last_name, $prefix){
		$this->name = strtr($first_name.'_'.$last_name, ' ' , '_');
		$this->setProperty('N',$last_name.';'.$first_name.';'.$prefix );
		$this->setProperty('FN',"$prefix $first_name $last_name"); 
	}
	
	function setEmail($address){
		$this->setProperty('EMAIL;INTERNET', $address);
	}
	
	function setPhoneNumber( $number, $type){
		$this->setProperty("TEL;$type", $number);
	}
	function setBirthDate($date){
			$this->setProperty('BDAY',$date);
	}
	function getProperty($name){
		if(isset($this->properties[$name]))
			return $this->properties[$name];
		return null;	
	}
	
	function setProperty($name, $value){
		$this->properties[$name] = $value;		
	}
	
	function toString(){
		$temp = "BEGIN:VCARD\n";
		foreach($this->properties as $key=>$value){
			$temp .= $key. ':'.$value."\n";	
		}	
		$temp.= "END:VCARD\n";
		
		
		return $temp;
	}	
	
	function saveVCard(){
		global $locale;
		
		$content = $this->toString();
		header("Content-Disposition: attachment; filename={$this->name}.vcf");
		header("Content-Type: text/x-vcard; charset=".$locale->getExportCharset());
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
		header("Cache-Control: max-age=0");
		header("Pragma: public");
		header("Content-Length: ".strlen($content));

		print $locale->translateCharset($content, 'UTF-8', $locale->getExportCharset());
	}
	
	function importVCard($filename, $module='Contacts'){
		global $current_user;
		$lines =	file($filename);
		$start = false;
		require_once($GLOBALS['beanFiles'][$GLOBALS['beanList'][$module]]);
		$contact = new $GLOBALS['beanList'][$module]();

		$contact->title = 'imported';
		$contact->assigned_user_id = $current_user->id;
		$fullname = '';

		for($index = 0; $index < sizeof($lines); $index++){
			$line = $lines[$index];
			
			$line = trim($line);
			if($start){
				//VCARD is done
				if(substr_count(strtoupper($line), 'END:VCARD')){
					if(!isset($contact->last_name)){
						$contact->last_name = $fullname;	
					}
					return $contact->save();
					
				}
				$keyvalue = split(':',$line);
				if(sizeof($keyvalue)==2){
					$value = $keyvalue[1];
					for($newindex= $index + 1;  $newindex < sizeof($lines), substr_count($lines[$newindex], ':') == 0; $newindex++){
							$value .= $lines[$newindex];
							$index = $newindex;
					}
					$values = split(';',$value );
					$key = strtoupper($keyvalue[0]);
					$key = strtr($key, '=', '');
					$key = strtr($key, ',',';');
					$keys = split(';' ,$key);
					
					if($keys[0] == 'TEL'){
						if(substr_count($key, 'WORK') > 0){
								if(substr_count($key, 'FAX') > 0){
										if(!isset($contact->phone_fax)){
											$contact->phone_fax = $value;
										}
								}else{
									if(!isset($contact->phone_work)){
											$contact->phone_work = $value;
									}	
								}
						}
						if(substr_count($key, 'HOME') > 0){
								if(substr_count($key, 'FAX') > 0){
										if(!isset($contact->phone_fax)){
											$contact->phone_fax = $value;
										}
								}else{
									if(!isset($contact->phone_home)){
											$contact->phone_home = $value;
									}	
								}
						}
						if(substr_count($key, 'CELL') > 0){
								if(!isset($contact->phone_mobile)){
										$contact->phone_mobile = $value;
								}	
								
						}
						if(substr_count($key, 'FAX') > 0){
										if(!isset($contact->phone_fax)){
											$contact->phone_fax = $value;
						}
						
						}
							
					}
					if($keys[0] == 'N'){
						if(sizeof($values) > 0)
							$contact->last_name = $values[0];
						if(sizeof($values) > 1)
							$contact->first_name = $values[1];
						
						
						
							
					}
					if($keys[0] == 'FN'){
						$fullname = $value;				
						
							
					}
					
	}
					if($keys[0] == 'ADR'){
						if(substr_count($key, 'WORK') > 0 && (substr_count($key, 'POSTAL') > 0|| substr_count($key, 'PARCEL') == 0)){

								if(!isset($contact->primary_address_street) && sizeof($values) > 2){
                                        $textBreaks = array("\n", "\r");
                                        $vcardBreaks = array("=0A", "=0D");
										$contact->primary_address_street = str_replace($vcardBreaks, $textBreaks, $values[2]);
								}
								if(!isset($contact->primary_address_city) && sizeof($values) > 3){
										$contact->primary_address_city = $values[3];
								}
								if(!isset($contact->primary_address_state) && sizeof($values) > 4){
										$contact->primary_address_state = $values[4];
								}
								if(!isset($contact->primary_address_postalcode) && sizeof($values) > 5){
										$contact->primary_address_postalcode = $values[5];
								}
								if(!isset($contact->primary_address_country) && sizeof($values) > 6){
										$contact->primary_address_country = $values[6];
								}
						}
					}
					
					if($keys[0] == 'TITLE'){
						$contact->title = $value;
						
					}
					if($keys[0] == 'EMAIL'){
						if(!isset($contact->email1)){
							//Set email1 as REQUEST variable for legacy email save
							$_REQUEST['email1'] = $value;
							$contact->email1 = $value;
						}else if(!isset($contact->email2)){
							$contact->email2 = $value;
						}
						
					}
					
					if($keys[0] == 'ORG'){
						if(sizeof($values) > 1){
								$contact->department = $values[1];
						}
						
					}
					
				}
					
				
					
			
			//FOUND THE BEGINING OF THE VCARD
			if(!$start && substr_count(strtoupper($line), 'BEGIN:VCARD')){
				$start = true;	
			}
			
		}

		return $contact->save();
	}
	}
	
	
	
	
	



?>
