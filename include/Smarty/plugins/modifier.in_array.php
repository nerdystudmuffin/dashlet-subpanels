<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty in_array modifier plugin
 *
 * Type:     modifier<br>
 * Name:     in_array<br>
 * Purpose:  check if value is in array
 * @author   Collin Lee <clee at sugarcrm com>
 * @param mixed
 * @param mixed
 * @return boolean
 */
function smarty_modifier_in_array($needle = null, $haystack = null)
{
	//Smarty barfs if Array is empty
    if($haystack == null || empty($haystack)) {
       return false;	
    }
    return in_array($needle, $haystack);
}

?>
