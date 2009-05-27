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

 * Description:  Contains a variety of utility functions used to display UI 
 * components such as form headers and footers.  Intended to be modified on a per 
 * theme basis.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

/**
 * Create javascript to validate the data entered into a record.
 */
function get_validate_chart_js () {
    global $current_user;
    global $mod_strings;
    global $app_strings;    
    global $timedate;
    
    $userDateFormat             = $timedate->get_user_date_format();
    $err_invalid_date_format    = $app_strings['ERR_INVALID_DATE_FORMAT'].strtoupper($userDateFormat);
    $err_invalid_month          = $app_strings['ERR_INVALID_MONTH'];
    $err_invalid_day            = $app_strings['ERR_INVALID_DAY'];
    $err_invalid_year           = $app_strings['ERR_INVALID_YEAR'];
    $err_invalid_date           = $app_strings['ERR_INVALID_DATE'];
    $minYear                    = 1900;
    $maxYear                    = 2100;

    $posAbbr  = array("Y", "m", "d");
    $position = array("strYear", "strMonth", "strDay");
    
    if(strpos($userDateFormat, "/")) {
        $separator = "/";
    } else {
        $separator = "-";
    }
    
    // determine position sequence:
    $dateFormatArray = explode($separator, $timedate->get_date_format());
    $dateOrder = array();

    for($i=0; $i<3; $i++) {
        if($dateFormatArray[$i] == $posAbbr[0]) {
            $dateOrder[$i] = $position[0];
        } elseif ($dateFormatArray[$i] == $posAbbr[1]) {
            $dateOrder[$i] = $position[1];
        } else {
            $dateOrder[$i] = $position[2];
        }
    }

$the_script  = "

<script type=\"text/javascript\" language=\"Javascript\">
<!--  to hide script contents from old browsers
/**
 * DHTML date validation script. Courtesy of SmartWebby.com (http://www.smartwebby.com/dhtml/)
 */
// Declaring valid date character, minimum year and maximum year
var dtCh= \"".$separator."\";
var minYear=".$minYear.";
var maxYear=".$maxYear.";

function isInteger(s){
	var i;
    for (i = 0; i < s.length; i++){   
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < \"0\") || (c > \"9\"))) return false;
    }
    // All characters are numbers.
    return true;
}

function stripCharsInBag(s, bag){
	var i;
    var returnString = \"\";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++){   
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function daysInFebruary (year){
	// February has 29 days in any year evenly divisible by four,
    // EXCEPT for centurial years which are not also divisible by 400.
    return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
}
function DaysArray(n) {
	for (var i = 1; i <= n; i++) {
		this[i] = 31
		if (i==4 || i==6 || i==9 || i==11) {this[i] = 30}
		if (i==2) {this[i] = 29}
   } 
   return this
}

function isDate(dtStr){
	var daysInMonth = DaysArray(12)
	var pos1=dtStr.indexOf(dtCh)
	var pos2=dtStr.indexOf(dtCh,pos1+1)

	var ".$dateOrder[0]."=dtStr.substring(0,pos1)
	var ".$dateOrder[1]."=dtStr.substring(pos1+1,pos2)
	var ".$dateOrder[2]."=dtStr.substring(pos2+1)

	strYr=strYear
	if (strDay.charAt(0)==\"0\" && strDay.length>1) strDay=strDay.substring(1)
	if (strMonth.charAt(0)==\"0\" && strMonth.length>1) strMonth=strMonth.substring(1)
	for (var i = 1; i <= 3; i++) {
		if (strYr.charAt(0)==\"0\" && strYr.length>1) strYr=strYr.substring(1)
	}
	month=parseInt(strMonth)
	day=parseInt(strDay)
	year=parseInt(strYr)
	if (pos1==-1 || pos2==-1){
		alert(\"".$err_invalid_date_format."\")
		return false
	}
	if (strMonth.length<1 || month<1 || month>12){
		alert(\"".$err_invalid_month."\")
		return false
	}
	if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
		alert(\"".$err_invalid_day."\")
		return false
	}
	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
		alert(\"".$err_invalid_year."\")
		return false
	}
	if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
		alert(\"".$err_invalid_date."\")
		return false
	}
return true
}


function verify_chart_data(form) {
	var isError = false;
	var errorMessage = \"\";
	if (form.date_start.value != '' && isDate(form.date_start.value)==false) {
		return false;
	}
	if (form.date_end.value != '' && isDate(form.date_end.value)==false) {
		return false;
	}
	return true;
}

function verify_chart_data_outcome_by_month() {
	obm_year = document.outcome_by_month.obm_year.value;
	year = parseInt(obm_year); 
	if (obm_year.length != 4 || isNaN(year) || year == '') {
		alert(\"".$err_invalid_year."\")
		return false
	} 
	return true;
}
// end hiding contents from old browsers  -->
</script>
";
    return $the_script;
}

?>
