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
		{if $is_admin == 1}
		<th colspan="3">
		{else}
		<th colspan="2">
		{/if}
			<div class="sectionTitle">{$app_strings.LBL_EMAIL_FOLDERS_TITLE}</div>
		</th>
	</tr>

	<tr>
		<td NOWRAP style="padding: 8px;" valign="top">
			<div>
				<b>{$app_strings.LBL_EMAIL_SETTINGS_USER_FOLDERS}:</b>
			</div>
			<br />

			<div>
				<select multiple size="8" name="userFolders[]" id="userFolders" onchange="SUGAR.email2.folders.updateSubscriptions();"></select>
			</div>
			<br />
			<i>{$app_strings.LBL_EMAIL_MULTISELECT}</i>
		</td>
		<td NOWRAP style="padding: 8px;" valign="top">
			<div>
				<b>{$app_strings.LBL_EMAIL_SETTINGS_GROUP_FOLDERS}:</b>
			</div>
			<br />

			<div>
				<select multiple size="8" name="groupFolders[]" id="groupFolders" onchange="SUGAR.email2.folders.updateSubscriptions();"></select>
			</div>
			<br />
			<i>{$app_strings.LBL_EMAIL_MULTISELECT}</i>
		</td>
		{if $is_admin == 1}
		<td NOWRAP style="padding: 8px;" valign="top">
			<div>			
				<b>{$app_strings.LBL_EMAIL_SETTINGS_GROUP_FOLDERS_EDIT}:</b>
				<select name="editGroupFolderList" id="editGroupFolderList" onchange="SUGAR.email2.folders.editGroupFolder(this.value);">
					
				</select>
			</div>
			<br />

			<div>
				{$app_strings.LBL_EMAIL_FOLDERS_NEW_FOLDER}:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="text" name="groupFolderAddName" id="groupFolderAddName">
			</div>
			<br />

			<div>
				{$app_strings.LBL_EMAIL_FOLDERS_ADD_THIS_TO}:
				<select name="groupFoldersAdd" id="groupFoldersAdd"></select>
			</div>
			<br />







			<input type="button" style="display:''" id="addNewFolders" name="addNewFolders" class="button" value="   {$app_strings.LBL_EMAIL_FOLDERS_ADD_NEW_FOLDER}   " onclick="SUGAR.email2.folders.addNewGroupFolder();">
			<input type="button" style="display:none" id="saveGroupFolder" name="saveGroupFolder" class="button" value="   {$app_strings.LBL_SAVE_BUTTON_LABEL}   " onclick="SUGAR.email2.folders.saveGroupFolder();">
			<input type="button" style="display:none" id="cancelEditGroupFolder" name="cancelEditGroupFolder" class="button" value="   {$app_strings.LBL_CANCEL_BUTTON_LABEL}   " onclick="SUGAR.email2.folders.editGroupFolder('');">
		</td>
		{/if}
	</tr>
</table>
