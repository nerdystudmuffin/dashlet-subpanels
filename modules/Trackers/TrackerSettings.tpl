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
<script type='text/javascript' src='include/javascript/overlibmws.js'></script>
<form name="TrackerSettings" method="POST">
<input type="hidden" name="action" value="TrackerSettings">
<input type="hidden" name="module" value="Trackers">
<input type="hidden" name="process" value="">

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td scope="row" width="100%" colspan="2">
<input type="button" onclick="document.TrackerSettings.process.value='true'; if(check_form('TrackerSettings')) {ldelim} document.TrackerSettings.submit(); {rdelim}" class="button" title="{$app.LBL_SAVE_BUTTON_TITLE}" accessKey="{$app.LBL_SAVE_BUTTON_KEY}" value="{$app.LBL_SAVE_BUTTON_LABEL}">
<input type="button" onclick="document.TrackerSettings.process.value='false'; document.TrackerSettings.submit();" class="button" title="{$app.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$app.LBL_CANCEL_BUTTON_KEY}" value="{$app.LBL_CANCEL_BUTTON_LABEL}">
</td>
</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="edit view">
<tr>
<td scope="row" width="50%">&nbsp;</td>
<td scope="row" width="50%">{$mod.LBL_ENABLE}</td>
</tr>
{foreach name=trackerEntries from=$trackerEntries key=key item=entry}
<tr>
<td scope="row" width="50%">{$entry.label}:&nbsp;{sugar_help text=$entry.helpLabel}</td>
<td  width="50%"><input type="checkbox" id="{$key}" name="{$key}" value="1" {if !$entry.disabled}CHECKED{/if}>
</tr>
{/foreach}
<tr>
<td scope="row">{$mod.LOG_SLOW_QUERIES}:</td>
{if !empty($config.dump_slow_queries)}
	{assign var='dump_slow_queries_checked' value='CHECKED'}
{else}
	{assign var='dump_slow_queries_checked' value=''}
{/if}
<td ><input type='hidden' name='dump_slow_queries' value='false'><input name='dump_slow_queries'  type="checkbox" value='true' {$dump_slow_queries_checked}></td>
</tr>

<tr>
<td scope="row" width="20%">{$mod.LBL_TRACKER_PRUNE_INTERVAL}</td>
<td><input type='text' id='tracker_prune_interval' name='tracker_prune_interval' size='5' value='{$tracker_prune_interval}'></td>
</tr>
<tr>
<td scope="row">{$mod.SLOW_QUERY_TIME_MSEC}: </td>
<td >
<input type='text' size='5' name='slow_query_time_msec' value='{$config.slow_query_time_msec}'>
</td>
</tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td scope="row" width="100%" colspan="2">
<input type="button" onclick="document.TrackerSettings.process.value='true'; if(check_form('TrackerSettings')) {ldelim} document.TrackerSettings.submit(); {rdelim}" class="button" title="{$app.LBL_SAVE_BUTTON_TITLE}" accessKey="{$app.LBL_SAVE_BUTTON_KEY}" value="{$app.LBL_SAVE_BUTTON_LABEL}">
<input type="button" onclick="document.TrackerSettings.process.value='false'; document.TrackerSettings.submit();" class="button" title="{$app.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$app.LBL_CANCEL_BUTTON_KEY}" value="{$app.LBL_CANCEL_BUTTON_LABEL}">
</td>
</tr>
</table>
</form>


<script type="text/javascript">
addToValidate('TrackerSettings', 'tracker_prune_interval', 'int', true, "{$mod.LBL_TRACKER_PRUNE_RANGE}");
addToValidateRange('TrackerSettings', 'tracker_prune_interval', 'range', true, '{$mod.LBL_TRACKER_PRUNE_RANGE}', 1, 180);
addToValidate('TrackerSettings', 'slow_query_time_msec', 'int', true, "{$mod.SLOW_QUERY_TIME_MSEC}");
</script>
