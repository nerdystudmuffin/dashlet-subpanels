<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {sugar_evalcolumn} function plugin
 *
 * Type:     function<br>
 * Name:     sugar_evalcolumn<br>
 * Purpose:  evaluate a string by substituting values in the rowData parameter. Used for ListViews<br>
 * 
 * @author Wayne Pan {wayne at sugarcrm.com
 * @param array
 * @param Smarty
 */
function smarty_function_sugar_evalcolumn($params, &$smarty)
{
    if (!isset($params['colData']['field']) ) {
        if(empty($params['colData']))  
            $smarty->trigger_error("evalcolumn: missing 'colData' parameter");
        if(!isset($params['colData']['field']))  
            $smarty->trigger_error("evalcolumn: missing 'colData.field' parameter");
        return;
    }

    if(empty($params['colData']['field'])) {
        return;
    }
    $params['var'] = $params['colData']['field'];
    if(isset($params['toJSON'])) {
        $json = getJSONobj();
        $params['var'] = $json->encode($params['var']);
    }

    if (!empty($params['var']['assign'])) {
        return '{$' . $params['colData']['field']['name'] . '}';
    } else {
    	$code = $params['var']['customCode'];
    	if(isset($params['tabindex']) && preg_match_all("'(<[ ]*?)(textarea|input|select)([^>]*?)(>)'si", $code, $matches, PREG_PATTERN_ORDER)) {
    	   $str_replace = array();
    	   $tabindex = ' tabindex="' . $params['tabindex'] . '" ';
    	   foreach($matches[3] as $match) {
    	   	       $str_replace[$match] = $tabindex . $match;
    	   }
    	   $code = str_replace(array_keys($str_replace), array_values($str_replace), $code);
    	}
    	
    	if(!empty($params['var']['displayParams']['enableConnectors'])) {
    	  require_once('include/connectors/utils/ConnectorUtils.php');
    	  $code .= '&nbsp;' . ConnectorUtils::getConnectorButtonScript($params['var']['displayParams'], $smarty);
    	}
    	return $code;
    }
    
    
}


?>
