{*
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
*}
<div id="outboundServers" class="">
	<form id="outboundEmailForm">
		<input type="hidden" id="mail_id" name="mail_id">
		<input type="hidden" id="mail_sendtype" name="mail_sendtype" value="SMTP">
	
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="edit view">
			<tr>
				<td scope="row" colspan="2" NOWRAP>
					<input type="button" class="button" value="   {$app_strings.LBL_EMAIL_ACCOUNTS_GMAIL_DEFAULTS}   " onclick="javascript:SUGAR.email2.accounts.fillGmailDefaults();">&nbsp;
				</td>
			</tr>

			<tr>
				<td scope="row" width="15%" NOWRAP>
					{$app_strings.LBL_EMAIL_ACCOUNTS_NAME}&nbsp; 
				</td>
				<td  width="35%">
					<input type="text" class="input" id="mail_name" name="mail_name" size="25" maxlength="64">
				</td>
			</tr>
		
			<tr>
				<td scope="row">
					{$app_strings.LBL_EMAIL_ACCOUNTS_SMTPSERVER} 
					<span class="required">
						{$app_strings.LBL_REQUIRED_SYMBOL}
					</span>
				</td>
				<td >
					<input type="text" id="mail_smtpserver" name="mail_smtpserver" size="25" maxlength="64" value="">
				</td>
			</tr>
			
			<tr>
				<td scope="row">
					{$app_strings.LBL_EMAIL_ACCOUNTS_SMTPPORT} 
					<span class="required">
						{$app_strings.LBL_REQUIRED_SYMBOL}
					</span>
				</td>
				<td >
					<input type="text" id="mail_smtpport" name="mail_smtpport" size="5" maxlength="5" value="25">
				</td>
			</tr>

			<tr>
				<td scope="row">
					{$app_strings.LBL_EMAIL_SMTP_SSL_OR_TLS} 
				</td>
				<td >
					<select id="mail_smtpssl" name="mail_smtpssl">{$MAIL_SSL_OPTIONS}</select>
				</td>
			</tr>

			<tr>
				<td scope="row">
					{$app_strings.LBL_EMAIL_ACCOUNTS_SMTPAUTH_REQ} 
				</td>
				<td >
					<input id='mail_smtpauth_req' name='mail_smtpauth_req' type="checkbox" class="checkbox" value="1">
				</td>
			</tr>
			<tr>
				<td scope="row">
					{$app_strings.LBL_EMAIL_ACCOUNTS_SMTPUSER} 
				</td>
				<td ">
					<input type="text" id="mail_smtpuser" name="mail_smtpuser" size="25" maxlength="64">
				</td>
			</tr>
			<tr>
				<td scope="row">
					{$app_strings.LBL_EMAIL_ACCOUNTS_SMTPPASS} 
				</td>
				<td >
					<input type="password" id="mail_smtppass" name="mail_smtppass" size="25" maxlength="64">
				</td>
			</tr>
			<tr>
				<td scope="row" colspan="2">
					<input type="button" class="button" value="   {$app_strings.LBL_CANCEL_BUTTON_LABEL}   " onclick="javascript:SUGAR.email2.accounts.outboundDialog.hide();">&nbsp;
					<input type="button" class="button" value="   {$app_strings.LBL_SAVE_BUTTON_LABEL}   " onclick="javascript:SUGAR.email2.accounts.saveOutboundSettings();">&nbsp;
				</td>
			</tr>



		</table>
	</form>
</div>
