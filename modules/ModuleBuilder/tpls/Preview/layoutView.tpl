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

<table cellspacing='2'>
	<tr>
	{$buttons}
	</tr>
</table>
<div style='width:675px;' class='preview'>
<div style='position: relative; left:245px; top:45px; float:left' id='layoutPreview'>
<h3>{$layouttitle}</h3>
{foreach from=$layout item='panel' key='panelid'}
	<div class='le_panel'>
        <div class='panel_label' id='le_panellabel_{$idCount}'>
          <span class='panel_name' id='le_panelname_{$idCount}'>{if !empty($translate)}{sugar_translate label=$panelid|upper module=$language}{else}{$panelid}{/if}</span>
          <span class='panel_id' id='le_panelid_{$idCount}'>{$panelid}</span>
        </div>
		{counter name='idCount' assign='idCount' print=false}
			
		{foreach from=$panel item='row' key='rid'}
			<div class='le_row'>
			{counter name='idCount' assign='idCount' print=false}	
			{foreach from=$row item='col' key='cid'}
				{if $col.name != "(empty)"}
				{assign var='nextcid' value=`$cid+1`}
				<div class='le_field' {if $cid == 0 && $row.$nextcid.name == "(empty)"}style="width:290px"{/if}> 
					{if isset($col.type) && ($col.type == 'address')}
						{$icon_address}
					{/if}
					{if isset($col.type) && ($col.type == 'phone')}
						{$icon_phone}
					{/if}
					<span >{if !empty($translate) && !empty($col.label)}
						{eval var=$col.label assign='newLabel'}
						{sugar_translate label=$newLabel module=$language}
					{else}
						{$col.label}
					{/if}</span>
					<span class='field_name'>{$col.name}</span>
					<span class='field_label'>{$col.label}</span>
					<span class='field_tabindex'>{$col.tabindex}</span>
				</div>
				{/if}
				{counter name='idCount' assign='idCount' print=false}
			{/foreach}
		</div>	
	{/foreach}
	</div>
{/foreach}
</div></div>
