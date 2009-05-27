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
<form name="emailSettingsGeneral" id="formEmailSettingsGeneral">
<table cellpadding="4" cellspacing="0" border="0" width="100%">
	<tr>
		<th colspan="4">
			<div class="sectionTitle">{$app_strings.LBL_EMAIL_SETTINGS_TITLE_PREFERENCES}</div>
		</th>
	</tr>
	<tr>
		<td width="20%" NOWRAP scope="row">
			{$app_strings.LBL_EMAIL_SETTINGS_CHECK_INTERVAL}:
		</td>
		<td width="30%" NOWRAP>
			{html_options options=$emailCheckInterval.options selected=$emailCheckInterval.selected name='emailCheckInterval' id='emailCheckInterval'}
		</td>
		<td NOWRAP scope="row">
			&nbsp;
		</td>
		<td NOWRAP>
			&nbsp;
		</td>
	</tr>
	<tr>
		<td width="20%" NOWRAP scope="row">
			{$app_strings.LBL_EMAIL_SETTINGS_SEND_EMAIL_AS}:
		</td>
		<td width="30%" NOWRAP>
			<input class="checkbox" type="checkbox" id="sendPlainText" name="sendPlainText" value="1" {$sendPlainTextChecked} />
		</td>
		<td NOWRAP scope="row">
			{$app_strings.LBL_EMAIL_SETTINGS_SAVE_OUTBOUND}:
		</td>
		<td NOWRAP>
			<input class='checkbox' type="checkbox" id="alwaysSaveOutbound" name="alwaysSaveOutbound" value="1" {$alwaysSaveOutboundChecked} />
		</td>
	</tr>
	<tr>
		<td NOWRAP scope="row">
        	{$app_strings.LBL_EMAIL_CHARSET}:
        </td>
		<td NOWRAP>
        	{html_options options=$charset.options selected=$charset.selected name='default_charset' id='default_charset'}
        </td>
		<td NOWRAP scope="row">
        	{$app_strings.LBL_EMAIL_SIGNATURES}:
        </td>
		<td NOWRAP>
        	{$signaturesSettings} {$signatureButtons} 
        	<input type="hidden" name="signatureDefault" id="signatureDefault" value="{$signatureDefaultId}">
        </td>
	</tr>
	<tr>
		<td NOWRAP scope="row">
        	{$mod_strings.LBL_SIGNATURE_PREPEND}:
        </td>
		<td NOWRAP>
        	<input type="checkbox" name="signature_prepend" {$signaturePrepend}>
        </td>
		<td NOWRAP scope="row">
        	{if isset($pro)}
				{$app_strings.LBL_EMAIL_TEAMS}:
        	{else}
        		&nbsp;
        	{/if}
        </td>
		<td NOWRAP>
        	{if isset($pro)}
				{html_options options=$teams.options selected=$teams.selected name='assign_to_team' id='assign_to_team'}
        	{else}
        		&nbsp;
        	{/if}
        </td>        
	</tr>


	<tr>
		<th colspan="4">
			<div class="sectionTitle">{$app_strings.LBL_EMAIL_SETTINGS_TITLE_LAYOUT}</div>
			<i>{$app_strings.LBL_EMAIL_SETTINGS_REQUIRE_REFRESH}</i>
		</th>
	</tr>
	<tr>
		<td NOWRAP scope="row">
			{$app_strings.LBL_EMAIL_SETTINGS_LAYOUT}:
		</td>
		<td NOWRAP>
			<input type="radio" name="layoutStyle" id="2rows" value="2rows" {$rowsChecked}>  <img src="modules/Emails/images/rowsView.gif" align="absmiddle" height="10"> {$app_strings.LBL_EMAIL_SETTINGS_2_ROWS}<br>
			<input type="radio" name="layoutStyle" id="2cols" value="2cols" {$colsChecked}>  <img src="modules/Emails/images/colsView.gif" align="absmiddle" height="10"> {$app_strings.LBL_EMAIL_SETTINGS_3_COLS}<br>
		</td>
		<td NOWRAP scope="row">
			{$app_strings.LBL_EMAIL_SETTINGS_TAB_POS}:
		</td>
		<td NOWRAP>
			<input type="checkbox" name="tabPosition" id="tabPosition" value="top" {$tabPositionChecked}>
		</td>
	</tr>
	<tr>
		<td NOWRAP scope="row">
			{$app_strings.LBL_EMAIL_SETTINGS_SHOW_NUM_IN_LIST}:
		</td>
		<td NOWRAP>
			<select name="showNumInList" id="showNumInList">
			{$showNumInList}
			</select>
		</td>
		<td NOWRAP scope="row">
			{$app_strings.LBL_EMAIL_SETTINGS_FULL_SCREEN}:
		</td>
		<td NOWRAP>
			<input type="checkbox" class="checkbox" id="fullScreen" name="fullScreen" value="1" {$fullScreenChecked} onchange="SUGAR.email2.settings.toggleFullScreen(this);">
		</td>
	</tr>



	<tr>
		<td NOWRAP>
			&nbsp;
		</td>
		<td NOWRAP>
			<input type="button" class="button" value="   {$app_strings.LBL_EMAIL_SAVE}   " onclick="javascript:SUGAR.email2.settings.saveOptionsGeneral(true);">
		</td>
		<td NOWRAP scope="row">
			&nbsp;
		</td>
		<td NOWRAP>
			&nbsp;
		</td>
	</tr>
</table>

</form>
