<?php

/**
 * This function will replace fields taken from the fields variable
 * and insert them into the passed string replacing [variableName] 
 * tokens where found.
 *
 * @param unknown_type $params
 * @param unknown_type $smarty
 * @return unknown
 */
function smarty_function_sugar_replace_vars($params, &$smarty)
{
	if(empty($params['subject']))  {
	    $smarty->trigger_error("sugarvar: missing 'subject' parameter");
	    return;
	} 
	$fields = $smarty->get_template_vars('fields');
	$subject = $params['subject'];
	$matches = array();
	$count = preg_match_all('/\[([^\]]*)\]/', $subject, $matches);
	for($i = 0; $i < $count; $i++) {
		$match = $matches[1][$i];
		if (!empty($fields[$match]) && isset($fields[$match]['value'])) {
			$value = $fields[$match]['value'];
			if (isset($fields[$match]['type']) && $fields[$match]['type']=='enum' 
				&& isset($fields[$match]['options']) && isset($fields[$match]['options'][$value]))
			{
				$subject = str_replace($matches[0][$i], $fields[$match]['options'][$value], $subject);
			} else 
			{
				$subject = str_replace($matches[0][$i], $value, $subject);
			}
		}
	}
		
	if (!empty($params['assign']))
	{
		$smarty->assign($params['assign'], $subject);
		return '';
	}
	
	return $subject;
}
