<?php
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

class Basic extends SugarBean{

	function Basic(){
		parent::SugarBean();
	}
	function get_summary_text()
	{
		return "$this->name";
	}
	
	function create_export_query($order_by, $where){
		return $this->create_new_list_query($order_by, $where);
	}
	
	/*
	 * FIXME for bug 20718,
	 * Because subpanels are not rendered using smarty and do not repsect the "currency_format" list def flag,
	 * we must convert currency values to the display format before dislplay only on subpanels.
	 * This code should be removed once all subpanels render properly using smarty rather than XTemplate.
	 */
	function get_list_view_data(){
		global $action;
		if (isset($this->currency_id) && ($action == 'DetailView' || $action == "SubPanelViewer"))
		{
			global $locale, $current_language, $current_user, $mod_strings, $app_list_strings, $sugar_config;
			$app_strings = return_application_language($current_language);
       		$params = array();
			
			$temp_array = $this->get_list_view_array();
			$params = array('currency_id' => $this->currency_id, 'convert' => true);
			foreach($temp_array as $field => $value)
			{
				$fieldLow = strToLower($field);
				if (!empty($this->field_defs[$fieldLow]) &&  $this->field_defs[$fieldLow]['type'] == 'currency')
				{
					$temp_array[$field] = currency_format_number($this->$fieldLow, $params);
				}
			}
			return $temp_array;
		}
		else 
		{
			return parent::get_list_view_data();
		}
		
	}
	
	
	
}
