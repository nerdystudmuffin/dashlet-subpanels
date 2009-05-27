<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {smarty_function_sugar_password_box} function plugin
 *
 * Type:     function<br>
 * Name:     smarty_function_sugar_password_box<br>
 * Purpose:  display the password requirement box in the User Module
 *
 * @author Aissah Fabrice {faissah at sugarcrm.com
 * @param array
 * @param Smarty
 */
function smarty_function_sugar_password_requirements_box($params, &$smarty)
{
global $current_language;
$administration_module_strings = return_module_language($current_language, 'Administration');
$pwd_settings=$GLOBALS['sugar_config']['passwordsetting'];
if ($pwd_settings['oneupper'] == '1')    $DIVFLAGS['1upcase']=$administration_module_strings['LBL_PASSWORD_ONE_UPPER_CASE']; 
if ($pwd_settings['onelower'] == '1')    $DIVFLAGS['1lowcase']=$administration_module_strings['LBL_PASSWORD_ONE_LOWER_CASE']; 
if ($pwd_settings['onenumber'] == '1')   $DIVFLAGS['1number']=$administration_module_strings['LBL_PASSWORD_ONE_NUMBER']; 
if ($pwd_settings['onespecial'] == '1')  $DIVFLAGS['1special']=$administration_module_strings['LBL_PASSWORD_ONE_SPECIAL_CHAR'];  
if ($pwd_settings['customregex'] != '')  $DIVFLAGS['regex']=$pwd_settings['regexcomment'];
if ($pwd_settings['minpwdlength'] >0 && $pwd_settings['maxpwdlength'] >0)
    $DIVFLAGS['lengths']=$administration_module_strings['LBL_PASSWORD_MINIMUM_LENGTH'].' ='.$pwd_settings['minpwdlength'].' '.$administration_module_strings['LBL_PASSWORD_AND_MAXIMUM_LENGTH'].' ='.$pwd_settings['maxpwdlength'];   
else if ($pwd_settings['minpwdlength'] >0)
        $DIVFLAGS['lengths']=$administration_module_strings['LBL_PASSWORD_MINIMUM_LENGTH'].' ='.$pwd_settings['minpwdlength'];    
    else if ($pwd_settings['maxpwdlength'] >0)
        $DIVFLAGS['lengths']=$administration_module_strings['LBL_PASSWORD_MAXIMUM_LENGTH'].' ='.$pwd_settings['maxpwdlength'];
           
if ($DIVFLAGS=='')
	return;
$table_style='';

foreach($params as $prop => $value){$table_style.= $prop."='".$value."'";}
$box="	<table ".$table_style.">
<tr><td width='18px'></td><td></td></tr>";
foreach($DIVFLAGS as $key => $value) {
	if ($key != '')
		$box.="<tr><td> <div class='bad' id='$key'></div> </td><td>  <div align='left'>$value</div></td></tr>";    	
}
$box.="</table>";
return $box;
}            
?>
