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
{if $count is odd}
<tr>
{/if}
    <td scope="row">{$property.label}:{if isset($property.required) && $property.required == true} <span class="required">*</span>{/if}{sugar_help text=$property.info_label} </td>
    <td >
        {if isset($property.custom)}
            {$property.custom}
        {elseif $property.type == "text"}
            <input type='text' size='40' name='{$name}' id='{$name}' value='{$property.value}'>
        {elseif $property.type == "number"}
            <input type='text' size='10' name='{$name}' id='{$name}' value='{$property.value}' onchange="verifyNumber('{$name}')">
        {elseif $property.type == "percent"}
            <input type='text' size='20' name='{$name}' id='{$name}' value='{$property.value}' onchange="verifyPercent('{$name}')">
        {elseif $property.type == "select"}
            {html_options name=$name options=$property.selectList selected=$property.value}
        {elseif $property.type == "multiselect"}
            <select name='{$name}[]' multiple size=4>
            {html_options options=$property.selectList selected=$property.value}
            </select>
        {elseif $property.type == "bool"}
            <input type="hidden" name='{$name}' value='false'>
            <input type='checkbox' name='{$name}' value='true' id='{$name}' {if $property.value == "true"}CHECKED{/if}>
        {elseif $property.type == "password"}
            <input type='password' size='20' name='{$name}' id='{$name}' value='{$property.value}'>
        {elseif $property.type == "file"}
            <input type="file" id='{$name}' name='{$name}' size="20"/>
        {elseif $property.type == "image"}
            <img src='{$property.path}'>
            <input type='hidden' id='{$name}' name='{$name}' value='{$property.value}'>
        {/if}
    </td>
{if $count is not odd}
</tr>
{/if}
