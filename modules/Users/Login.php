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
/*********************************************************************************

 * Description: TODO:  To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/












global $app_language, $sugar_config;
//we don't want the parent module's string file, but rather the string file specifc to this subpanel
global $current_language;
$current_module_strings = return_module_language($current_language, 'Users');





















	/*$GLOBALS['db']->query("INSERT INTO temporary_passwords  values(1,'test', '2007-09-20 15:40:47')");*/
 	//$generated_key = $GLOBALS['db']->query("SELECT gen_key from temporary_passwords where user_id='test'");
 	//_pp( $GLOBALS['db']->fetchByAssoc($generated_key));
 


// Retrieve username and password from the session if possible.
if(isset($_SESSION["login_user_name"])) {
	if (isset($_REQUEST['default_user_name']))
		$login_user_name = $_REQUEST['default_user_name'];
	else
		$login_user_name = $_SESSION['login_user_name'];
} else {
	if(isset($_REQUEST['default_user_name'])) {
		$login_user_name = $_REQUEST['default_user_name'];
	} elseif(isset($_REQUEST['ck_login_id_20'])) {
		$login_user_name = get_user_name($_REQUEST['ck_login_id_20']);
	} else {
		$login_user_name = $sugar_config['default_user_name'];
	}
	$_SESSION['login_user_name'] = $login_user_name;
}



$current_module_strings['VLD_ERROR'] = $GLOBALS['app_strings']["\x4c\x4f\x47\x49\x4e\x5f\x4c\x4f\x47\x4f\x5f\x45\x52\x52\x4f\x52"];

// Retrieve username and password from the session if possible.
if(isset($_SESSION["login_password"])) {
	$login_password = $_SESSION['login_password'];
} else {
	$login_password = $sugar_config['default_password'];
	$_SESSION['login_password'] = $login_password;
}

if(isset($_SESSION["login_error"])) {
	$login_error = $_SESSION['login_error'];
}
if(isset($_SESSION["waiting_error"])) {
    $waiting_error = $_SESSION['waiting_error'];
}
//echo get_module_title($current_module_strings['LBL_MODULE_NAME'], $current_module_strings['LBL_LOGIN'], false);


// RECAPTCHA
	
	$admin = new Administration();
	$admin->retrieveSettings('captcha');
	$captcha_privatekey = "";
	$captcha_publickey="";
	$captcha_js = "";
	$Captcha='';
	
	// if the admin set the captcha stuff, assign javascript and div		
	if(isset($admin->settings['captcha_on'])&& $admin->settings['captcha_on']=='1' && !empty($admin->settings['captcha_private_key']) && !empty($admin->settings['captcha_public_key'])){
		
			$captcha_privatekey = $admin->settings['captcha_private_key'];
			$captcha_publickey = $admin->settings['captcha_public_key'];
			$captcha_js .="<script type='text/javascript' src='" . getJSPath('include/javascript/sugar_grp1_yui.js') . "'></script><script type='text/javascript' src='" . getJSPath('include/javascript/sugar_grp_yui2.js') . "'></script>
			<script type='text/javascript' src='http://api.recaptcha.net/js/recaptcha_ajax.js'></script>
			<script>
			function initCaptcha(){
			Recaptcha.create('$captcha_publickey' ,'captchaImage',{theme:'custom'});
			}
			window.onload=initCaptcha;
		
			var handleFailure=handleSuccess;
			var handleSuccess = function(o){
				if(o.responseText!==undefined && o.responseText =='Success'){
					generatepwd();
					Recaptcha.reload();
				}
				else{
					if(o.responseText!='')
						document.getElementById('generate_success').innerHTML =o.responseText;
					Recaptcha.reload();
				}
			}
			var callback2 ={ success:handleSuccess, failure: handleFailure };
			
			function validateAndSubmit(){
					var form = document.getElementById('form');
					var url = '&to_pdf=1&module=Home&action=index&entryPoint=Changenewpassword&recaptcha_challenge_field='+Recaptcha.get_challenge()+'&recaptcha_response_field='+ Recaptcha.get_response();
					YAHOO.util.Connect.asyncRequest('POST','index.php',callback2,url);
			}</script>";
		$Captcha.="<tr>
			<td scope='row' width='20%'>".$mod_strings['LBL_RECAPTCHA_INSTRUCTION'].":</td>
		    <td width='70%'><input type='text' size='26' id='recaptcha_response_field' value=''></td>
			
		</tr>
		<tr>		
				 	
		 	<td colspan='2'><div style='margin-left:2px'class='x-sqs-list' id='recaptcha_image'></div></td>
		</tr>
		<tr>
			<td colspan='2' align='right'><a href='javascript:Recaptcha.reload()'>".$mod_strings['LBL_RECAPTCHA_NEW_CAPTCHA']."</a>&nbsp;&nbsp;
			 		<a class='recaptcha_only_if_image' href='javascript:Recaptcha.switch_type(\"audio\")'>".$mod_strings['LBL_RECAPTCHA_SOUND']."</a>
			 		<a class='recaptcha_only_if_audio' href='javascript:Recaptcha.switch_type(\"image\")'> ".$mod_strings['LBL_RECAPTCHA_IMAGE']."</a>
		 	</td>
		</tr>";
		echo $captcha_js;
	
	}else{
		echo "<script>
		function validateAndSubmit(){generatepwd();}
		</script>";
	}

?>
<script type="text/javascript" language="JavaScript">
<!-- Begin
function set_focus() {
	if (document.DetailView.user_name.value != '') {
		document.DetailView.user_password.focus();
		document.DetailView.user_password.select();
	}
	else document.DetailView.user_name.focus();
}

function toggleDisplay(id){

	if(this.document.getElementById(id).style.display=='none'){
		this.document.getElementById(id).style.display='inline'
		if(this.document.getElementById(id+"link") != undefined){
			this.document.getElementById(id+"link").style.display='none';
		}
        document.getElementById(id+"_options").src = '<?php echo SugarThemeRegistry::current()->getImageURL('basic_search.gif'); ?>';		
	}else{
		this.document.getElementById(id).style.display='none'
		if(this.document.getElementById(id+"link") != undefined){
			this.document.getElementById(id+"link").style.display='inline';
		}
	document.getElementById(id+"_options").src = '<?php echo SugarThemeRegistry::current()->getImageURL('advanced_search.gif'); ?>';	
	}
}


function generatepwd(){
	document.getElementById('generate_pwd_button').value='Please Wait';
	document.getElementById('generate_pwd_button').disabled =1;
	document.getElementById('wait_pwd_generation').innerHTML = '<img src="themes/default/images/img_loading.gif" >';
var callback;
       callback = {
			success: function(o){
			document.getElementById('generate_pwd_button').value='<?php echo $current_module_strings['LBL_LOGIN_SUBMIT'] ?>';
        	document.getElementById('generate_pwd_button').disabled =0;
        	document.getElementById('wait_pwd_generation').innerHTML = '';
			checkok=o.responseText;
			if (checkok.charAt(0) != '1')
				document.getElementById('generate_success').innerHTML =checkok;
			if (checkok.charAt((checkok.length)-1) == '1')
				document.getElementById('generate_success').innerHTML = '<?php echo $current_module_strings['LBL_REQUEST_SUBMIT'] ?>';
			},
            failure: function(o){
            document.getElementById('generate_pwd_button').value='<?php echo $current_module_strings['LBL_LOGIN_SUBMIT'] ?>';
        	document.getElementById('generate_pwd_button').disabled =0;
			document.getElementById('wait_pwd_generation').innerHTML = '';
			alert("Ajax failure");
            }
        }   
    postData = '&to_pdf=1&module=Home&action=index&entryPoint=GeneratePassword&username='+document.getElementById("fp_user_name").value+'&user_email='+document.getElementById("fp_user_mail").value+'&link=1';
    YAHOO.util.Connect.asyncRequest('POST', 'index.php', callback, postData);   
}
		//  End -->
	</script>
	<style type="text/css">
	
	.body { 
		font-size: 12px;
		}
		
	.buttonLogin {
		border: 1px solid #444444;
		font-size: 11px;
		color: #ffffff;
		background-color: #666666;
		font-weight: bold;
		}
		
	table.tabForm td {
	border: none;
	}
		
	table,td {
		}
	
	p {
		MARGIN-TOP: 0px;
		MARGIN-BOTTOM: 10px;
		}
		
	form {
		margin: 0px;
		}

	#recaptcha_image {
        height: 47.5px !important;
        width: 250px !important;
		}

	#recaptcha_image img {
        height: 47.5px;
        width: 250px;
		} 		
	</style><br>
	<br>
	
<table cellpadding="0" align="center" width="100%" cellspacing="0" border="0">
<tr>
<td>
<form action="index.php" method="post" name="DetailView" id="form" onsubmit="return document.getElementById('cant_login').value == ''">

<table cellpadding="0"  cellspacing="0" border="0" align="center" width='350px'>
<tr>
<td style="padding-bottom: 10px;" ><b><?php echo $mod_strings['LBL_LOGIN_WELCOME_TO']; ?></b><br>
<IMG src="<?php echo getWebPath('include/images/sugar_md.png');?>" alt="Sugar" width="340" height="25"></td>
</tr>
<tr>
<td align="center">
	<div class="edit view login">
		<table cellpadding="0" cellspacing="2" border="0" align="center" width="100%">
		<tr>
			<td scope="row" colspan="2" width="100%" style="font-size: 12px; padding-bottom: 5px; font-weight: normal;"><?php echo $app_strings['NTC_LOGIN_MESSAGE']; ?>
			<input type="hidden" name="module" value="Users">
			<input type="hidden" name="action" value="Authenticate">
			<input type="hidden" name="return_module" value="Users">
			<input type="hidden" name="return_action" value="Login">
			<input type="hidden" id="cant_login" name="cant_login" value="">
			<input type="hidden" name="login_module" value="<?php if (isset($_GET['login_module'])) echo $_GET['login_module']; ?>">
			<input type="hidden" name="login_action" value="<?php if (isset($_GET['login_action'])) echo $_GET['login_action']; ?>">
			<input type="hidden" name="login_record" value="<?php if (isset($_GET['login_record'])) echo $_GET['login_record']; ?>">
			</td>
		</tr>
			
<?php

if(isset($login_error) && $login_error != "") {
?>
		<tr>
			<td scope="row" colspan="2"><span class="error"><?php echo $login_error ?></span></td>
    <?php if (isset($waiting_error) && $waiting_error != ""){ ?>
        <tr>
            <td scope="row" colspan="2"><span class="error"><?php echo $waiting_error ?></span></td>
		</tr>
	<?php } ?>
		</tr>
<?php
} else {
?>
		<tr>
			<td scope="row" width='1%'></td>
			<td scope="row"><span id='post_error' class="error"></span></td>
		</tr>
<?php
} // end if(isset($login_error) ....

if (isset($_REQUEST['ck_login_language_20'])) {
	$display_language = $_REQUEST['ck_login_language_20'];
} else {
	$display_language = $sugar_config['default_language'];
}

if (isset($_REQUEST['ck_login_theme_20'])) {
	$display_theme = $_REQUEST['ck_login_theme_20'];
} else {
	$display_theme = $sugar_config['default_theme'];
}

?>
		<tr>
			<td scope="row" width="30%"><?php echo $current_module_strings['LBL_USER_NAME'] ?>:</td>
			<td width="70%"><input type="text" size='35' tabindex="1" id="user_name" name="user_name"  value=<?php echo "\"$login_user_name\"/>"; if (!empty($sugar_config['default_user_name'])) echo "({$sugar_config['default_user_name']})"; ?></td>
		</tr>
		<tr>
			<td scope="row"><?php echo $current_module_strings['LBL_PASSWORD'] ?>:</td>
			<td width="30%"><input type="password" size='26' tabindex="2" id="user_password" name="user_password" value=<?php echo "\"$login_password\"/>"; if (!empty($sugar_config['default_password'])) echo " ({$sugar_config['default_password']})"; ?></td>
		</tr>
		<tr>
			<td colspan="2" class="login_more"><div  style="cursor: hand; cursor: pointer; display:<?php if ( !empty($logindisplay) ) echo $logindisplay; ?>" onclick='toggleDisplay("more");'><IMG src="<?php echo SugarThemeRegistry::current()->getImageURL('advanced_search.gif'); ?>" border="0" alt="Hide Options" id="more_options">&nbsp;<a href='javascript:void(0)'><?php echo $mod_strings['LBL_LOGIN_OPTIONS']; ?></a></div>
				<div id='more' style='display: none'>
					<table cellpadding="0" cellspacing="2" border="0" align="center" width="100%">
						<tr>
							<td scope="row" width="30%"><?php echo $current_module_strings['LBL_THEME'] ?></td>
							<td width="70%"><select style='width: 152px' name='login_theme'><?php echo get_select_options_with_id(SugarThemeRegistry::availableThemes(), $display_theme) ?></select></td>
							</tr>
						<tr>
							<td scope="row"><?php echo $current_module_strings['LBL_LANGUAGE'] ?></td>
							<td><select style='width: 152px' name='login_language'><?php $the_languages = get_languages(); echo get_select_options_with_id($the_languages, $display_language); ?></select></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input title="<?php echo $current_module_strings['LBL_LOGIN_BUTTON_TITLE'] ?>" accessKey="<?php echo $current_module_strings['LBL_LOGIN_BUTTON_TITLE'] ?>" class="button" type="submit" tabindex="3" id="login_button" name="Login" value="  <?php echo $current_module_strings['LBL_LOGIN_BUTTON_LABEL'] ?>  "><br>&nbsp;</td>		
		</tr>
		</form>
		<tr>
			<td colspan="2" class="login_more"><div  style="cursor: hand; cursor: pointer;" onclick='toggleDisplay("forgot_password_dialog");' ><IMG src="<?php echo SugarThemeRegistry::current()->getImageURL('advanced_search.gif'); ?>" border="0" alt="Hide Options" id="forgot_password_dialog_options">&nbsp;<a href='javascript:void(0)'><?php echo $current_module_strings['LBL_LOGIN_FORGOT_PASSWORD'] ?></a></div>
				<div id="forgot_password_dialog" style="display:none" > 
				<form action="index.php" method="post" name="fp_form" id="fp_form" >
					<input type="hidden" name="entryPoint" value="GeneratePassword">
						<table cellpadding="0" cellspacing="2" border="0" align="center" width="100%" >
							<tr>
								<td colspan="2">
									<div id="generate_success" class='error' style="display:inline;"> </div>
								</td>
							</tr>
							<tr>
								<td scope="row" width="30%"><?php echo $current_module_strings['LBL_USER_NAME'] ?>:</td>
								<td width="70%"><input type="text" size='26' id="fp_user_name" name="fp_user_name"  value=<?php echo "\"$login_user_name\"/>"; if (!empty($sugar_config['default_user_name'])) echo " ({$sugar_config['default_user_name']})"; ?></td>
							</tr>
							<tr>
					            <td scope="row" width="30%"><?php echo $current_module_strings['LBL_EMAIL'] ?>:</td>
					            <td width="70%"><input type="text" size='26' id="fp_user_mail" name="fp_user_mail"  value='' ></td>
					     	</tr>
							<?php echo $Captcha ?>
							<tr>
							    <td scope="row" width="30%"><div id='wait_pwd_generation'></div></td>
								<td width="70%"><input title="Email Temp Password" class="button" type="button" style="display:inline" onclick="validateAndSubmit(); return document.getElementById('cant_login').value == ''" id="generate_pwd_button" name="fp_login" value="<?php echo $current_module_strings['LBL_LOGIN_SUBMIT'] ?>"></td>		
							</tr>
					</table>
				</div>
			</td>
		</tr>
		</table>
		</div>
	</td>
</tr>
</table>
</td>
</tr>

</table><br>
<br>
