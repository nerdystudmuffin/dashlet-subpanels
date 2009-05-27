<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * SugarCRM is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004 - 2009 SugarCRM Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 ********************************************************************************/


global $sugar_config,$db;

if( !isset( $install_script ) || !$install_script ){
    die($mod_strings['ERR_NO_DIRECT_SCRIPT']);
}







































































$dbType = '';
$oci8 = '';









$dbCreate = "({$mod_strings['LBL_CONFIRM_WILL']} ";
if(!$_SESSION['setup_db_create_database']){
	$dbCreate .= $mod_strings['LBL_CONFIRM_NOT'];
}
$dbCreate .= " {$mod_strings['LBL_CONFIRM_BE_CREATED']})";

$dbUser = "{$_SESSION['setup_db_sugarsales_user']} ({$mod_strings['LBL_CONFIRM_WILL']} ";
if( $_SESSION['setup_db_create_sugarsales_user'] != 1 ){
	$dbUser .= $mod_strings['LBL_CONFIRM_NOT'];
}
$dbUser .= " {$mod_strings['LBL_CONFIRM_BE_CREATED']})";
$yesNoDropCreate = $mod_strings['LBL_NO'];
if ($_SESSION['setup_db_drop_tables']===true ||$_SESSION['setup_db_drop_tables'] == 'true'){
    $yesNoDropCreate = $mod_strings['LBL_YES'];
}
$yesNoSugarUpdates = ($_SESSION['setup_site_sugarbeet']) ? $mod_strings['LBL_YES'] : $mod_strings['LBL_NO'];
$yesNoCustomSession = ($_SESSION['setup_site_custom_session_path']) ? $mod_strings['LBL_YES'] : $mod_strings['LBL_NO'];
$yesNoCustomLog = ($_SESSION['setup_site_custom_log_dir']) ? $mod_strings['LBL_YES'] : $mod_strings['LBL_NO'];
$yesNoCustomId = ($_SESSION['setup_site_specify_guid']) ? $mod_strings['LBL_YES'] : $mod_strings['LBL_NO'];
$nameFormat = $locale->getLocaleFormattedName($mod_strings['LBL_LOCALE_NAME_FIRST'], $mod_strings['LBL_LOCALE_NAME_LAST'], $mod_strings['LBL_LOCALE_NAME_SALUTATION'], $_SESSION['default_locale_name_format']);
$yesNoDemoData = ($_SESSION['setup_db_pop_demo_data']) ? $mod_strings['LBL_YES'] : $mod_strings['LBL_NO'];
if($_SESSION['setup_db_use_mb_demo_data']){
    $yesNoDemoData = $mod_strings['LBL_YES_MULTI'];
}


// Populate the default date format, time format, and language for the system
$defaultDateFormat = "";
$defaultTimeFormat = "";
$defaultLanguages = "";

// Fixes bug 7810 (Offline Client install)
if(isset($sugar_config)){
	if(isset($sugar_config['date_formats'])){
		$defaultDateFormat = $sugar_config['date_formats'][$_SESSION["default_date_format"]];
	}
	if(isset($sugar_config['time_formats'])){
		$defaultTimeFormat = $sugar_config['time_formats'][$_SESSION["default_time_format"]];
	}
	if(isset($sugar_config['languages'])){
		$defaultLanguages = $sugar_config['languages'][$_SESSION["default_language"]];
	}
}
// Fixes Bug 6585
else{
	$sugar_config_defaults = get_sugar_config_defaults();
	// sets the string to have the correct value based on the sugar_config array
	if(isset($_REQUEST['default_date_format'])){
		$defaultDateFormat = $sugar_config_defaults['date_formats'][$_REQUEST['default_date_format']];
	}
	if(isset($_REQUEST['default_time_format'])){
		$defaultTimeFormat = $sugar_config_defaults['time_formats'][$_REQUEST['default_time_format']];
	}
	if(isset($_REQUEST['default_language'])){
		$defaultLanguages = $sugar_config_defaults['languages'][$_REQUEST['default_language']];
	}	
}

///////////////////////////////////////////////////////////////////////////////
////	START OUTPUT

$out =<<<EOQ
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   <meta http-equiv="Content-Script-Type" content="text/javascript">
   <meta http-equiv="Content-Style-Type" content="text/css">
   <title>{$mod_strings['LBL_WIZARD_TITLE']} {$mod_strings['LBL_CONFIRM_TITLE']}</title>
   <link REL="SHORTCUT ICON" HREF="$icon">
   <link rel="stylesheet" href="$css" type="text/css" />
</head>
<body onload="javascript:document.getElementById('defaultFocus').focus();">
<form action="install.php" method="post" name="setConfig" id="form">
<input type="hidden" name="current_step" value="{$next_step}">
<table cellspacing="0" cellpadding="0" border="0" align="center" class="shell">
      <tr><td colspan="2" id="help"><a href="{$help_url}" target='_blank'>{$mod_strings['LBL_HELP']} </a></td></tr>
    <tr>
      <th width="500">
		<p><img src="$sugar_md" alt="SugarCRM" border="0"></p>
		{$mod_strings['LBL_CONFIRM_TITLE']}</th>
        <th width="200" style="text-align: right;"><a href="http://www.sugarcrm.com" target="_blank"><IMG src="$loginImage" width="145" height="30" alt="SugarCRM" border="0"></a>
        </th>
    </tr>
    <tr>
        <td colspan="2">
            
        <table width="100%" cellpadding="0" cellpadding="0" border="0" class="StyleDottedHr">
            <tr><th colspan="3" align="left">{$mod_strings['LBL_DBCONF_TITLE']}</th></tr>
            {$dbType}
            {$oci8}
            <tr>
                <td></td>
                <td><b>{$mod_strings['LBL_DBCONF_DB_NAME']}</b></td>
                <td>
					{$_SESSION['setup_db_database_name']} {$dbCreate}
                </td>
            </tr>
EOQ;

$out .=<<<EOQ
            <tr>
                <td></td>
                <td><b>{$mod_strings['LBL_DBCONF_DB_ADMIN_USER']}</b></td>
                <td>{$_SESSION['setup_db_admin_user_name']}</td>
            </tr>
            <tr>
                <td></td>
                <td><b>{$mod_strings['LBL_DBCONF_DEMO_DATA']}</b></td>
                <td>{$yesNoDemoData}</td>
            </tr>
EOQ;
if($yesNoDropCreate){

$out .=<<<EOQ
            <tr>
                <td></td>
                <td><b>{$mod_strings['LBL_DBCONF_DB_DROP']}</b></td>
                <td>{$yesNoDropCreate}</td>
            </tr>
EOQ;
    
}


















if(isset($_SESSION['install_type'])  && !empty($_SESSION['install_type'])  && $_SESSION['install_type']=='custom'){
$out .=<<<EOQ

	   <tr><td colspan="3" align="left"></td></tr>
            <tr>
            	<th colspan="3" align="left">{$mod_strings['LBL_SITECFG_TITLE']}</th>
            </tr>
            <tr>
                <td></td>
                <td><b>{$mod_strings['LBL_SITECFG_URL']}</b></td>
                <td>{$_SESSION['setup_site_url']}</td>
            </tr>
            <tr>
	   <tr><td colspan="3" align="left"></td></tr>
            	<th colspan="3" align="left">{$mod_strings['LBL_SITECFG_SUGAR_UPDATES']}</th>
            </tr>
            <tr>
                <td></td>
                <td><b>{$mod_strings['LBL_SITECFG_SUGAR_UP']}</b></td>
                <td>{$yesNoSugarUpdates}</td>
            </tr>
            <tr>
	   <tr><td colspan="3" align="left"></td></tr>
            	<th colspan="3" align="left">{$mod_strings['LBL_SITECFG_SITE_SECURITY']}</th>
            </tr>
            <tr>
                <td></td>
                <td><b>{$mod_strings['LBL_SITECFG_CUSTOM_SESSION']}?</b></td>
                <td>{$yesNoCustomSession}</td>
            </tr>
            <tr>
                <td></td>
                <td><b>{$mod_strings['LBL_SITECFG_CUSTOM_LOG']}?</b></td>
                <td>{$yesNoCustomLog}</td>
            </tr>
            <tr>
                <td></td>
                <td><b>{$mod_strings['LBL_SITECFG_CUSTOM_ID']}?</b></td>
                <td>{$yesNoCustomId}</td>
            </tr>
EOQ;
}
/*
if(isset($_SESSION['licenseKey_submitted']) && ($_SESSION['licenseKey_submitted']) 
            && (isset($GLOBALS['db']) && !empty($GLOBALS['db']))){
$out .=<<<EOQ

<!--




























-->
EOQ;
}
*/

$out .=<<<EOQ

	   <tr><td colspan="3" align="left"></td></tr>
            <tr>
            	<th colspan="3" align="left">{$mod_strings['LBL_LOCALE_TITLE']}</th>
            </tr>
				<tr>	
					<td></td>
					<td>
						<b>{$mod_strings['LBL_LOCALE_DATEF']}</b>
					</td>
					<td>
						{$defaultDateFormat}
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<b>{$mod_strings['LBL_LOCALE_TIMEF']}</b>
					</td>
					<td>
						{$defaultTimeFormat}
					</td>
				</tr>
EOQ;


if(isset($_SESSION['install_type'])  && !empty($_SESSION['install_type'])  && $_SESSION['install_type']=='custom'){
$out .=<<<EOQ


				<tr>
					<td></td>
					<td>
						<b>{$mod_strings['LBL_LOCALE_LANG']}</b>
					</td>
					<td>
						{$defaultLanguages}
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<b>{$mod_strings['LBL_LOCALE_NAMEF']}</b>
					</td>
					<td>
						{$nameFormat}
					</td>
				</tr>
                <tr>
                    <td></td>
                    <td>
                        <b>{$mod_strings['LBL_EMAIL_CHARSET_CONF']}</b>
                    </td>
                    <td>
                        {$_SESSION["default_email_charset"]}
                    </td>
                </tr>                
				<tr>
					<td></td>
					<td>
						<b>{$mod_strings['LBL_LOCALE_EXPORT']}</b>
					</td>
					<td>
						{$_SESSION["default_export_charset"]}
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<b>{$mod_strings['LBL_LOCALE_EXPORT_DELIMITER']}</b>
					</td>
					<td>
						{$_SESSION["export_delimiter"]}
					</td>
				</tr>
EOQ;
}


$out .=<<<EOQ


				<tr>
					<td></td>
					<td>
						<b>{$mod_strings['LBL_LOCALE_CURR_DEFAULT']}</b>
					</td>
					<td>
						{$_SESSION["default_currency_name"]}
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<b>{$mod_strings['LBL_LOCALE_CURR_SYMBOL']}</b>
					</td>
					<td>
						{$_SESSION["default_currency_symbol"]}
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<b>{$mod_strings['LBL_LOCALE_CURR_ISO']}</b>
					</td>
					<td>
						{$_SESSION["default_currency_iso4217"]}
					</td>
				</tr>
                <tr>
                    <td></td>
                    <td>
                        <b>{$mod_strings['LBL_LOCALE_CURR_SIG_DIGITS']}</b>
                    </td>
                    <td>
                        {$_SESSION["default_currency_significant_digits"]}
                    </td>
                </tr>
				<tr>
					<td></td>
					<td>
						<b>{$mod_strings['LBL_LOCALE_CURR_1000S']}</b>
					</td>
					<td>
						{$_SESSION["default_number_grouping_seperator"]}
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<b>{$mod_strings['LBL_LOCALE_CURR_DECIMAL']}</b>
					</td>
					<td>
						{$_SESSION["default_decimal_seperator"]}
					</td>
				</tr>


	   <tr><td colspan="3" align="left"></td></tr>
          <tr><th colspan="3" align="left">{$mod_strings['LBL_SYSTEM_CREDS']}</th></tr>
            <tr>
                <td></td>
                <td><b>{$mod_strings['LBL_DBCONF_DB_USER']}</b></td>
                <td>
                    {$_SESSION['setup_db_sugarsales_user']} 
                </td>
            </tr>
            <tr>
                <td></td>
                <td><b>{$mod_strings['LBL_DBCONF_DB_PASSWORD']}</b></td>
                <td>
                    <span id='hide_db_admin_pass'>{$mod_strings['LBL_HIDDEN']}</span>
                    <span style='display:none' id='show_db_admin_pass'>{$_SESSION['setup_db_sugarsales_password']}</span> 
                </td>
            </tr>            
            <tr>
                <td></td>
                <td><b>{$mod_strings['LBL_SITECFG_ADMIN_Name']}</b></td>
                <td>
                    Admin 
                </td>
            </tr>
            <tr>
                <td></td>
                <td><b>{$mod_strings['LBL_SITECFG_ADMIN_PASS']}</b></td>
                <td>
                    <span id='hide_site_admin_pass'>{$mod_strings['LBL_HIDDEN']}</span>
                    <span style='display:none' id='show_site_admin_pass'>{$_SESSION['setup_site_admin_password']}</span> 
                </td>
            </tr>                        
                
EOQ;



    
    
    
$envString = '
	   <tr><td colspan="3" align="left"></td></tr><tr><th colspan="3" align="left">'.$mod_strings['LBL_SYSTEM_ENV'].'</th></tr>';    

    // PHP VERSION
        $envString .='
          <tr> 
             <td></td>
            <td><b>'.$mod_strings['LBL_CHECKSYS_PHPVER'].'</b></td>
            <td >'.constant('PHP_VERSION').'</td>
          </tr>';   


//Begin List of already known good variables.  These were checked during the initial sys check
// XML Parsing
        $envString .='
      <tr>
        <td></td>
        <td><strong>'.$mod_strings['LBL_CHECKSYS_XML'].'</strong></td>
        <td  >'.$mod_strings['LBL_CHECKSYS_OK'].'</td>
      </tr>';    



// mbstrings

        $envString .='
      <tr>
        <td></td>
        <td><strong>'.$mod_strings['LBL_CHECKSYS_MBSTRING'].'</strong></td>
        <td  >'.$mod_strings['LBL_CHECKSYS_OK'].'</td>
      </tr>';    

// config.php
        $envString .='
      <tr>
        <td></td>
        <td><strong>'.$mod_strings['LBL_CHECKSYS_CONFIG'].'</strong></td>
        <td  >'.$mod_strings['LBL_CHECKSYS_OK'].'</td>
      </tr>';    

// custom dir


        $envString .='
      <tr>
        <td></td>
        <td><strong>'.$mod_strings['LBL_CHECKSYS_CUSTOM'].'</strong></td>
        <td  >'.$mod_strings['LBL_CHECKSYS_OK'].'</td>
      </tr>';    


// modules dir
        $envString .='
      <tr>
        <td></td>
        <td><strong>'.$mod_strings['LBL_CHECKSYS_MODULE'].'</strong></td>
        <td  >'.$mod_strings['LBL_CHECKSYS_OK'].'</td>
      </tr>';    

// data dir

        $envString .='
      <tr>
        <td></td>
        <td><strong>'.$mod_strings['LBL_CHECKSYS_DATA'].'</strong></td>
        <td  >'.$mod_strings['LBL_CHECKSYS_OK'].'</td>
      </tr>';    

// cache dir
    $error_found = true;
        $envString .='
      <tr>
        <td></td>
        <td><strong>'.$mod_strings['LBL_CHECKSYS_CACHE'].'</strong></td>        
        <td  >'.$mod_strings['LBL_CHECKSYS_OK'].'</td>
      </tr>';    
// End already known to be good

// memory limit
$memory_msg     = "";
// CL - fix for 9183 (if memory_limit is enabled we will honor it and check it; otherwise use unlimited)
$memory_limit = ini_get('memory_limit');
if(empty($memory_limit)){
    $memory_limit = "-1";
}
if(!defined('SUGARCRM_MIN_MEM')) {
    define('SUGARCRM_MIN_MEM', 40);
}
$sugarMinMem = constant('SUGARCRM_MIN_MEM');
// logic based on: http://us2.php.net/manual/en/ini.core.php#ini.memory-limit
if( $memory_limit == "" ){          // memory_limit disabled at compile time, no memory limit
    $memory_msg = "<b>{$mod_strings['LBL_CHECKSYS_MEM_OK']}</b>";
} elseif( $memory_limit == "-1" ){   // memory_limit enabled, but set to unlimited
    $memory_msg = "{$mod_strings['LBL_CHECKSYS_MEM_UNLIMITED']}";
} else {
    $mem_display = $memory_limit;
    rtrim($memory_limit, 'M');
    $memory_limit_int = (int) $memory_limit;
    $SUGARCRM_MIN_MEM = (int) constant('SUGARCRM_MIN_MEM');
    if( $memory_limit_int < constant('SUGARCRM_MIN_MEM') ){
        $memory_msg = "<span class='stop'><b>$memory_limit{$mod_strings['ERR_CHECKSYS_MEM_LIMIT_1']}" . constant('SUGARCRM_MIN_MEM') . "{$mod_strings['ERR_CHECKSYS_MEM_LIMIT_2']}</b></span>";
        $memory_msg = str_replace('$memory_limit', $mem_display, $memory_msg);
    } else {
        $memory_msg = "{$mod_strings['LBL_CHECKSYS_OK']} ({$memory_limit})";
    }
}
        
          $envString .='
      <tr>
        <td></td>
        <td><strong>'.$mod_strings['LBL_CHECKSYS_MEM'].'</strong></td>
        <td  >'.$memory_msg.'</td>
      </tr>';    

    // zlib
    if(function_exists('gzclose')) {
        $zlibStatus = "{$mod_strings['LBL_CHECKSYS_OK']}";
    } else {
        $zlibStatus = "<span class='stop'><b>{$mod_strings['ERR_CHECKSYS_ZLIB']}</b></span>";
    }
            $envString .='
          <tr>
            <td></td>
            <td><strong>'.$mod_strings['LBL_CHECKSYS_ZLIB'].'</strong></td>        
            <td  >'.$zlibStatus.'</td>
          </tr>';    
    
    
    
    
    // imap
    if(function_exists('imap_open')) {
        $imapStatus = "{$mod_strings['LBL_CHECKSYS_OK']}";
    } else {
        $imapStatus = "<span class='stop'><b>{$mod_strings['ERR_CHECKSYS_IMAP']}</b></span>";
    }
    
            $envString .='
          <tr>
            <td></td>
            <td><strong>'.$mod_strings['LBL_CHECKSYS_IMAP'].'</strong></td>        
            <td  >'.$imapStatus.'</td>
          </tr>';    
    
    
    // cURL
    if(function_exists('curl_init')) {
        $curlStatus = "{$mod_strings['LBL_CHECKSYS_OK']}";
    } else {
        $curlStatus = "<span class='stop'><b>{$mod_strings['ERR_CHECKSYS_CURL']}</b></span>";
    }
    
            $envString .='
          <tr>
            <td></td>
            <td><strong>'.$mod_strings['LBL_CHECKSYS_CURL'].'</strong></td>        
            <td  >'.$curlStatus.'</td>
          </tr>';    
    
    
      //CHECK UPLOAD FILE SIZE
        $upload_max_filesize = ini_get('upload_max_filesize');
        $upload_max_filesize_bytes = return_bytes($upload_max_filesize);
        if(!defined('SUGARCRM_MIN_UPLOAD_MAX_FILESIZE_BYTES')){
            define('SUGARCRM_MIN_UPLOAD_MAX_FILESIZE_BYTES', 6 * 1024 * 1024);
        }
        
        if($upload_max_filesize_bytes > constant('SUGARCRM_MIN_UPLOAD_MAX_FILESIZE_BYTES')) {
            $fileMaxStatus = "{$mod_strings['LBL_CHECKSYS_OK']}</font>";
        } else {
            $fileMaxStatus = "<span class='stop'><b>{$mod_strings['ERR_UPLOAD_MAX_FILESIZE']}</font></b></span>";
        }
    
            $envString .='
          <tr>
            <td></td>
            <td><strong>'.$mod_strings['LBL_UPLOAD_MAX_FILESIZE_TITLE'].'</strong></td>        
            <td  >'.$fileMaxStatus.'</td>
          </tr>';    
    
    
    


// PHP.ini
$phpIniLocation = get_cfg_var("cfg_file_path");
          $envString .='        
      <tr>
        <td></td>
        <td><strong>'.$mod_strings['LBL_CHECKSYS_PHP_INI'].'</strong></td>
        <td  >'.$phpIniLocation.'</td>
      </tr>';    
    
$out .= $envString;
    
    
$out .=<<<EOQ
    
        </table>
        </td>
    </tr>
    <tr>    
          <td colspan='3' align='right'>
                <input type="button" class="button" name="print_summary" value="{$mod_strings['LBL_PRINT_SUMM']}" 
                onClick='window.print()' onCluck='window.open("install.php?current_step="+(document.setConfig.current_step.value -1)+"&goto={$mod_strings["LBL_NEXT"]}&print=true");' />&nbsp;
      </td>
    </tr>    
    <tr>
        <td align="right" colspan="2">
        <hr>
        <table cellspacing="0" cellpadding="0" border="0" class="stdTable">
            <tr>
              <td align=right>
                    <input type="button" class="button" id="show_pass_button" value="{$mod_strings['LBL_SHOW_PASS']}" 
                    onClick='togglePass();' />           
              </td>
                <td>
                	<input type="hidden" name="goto" id="goto">
                    <input class="button" type="button" value="{$mod_strings['LBL_BACK']}" onclick="document.getElementById('goto').value='{$mod_strings['LBL_BACK']}';document.getElementById('form').submit();" />
                </td>
                <td>
                	<input class="button" type="button" value="{$mod_strings['LBL_LANG_BUTTON_COMMIT']}" onclick="document.getElementById('goto').value='{$mod_strings['LBL_NEXT']}';document.getElementById('form').submit();" id="defaultFocus"/>
                </td>
            </tr>
        </table>
        </td>
    </tr>
</table>
</form>
<br>
<script>
function togglePass(){
    if(document.getElementById('show_site_admin_pass').style.display == ''){
        document.getElementById('show_pass_button').value = "{$mod_strings['LBL_SHOW_PASS']}";
        document.getElementById('hide_site_admin_pass').style.display = '';
        document.getElementById('hide_db_admin_pass').style.display = '';   
        document.getElementById('show_site_admin_pass').style.display = 'none';
        document.getElementById('show_db_admin_pass').style.display = 'none';   

    }else{
        document.getElementById('show_pass_button').value = "{$mod_strings['LBL_HIDE_PASS']}";
        document.getElementById('show_site_admin_pass').style.display = '';
        document.getElementById('show_db_admin_pass').style.display = '';
        document.getElementById('hide_site_admin_pass').style.display = 'none';
        document.getElementById('hide_db_admin_pass').style.display = 'none';   
        
    }
}
</script>
</body>
</html>


EOQ;
echo $out;

?>









