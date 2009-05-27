<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {sugar_include} function plugin
 *
 * Type:     function<br>
 * Name:     sugar_include<br>
 * Purpose:  Handles rendering the global file includes from the metadata files defined
 *           in templateMeta=>includes.
 * 
 * @author Collin Lee {clee@sugarcrm.com}
 * @param array
 * @param Smarty
 */
function smarty_function_sugar_include($params, &$smarty)
{
    global $app_strings;

    if(isset($params['type']) && $params['type'] == 'php') {
		if(!isset($params['file'])) {
		   $smarty->trigger_error($app_strings['ERR_MISSING_REQUIRED_FIELDS'] . 'include');
		} 
		
		$includeFile = $params['file'];
		if(!file_exists($includeFile)) {
		   $smarty->trigger_error($app_strings['ERR_NO_SUCH_FILE'] . ': ' . $includeFile);
		}
		
	    ob_start();
	    require($includeFile);
	    $output_html = ob_get_contents();
	    ob_end_clean();
	    echo $output_html; 
    } else if(is_array($params['include'])) {
	   	  $code = '';
	   	  foreach($params['include'] as $include) {
	   	  	      if(isset($include['file'])) {
	   	  	         $file = $include['file'];
	   	  	         if(preg_match('/[\.]js$/si',$file)) {
	   	  	            $code .= "<script src=\"". getJSPath($include['file']) ."\"></script>";
	   	  	         } else if(preg_match('/[\.]php$/si', $file)) {
	   	  	            require_once($file);	
	   	  	         }
	   	  	      } 
	   	  } //foreach
	      return $code;
   	} //if
}
?>
