<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {sugarvar} function plugin
 *
 * Type:     function<br>
 * Name:     sugarvar<br>
 * Purpose:  creates a smarty variable from the parameters
 * 
 * @author Wayne Pan {wayne at sugarcrm.com}
 * @param array
 * @param Smarty
 */

function smarty_function_sugarvar($params, &$smarty)
{
	if(empty($params['key']))  {
	    $smarty->trigger_error("sugarvar: missing 'key' parameter");
	    return;
	}    

	$object = (empty($params['objectName']))?$smarty->get_template_vars('parentFieldArray'): $params['objectName'];
	$displayParams = $smarty->get_template_vars('displayParams');
	
	
	if(empty($params['memberName'])){
		$member = $smarty->get_template_vars('vardef');
		$member = $member['name'];
	}else{
		$members = explode('.', $params['memberName']);
		$member =  $smarty->get_template_vars($members[0]);
		for($i = 1; $i < count($members); $i++){
			$member = $member[$members[$i]];	
		}
	}      
	
    $_contents =  '$'. $object . '.' . $member . '.' . $params['key'];
	if(empty($params['stringFormat']) && empty($params['string'])) {
		$_contents = '{' . $_contents;
		if(!empty($displayParams['url2html'])){
			$_contents .= '|url2html';
		}	
		if(!empty($displayParams['nl2br'])){
			$_contents .= '|nl2br';
		}
		$_contents .= '}';
    }
    return $_contents;
}
?>
