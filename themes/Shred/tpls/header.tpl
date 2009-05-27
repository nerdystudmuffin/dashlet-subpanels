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
{include file="_head.tpl" theme_template=true}
<body onMouseOut="closeMenus();">

<div id="HideMenu" class="leftList">
{if $AUTHENTICATED}
{include file="_leftFormHiddenLastViewed.tpl" theme_template=true}
{include file="_leftFormHiddenShortcuts.tpl" theme_template=true}
{/if}
</div>

<div id="header">
    {include file="_companyLogo.tpl" theme_template=true}
    {include file="_colorFontPicker.tpl" theme_template=true}
    {include file="_globalLinks.tpl" theme_template=true}
    <img id="boarder" height="45" border="0" width="43" style="margin-left: 3em;" alt="Getting Air" src="themes/Shred/images/boarder.png"/>
    <div class="clear"></div>
    <img id="icicle_left" src="themes/Shred/images/icicle_left.png"/>
    <img id="icicle_right" src="themes/Shred/images/icicle_right.png"/>
    <div class="clear"></div>
    {if !$AUTHENTICATED}
    <br /><br />
    {/if}
    {if $USE_GROUP_TABS}
    {include file="_headerModuleListGroupTabs.tpl" theme_template=true}
    {else}
    {include file="_headerModuleList.tpl" theme_template=true}
    {/if}
    {include file="_welcome.tpl" theme_template=true}
    {include file="_headerSearchAlt.tpl" theme_template=true}
    <div class="clear"></div>
    {if $AUTHENTICATED}
    {include file="_headerLastViewed.tpl" theme_template=true}
    {include file="_headerShortcuts.tpl" theme_template=true}
    {/if}
</div>

<div id="main">
    {if $AUTHENTICATED}
    {include file="_leftFormHide.tpl" theme_template=true}
    <div id="leftColumn">
        {include file="_leftFormLastViewed.tpl" theme_template=true}
        {include file="_leftFormShortcuts.tpl" theme_template=true}
        {include file="_leftFormNewRecord.tpl" theme_template=true}
    </div>
    {/if}
    <div id="content" {if !$AUTHENTICATED}class="noLeftColumn" {/if}>
        <table><tr><td>
