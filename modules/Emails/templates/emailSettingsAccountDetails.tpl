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
{$rollover}
{overlib_includes}
<table border="0" cellspacing="0" cellpadding="0">
	<tr>
	   <td colspan="2">
	   <form id="ieSelect" name="ieSelect">
	   <table><tr><td>
       <input type="hidden" id="emailUIAction2" name="emailUIAction" />
				<input type="hidden" id="module" name="module" value="Emails">
				<input type="hidden" id="action" name="action" value="EmailUIAjax">
				<input type="hidden" id="to_pdf" name="to_pdf" value="true">
		<td NOWRAP scope="row" >
            {$app_strings.LBL_EMAIL_SETTINGS_EDIT_ACCOUNT}:&nbsp;
		</td>
		<td> <select name="ieId" id="ieAccountList" onchange="SUGAR.email2.accounts.getIeAccount(this.value);">
                {$ieAccounts}
             </select> 
        </td></tr></table>
        </form>
	</tr>
    <tr>
		<td valign="top" width="15%" NOWRAP>
			&nbsp;
		</td>
		<td valign="top"  width="35%">
			&nbsp;
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<form id="ieAccount" name="ieAccount">
				<input type="hidden" id="ie_id" name="ie_id">
				<input type="hidden" id="ie_status" name="ie_status" value="Active">
				<input type="hidden" id="ie_team" name="ie_team" value="{$ie_team}">
				<input type="hidden" id="group_id" name="group_id">
				<input type="hidden" id="group_id" name="mark_read" value="1">
				<input type="hidden" name="searchField" value="">
			
			<table border="0" cellspacing="0" cellpadding="0" class="edit view">
			    <tr>
					<td valign="top" scope="row" width="15%" NOWRAP>
						{$app_strings.LBL_EMAIL_SETTINGS_NAME}: <span class="required">{$app_strings.LBL_REQUIRED_SYMBOL}</span>
					</td>
					<td valign="top"  width="35%">
						<input id='ie_name' name='ie_name' type="text" size="30">
					</td>
				</tr>
			
			
			    <tr>
					<td valign="top" scope="row" width="15%" NOWRAP>
						{$app_strings.LBL_EMAIL_SETTINGS_FROM_NAME}:
					</td>
					<td valign="top"  width="35%">
						<input id='from_name' name='from_name' type="text" size="30">
					</td>
				</tr>
			    <tr>
					<td valign="top" scope="row" width="15%" NOWRAP>
						{$app_strings.LBL_EMAIL_SETTINGS_FROM_ADDR}: <span class="required">{$app_strings.LBL_REQUIRED_SYMBOL}</span>
					</td>
					<td valign="top"  width="35%">
						<input id='from_addr' name='from_addr' type="text" size="30">
					</td>
				</tr>
			
			    <tr>
					<td valign="top" scope="row">
						{$ie_mod_strings.LBL_LOGIN}: <span class="required">{$app_strings.LBL_REQUIRED_SYMBOL}</span>&nbsp;
					</td>
					<td valign="top" >
						<input id='email_user' name='email_user' size='30' maxlength='100' type="text" onclick="SUGAR.email2.accounts.ieAccountError(SUGAR.email2.accounts.normalStyle);">
					</td>
			    </tr>
			
			    <tr>
					<td valign="top" scope="row">
						{$ie_mod_strings.LBL_PASSWORD}: <span class="required">{$app_strings.LBL_REQUIRED_SYMBOL}</span>&nbsp;
					</td>
					<td valign="top" >
						<input id='email_password' name='email_password' size='30' maxlength='100' type="password" onclick="SUGAR.email2.accounts.ieAccountError(SUGAR.email2.accounts.normalStyle);">
					</td>
			    </tr>
			    
			     <tr>
                    <td valign="top" scope="row" NOWRAP>
                        {$ie_mod_strings.LBL_SERVER_URL}: <span class="required">{$app_strings.LBL_REQUIRED_SYMBOL}</span>&nbsp;
                    </td>
                    <td valign="top" >
                        <input id='server_url' name='server_url' size='30' maxlength='100' type="text" onclick="SUGAR.email2.accounts.ieAccountError(SUGAR.email2.accounts.normalStyle);">
                    </td>
                </tr>                
			    <tr>
					<td valign="top" scope="row" NOWRAP>
						{$ie_mod_strings.LBL_SERVER_TYPE}: <span class="required">{$app_strings.LBL_REQUIRED_SYMBOL}</span>&nbsp;
					</td>
					<td valign="top" >
						<select name='protocol' id="protocol" onchange="SUGAR.email2.accounts.setPortDefault(); SUGAR.email2.accounts.ieAccountError(SUGAR.email2.accounts.normalStyle);">{$PROTOCOL}</select>
					</td>
				</tr>
			     <tr id="mailboxdiv" style="dispay:'none';">
                    <td valign="top" scope="row" NOWRAP>
                        {$ie_mod_strings.LBL_MAILBOX}: <span class="required">{$app_strings.LBL_REQUIRED_SYMBOL}</span>&nbsp;
                    </td>
                    <td valign="top" >
                        <input id='mailbox' value="" name='mailbox' size='30' maxlength='500' type="text" onclick="SUGAR.email2.accounts.ieAccountError(SUGAR.email2.accounts.normalStyle);" />
					<input type="button" id="subscribeFolderButton" name="subscribeFolderButton" class="button" onclick='this.form.searchField.value="";SUGAR.email2.accounts.getFoldersListForInboundAccountForEmail2();' value="{$app_strings.LBL_EMAIL_SELECT}" />
                    </td>
                </tr>			
			     <tr id="trashFolderdiv" style="dispay:'none';">
                    <td valign="top" scope="row" NOWRAP>
                        {$ie_mod_strings.LBL_TRASH_FOLDER}: <span class="required">{$app_strings.LBL_REQUIRED_SYMBOL}</span>&nbsp;
                    </td>
                    <td valign="top" >
                        <input id='trashFolder' value="" name='trashFolder' size='30' maxlength='100' type="text" onclick="SUGAR.email2.accounts.ieAccountError(SUGAR.email2.accounts.normalStyle);" />
					<input type="button" id="trashFolderButton" name="trashFolderButton" class="button" onclick='this.form.searchField.value="trash";SUGAR.email2.accounts.getFoldersListForInboundAccountForEmail2();' value="{$app_strings.LBL_EMAIL_SELECT}" />
                    </td>
                </tr>			
			     <tr id="sentFolderdiv" style="dispay:'none';">
                    <td valign="top" scope="row" NOWRAP>
                        {$ie_mod_strings.LBL_SENT_FOLDER}: &nbsp;
                    </td>
                    <td valign="top" >
                        <input id='sentFolder' value="" name='sentFolder' size='30' maxlength='100' type="text" onclick="SUGAR.email2.accounts.ieAccountError(SUGAR.email2.accounts.normalStyle);" />
					<input type="button" id="sentFolderButton" name="sentFolderButton" class="button" onclick='this.form.searchField.value="sent";SUGAR.email2.accounts.getFoldersListForInboundAccountForEmail2();' value="{$app_strings.LBL_EMAIL_SELECT}" />
                    </td>
                </tr>			
			    <tr>
					<td valign="top" scope="row" NOWRAP>
						{$ie_mod_strings.LBL_PORT}: <span class="required">{$app_strings.LBL_REQUIRED_SYMBOL}</span>&nbsp;
					</td>
					<td valign="top" >
						<input name='port' id='port' size='10' onclick="SUGAR.email2.accounts.ieAccountError(SUGAR.email2.accounts.normalStyle);">
					</td>
				</tr>
				<tr>
					<td valign="top" scope="row" NOWRAP>
						{$ie_mod_strings.LBL_SSL}:&nbsp;
                        <div id="rollover">
                            <a href="#" class="rollover"><img border="0" src="{sugar_getimagepath file='helpInline.gif'}"><span>{$ie_mod_strings.LBL_SSL_DESC}</span></a>
                        </div>
					</td>
					<td valign="top"  width="15%">
					   <div class="maybe">
						   <input name='ssl' id='ssl' {$CERT} value='1' type='checkbox' {$SSL} onClick="SUGAR.email2.accounts.setPortDefault();">
					   </div>
					</td>
				</tr>
				
				<tr>
					<td valign="top" scope="row" NOWRAP>
						{$ie_mod_strings.LBL_OUTBOUND_SERVER}: <span class="required">{$app_strings.LBL_REQUIRED_SYMBOL}</span>&nbsp;
					</td>
					<td valign="top"  NOWRAP>
					   <div><table><tr><td>
						<select name='outbound_email' id='outbound_email' onchange="SUGAR.email2.accounts.handleOutboundSelectChange();"></select>&nbsp;
						<input id="outbound_email_edit_button" style="display:none;" type="button" class="button" 
						    onclick="javascript:SUGAR.email2.accounts.editOutbound();" value="   {$app_strings.LBL_EMAIL_ACCOUNTS_EDIT}   ">
						<input id="outbound_email_delete_button" style="display:none;" type="button" class="button" 
						    onclick="javascript:SUGAR.email2.accounts.deleteOutbound();" value="   {$app_strings.LBL_EMAIL_DELETE}   ">
					    </td><td style="padding-bottom: 2px">
					    <input id="outbound_email_add_button" title="{$app_strings.LBL_EMAIL_FOLDERS_ADD}" type='button' 
					        class="button" onClick='SUGAR.email2.accounts.showAddSmtp();'
                            name="button" value="  {$app_strings.LBL_EMAIL_FOLDERS_ADD}   ">
                        </td></tr></table>
                       </div>     
					</td>
				</tr>

				<tr>
					<td colspan="2">
						<hr>
					</td>
				</tr>
			
				<tr>
					<td NOWRAP colspan="2" style="padding-bottom: 2px">
					   <input title="{$app_strings.LBL_EMAIL_SETTINGS_ADD_ACCOUNT}"
                        type='button' 
                        accessKey="n" 
                        class="button"
                        onClick='SUGAR.email2.accounts.addNewAccount();'
                        name="button" id="addButton" value="  {$app_strings.LBL_EMAIL_SETTINGS_ADD_ACCOUNT}  ">
                        &nbsp;
						<input title="{$ie_mod_strings.LBL_TEST_BUTTON_TITLE}"
							type='button' 
							accessKey="{$ie_mod_strings.LBL_TEST_BUTTON_KEY}" 
							class="button"
							onClick='SUGAR.email2.accounts.testSettings();'
							name="button" id="testButton" value="  {$ie_mod_strings.LBL_TEST_SETTINGS}  ">
						&nbsp;
						<input title="{$ie_mod_strings.LBL_EMAIL_SAVE}"
							type='button' 
							accessKey="s" 
							class="button"
							onClick='SUGAR.email2.accounts.saveIeAccount();'
							name="button" id="saveButton" value="  {$app_strings.LBL_EMAIL_SAVE}  ">
						&nbsp;
						<input title="{$ie_mod_strings.LBL_EMAIL_DELETE}"
							type='button' 
							accessKey="x" 
							class="button"
							onClick='SUGAR.email2.accounts.deleteIeAccount();'
							name="button" id="deleteButton" value="  {$app_strings.LBL_EMAIL_DELETE}  "
							style="display:none;">
					</td>
				</tr>
			</table>
			</form>
			<form action="index.php" method="post" name="testSettingsView" id="testSettingsView">
				<input type="hidden" name="module" value="" />
				<input type="hidden" name="action" />
				<input type="hidden" name="target1"/>
				<input type="hidden" name="server_url" value="" />
				<input type="hidden" name="email_user" value="" />
				<input type="hidden" name="protocol" value="" />
				<input type="hidden" name="port" value="" />
				<input type="hidden" name="email_password" value="" />
				<input type="hidden" name="mailbox" value="" />
				<input type="hidden" name="ssl" value="" />
				<input type="hidden" name="personal" value="" />
				<input type="hidden" name="to_pdf" value="1">
				<input type="hidden" name="searchField" value="">
			</form>
			
		</td>
	</tr>
</table>
