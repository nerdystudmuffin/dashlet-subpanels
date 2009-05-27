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
{if $AUTHENTICATED}
<div id="search" class="leftList">
    <h3><span>{$APP.LBL_SEARCH}</span></h3>
    <ul>
        <li>
            <form name='UnifiedSearch' onsubmit='return SUGAR.unifiedSearchAdvanced.checkUsaAdvanced()'>
            <input type="hidden" name="action" value="UnifiedSearch">
            <input type="hidden" name="module" value="Home">
            <input type="hidden" name="search_form" value="false">
            <input type="hidden" name="advanced" value="false">
            <input type="text" class="searchField" name="query_string" id="query_string" size="20" value="{$SEARCH}">&nbsp;
            <input type="submit" class="button" value="GO">
            </form>
        </li>
        <li id="unified_search_advanced_div" style="display: none; height: 1px; position: absolute; overflow: hidden; width: 300px; padding-top: 5px; left:-62px;"> </li>
        <li> <a id="unified_search_advanced_img">
            Advanced Search
            <img src="{sugar_getimagepath file='MoreDetail.png'}" border="0" alt="{$APP.LBL_ADVANCED_SEARCH}" />&nbsp;
            </a>
        </li>

        <li id="sitemapLink">
            <span id="sitemapLinkSpan">
            {$APP.LBL_SITEMAP}
            <img src="{sugar_getimagepath file='MoreDetail.png'}">
            </span>
            <span id='sm_holder'></span>
        </li>
    </ul>
</div>
{literal}
<script type="text/javascript">
<!--
document.getElementById('sitemapLinkSpan').onclick = function()
{
    ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_LOADING_PAGE'));

    var smMarkup = '';
    var callback = {
         success:function(r) {     
             ajaxStatus.hideStatus();
             document.getElementById('sm_holder').innerHTML = r.responseText;
             with ( document.getElementById('sitemap').style ) {
                 display = "block";
                 position = "absolute";
                 right = 0;
                 top = 80;
             }
             document.getElementById('sitemapClose').onclick = function()
             {
                 document.getElementById('sitemap').style.display = "none";
             }
         } 
    } 
    postData = 'module=Home&action=sitemap&GetSiteMap=now&sugar_body_only=true';    
    YAHOO.util.Connect.asyncRequest('POST', 'index.php', callback, postData);
}
-->
</script>
{/literal}
{/if}
