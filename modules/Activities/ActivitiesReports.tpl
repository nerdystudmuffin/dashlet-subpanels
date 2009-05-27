{*
/**
 *
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

<table width="100%" cellpadding="1" cellspacing="1" border="0" >
	<tr>
		<td style="padding-bottom: 2px;" colspan=6>
			<form name="ActivitiesReports" id="ActivitiesReports" method="post" action="index.php">
				<input type="hidden" name="module" value="Activities" />
				<input type="hidden" name="run_report" id="run_report" value="0" />
				<input type="hidden" name="export_report" id="export_report" value="0" />
				<input type="hidden" name="action" id="action" value="ActivitiesReports" />
		</td>
	</tr>
	<tr>
		<td width="10%">{$MOD.LBL_SELECT_MODULE}:<span class="required">*</span></td>
		<td><select id='parent_type' name='parent_type' onChange='changeQS();clearFields(false);'>
			{foreach from=$PARENT_TYPES key="KEY" item="PARENT"}
				{if $PARENT_TYPE == $KEY}
					<option value="{$KEY}" selected>{$PARENT}</option>
				{else}
					<option value="{$KEY}">{$PARENT}</option>
				{/if}

			{/foreach}		
			</select>
		</td>
	</tr>
	<tr>
		<td>{$MOD.LBL_SELECT_RECORD}:<span class="required">*</span></td>
		<td>
		<input id="parent_name" class="sqsEnabled" type="text" autocomplete="off" value="{$object_name}" size="" tabindex="p" name="parent_name"/>
		<input id="parent_id" type="hidden" value="{$object_id}" name="parent_id"/>
		<input id="object_name" type="hidden" value="{$object_name}" name="object_name"/>
		<input type="button" onclick='open_popup(document.ActivitiesReports.parent_type.value, 600, 400, "", true, false, {ldelim}"call_back_function":"set_return","form_name":"ActivitiesReports","field_to_name_array":{ldelim}"id":"parent_id","name":"parent_name"{rdelim}{rdelim}, "single", true);' value="Select" class="button" accesskey="T" title="Select [Alt+T]" tabindex="p" name="btn_parent_name"/>		
		<input type="button" value='{$MOD.LBL_CLEAR}' onclick="clearFields(true);" class="button" accesskey="C" title="Clear [Alt+C]" tabindex="p" name="btn_clr_parent_name"/>
		</td>
	</tr>	
	<tr>
		<td>{$MOD.LBL_FILTER_DATE_RANGE_START}: </td>
		<td><input name="date_start" id="date_start" onblur="parseDate(this, '{$CALENDAR_DATEFORMAT}');" type="input" tabindex='2' size='11' maxlength='10' value='{$DATE_START}' /> 
		</td>
	</tr>
	<tr>
		<td>{$MOD.LBL_FILTER_DATE_RANGE_FINISH}: </td>
		<td><input name="date_finish" id="date_finish" onblur="parseDate(this, '{$CALENDAR_DATEFORMAT}');" type="input" tabindex='2' size='11' maxlength='10' value='{$DATE_FINISH}' /> </td>
	</tr>
	<tr>
		<td colspan=2><br/><input class="button" type="button" name="button" value="{$MOD.LBL_RUN_REPORT_BUTTON_LABEL}" onclick="submitForm('run');"  />
		&nbsp;&nbsp;<input class="button" type="button" name="button" value="{$MOD.LBL_EXPORT}" onclick="submitForm('export');"  />
		&nbsp;&nbsp;<input class="button" type="button" name="button" value="{$MOD.LBL_CLEAR}" onclick="clearFields(false);"  /></td>
		

	</tr>

</form>
</table>
<br/>
<h2></h2>
{if $count != 0}
<table id="acitvitiesTable" cellspacing="1" class="other view">
	<tr>
		<th width="3%" nowrap><div align='left'>&nbsp;&nbsp;{$MOD.LBL_TYPE}</div></th>
		<th width="15%" nowrap><div align='left'>&nbsp;&nbsp;{$MOD.LBL_SUBJECT}</div></th>
		<th width="5%" nowrap><div align='left'>&nbsp;&nbsp;{$MOD.LBL_DATE}</div></th>
		<th width="5%" nowrap><div align='left'>&nbsp;&nbsp;{$MOD.LBL_STATUS}</div></th>

	</tr>	
		
	{foreach from=$Activities item="activity"}
	<tr>
		<td>{$activity->type}</td>
		<td><a href='index.php?module={$activity->type}s&action=DetailView&record={$activity->id}'>{$activity->name}</a></td>
		<td>{$activity->date_start}</td>
		<td>{$activity->status}</td>
	</tr>
	{/foreach}
	
</table>
<br/>
{else}
{/if}
<script type="text/javascript">
Calendar.setup ({literal}{{/literal}
	inputField : "date_start", ifFormat : '{$CALENDAR_DATEFORMAT}', showsTime : false, button : "date_start", singleClick : true, step : 1{literal}}{/literal});
Calendar.setup ({literal}{{/literal}
	inputField : "date_finish", ifFormat : '{$CALENDAR_DATEFORMAT}', showsTime : false, button : "date_finish", singleClick : true, step : 1{literal}}{/literal});
</script>
{$quicksearch_js}
<script type="text/javascript">
function submitForm(type) {ldelim}
	//clear_all_errors();
	if (trim(document.getElementById('parent_id').value) == '') {ldelim}
		//add_error_style('ActivitiesReports', 'parent_id', requiredTxt);
		alert(requiredTxt);
		return;
	{rdelim}
	
	if (type == 'export') {ldelim}
		document.ActivitiesReports.object_name.value=document.getElementById('parent_name').value;
		document.ActivitiesReports.export_report.value='1';
		document.getElementById('ActivitiesReports').submit();
		
	{rdelim}
	else {ldelim}
		document.ActivitiesReports.object_name.value=document.getElementById('parent_name').value;
		document.ActivitiesReports.export_report.value='0';
		document.ActivitiesReports.run_report.value='1';
		document.getElementById('ActivitiesReports').submit();
	{rdelim}
{rdelim}

function clearFields(skipDate) {ldelim}
	document.getElementById('object_name').value = '';
	document.getElementById('parent_name').value = ''; 
	document.getElementById('parent_id').value = ''; 
	if (!skipDate) {ldelim}
		document.getElementById('date_start').value = ''; 
		document.getElementById('date_finish').value = '';
	{rdelim} 

{rdelim}

function changeQS() {ldelim}
	new_module = document.ActivitiesReports.parent_type.value;
	sqs_objects['parent_name']['disable'] = false;
	document.getElementById('parent_name').readOnly = false;
	sqs_objects['parent_name']['modules'] = new Array(new_module);
    enableQS(false);
{rdelim}

function set_return(popup_reply_data) {ldelim}
	var form_name = popup_reply_data.form_name;
	var name_to_value_array = popup_reply_data.name_to_value_array;
 	document.getElementById('parent_id').value = name_to_value_array['parent_id'];
 	if (name_to_value_array['name'] == 'undefined')
	 	document.getElementById('parent_name').value = name_to_value_array['parent_id'];
	else
	 	document.getElementById('parent_name').value = name_to_value_array['parent_name'];
{rdelim}

</script>

