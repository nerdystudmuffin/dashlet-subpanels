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
<script>
formsWithFieldLogic=null;
</script>

{include file="modules/DynamicFields/templates/Fields/Forms/coreTop.tpl"}
<tr>
	<td class='mbLBL'>{$MOD.COLUMN_TITLE_DEFAULT_VALUE}:</td><td>
	{if $hideLevel < 5}
		<input type='text' name='default' id='int_default' value='{$vardef.default}'>
		<script>addToValidate('popup_form', 'default', 'int', false,'{$MOD.COLUMN_TITLE_DEFAULT_VALUE}' );</script>
	{else}
		<input type='hidden' name='default' id='int_default' value='{$vardef.default}'>{$vardef.default}
	{/if}
	</td>
</tr>
<tr>
	<td class='mbLBL'>{$MOD.COLUMN_TITLE_MIN_VALUE}:</td>
	<td>
	{if $hideLevel < 5}
		<input type='text' name='min' id='int_min' value='{$vardef.validation.min}'>
		<script>addToValidate('popup_form', 'min', 'int', false,'{$MOD.COLUMN_TITLE_MIN_VALUE}' );</script>
	{else}
		<input type='hidden' name='min' id='int_min' value='{$vardef.validation.min}'>{$vardef.range.min}
	{/if}
	</td>
</tr>
<tr>
	<td class='mbLBL'>{$MOD.COLUMN_TITLE_MAX_VALUE}:</td>
	<td>
	{if $hideLevel < 5}
		<input type='text' name='max' id='int_max' value='{$vardef.validation.max}'>
		<script>addToValidate('popup_form', 'max', 'int', false,'{$MOD.COLUMN_TITLE_MAX_VALUE}' );</script>
	{else}
		<input type='hidden' name='max' id='int_max' value='{$vardef.validation.max}'>{$vardef.range.max}
	{/if}
	</td>
</tr>
<tr>
	<td class='mbLBL'>{$MOD.COLUMN_TITLE_MAX_SIZE}:</td>
	<td>
	{if $hideLevel < 5}
		<input type='text' name='len' id='int_len' value='{$vardef.len|default:11}'></td>
		<script>addToValidate('popup_form', 'len', 'int', false,'{$MOD.COLUMN_TITLE_MAX_SIZE}' );</script>
	{else}
		<input type='hidden' name='len' id='int_len' value='{$vardef.len}'>{$vardef.len}
	{/if}
	</td>
</tr>
<tr>
    <td class='mbLBL'>{$MOD.COLUMN_DISABLE_NUMBER_FORMAT}:</td>
    <td>
        <input type='checkbox' name='ext3' value=1 {if !empty($vardef.disable_num_format) }checked{/if} {if $hideLevel > 5}disabled{/if} />
        {if $hideLevel > 5}<input type='hidden' name='ext3' value='{$vardef.disable_num_format}'>{/if}
    </td>
</tr>
<script>
	formsWithFieldLogic=new addToValidateFieldLogic('popup_form_id', 'int_min', 'int_max', 'int_default', 'int_len', 'int', 'Invalid Logic.');
</script>
{include file="modules/DynamicFields/templates/Fields/Forms/coreBottom.tpl"}
