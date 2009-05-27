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
<table cellpadding="4" cellspacing="0" border="0" width="100%">
	<tr>
		<th colspan="2">
			<div class="sectionTitle">{$app_strings.LBL_EMAIL_ACCOUNTS_TITLE}</div>
		</th>
	</tr>
	<tr>
		<td width="1%" align="left" scope="row" style="white-space: normal">
			<form id="ieSubscribe" name="ieSubscribe">
				<input type="hidden" id="emailUIAction2" name="emailUIAction" />
				<input type="hidden" id="module" name="module" value="Emails">
				<input type="hidden" id="action" name="action" value="EmailUIAjax">
				<input type="hidden" id="to_pdf" name="to_pdf" value="true">

				<div style="text-align: left;">
					{$app_strings.LBL_EMAIL_SETTINGS_SHOW_IN_FOLDERS}:<br />
					<select multiple style="width: 100px;" size="8" name="ieIdShow[]" id="ieAccountListShow" onchange="SUGAR.email2.folders.setFolderSelection();">
						{$ieAccountsFull}
					</select>
				</div>
				<div style="text-align: left;">
				<i>{$app_strings.LBL_EMAIL_MULTISELECT}</i>
				</div>
			</form>
		</td>
		<td NOWRAP>
			{include file="modules/Emails/templates/emailSettingsAccountDetails.tpl"}
		</td>
	</tr>
</table>
