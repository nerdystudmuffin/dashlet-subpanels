<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * This is a Smarty plugin to handle the creation of overlib popups for inline help
 *
 * NOTE: Be sure to include the following code somewhere on the page you'll be using this on.
 * {overlib_includes}
 *
 *
 * @author John Mertic {jmertic@sugarcrm.com}
 */

/**
 * smarty_function_sugar_help
 * This is the constructor for the Smarty plugin.
 *
 * @param $params The runtime Smarty key/value arguments
 * @param $smarty The reference to the Smarty object used in this invocation
 */
function smarty_function_sugar_help($params, &$smarty)
{
    $text = htmlspecialchars($params['text'], ENT_QUOTES);
	//append any additional parameters.
	$onmouseover  = "return overlib('$text', FGCLASS, 'olFgClass', CGCLASS, 'olCgClass', BGCLASS, 'olBgClass', TEXTFONTCLASS, 'olFontClass', CAPTIONFONTCLASS, 'olCapFontClass', CLOSEFONTCLASS, 'olCloseFontClass', WIDTH, -1, NOFOLLOW, 'ol_nofollow'";

	if (count( $params) > 1){
		unset($params['text']);
		foreach($params as $prop => $value){
			$onmouseover .=",".$prop.",".$value;
		}
	}
    $helpImage = SugarThemeRegistry::current()->getImageURL('helpInline.gif');
	$onmouseover .= " );" ;
    return <<<EOHTML
<img border="0" onmouseout="return nd();"
    onmouseover="$onmouseover"
    src="$helpImage"/>
EOHTML;
}

?>
