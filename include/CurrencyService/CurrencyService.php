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
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc. All Rights
 * Reserved. Contributor(s): ______________________________________..
 * *******************************************************************************/

class CurrencyService {
	var $currencyDefault;
	var $currencyFrom;
	var $currencyTo;

	var $numbers;
	var $db;
	
	/**
	 * sole constructor
	 */
	function CurrencyService() {
		global $sugar_config;
		
        if(!class_exists('DBManagerFactory')) {
            
        }
		$this->db = &DBManagerFactory::getInstance();
		
	}
	
	/**
	 * inserts default (usually US Dollar) as default currency
	 */
	function insertDefaults() {
		global $sugar_config;
		
		$insert=true;
		
		if($insert) {
			$q = "INSERT INTO currencies (id, name, symbol, iso4217, conversion_rate, status, deleted, date_entered, date_modified, created_by)
					VALUES('".create_guid()."', 
						'{$sugar_config['default_currency_name']}',
						'{$sugar_config['default_currency_symbol']}',
						'{$sugar_config['default_currency_iso4217']}',
						1.0, 'Active', 0, '".date($GLOBALS['timedate']->get_db_date_time_format())."', '".date($GLOBALS['timedate']->get_db_date_time_format())."', '1')";
		}	
	}
	
} // end class def
?>
