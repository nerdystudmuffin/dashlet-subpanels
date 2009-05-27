<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * This is a Smarty plugin to handle the inclusion of the overlib js library.
 *
 * @author John Mertic {jmertic@sugarcrm.com}
 */
 
/**
 * smarty_function_overlib_includes
 * This is the constructor for the Smarty plugin.
 * 
 * @param $params The runtime Smarty key/value arguments
 * @param $smarty The reference to the Smarty object used in this invocation 
 */
function smarty_function_overlib_includes($params, &$smarty)
{
    $path = getJSPath('include/javascript/sugar_grp_overlib.js');
	return <<<EOHTML
<!-- begin includes for overlib -->
<script type="text/javascript" src="$path"></script>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000"></div>
<!-- end includes for overlib -->
EOHTML;
}
