<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {sugar_currency_format} function plugin
 *
 * Type:     function<br>
 * Name:     sugar_currency_format<br>
 * Purpose:  formats a number
 * 
 * @author Wayne Pan {wayne at sugarcrm.com}
 * @param array
 * @param Smarty
 */
function smarty_function_sugar_currency_format($params, &$smarty) {

	if(!isset($params['var']) || $params['var'] == '') {  
        return '';
    } 
    
    global $locale;
    if(empty($params['currency_id'])){
    	$params['currency_id'] = $locale->getPrecedentPreference('currency');
    	if(!isset($params['convert'])) {
    	    $params['convert'] = true;
    	}
    	if(!isset($params['currency_symbol'])) {
    	   $params['currency_symbol'] = $locale->getPrecedentPreference('default_currency_symbol');
    	}
    }
   
    $_contents = currency_format_number($params['var'], $params);

    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $_contents);
    } else {
        return $_contents;
    }
}

?>
