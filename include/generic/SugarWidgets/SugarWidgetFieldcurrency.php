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

                                                                                       
require_once('include/generic/SugarWidgets/SugarWidget.php');
                                                                                       

global $current_user;
                                                                                       
$global_currency_obj = null;
                                                                                       
function get_currency()
{
        global $current_user,$global_currency_obj;
        if (empty($global_currency_obj))
        {
        $global_currency_obj = new Currency();
      //  $global_currency_symbol = '$';
                                                                                       
        if($current_user->getPreference('currency') )
        {
                $global_currency_obj->retrieve($current_user->getPreference('currency'));
        }
        else
        {
                $global_currency_obj->retrieve('-99');
        }
        }
        return $global_currency_obj;
}


class SugarWidgetFieldCurrency extends SugarWidgetFieldInt
{
        function & displayList($layout_def)
        {
//                $global_currency_obj = get_currency();
//                  $display = format_number($this->displayListPlain($layout_def), 2, 2, array('convert' => true, 'currency_symbol' => true));
//                $display =  $global_currency_obj->symbol. round($global_currency_obj->convertFromDollar($this->displayListPlain($layout_def)),2);
            $display = $this->displayListPlain($layout_def); 
            return $display;
        }
                             
    function displayListPlain($layout_def) {
//        $value = $this->_get_list_value($layout_def);
        $value = format_number(parent::displayListPlain($layout_def), 2, 2, array('convert' => false, 'currency_symbol' => false));
        return $value;
    }                                                          
 function queryFilterEquals(&$layout_def)
 {
		$global_currency_obj = get_currency();
                return $this->_get_column_select($layout_def)."=".$GLOBALS['db']->quote( round($global_currency_obj->convertToDollar($layout_def['input_name0'])))."\n";
 }
                                                                                       
 function queryFilterNot_Equals(&$layout_def)
 {
		$global_currency_obj = get_currency();
                return $this->_get_column_select($layout_def)."!=".$GLOBALS['db']->quote( round($global_currency_obj->convertToDollar($layout_def['input_name0'])))."\n";
 }
                                                                                       
 function queryFilterGreater(&$layout_def)
 {
		$global_currency_obj = get_currency();
                return $this->_get_column_select($layout_def)." > ".$GLOBALS['db']->quote( round($global_currency_obj->convertToDollar($layout_def['input_name0'])))."\n";
 }
                                                                                       
 function queryFilterLess(&$layout_def)
 {
		$global_currency_obj = get_currency();
                return $this->_get_column_select($layout_def)." < ".$GLOBALS['db']->quote( round($global_currency_obj->convertToDollar($layout_def['input_name0'])))."\n";
 }

 function queryFilterBetween(&$layout_def){
 	$global_currency_obj = get_currency();
    return $this->_get_column_select($layout_def)." > ".$GLOBALS['db']->quote( round($global_currency_obj->convertToDollar($layout_def['input_name0']))). " AND ". $this->_get_column_select($layout_def)." < ".$GLOBALS['db']->quote( round($global_currency_obj->convertToDollar($layout_def['input_name1'])))."\n";
 }


}

?>
