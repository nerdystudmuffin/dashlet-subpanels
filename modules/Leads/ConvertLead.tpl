<!--
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
-->

<span class="color">{$ERROR}</span>
<script>
function isChecked(field) {ldelim}
	return eval("document.forms['ConvertLead']."+field+".checked");
 {rdelim}
function checkOpportunity(){ldelim}
		if(!isChecked('newopportunity')){ldelim}
			return true;
		{rdelim}

		
		removeFromValidate('ConvertLead', 'Opportunitiesaccount_name');
		if(validate_form('ConvertLead', 'Opportunities')){ldelim}

			if(this.document.forms['ConvertLead'].selectedAccount.value != ''){ldelim}
				return true;
			{rdelim}
			if(!isChecked('newaccount')){ldelim}
				alert('{$OPPNEEDSACCOUNT}');
				return false;					
			{rdelim}
			return true;
		{rdelim}
		return false;

{rdelim}
</script>
{$DUPLICATEFORMBODY}


<form action='index.php' method='post' name='ConvertLead' onsubmit="return (validate_form('ConvertLead', 'Contacts') 
&& (!isChecked('newaccount') || validate_form('ConvertLead', 'Accounts')) 
{$CHECKOPPORTUNITY} 
&& (!isChecked('newmeeting') || validate_form('ConvertLead', 'Appointments'))
&& (!isChecked('newcontactnote') || validate_form('ConvertLead', 'ContactNotes')) 
&& (!isChecked('newaccountnote') || !isChecked('newaccount') || validate_form('ConvertLead', 'AccountNotes')) 
&& (!isChecked('newoppnote') || !isChecked('newopportunity') || validate_form('ConvertLead', 'OpportunityNotesname')) 
);">

<input type="hidden" name="module" value="Leads">
<input type="hidden" name="action" value="ConvertLead">
<input type="hidden" name="handle" value="Save">
<input type="hidden" name="record" value="{$RECORD}">
	<script>
		function toggleDisplay(id){ldelim}
			if(this.document.getElementById( id).style.display=='none'){ldelim}
				this.document.getElementById( id).style.display='inline'
			{rdelim}else{ldelim}
				this.document.getElementById(  id).style.display='none'
			{rdelim}
		{rdelim}
	</script>
<p>	<table class='{$TABLECLASS}' cellpadding="0" cellspacing="0" width="100%" border="0" >
	<tr><td>
	<table cellpadding="0" cellspacing="0" width="100%" border="0" >

	{foreach from=$ROWVALUES item=row}
		<tr><td>{$row} </td></tr>
	{/foreach}

	<tr ><td valign='top' align='left' border='0' class="{$CLASS}"><h4 class="{$CLASS}">{$FORMHEADER}</h4></td></tr>
	<tr><td  valign='top' align='left'>{$FORMBODY}{$FORMFOOTER}{$POSTFORM}</td></tr>

	</table>
	</td>
	</tr>
	</table></p>
	
<p>	<table class='{$TABLECLASS}' cellpadding="0" cellspacing="0" width="100%" border="0" >
	<tr><td>
	<table cellpadding="0" cellspacing="0" width="100%" border="0" >

	<td class="{$CLASS}"><h4 class="{$CLASS}">{$RELATED_RECORDS_HEADER}</h4></td>

	{foreach from=$Related_records item=related}
		<tr><td  valign='top' align='left'  border='0'>{$related.FORMBODY}{$related.FORMFOOTER}{$related.POSTFORM}</td></tr>
	{/foreach}

	</table>
	</td>
	</tr>
	</table></p>

<p>	<table  width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
	    <td align="left"><input title='{$APP.LBL_SAVE_BUTTON_TITLE}' accessKey='{$APP.LBL_SAVE_BUTTON_KEY}' class='button' type='submit' name='button' value='{$APP.LBL_SAVE_BUTTON_LABEL}' {$SAVE_BUTTON_DISPLAY}></td>
	</tr>
	</table></p>
	</form>
