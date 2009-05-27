<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty strip_semicolon modifier plugin
 *
 * Type:     modifier<br>
 * Name:     strip<br>
 * Purpose:  Replace strings with trailing semicolon with blank string
 * @author   Collin Lee

 * @param string
 * @return string
 */
function smarty_modifier_strip_semicolon($text)
{   
	return preg_replace('/[:][\s]*$/', '', trim($text));
}
?>
