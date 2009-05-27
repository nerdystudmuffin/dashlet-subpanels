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

class TemplateDate extends TemplateText{
	var $type = 'date';
	var $len = '';
    var $dateStrings = array(
    		'-none-'=>'',
            'yesterday'=> '-1 day',
            'today'=>'now',
            'tomorrow'=>'+1 day',
            'next week'=> '+1 week',
            'next monday'=>'next monday + 1 day',
            'next friday'=>'next friday + 1 day',
            'two weeks'=> '+2 weeks',
            'next month'=> '+1 month',
            'first day of next month'=> 'first of next month', // must handle this non-GNU date string in SugarBean->populateDefaultValues; if we don't this will evaluate to 1969...
            'three months'=> '+3 months',  //kbrill Bug #17023
            'six months'=> '+6 months',
            'next year'=> '+1 year',
        );



function get_db_type(){

    if($GLOBALS['db']->dbType == 'mssql'){
        return " DATETIME ";
    } else {
        return " DATE ";
    }
}
function get_db_default($modify=false){
		return '';
}

//BEGIN BACKWARDS COMPATABILITY
function get_xtpl_edit(){
		global $timedate;
		$name = $this->name;
		$returnXTPL = array();
		if(!empty($this->help)){
		    $returnXTPL[strtoupper($this->name . '_help')] = translate($this->help, $this->bean->module_dir);
		}
		$returnXTPL['USER_DATEFORMAT'] = $timedate->get_user_date_format();
		$returnXTPL['CALENDAR_DATEFORMAT'] = $timedate->get_cal_date_format();
		if(isset($this->bean->$name)){
			$returnXTPL[strtoupper($this->name)] = $this->bean->$name;
		}else{
		    if(empty($this->bean->id) && !empty($this->default_value) && !empty($this->dateStrings[$this->default_value])){
		        $returnXTPL[strtoupper($this->name)] = $GLOBALS['timedate']->to_display_date(date($GLOBALS['timedate']->dbDayFormat,strtotime($this->dateStrings[$this->default_value])), false);
		    }
		}
		return $returnXTPL;
	}

function get_field_def(){
		$def = parent::get_field_def();
		if(!empty($def['default'])){
			$def['display_default'] = $def['default'];
			$def['default'] = '';
		}
		return $def;
	}
}
?>
