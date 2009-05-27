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
<form name="advancedSearchForm" id="advancedSearchForm">
<table cellpadding="4" cellspacing="0" border="0">
	<tr>
		<th NOWRAP>
			<b>{$app_strings.LBL_EMAIL_SEARCH__FROM_ACCOUNTS}:</b> <select name="accountListSearch" id="accountListSearch" onchange="SUGAR.email2.search.accountListSearchChange(this)"></select>
		</th>
	</tr>
	<tr>
		<td>&nbsp;
		</td>
	</tr>
	
	<tr>
		<td NOWRAP>
			{$app_strings.LBL_EMAIL_SUBJECT}:<br>
			<input type="text" class="input" name="subject" id="searchSubject" size="20">
		</td>
	</tr>
	<tr>
		<td id="searchBodyDiv" style="display:''" NOWRAP>
			{$app_strings.LBL_EMAIL_SEARCH_FULL_TEXT}:<br>
			<input type="text" class="input" name="body" id="searchBody" size="20">
		</td>
	</tr>
	<tr>
		<td NOWRAP>
			{$app_strings.LBL_EMAIL_FROM}:<br>
			<input type="text" class="input" name="from" id="searchFrom" size="20">
		</td>
	</tr>
	<tr>
		<td NOWRAP>
			{$app_strings.LBL_EMAIL_TO}:<br>
			<input type="text" class="input" name="searchTo" id="searchTo" size="20">
		</td>
	</tr>

	<tr>
		<td NOWRAP>
			{$app_strings.LBL_EMAIL_SEARCH_DATE_FROM}:&nbsp;<i>({$dateFormatExample})</i><br>
			<input name='dateFrom' id='searchDateFrom' onblur="parseDate(this, '{$dateFormat}');" maxlength='10' size='11' value="" type="text">&nbsp;
			<img src="{sugar_getimagepath file='jscalendar.gif'}" alt="{$app_strings.LBL_ENTER_DATE}" id="jscal_trigger_from" align="absmiddle">
		</td>
	</tr>

	<tr>
		<td NOWRAP>
			{$app_strings.LBL_EMAIL_SEARCH_DATE_UNTIL}:&nbsp;<i>({$dateFormatExample})</i><br>
			<input name='dateTo' id='searchDateTo' onblur="parseDate(this, '{$dateFormat}');" maxlength='10' size='11' value="" type="text">&nbsp;
			<img src="{sugar_getimagepath file='jscalendar.gif'}" alt="{$app_strings.LBL_ENTER_DATE}" id="jscal_trigger_to" align="absmiddle">
		</td>
	</tr>
	<tr>
		<td NOWRAP>
			<br />&nbsp;<br />
		
			<input type="button" id="advancedSearchButton" class="button" onclick="SUGAR.email2.search.searchAdvanced()" value="   {$app_strings.LBL_SEARCH_BUTTON_LABEL}   ">&nbsp;
			<input type="button" class="button" onclick="SUGAR.email2.search.searchClearAdvanced()" value="   {$app_strings.LBL_CLEAR_BUTTON_LABEL}   ">
		</td>
	</tr>
</table>
</form>
