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


{literal}
<br>


<script type="text/javascript" src="modules/Studio/JSTransaction.js" ></script>
<script>
	var jstransaction = new JSTransaction();
</script>
<script src = "include/javascript/yui/dragdrop.js" ></script>
<script src='modules/Studio/studiotabgroups.js'></script>
<script src = "modules/Studio/ygDDListStudio.js" ></script>				 	
<script type="text/javascript" src="modules/Studio/studiodd.js" ></script>	
<script type="text/javascript" src="modules/Studio/studio.js" ></script>	
<style type='text/css'>
.slot {
	border-width:1px;border-color:#999999;border-style:solid;padding:0px 1px 0px 1px;margin:2px;cursor:move;

}


.slotSub {
	border-width:1px;border-color:#006600;border-style:solid;padding:0px 1px 0px 1px;margin:2px;cursor:move;

}
.slotB {
	border-width:0;cursor:move;

}
.listContainer
{
	margin-left: 4;
	padding-left: 4;
	margin-right: 4;
	padding-right: 4;
	list-style-type: none;
}

.tableContainer
{
	
}
.tdContainer{
	border: thin solid gray;
	padding: 10;
}
.fieldValue{
	color: #999;
	font-size: 75%;
	cursor:move;
}


	
}

</style>
{/literal}




<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td colspan='100'><h2>{$title}</h2></td></tr>
<tr><td colspan='100'>
{$description}
</td></tr><tr><td><br></td></tr><tr><td colspan='100'>{$buttons}</td></tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class='tabForm'>
<tr>
{counter start=0 name="slotCounter" print=false assign="slotCounter"}
{counter start=0 name="modCounter" print=false assign="modCounter"}
{foreach from=$groups key='label' item='list'}

<td valign='top' nowrap width='20%'>
<h3>{$label}</h3>
<div style="width: 100%; height: 400; overflow: auto;">
<ul class='listContainer' id='ul{$slotCounter}'>

{foreach from=$list key='key' item='value'}


<li id='subslot{$modCounter}'>
<span class='slotB'>{if !empty($translate)}{sugar_translate label=$value.label module=$module}{else}{$value.label}{/if}</span>
{if empty($hideKeys)} <br><span class='fieldValue'>[{$key}]{/if}</span>
</li>
<script>
tabLabelToValue['{$value.label}|{$key}'] = '{$key}';
if(typeof(subtabModules['subslot{$modCounter}']) == 'undefined')subtabModules['subslot{$modCounter}'] = '{$value.label}|{$key}';
</script>
{counter name="modCounter"}
{/foreach}
<li  id='topslot{$slotCounter}' class='noBullet'>&nbsp;</span>
</ul>
</div>
</td>
{counter name="slotCounter"}
{/foreach}
<td width='100%'>&nbsp;</td>
</tr></table>


<span class='error'>{$error}</span>



{literal}

	<script>
		
	  	var slotCount = {/literal}{$slotCounter}{literal};
	 	var modCount = {/literal}{$modCounter}{literal};
		var subSlots = [];
		var yahooSlots = [];
		 
		function dragDropInit(){
		
			YAHOO.util.DDM.mode = YAHOO.util.DDM.POINT;
			for(msi = 0; msi <= slotCount ; msi++){
				yahooSlots["topslot"+ msi] = new ygDDListStudio("topslot" + msi, "subTabs", true);
			}
			for(msi = 0; msi <= modCount ; msi++){
					yahooSlots["subslot"+ msi] = new ygDDListStudio("subslot" + msi, "subTabs", false);
			}
			
			yahooSlots["subslot"+ (msi - 1) ].updateTabs();
			  // initPointMode();
		}
		
		YAHOO.util.DDM.mode = YAHOO.util.DDM.INTERSECT; 
		YAHOO.util.Event.addListener(window, "load", dragDropInit);

</script>	
{/literal}


<div id='logDiv' style='display:none'> 
</div>

{$additionalFormData}
	
</form>


