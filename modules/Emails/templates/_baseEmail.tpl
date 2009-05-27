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
<link rel="stylesheet" type="text/css" href="include/javascript/yui/assets/menu.css" />
<link rel="stylesheet" type="text/css" href="modules/Emails/EmailUI.css" />
<script type="text/javascript" src='{sugar_getjspath file ='include/javascript/yui/build/yuiloader/yuiloader.js'}' ></script>
<script type="text/javascript" language="Javascript" src="include/javascript/yui/ygDDList.js"></script>
<script type="text/javascript" language="Javascript" src="include/javascript/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" language="Javascript" src="include/SugarEmailAddress/SugarEmailAddress.js?"></script>




<script type="text/javascript" language="Javascript" src="modules/InboundEmail/InboundEmail.js?"></script>
<script type="text/javascript" language="Javascript" src="modules/Emails/javascript/ajax.js?"></script>
<script type="text/javascript" language="Javascript" src="modules/Emails/javascript/EmailUI.js?"></script>
<script type="text/javascript" language="Javascript" src="modules/Emails/javascript/grid.js"></script>
<script type="text/javascript" language="Javascript" src="modules/Emails/javascript/complexLayout.js"></script>
<script type="text/javascript" language="Javascript" src="modules/Emails/javascript/init.js"></script>
<script type="text/javascript" language="Javascript" src="modules/Emails/javascript/composeEmailTemplate.js"></script>
<script type="text/javascript" language="Javascript" src="modules/Emails/javascript/displayOneEmailTemplate.js?"></script>
<script type="text/javascript" language="Javascript" src="modules/Emails/javascript/viewPrintable.js?"></script>


<script type="text/javascript" language="Javascript">
    var calFormat = '{$calFormat}';
    var theme = "{$theme}";
    var viewRawEmail = '{$viewRawSource}';
    {$lang}
    {$tinyMCE}
    SUGAR.email2.detailView.qcmodules = {$qcModules};
    SUGAR.email2.userPrefs = {$userPrefs};
    SUGAR.email2.signatures = {$defaultSignature};
    SUGAR.email2.composeLayout.charsets = {$emailCharsets};



    linkBeans = {$linkBeans};
    var loadingSprite = app_strings.LBL_EMAIL_LOADING + " <img src='include/javascript/yui/ext/resources/images/grid/grid-loading.gif' height='14' align='absmiddle'>";
</script>

<form id="emailUIForm" name="emailUIForm">
    <input type="hidden" id="module" name="module" value="Emails">
    <input type="hidden" id="action" name="action" value="EmailUIAjax">
    <input type="hidden" id="to_pdf" name="to_pdf" value="true">
    <input type="hidden" id="emailUIAction" name="emailUIAction">
    <input type="hidden" id="mbox" name="mbox">
    <input type="hidden" id="uid" name="uid">
    <input type="hidden" id="ieId" name="ieId">
    <input type="hidden" id="forceRefresh" name="forceRefresh">
    <input type="hidden" id="focusFolder" name="focusFolder">
    <input type="hidden" id="focusFolderOpen" name="focusFolderOpen">
    <input type="hidden" id="sortBy" name="sortBy">
    <input type="hidden" id="reverse" name="reverse">
</form>


<table cellpadding="0" cellspacing="0" border="0" width="100%">
    <tr>
        <td NOWRAP style="padding-bottom: 2px;">
            <button class="button" id="checkEmailButton" onclick="SUGAR.email2.folders.startEmailAccountCheck();"><img src="themes/default/images/icon_email_check.gif" align="absmiddle" border="0"> {$app_strings.LBL_EMAIL_CHECK}</button>
            <button class="button" id="composeButton" onclick="SUGAR.email2.composeLayout.c0_composeNewEmail();"><img src="themes/default/images/icon_email_compose.gif" align="absmiddle" border="0"> {$app_strings.LBL_EMAIL_COMPOSE}</button>
            <button class="button" id="settingsButton" onclick="SUGAR.email2.settings.showSettings();"><img src="themes/default/images/icon_email_settings.gif" align="absmiddle" border="0"> {$app_strings.LBL_EMAIL_SETTINGS}</button>
            &nbsp;&nbsp;{$app_strings.LBL_EMAIL_VIEWS}:
            <button class="button" onclick="SUGAR.email2.settings.changeView('cols');"><img src="themes/default/images/icon_email_view1.gif" align="absmiddle" border="0"></button>
            <button class="button" onclick="SUGAR.email2.settings.changeView('rows');"><img src="themes/default/images/icon_email_view2.gif" align="absmiddle" border="0"></button>
            <button class="button" onclick="SUGAR.email2.settings.toggleFullScreenQuick();"><img src="themes/default/images/icon_email_fullscreen.gif" align="absmiddle" border="0"></button>
        </td>
        <td NOWRAP align="right" style="padding-bottom: 2px;">
            <a href="javascript:void window.open('index.php?module=Administration&action=SupportPortal&view=documentation&version={$sugar_version}&edition={$sugar_flavor}&lang={$current_language}&help_module=Emails&help_action=index&key={$server_unique_key}','helpwin','width=600,height=600,status=0,resizable=1,scrollbars=1,toolbar=0,location=1')" class='utilsLink'><img src="{sugar_getimagepath file='help.gif'}" width='13' height='13' alt='{$app_strings.LNK_HELP}' border='0' align='absmiddle'></a>
            &nbsp;
            <a href="javascript:void window.open('index.php?module=Administration&action=SupportPortal&view=documentation&version={$sugar_version}&edition={$sugar_flavor}&lang={$current_language}&help_module=Emails&help_action=index&key={$server_unique_key}','helpwin','width=600,height=600,status=0,resizable=1,scrollbars=1,toolbar=0,location=1')" class='utilsLink'>{$app_strings.LNK_HELP}</a>
        </td>
    </tr>
</table>


{include file="modules/Emails/templates/overlay.tpl"}

<div id="emailContextMenu"></div>
<div id="folderContextMenu"></div>
<div id="container" style="position:relative; height:550px; overflow:hidden;"></div>
<div id="innerLayout" class="yui-hidden"></div>
<div id="listViewLayout" class="yui-hidden"></div>
<div id="settingsDialog"></div>

<!-- Hidden Content -->
<div class="yui-hidden">
    <div id="searchTab" style="padding:5px">
        {include file="modules/Emails/templates/advancedSearch.tpl"}
    </div>
    <div id="contactsTab" style="overflow:hidden; width:100%; height:100%;">
        {include file="modules/Emails/templates/addressBook.tpl"}
    </div>
    <div id="settings">
        {include file="modules/Emails/templates/emailSettings.tpl"}
    </div>
    
    <div id="footerLinks" class="yui-hidden">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
            <tr>
                <td NOWRAP width="100%" align="center">



                    <img height="18" width="83" class="img" src="include/images/powered_by_sugarcrm.gif" border="0" align="absmiddle"/>



                    &nbsp;{$app_strings.LBL_SUGAR_COPYRIGHT}
                </td>
            </tr>
        </table>
    </div>





</div>


<div id="editContact" class="yui-hidden"></div>
<div id="editContactTab" class="yui-hidden"></div>
<div id="editMailingList" class="yui-hidden"></div>
<div id="editMailingListTab" class="yui-hidden"></div>


<!-- for detailView quickCreate() calls -->
<div id="quickCreate"></div>
<div id="quickCreateContent"></div>


<div id="importDialog"></div>
<div id="importDialogContent" ></div>


<div id="relateDialog"  ></div>
<div id="relateDialogContent"  ></div>


<div id="assignmentDialog"  ></div>
<div id="assignmentDialogContent"  ></div>


<div id="emailDetailDialog"  ></div>
<div id="emailDetailDialogContent"  ></div>


<!-- for detailView views -->
<div id="viewDialog"></div>
<div id="viewDialogContent"></div>


<!-- addressBook select -->
<div id="contactsDialogue"></div>
<div id="contactsDialogueHTML" class="yui-hidden">
	<div id="contactsDialogueBody">
		<div id='addressBookTabsDiv'></div>
		<div id='contactsSearchTabs'>
		  {include file="modules/Emails/templates/addressSearch.tpl"}





		</div>
	   <div id="addrSearchGrid"></div>
    </div>
</div>

<!-- accounts outbound server dialogue -->
<div id="outboundDialog" class="yui-hidden">
    {include file="modules/Emails/templates/outboundDialog.tpl"}
</div>
