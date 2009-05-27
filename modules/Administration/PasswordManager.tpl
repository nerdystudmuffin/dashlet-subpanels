{*

/**
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
 */



*}
<script type='text/javascript' src='include/javascript/overlibmws.js'></script>
<form name="ConfigurePasswordSettings" method="POST" action="index.php" >
<input type='hidden' name='action' value='PasswordManager'/>
<input type='hidden' name='module' value='Administration'/>
<input type='hidden' name='saveConfig' value='1'/>
<span class='error'>{$error.main}</span>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
	
		<td style="padding-bottom: 2px;" >
			<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="button"  type="submit" onclick="addcheck(form);return check_form('ConfigurePasswordSettings');"  name="save" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " >
			&nbsp;<input title="{$MOD.LBL_CANCEL_BUTTON_TITLE}"  onclick="document.location.href='index.php?module=Administration&action=index'" class="button"  type="button" name="cancel" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " >
		</td>
	</tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="edit view">
				<tr>
					<th align="left" scope="row" colspan="4">
						<h4>
							{$MOD.LBL_PASSWORD_RULES_MANAGEMENT}
						</h4>
					</th>
				</tr>
				<tr>
					<td>
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td  scope="row" width='25%'>
									{$MOD.LBL_PASSWORD_MINIMUM_LENGTH}:
								</td>
								<td  	width='25%' >
									<input type='text' size='4' name='passwordsetting_minpwdlength' value='{$config.passwordsetting.minpwdlength}'>
								</td>
								<td  scope="row"	width='25%'>
									{$MOD.LBL_PASSWORD_MAXIMUM_LENGTH}:
								</td>
								<td  	width='25%'>
									<input type='text' size='4' id='passwordsetting_maxpwdlength' name='passwordsetting_maxpwdlength' value='{$config.passwordsetting.maxpwdlength}'>
								</td>
							</tr>
							<tr>
								<td  scope="row">
									{$MOD.LBL_PASSWORD_ONE_UPPER_CASE}: 
								</td>
								{if ($config.passwordsetting.oneupper ) == '1'}
									{assign var='oneupper_checked' value='CHECKED'}
								{else}
									{assign var='oneupper_checked' value=''}
								{/if}
								<td >
									<input type='hidden' name='passwordsetting_oneupper' value='0'>
									<input name='passwordsetting_oneupper'  type='checkbox' value='1' {$oneupper_checked}>
								</td>
								<td  scope="row">
									{$MOD.LBL_PASSWORD_ONE_LOWER_CASE}:
								</td>
								{if ($config.passwordsetting.onelower ) == '1'}
									{assign var='onelower_checked' value='CHECKED'}
								{else}
									{assign var='onelower_checked' value=''}
								{/if}
								<td >
									<input type='hidden' name='passwordsetting_onelower' value='0'>
									<input name='passwordsetting_onelower'  type='checkbox' value='1' {$onelower_checked}>
								</td>
							</tr>
							<tr>
								<td  scope="row">
									{$MOD.LBL_PASSWORD_ONE_NUMBER}: 
								</td>
								{if ($config.passwordsetting.onenumber ) == '1'}
									{assign var='onenumber_checked' value='CHECKED'}
								{else}
									{assign var='onenumber_checked' value=''}
								{/if}
								<td >
									<input type='hidden' name='passwordsetting_onenumber' value='0'>
									<input name='passwordsetting_onenumber'  type='checkbox' value='1' {$onenumber_checked}>
								</td>
								<td  scope="row">
									{$MOD.LBL_PASSWORD_ONE_SPECIAL_CHAR}:
								</td>
								{if ($config.passwordsetting.onespecial ) == '1'}
									{assign var='onespecial_checked' value='CHECKED'}
								{else}
									{assign var='onespecial_checked' value=''}
								{/if}
								<td >
									<input type='hidden' name='passwordsetting_onespecial' value='0'>
									<input name='passwordsetting_onespecial'  type='checkbox' value='1' {$onespecial_checked}>
								</td>
							</tr>
					{*		<tr>
								<td  scope="row">
									{$MOD.LBL_PASSWORD_PROHIBITED_CARACTERS}:
								</td>
								<td  >
									<input type='text' size='10' name='passwordsetting_prohibitedcaracters' value="{$config.passwordsetting.prohibitedcaracters}">
								</td>
								<td  scope="row" nowrap>
									{$MOD.LBL_PASSWORD_NEEDED_CARACTERS}:
								</td>
								<td   width='30%'>
									<input type='text'  size='10' name='passwordsetting_neededcaracters' value='{$config.passwordsetting.neededcaracters}'>
								</td>
							</tr>
							<tr>
								<td  scope="row">
									{$MOD.LBL_PASSWORD_FIRSTNAME_PROHIBITED}: 
								</td>
								{if ($config.passwordsetting.firstnameallowed ) == '1'}
									{assign var='first_name_allowed_checked' value='CHECKED'}
								{else}
									{assign var='first_name_allowed_checked' value=''}
								{/if}
								<td >
									<input type='hidden' name='passwordsetting_firstnameallowed' value='0'>
									<input name='passwordsetting_firstnameallowed'  type='checkbox' value='1' {$first_name_allowed_checked}>
								</td>
								<td  scope="row">
									{$MOD.LBL_PASSWORD_LASTNAME_PROHIBITED}:
								</td>
						
								{if ($config.passwordsetting.lastnameallowed ) == '1'}
									{assign var='last_name_allowed_checked' value='CHECKED'}
								{else}
									{assign var='last_name_allowed_checked' value=''}
								{/if}
								<td >
									<input type='hidden' name='passwordsetting_lastnameallowed' value='0'>
									<input name='passwordsetting_lastnameallowed'  type='checkbox' value='1' {$last_name_allowed_checked}>
								</td>
							</tr>
					*}		<tr>
								<td  scope="row">
									{$MOD.LBL_PASSWORD_REGEX}: {sugar_help text=$MOD.LBL_REGEX_HELP_TEXT WIDTH=500}
								</td>
								<td  >
									<input type='text'  style="width: 200px;" size='10' name='passwordsetting_customregex' id='customregex' value='{$config.passwordsetting.customregex}' onblur='testregex(this)'>
								</td>
								<td  scope="row" nowrap>
									{$MOD.LBL_PASSWORD_REGEX_COMMENT}: {sugar_help text=$MOD.LBL_REGEX_DESC_HELP_TEXT WIDTH=500}
								</td>
								<td   width='30%'>
									<input type='text'  style="width: 250px;" size='10' name='passwordsetting_regexcomment' value='{$config.passwordsetting.regexcomment}'>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<td>
					<tr>
						<table width="100%" border="0" cellspacing="0" cellpadding="0" class="edit view">
							<tr>
								<th align="left" scope="row" colspan="4">
									<h4>
										{$MOD.LBL_PASSWORD_TEMPLATE}
									</h4>
								</th>
							</tr>
							<tr>
								<td>
									<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr>
									        <td  scope="row">{$MOD.LBL_PASSWORD_GENERATE_TEMPLATE_MSG}: </td>
									        <td  >
										        <slot>
									        		<select tabindex='251' id="generatepasswordtmpl" name="passwordsetting_generatepasswordtmpl" {$IE_DISABLED}>{$TMPL_DRPDWN_GENERATE}</select>
													<input type="button" class="button" onclick="javascript:open_email_template_form('generatepasswordtmpl')" value="{$MOD.LBL_PASSWORD_CREATE_TEMPLATE}" {$IE_DISABLED}>
													<input type="button" value="{$MOD.LBL_PASSWORD_EDIT_TEMPLATE}" class="button" onclick="javascript:edit_email_template_form('generatepasswordtmpl')" name='edit_generatepasswordtmpl' id='edit_generatepasswordtmpl' style="{$EDIT_TEMPLATE}">
												</slot>
									        </td>
									        <td  scope="row"></td>
									        <td  ></td>
										</tr>
										<tr>
									        <td  scope="row">{$MOD.LBL_PASSWORD_LOST_TEMPLATE_MSG}: </td>
									        <td  >
							        			<slot>
									        		<select tabindex='251' id="lostpasswordtmpl" name="passwordsetting_lostpasswordtmpl" {$IE_DISABLED}>{$TMPL_DRPDWN_LOST}</select>
													<input type="button" class="button" onclick="javascript:open_email_template_form('lostpasswordtmpl')" value="{$MOD.LBL_PASSWORD_CREATE_TEMPLATE}" {$IE_DISABLED}>
													<input type="button" value="{$MOD.LBL_PASSWORD_EDIT_TEMPLATE}" class="button" onclick="javascript:edit_email_template_form('lostpasswordtmpl')" name='edit_lostpasswordtmpl' id='edit_lostpasswordtmpl' style="{$EDIT_TEMPLATE}">
												</slot>
							        		 </td>
									        <td  scope="row"></td>
									        <td ></td>
										</tr>
									</table>
								</td>
							</tr>
							
						</table>
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<td>
								<tr>
									<table width="100%" border="0" cellspacing="0" cellpadding="0" class="edit view">
										<tr>
										<th align="left" scope="row" colspan="4">
											<h4 scope="row" >
												{$MOD.LBL_PASSWORD_LINK_EXPIRATION}
											</h4>
										</th>
									</tr>
									<tr>
								<td>
									<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr>
									            {assign var='linkexptime' value=''}
                                                {assign var='linkexpnone' value=''}
                                            {if ($config.passwordsetting.linkexpiration) == '0'}
                                                {assign var='linkexpnone' value='CHECKED'}
                                            {/if}
                                            {if ($config.passwordsetting.linkexpiration) == '1'}
                                                {assign var='linkexptime' value='CHECKED'}
                                            {/if}
                                            <td  scope="row" width='30%'>
                                                <input type="radio" name="passwordsetting_linkexpiration" value='0' {$linkexpnone} onclick="form.passwordsetting_linkexpirationtime.value='';">
                                               {$MOD.LBL_UW_NONE}
                                            </td>
    										<td  scope="row" width='30%'>
												<input type="radio" name="passwordsetting_linkexpiration" id="required_link_exp_time" value='1' {$linkexptime}>
												{$MOD.LBL_PASSWORD_LINK_EXP_IN}
												{assign var='ldays' value=''}
												{assign var='lweeks' value=''}
												{assign var='lmonths' value=''}
											{if ($config.passwordsetting.linkexpirationtype ) == '1'}
												{assign var='ldays' value='SELECTED'}
											{/if}
											{if ($config.passwordsetting.linkexpirationtype ) == '60'}
												{assign var='lweeks' value='SELECTED'}
											{/if}
											{if ($config.passwordsetting.linkexpirationtype ) == '1440'}
												{assign var='lmonths' value='SELECTED'}
											{/if}
												<input type='text' maxlength="3" and style="width:2em" name='passwordsetting_linkexpirationtime' value='{$config.passwordsetting.linkexpirationtime}'>
												<SELECT NAME="passwordsetting_linkexpirationtype">
													<OPTION VALUE='1' {$ldays}>Minutes
													<OPTION VALUE='60' {$lweeks}>Hours
													<OPTION VALUE='1440' {$lmonths}>Days
												</SELECT>
											</td width='40%'>
											<td >
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<td>
								<tr>
									<table width="100%" border="0" cellspacing="0" cellpadding="0" class="edit view">
										<tr>
										<th align="left" scope="row" colspan="4">
											<h4 scope="row" >
												{$MOD.LBL_PASSWORD_USER_EXPIRATION}
											</h4>
										</th>
									</tr>
									<tr>
								<td>
									<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr>
									            {assign var='userexplogin' value=''}
                                                {assign var='userexptime' value=''}
                                                {assign var='userexpnone' value=''}
                                            {if ($config.passwordsetting.userexpiration) == '0'}
                                                {assign var='userexpnone' value='CHECKED'}
                                            {/if}
                                            {if ($config.passwordsetting.userexpiration) == '1'}
                                                {assign var='userexptime' value='CHECKED'}
                                            {/if}
                                            {if ($config.passwordsetting.userexpiration) == '2'}
                                                {assign var='userexplogin' value='CHECKED'}
                                            {/if}
										    <td  scope="row" width='30%'>
                                                <input type="radio" name="passwordsetting_userexpiration"  value='0' {$userexpnone} onclick="form.passwordsetting_userexpirationtime.value='';form.passwordsetting_userexpirationlogin.value='';">
                                               {$MOD.LBL_UW_NONE}
                                            </td>
    										<td  scope="row" width='30%'>
												<input type="radio" name="passwordsetting_userexpiration" id="required_pwd_exp_time" value='1' {$userexptime} onclick="form.passwordsetting_userexpirationlogin.value='';">
												{$MOD.LBL_PASSWORD_EXP_IN} 
												{assign var='udays' value=''}
												{assign var='uweeks' value=''}
												{assign var='umonths' value=''}
											{if ($config.passwordsetting.userexpirationtype ) == '1'}
												{assign var='udays' value='SELECTED'}
											{/if}
											{if ($config.passwordsetting.userexpirationtype ) == '7'}
												{assign var='uweeks' value='SELECTED'}
											{/if}
											{if ($config.passwordsetting.userexpirationtype ) == '30'}
												{assign var='umonths' value='SELECTED'}
											{/if}
												<input type='text' maxlength="3" and style="width:2em" name='passwordsetting_userexpirationtime' value='{$config.passwordsetting.userexpirationtime}'>
												<SELECT NAME="passwordsetting_userexpirationtype">
													<OPTION VALUE='1' {$udays}>Days
													<OPTION VALUE='7' {$uweeks}>Weeks
													<OPTION VALUE='30' {$umonths}>Months
												</SELECT>
											</td>
											<td colspan='2' scope="row" width='40%'>
												<input type="radio" name="passwordsetting_userexpiration" id="required_pwd_exp_login" value='2' {$userexplogin} onclick="form.passwordsetting_userexpirationtime.value='';">
												{$MOD.LBL_PASSWORD_EXP_AFTER} 
												<input type='text' maxlength="3" and style="width:2em" name='passwordsetting_userexpirationlogin' value="{$config.passwordsetting.userexpirationlogin}">
												{$MOD.LBL_PASSWORD_LOGINS}
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table> 
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
									<table width="100%" border="0" cellspacing="0" cellpadding="0" class="edit view">
								<tr>
								<th align="left" scope="row" colspan="4">
									<h4>
										{$MOD.LBL_PASSWORD_LOCKOUT}
									</h4>
								</th>
							</tr>
							<tr>
								<td>
									<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr>
										        {assign var='lockouttypelogin' value=''}
                                                {assign var='lockouttypetime' value=''}
                                                {assign var='lockoutnone' value=''}
                                            {if ($config.passwordsetting.lockoutexpiration) == '0'}
                                                {assign var='lockoutnone' value='CHECKED'}
                                            {/if}
                                            {if ($config.passwordsetting.lockoutexpiration) == '1'}
                                                {assign var='lockouttypelogin' value='CHECKED'}
                                            {/if}
                                            {if ($config.passwordsetting.lockoutexpiration) == '2'}
                                                {assign var='lockouttypelogin' value='CHECKED'}
                                                {assign var='lockouttypetime' value='CHECKED'}
                                            {/if}
                                            <td  scope="row" width='30%'>
                                                <input type="radio" name="passwordsetting_lockoutexpiration" value='0' {$lockoutnone} onclick="form.passwordsetting_lockoutexpirationtime.value='';form.passwordsetting_lockoutexpirationlogin.value=''; document.getElementById('dione').style.display='none'; document.getElementById('required_lockout_exp_time').checked=false;">
                                               {$MOD.LBL_UW_NONE}

                                            </td>
											<td scope="row" width='30%'>
                                                <input type="radio" id="required_lockout_exp_login" name="passwordsetting_lockoutexpiration" value='1' {$lockouttypelogin} onclick="document.getElementById('dione').style.display=''">
                                                {$MOD.LBL_PASSWORD_LOCKOUT_ATTEMPT1}
                                                <input type='text' maxlength="3" and style="width:2em" name='passwordsetting_lockoutexpirationlogin' value='{$config.passwordsetting.lockoutexpirationlogin}'>
                                            	{$MOD.LBL_PASSWORD_LOCKOUT_ATTEMPT2}
                                            </td>
                                            <td width='40%'>
											</td>
                                        </tr>
                                        <tr>
											<td>
											</td>
										    <td>
    											<div id="dione" style="display:{$LOGGED_OUT_DISPLAY_STATUS};">
        											<table width='100%'>
        											 <td  scope="row" width='25%'>
                                                <input type="checkbox" id="required_lockout_exp_time" name="passwordsetting_lockoutexpiration"value='2' {$lockouttypetime}>
                                                {$MOD.LBL_PASSWORD_LOGIN_DELAY}: 
                                                    </td>
        											<td    width='25%'>
        												{assign var='lminutes' value=''}
        												{assign var='lhours' value=''}
        												{assign var='ldays' value=''}
        											{if ($config.passwordsetting.lockoutexpirationtype ) == '1'}
        												{assign var='lminutes' value='SELECTED'}
        											{/if}
        											{if ($config.passwordsetting.lockoutexpirationtype ) == '60'}
        												{assign var='lhours' value='SELECTED'}
        											{/if}
        											{if ($config.passwordsetting.lockoutexpirationtype ) == '1440'}
        												{assign var='ldays' value='SELECTED'}
        											{/if}
        												<input type='text' maxlength="3" and style="width:2em" name='passwordsetting_lockoutexpirationtime' value="{$config.passwordsetting.lockoutexpirationtime}">
        												<SELECT NAME="passwordsetting_lockoutexpirationtype">
        													<OPTION VALUE='1' {$lminutes}>Minutes
        													<OPTION VALUE='60' {$lhours}>Hours
        													<OPTION VALUE='1440' {$ldays}>Days
        												</SELECT>
        											</td>
        											</table>
    											</div>
											</td>
								<td>
								</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<table width="100%" border="0" cellspacing="0" cellpadding="0" class="edit view">
							{if !($VALID_PUBLIC_KEY)}
								<tr><td  colspan="2"><span class='error'>{$MOD.ERR_PUBLIC_CAPTCHA_KEY}</span></td></tr>
							{/if}
							<tr><th align="left" scope="row" colspan="2"><h4>{$MOD.CAPTCHA}</h4></th>
							</tr>
								
							<tr>
							{if !empty($settings.captcha_on) || !($VALID_PUBLIC_KEY)}
								{assign var='captcha_checked' value='CHECKED'}
							{else}
								{assign var='captcha_checked' value=''}
							{/if}
								<td width="25%" scope="row">{$MOD.ENABLE_CAPTCHA}&nbsp{sugar_help text=$MOD.LBL_CAPTCHA_HELP_TEXT WIDTH=400}</td><td scope="row"><input type='hidden' name='captcha_on' value='0'><input name="captcha_on" value="1" class="checkbox" tabindex='1' type="checkbox"  onclick='toggleDisplay("captcha_config_display")' {$captcha_checked}></td>
							</tr>
						
							<tr>
							<td colspan="2">
								<div id="captcha_config_display" style="display:{$CAPTCHA_CONFIG_DISPLAY}">
									<table width="100%" cellpadding="0" cellspacing="0">
									<tr>
						
										<td width="10%" scope="row">Public Key<span class="required">*</span></td>
										<td width="40%" ><input type="text" name="captcha_public_key" id="captcha_public_key" size="45"  value="{$settings.captcha_public_key}" tabindex='1' ></td>
										<td width="10%" scope="row">Private Key<span class="required">*</span></td>
										<td width="40%" ><input type="text" name="captcha_private_key" size="45"  value="{$settings.captcha_private_key}" tabindex='1' ></td>
									</tr>
									</table>
								</div>
							</td>
							</tr>
						</table>
						{if !empty($settings.system_ldap_enabled)}
								{assign var='system_ldap_enabled_checked' value='CHECKED'}
								{assign var='ldap_display' value='inline'}
							{else}
								{assign var='system_ldap_enabled_checked' value=''}
								{assign var='ldap_display' value='none'}
						{/if}
						<div style="padding-top: 2px;">
							<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" class="button"  type="submit" onclick="addcheck(form);return check_form('ConfigurePasswordSettings');" name="save" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " />
							&nbsp;<input title="{$MOD.LBL_CANCEL_BUTTON_TITLE}"  onclick="document.location.href='index.php?module=Administration&action=index'" class="button"  type="button" name="cancel" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " />
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>
{$JAVASCRIPT}
{if !($VALID_PUBLIC_KEY)}
<script>
document.getElementById('captcha_public_key').focus();
</script>
{/if}
{literal}
<script>
function addcheck(form){{/literal}
	addForm('ConfigurePasswordSettings');
	removeFromValidate('ConfigurePasswordSettings','passwordsetting_minpwdlength');
	addToValidateLessThan('ConfigurePasswordSettings', 'passwordsetting_minpwdlength', 'int', false,"{$MOD.LBL_PASSWORD_MINIMUM_LENGTH}",document.getElementById('passwordsetting_maxpwdlength').value);
	addToValidate('ConfigurePasswordSettings', 'passwordsetting_maxpwdlength', 'int', false,"{$MOD.LBL_PASSWORD_MAXIMUM_LENGTH}" );
	addToValidate('ConfigurePasswordSettings', 'passwordsetting_linkexpirationtime', 'int', form.required_link_exp_time.checked,"{$MOD.ERR_PASSWORD_LINK_EXPIRE_TIME} ");
	addToValidate('ConfigurePasswordSettings', 'passwordsetting_userexpirationtime', 'int', form.required_pwd_exp_time.checked,"{$MOD.ERR_PASSWORD_EXPIRE_TIME}" );
	addToValidate('ConfigurePasswordSettings', 'passwordsetting_userexpirationlogin', 'int', form.required_pwd_exp_login.checked,"{$MOD.ERR_PASSWORD_EXPIRE_LOGIN}" );
	addToValidate('ConfigurePasswordSettings', 'passwordsetting_lockoutexpirationlogin', 'int', form.required_lockout_exp_login.checked,"{$MOD.ERR_PASSWORD_LOCKOUT_LOGIN}" );
	addToValidate('ConfigurePasswordSettings', 'passwordsetting_lockoutexpirationtime', 'int', form.required_lockout_exp_time.checked,"{$MOD.ERR_PASSWORD_LOCKOUT_TIME}" );
	if (document.getElementById('customregex').value!='')
		addToValidate('ConfigurePasswordSettings', 'passwordsetting_regexcomment','alpha','true',"{$MOD.ERR_EMPTY_REGEX_DESCRIPTION}");
{literal}	}


function open_email_template_form(fieldToSet) {
	fieldToSetValue = fieldToSet;
	URL="index.php?module=EmailTemplates&action=EditView&inboundEmail=true&show_js=1";
	windowName = 'email_template';
	windowFeatures = 'width=800' + ',height=600' 	+ ',resizable=1,scrollbars=1';

	win = window.open(URL, windowName, windowFeatures);
	if(window.focus)
	{
		// put the focus on the popup if the browser supports the focus() method
		win.focus();
	}
}
function edit_email_template_form(templateField) {
	fieldToSetValue = templateField;
	var field=document.getElementById(templateField);
	URL="index.php?module=EmailTemplates&action=EditView&inboundEmail=true&show_js=1";
	if (field.options[field.selectedIndex].value != 'undefined') {
		URL+="&record="+field.options[field.selectedIndex].value;
	}
	windowName = 'email_template';
	windowFeatures = 'width=800' + ',height=600' 	+ ',resizable=1,scrollbars=1';

	win = window.open(URL, windowName, windowFeatures);
	if(window.focus)
	{
		// put the focus on the popup if the browser supports the focus() method
		win.focus();
	}
}

function refresh_email_template_list(template_id, template_name) {
	var field=document.getElementById(fieldToSetValue);
	var bfound=0;
	for (var i=0; i < field.options.length; i++) {
			if (field.options[i].value == template_id) {
				if (field.options[i].selected==false) {
					field.options[i].selected=true;
				}
				field.options[i].text = template_name;
				bfound=1;
			}
	}
	//add item to selection list.
	if (bfound == 0) {
		var newElement=document.createElement('option');
		newElement.text=template_name;
		newElement.value=template_id;
		field.options.add(newElement);
		newElement.selected=true;
	}

	//enable the edit button.
	var editButtonName = 'edit_generatepasswordtmpl';
	if (fieldToSetValue == 'generatepasswordtmpl') {
		editButtonName = 'edit_lostpasswordtmpl';
	} // if
	var field1=document.getElementById(editButtonName);
	field1.style.visibility="visible";

	var applyListToTemplateField = 'generatepasswordtmpl';
	if (fieldToSetValue == 'generatepasswordtmpl') {
		applyListToTemplateField = 'lostpasswordtmpl';
	} // if
	var field=document.getElementById(applyListToTemplateField);
	if (bfound == 1) {
		for (var i=0; i < field.options.length; i++) {
			if (field.options[i].value == template_id) {
				field.options[i].text = template_name;
			} // if
		} // for

	} else {
		var newElement=document.createElement('option');
		newElement.text=template_name;
		newElement.value=template_id;
		field.options.add(newElement);
	} // else
	-->
}

function testregex(customregex)
{
try
  {
var string = 'hello';
string.match(customregex.value);
  }
catch(err)
  {
  	alert(SUGAR.language.get("Administration", "ERR_INCORRECT_REGEX"));
  	setTimeout("document.getElementById('customregex').select()",10);
  }
}

</script>

{/literal}
