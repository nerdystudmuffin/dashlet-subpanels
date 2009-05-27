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
<!--<script type='text/javascript' src='include/javascript/overlibmws.js'></script>-->
<script type='text/javascript'>
var ERR_RULES_NOT_MET = '{$MOD.ERR_RULES_NOT_MET}';
var ERR_ENTER_OLD_PASSWORD = '{$MOD.ERR_ENTER_OLD_PASSWORD}';
var ERR_ENTER_NEW_PASSWORD = '{$MOD.ERR_ENTER_NEW_PASSWORD}';
var ERR_ENTER_CONFIRMATION_PASSWORD = '{$MOD.ERR_ENTER_CONFIRMATION_PASSWORD}';
var ERR_REENTER_PASSWORDS = '{$MOD.ERR_REENTER_PASSWORDS}';
</script>
<link rel='stylesheet' type="text/css" href='modules/Users/PasswordRequirementBox.css'>
<script type='text/javascript' src='modules/Users/PasswordRequirementBox.js'></script>
<form name="ConfigurePasswordSettings" method="POST" action="index.php" >
<input type='hidden' name='action' value='ChangePassword'/>
<input type='hidden' name='module' value='Users'/>
<input type="hidden" name="password_change" id="password_change" value="false">
<input type='hidden' name='is_admin' value="{$IS_ADMIN}"/>
<input type="hidden" name="return_module" value="Home">
<input type="hidden" name="page" value="Change">
<input type="hidden" name="return_id" value="{$ID}">
<input type="hidden" name="return_action" value="index">
<input type="hidden" name="record" value="{$ID}">
<input type="hidden" name="user_name" value="{$USER_NAME}">
<input type='hidden' name='saveConfig' value='0'/>
<input type='hidden' name='' value='0'/>
<div id="generate_password">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="edit view">
    <tr>
        <td width='40%'>
            <form>
                <table width='100%' cellspacing='0' cellpadding='0' border='0' >
                    <tr>
                        <th align="left" scope="row" colspan="4">
                            <h4>{$MOD.LBL_CHANGE_PASSWORD}</h4><br>
                        		<h4><span class="error">{$EXPIRATION_TYPE}</span></h4><br>
                    	
                        </th>
                        
                    </tr>
                    <tr>
                        
                        {if !($IS_ADMIN)} 
                        <td width='15%' class='dataLabel'>
                            {$MOD.LBL_OLD_PASSWORD}
                        </td>
                        <td width='85%' class='dataField'>
                            <input name='old_password' id= "old_password"  type='password' tabindex='1' onkeyup="confirm();" />
                        </td>
                        
                    </tr>
                    <tr>
                    
                    {else} 
                        <input name='old_password' id= "old_password" type='hidden'>
                        
                    {/if}
                        
                    </tr>
                    <tr>
                        <td width='30%' class='dataLabel'nowrap>
                            {$MOD.LBL_NEW_PASSWORD}
                        </td>
                        <td width='30%' class='dataField'>
 							<input name='new_password' id= "new_password" type='password' tabindex='1' onkeyup="confirm();newrules('{$PWDSETTINGS.minpwdlength}','{$PWDSETTINGS.maxpwdlength}','{$REGEX}');" />
                        </td>
                        <td width='40%'>
                        </td>
                    </tr>
                    <tr>
                        <td class='dataLabel' >
                            {$MOD.LBL_CONFIRM_PASSWORD}
                        </td>
                        <td class='dataField'>
                            <input name='confirm_new_password' id='confirm_pwd' style ='' type='password' tabindex='1' onkeyup="confirm();"  > <div id="comfirm_pwd_match" class="error" style="display: none;">mis-match</div>
                        </td>
                        <td>
                        <div id="comfirm_pwd_match" class="error" style="display: none;">mismatch</div>
                             {*<span id="ext-gen63" class="x-panel-header-text">
                                Requirements
                                <span id="Filter.1_help" onmouseout="return nd();" onmouseover="return overlib(help(), FGCLASS, 'olFgClass', CGCLASS, 'olCgClass', BGCLASS, 'olBgClass', TEXTFONTCLASS, 'olFontClass', CAPTIONFONTCLASS, 'olCapFontClass', CLOSEFONTCLASS, 'olCloseFontClass' );">
                                    <img src="themes/default/images/help.gif"/>
                                </span>
                            </span>*}
                        </td>
                    </tr>
                    <tr>
                        <td class='dataLabel'></td>
                        <td class='dataField'></td>
                    </td>                    
                </table>

                <table width='15%' cellspacing='0' cellpadding='1' border='0'>
                    <tr>
                        <td width='50%'>
                            <input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey='{$APP.LBL_SAVE_BUTTON_KEY}' class='button' id='save_new_pwd_button' LANGUAGE=javascript onclick='if (set_password(this.form))) window.close(); else return false;' type='submit' name='button' style='display:none;' value='{$APP.LBL_SAVE_BUTTON_LABEL}'>
                        </td>
                        <td width='50%'>
                        </td>
                    </tr>
                </table>
            </form>
        </td>
        <td width='60%'>
                        {sugar_password_requirements_box width='300px' class='x-sqs-list' style='background-color:white; padding:5px !important;'}
        </td>
    </tr>
</table>
<br />
</div>

<div style="padding-top:2px;">
	<input	title="{$APP.LBL_SAVE_BUTTON_TITLE}" class="button" onclick="if (!set_password(form,newrules('{$PWDSETTINGS.minpwdlength}','{$PWDSETTINGS.maxpwdlength}','{$REGEX}'))) return false; this.form.action.value='Save'; {$REASSIGN_JS} {$CHOOSER_SCRIPT}" 
			type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " />
	<input	title="{$APP.LBL_CANCEL_BUTTON_TITLE}" class="button" onclick="this.form.action.value='index'; this.form.module.value='Users'; this.form.record.value='{$ID}'" 
			type="submit" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " />
</div>
</form>
{$JAVASCRIPT}
