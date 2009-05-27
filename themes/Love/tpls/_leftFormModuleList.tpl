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
<div id="moduleList" class"leftList">
<ul class="shortcuts">
    <li class="TopBorder">
    	<span class="TopBorderLeft"></span>
    </li>
    {foreach from=$moduleTopMenu item=module key=name name=moduleList}
    {if $name == $MODULE_NAME}
	    <li class="currentShortcut">
	        {sugar_link id="moduleTab_$name" module=$name}
		</li>
		{if $name!='Home'}
		    {counter start=1 name="num" assign="num"}
		    {foreach from=$SHORTCUT_MENU item=item}
			    <li class="subshortcut">
			        <a href="{$item.URL}">{$item.IMAGE}&nbsp;
			        	<span>{$item.LABEL}</span>
			    	</a>
			    </li>
			    {counter name="num"}
		    {/foreach}
	    {/if}
	{else}
    <li class="notCurrentShortcut">
       {sugar_link id="moduleTab_$name" module=$name}
    {/if}
    </li>
    {/foreach}
    {if count($moduleExtraMenu) > 0}
        {foreach from=$moduleExtraMenu item=module key=name name=moduleList}
        {if $name == $MODULE_NAME}
		<li class="noBorder subshortcut">
		    {sugar_link id="moduleTab_$name" module=$name}
		{else}
		<li class="notCurrentShortcut">
		    {sugar_link id="moduleTab_$name" module=$name}
		{/if}
		</li>
        {/foreach}
        <li class="BottomBorder">
        <span class="BottomBorderLeft"></span>
    </li>
    
    {/if}
</ul>
</div>
