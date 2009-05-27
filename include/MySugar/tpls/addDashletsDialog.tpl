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
<div align="right" id="dashletSearch">
	<table>
		<tr>
			<td>{sugar_translate label='LBL_DASHLET_SEARCH' module='Home'}: <input id="search_string" type="text" length="15" onKeyPress="javascript:if(event.keyCode==13)SUGAR.mySugar.searchDashlets(this.value,document.getElementById('search_category').value);" />
			<input type="button" class="button" value="{sugar_translate label='LBL_SEARCH' module='Home'}" onClick="javascript:SUGAR.mySugar.searchDashlets(document.getElementById('search_string').value,document.getElementById('search_category').value);" />
			<input type="button" class="button" value="{sugar_translate label='LBL_CLEAR' module='Home'}" onClick="javascript:SUGAR.mySugar.clearSearch();" />			
			{if $moduleName == 'Home'}
			<input type="hidden" id="search_category" value="module" />
			{else}
			<input type="hidden" id="search_category" value="chart" />
			{/if}
			</td>
		</tr>
	</table>
	<br>
</div>

{if $moduleName == 'Home'}
 <ul class="subpanelTablist" id="dashletCategories">
	<li id="moduleCategory" class="active"><a href="javascript:SUGAR.mySugar.toggleDashletCategories('module');" class="current" id="moduleCategoryAnchor">{sugar_translate label='LBL_MODULES' module='Home'}</a></li>
	<li id="chartCategory" class=""><a href="javascript:SUGAR.mySugar.toggleDashletCategories('chart');" class="" id="chartCategoryAnchor">{sugar_translate label='LBL_CHARTS' module='Home'}</a></li>
	<li id="toolsCategory" class=""><a href="javascript:SUGAR.mySugar.toggleDashletCategories('tools');" class="" id="toolsCategoryAnchor">{sugar_translate label='LBL_TOOLS' module='Home'}</a></li>	
</ul>
{/if}

{if $moduleName == 'Home'}
<div id="moduleDashlets" style="overflow-y:auto;height:400px;display:;">
	<h3>{sugar_translate label='LBL_MODULES' module='Home'}</h3>
	<div id="moduleDashletsList">
	<table width="95%">
		{counter assign=rowCounter start=0 print=false}
		{foreach from=$modules item=module}
		{if $rowCounter % 2 == 0}
		<tr>
		{/if}
			<td width="50%" align="left"><a href="#" onclick="{$module.onclick}">{$module.icon}</a>&nbsp;<a class="mbLBLL" href="#" onclick="{$module.onclick}">{$module.title}</a><br /></td>
		{if $rowCounter % 2 == 1}
		</tr>
		{/if}
		{counter}
		{/foreach}
	</table>
	</div>
</div>
{/if}
<div id="chartDashlets" style="overflow-y:auto;{if $moduleName == 'Home'}height:400px;display:none;{else}height:425px;display:;{/if}">
	{if $charts != false}
	<h3><span id="basicChartDashletsExpCol"><a href="#" onClick="javascript:SUGAR.mySugar.collapseList('basicChartDashlets');"><img border="0" src="{sugar_getimagepath file='basic_search.gif'}" align="absmiddle" /></span></a>&nbsp;{sugar_translate label='LBL_BASIC_CHARTS' module='Home'}</h3>
	<div id="basicChartDashletsList">
	<table width="100%">
		{foreach from=$charts item=chart}
		<tr>
			<td align="left"><a href="#" onclick="{$chart.onclick}">{$chart.icon}</a>&nbsp;<a class="mbLBLL" href="#" onclick="{$chart.onclick}">{$chart.title}</a><br /></td>
		</tr>
		{/foreach}
	</table>



	</div>
	{/if}





















</div>

{if $moduleName == 'Home'}
<div id="toolsDashlets" style="overflow-y:auto;height:400px;display:none;">
	<h3>{sugar_translate label='LBL_TOOLS' module='Home'}</h3>
	<div id="toolsDashletsList">
	<table width="95%">
		{counter assign=rowCounter start=0 print=false}
		{foreach from=$tools item=tool}
		{if $rowCounter % 2 == 0}
		<tr>
		{/if}
			<td align="left"><a href="#" onclick="{$tool.onclick}">{$tool.icon}</a>&nbsp;<a class="mbLBLL" href="#" onclick="{$tool.onclick}">{$tool.title}</a><br /></td>
		{if $rowCounter % 2 == 1}
		</tr>
		{/if}
		{counter}
		{/foreach}
	</table>
	</div>
</div>
{/if}

<div id="searchResults" style="overflow-y:auto;display:none;{if $moduleName == 'Home'}height:400px;{else}height:425px;{/if}">
</div>
