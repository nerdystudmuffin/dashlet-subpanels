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

<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-top: 0px none; margin-bottom: 4px" >
<tr valign='top'>
	<td width='37%' align='left' rowspan='4' colspan='2'>
		<input id='displayColumnsDef' type='hidden' name='displayColumns'>
		<input id='hideTabsDef' type='hidden' name='hideTabs'>
		{$columnChooser}
		<br>
	</td>
	<td scope='row' align='left' width='10%'>
		{sugar_translate label='LBL_ORDER_BY_COLUMNS' module='SavedSearch'}

	</td>
	<td width='30%'>
		<select name='orderBy' id='orderBySelect'>
		</select>
	</td>
	<td scope='row' width='10%'>
		{sugar_translate label='LBL_DIRECTION' module='SavedSearch'}
	</td>
	<td width='30%'>
		<input id='sort_order_desc_radio' type='radio' name='sortOrder' value='DESC' {if $selectedSortOrder == 'DESC'}checked{/if}> <span onclick='document.getElementById("sort_order_desc_radio").checked = true' style="cursor: pointer; cursor: hand">{$MOD.LBL_DESCENDING}</span><br>
		<input id='sort_order_asc_radio' type='radio' name='sortOrder' value='ASC' {if $selectedSortOrder == 'ASC'}checked{/if}> <span onclick='document.getElementById("sort_order_asc_radio").checked = true' style="cursor: pointer; cursor: hand">{$MOD.LBL_ASCENDING}</span>
	</td>
	
	</tr>
	{if $SEARCH_MODULE == 'Users'}
<tr>
    <td class='dataLabel' nowrap width='7%'>
        {sugar_translate label='LBL_SAVE_SEARCH_AS' module='SavedSearch'}
    </td>
    <td class='dataField' width='30%'>
        <input type='text' name='saved_search_name'>
        <input type='hidden' name='search_module' value=''>
        <input type='hidden' name='saved_search_action' value=''>
        <input value='{$SAVE}' title='{$MOD.LBL_SAVE_BUTTON_TITLE}' class='button' type='button' name='saved_search_submit' onclick='SUGAR.savedViews.setChooser(); return SUGAR.savedViews.saved_search_action("save");'>
    </td>
    <td nowrap  class='dataLabel' width='*' colspan=2>{sugar_translate label='LBL_MODIFY_CURRENT_SEARCH' module='SavedSearch'}:&nbsp;
        <input class='button' onclick='SUGAR.savedViews.setChooser(); return SUGAR.savedViews.saved_search_action("update")' value='{$UPDATE}' title='{$MOD.LBL_UPDATE_BUTTON_TITLE}' name='ss_update' id='ss_update' type='button' >
        <input class='button' onclick='return SUGAR.savedViews.saved_search_action("delete", "{$MOD.LBL_DELETE_CONFIRM}")' value='{$DELETE}' title='{$MOD.LBL_DELETE_BUTTON_TITLE}' name='ss_delete' id='ss_delete' type='button'>
        <br><span id='curr_search_name'></span>
    </td>
</tr>
    {/if}

</table>
<script>
	SUGAR.savedViews.columnsMeta = {$columnsMeta};
	columnsMeta = {$columnsMeta};
	saved_search_select = "{$SAVED_SEARCH_SELECT}";
	selectedSortOrder = "{$selectedSortOrder|default:'DESC'}";
	selectedOrderBy = "{$selectedOrderBy}";


{literal}
	//this populates the label that shows the name of the current saved view
	//The label is located under the update/delete buttons
	function fillInLabels(){
		//this javascript runs and populates values in savedSearchForm.tpl
		x = document.getElementById('saved_search_select');
		if ((typeof(x) != 'undefined' && x != null) && x.selectedIndex !=0) {
			document.getElementById('curr_search_name').innerHTML = '"'+x.options[x.selectedIndex].text+'"';
			document.getElementById('ss_update').disabled = false;
			document.getElementById('ss_delete').disabled = false;
		}else{
			document.getElementById('ss_update').disabled = true;
			document.getElementById('ss_delete').disabled = true;
			document.getElementById('curr_search_name').innerHTML = '';
		}
	}
	//call scripts that need to get run onload of this form.  This function is called when image
	//to collapse/show subpanels is loaded
	function loadSSL_Scripts(){
		//this will fill in the name of the current module, and enable/disable update/delete buttons
		fillInLabels();
		//this populates the order by dropdown, and activates the chooser widget.
		SUGAR.savedViews.handleForm();
	}

{/literal}
</script>


