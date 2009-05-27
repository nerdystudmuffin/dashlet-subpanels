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

<table id='layoutEditorButtons' cellspacing='2'>
	<tr>
	{$buttons}
	</tr>
</table>
<div id='layoutEditor' style="width:675px;">
<input type='hidden' id='fieldwidth' value='{$fieldwidth}'>
<input type='hidden' id='maxColumns' value='{$maxColumns}'>
<input type='hidden' id='nextPanelId' value='{$nextPanelId}'>
<div id='toolbox' style='float:left; overflow-y:auto; overflow-x:hidden';>
	<h2 style='margin-bottom:20px;'>{$mod.LBL_TOOLBOX}</h2>
	<div id='delete'>
	{sugar_image name=Delete width=48 height=48}
	</div>

	{if ! isset($fromPortal)}
	<div id='panelproxy'></div>
	{/if}
	<div id='rowproxy'></div>
	<div id='availablefields'>
	<p id='fillerproxy'></p>

	{counter name='idCount' assign='idCount' start='1'}
	{foreach from=$available_fields item='col' key='id'}
		<div class='le_field' id='{$idCount}'>
			{if ! $fromModuleBuilder && ($col.name != '(filler)')}
				<img class='le_edit' src="{sugar_getimagepath file='edit_inline.gif'}" style='float:right; cursor:pointer;' onclick="var value_label = document.getElementById('le_label_{$idCount}').innerHTML; var value_tabindex = document.getElementById('le_tabindex_{$idCount}').innerHTML;ModuleBuilder.getContent('module=ModuleBuilder&action=editProperty&view_module={$view_module}&view={$view}&id_label=le_label_{$idCount}&name_label=label_{$col.label}&title_label={sugar_translate label='LBL_LABEL_TITLE' module='ModuleBuilder'}&value_label=' + value_label + '&id_tabindex=le_tabindex_{$idCount}&title_tabindex={sugar_translate label='LBL_TAB_ORDER' module='ModuleBuilder'}&name_tabindex=tabindex&value_tabindex=' + value_tabindex );" />
			{/if}
			{if isset($col.type) && ($col.type == 'address')}
				{$icon_address}
			{/if}
			{if isset($col.type) && ($col.type == 'phone')}
				{$icon_phone}
			{/if}
			<span id='le_label_{$idCount}'>
			{if !empty($translate) && !empty($col.label)}
				{eval var=$col.label assign='newLabel'}
				{sugar_translate label=$newLabel module=$language}
			{else}
				{$col.label}
			{/if}</span>
			<span class='field_name'>{$col.name}</span>
			<span class='field_label'>{$col.label}</span>
			<span id='le_tabindex_{$idCount}' class='field_tabindex'>{$col.tabindex}</span>
		</div>
		{counter name='idCount' assign='idCount' print=false}
	{/foreach}
	</div>
</div>

<div id='panels' style='float:left; overflow-y:auto; overflow-x:hidden'>

<h3>{$layouttitle}</h3>

{foreach from=$layout item='panel' key='panelid'}

	<div class='le_panel' id='{$idCount}'>

		<div class='panel_label' id='le_panellabel_{$idCount}'>
		  <span class='panel_name' id='le_panelname_{$idCount}'>{if $panelid eq 'default'}{$mod.LBL_DEFAULT}{elseif !empty($translate)}{sugar_translate label=$panelid|upper module=$language}{else}{$panelid}{/if}</span>
		  <span class='panel_id' id='le_panelid_{$idCount}'>{$panelid}</span>
		</div>
		{if $panelid ne 'default'}
        <img class='le_edit' src="{sugar_getimagepath file='edit_inline.gif'}" style='float:right; cursor:pointer;' onclick="var value_label = document.getElementById('le_panelname_{$idCount}').innerHTML;ModuleBuilder.getContent('module=ModuleBuilder&action=editProperty&view_module={$view_module}{if $fromModuleBuilder}&view_package={$view_package}{/if}&view={$view}&id_label=le_panelname_{$idCount}&name_label=label_{$panelid|upper}&title_label={sugar_translate label='LBL_LABEL_TITLE' module='ModuleBuilder'}&value_label=' + value_label );" />
        {/if}
		{counter name='idCount' assign='idCount' print=false}

		{foreach from=$panel item='row' key='rid'}
			<div class='le_row' id='{$idCount}'>
			{counter name='idCount' assign='idCount' print=false}

			{foreach from=$row item='col' key='cid'}
				<div class='le_field' id='{$idCount}'>
			        {if ! $fromModuleBuilder && ($col.name != '(filler)')}
						<img class='le_edit' src="{sugar_getimagepath file='edit_inline.gif'}" style='float:right; cursor:pointer;' onclick="var value_label = document.getElementById('le_label_{$idCount}').innerHTML; var value_tabindex = document.getElementById('le_tabindex_{$idCount}').innerHTML;ModuleBuilder.getContent('module=ModuleBuilder&action=editProperty&view_module={$view_module}{if $fromModuleBuilder}&view_package={$view_package}{/if}&view={$view}&id_label=le_label_{$idCount}&name_label=label_{$col.label}&title_label={sugar_translate label='LBL_LABEL_TITLE' module='ModuleBuilder'}&value_label=' + value_label + '&id_tabindex=le_tabindex_{$idCount}&title_tabindex={sugar_translate label='LBL_TAB_ORDER' module='ModuleBuilder'}&name_tabindex=tabindex&value_tabindex=' + value_tabindex );" />
					{/if}

					{if isset($col.type) && ($col.type == 'address')}
						{$icon_address}
					{/if}
					{if isset($col.type) && ($col.type == 'phone')}
						{$icon_phone}
					{/if}
					<span id='le_label_{$idCount}'>
					{if !empty($translate) && !empty($col.label)}
						{eval var=$col.label assign='evalLabel'}
						{sugar_translate label=$evalLabel module=$language}
					{else}
						{$col.label}
					{/if}</span>
					<!--span id='le_label_{$idCount}' class='field_label'>{sugar_translate label=$col.label module=$language}</span-->
					<span class='field_name'>{$col.name}</span>
					<span class='field_label'>{$col.label}</span>
					<span id='le_tabindex_{$idCount}' class='field_tabindex'>{$col.tabindex}</span>
				</div>
				{counter name='idCount' assign='idCount' print=false}
			{/foreach}

		</div>
	{/foreach}

	</div>
{/foreach}

</div>
<input type='hidden' id='idCount' value='{$idCount}'>
</div>

<form name='prepareForSave' id='prepareForSave' action='index.php'>
<input type='hidden' name='module' value='ModuleBuilder'>
<input type='hidden' name='view_module' value='{$view_module}'>
<input type='hidden' name='view' value='{$view}'>
{if $fromPortal}
	<input type='hidden' name='PORTAL' value='1'>
{/if}
{if $fromModuleBuilder}
	<input type='hidden' name='MB' value='1'>
	<input type='hidden' name='view_package' value='{$view_package}'>
{/if}
<input type='hidden' name='to_pdf' value='1'>
</form>
<script>
Studio2.init();
if('{$view}'.toLowerCase() != 'editview')
	ModuleBuilder.helpSetup('layoutEditor','default'+'{$view}'.toLowerCase());
if('{$from_mb}')
	ModuleBuilder.helpUnregisterByID('saveBtn');

ModuleBuilder.MBpackage = "{$view_package}";
{literal}
function countGridFields() {
	var count = 0;
	var divs = document.getElementById( 'panels' ).getElementsByTagName( 'div' ) ;
	for ( var j=0;j<divs.length;j++) {
		if (divs[j].className == 'le_field') count++;
	}
	return count;
};	
{/literal}
</script>
