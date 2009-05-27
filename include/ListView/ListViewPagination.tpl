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
	<tr class='pagination'>
		<td colspan='{$colCount+1}' align='right'>
			<table border='0' cellpadding='0' cellspacing='0' width='100%'>
				<tr>
					<td nowrap="nowrap">
						{$selectLink}
						{$deleteLink}
						{$exportLink}
						{$targetLink}
						{$mergeLink}
						{$mergedupLink}
						{$favoritesLink}
						{$composeEmailLink}





						&nbsp;{$selectedObjectsSpan}		
					</td>
					<td  align='right' nowrap='nowrap' width='90%'>						
						{if $pageData.urls.startPage}
							<button type='button' title='{$navStrings.start}' class='button' {if $prerow}onclick='return sListView.save_checks(0, "{$moduleString}");'{else} onClick='location.href="{$pageData.urls.startPage}"' {/if}>
								<img src='{sugar_getimagepath file='start.gif'}' alt='{$navStrings.start}' align='absmiddle' border='0' width='13' height='11'>
							</button>
						{else}
							<button type='button' title='{$navStrings.start}' class='button' disabled>
								<img src='{sugar_getimagepath file='start_off.gif'}' alt='{$navStrings.start}' align='absmiddle' border='0' width='13' height='11'>
							</button>
						{/if}
						{if $pageData.urls.prevPage}
							<button type='button' title='{$navStrings.previous}' class='button' {if $prerow}onclick='return sListView.save_checks({$pageData.offsets.prev}, "{$moduleString}")' {else} onClick='location.href="{$pageData.urls.prevPage}"'{/if}>
								<img src='{sugar_getimagepath file='previous.gif'}' alt='{$navStrings.previous}' align='absmiddle' border='0' width='8' height='11'>							
							</button>
						{else}
							<button type='button' class='button' disabled title='{$navStrings.previous}'>
								<img src='{sugar_getimagepath file='previous_off.gif'}' alt='{$navStrings.previous}' align='absmiddle' border='0' width='8' height='11'>
							</button>
						{/if}
							<span class='pageNumbers'>({if $pageData.offsets.lastOffsetOnPage == 0}0{else}{$pageData.offsets.current+1}{/if} - {$pageData.offsets.lastOffsetOnPage} {$navStrings.of} {if $pageData.offsets.totalCounted}{$pageData.offsets.total}{else}{$pageData.offsets.total}{if $pageData.offsets.lastOffsetOnPage != $pageData.offsets.total}+{/if}{/if})</span>
						{if $pageData.urls.nextPage}
							<button type='button' title='{$navStrings.next}' class='button' {if $prerow}onclick='return sListView.save_checks({$pageData.offsets.next}, "{$moduleString}")' {else} onClick='location.href="{$pageData.urls.nextPage}"'{/if}>
								<img src='{sugar_getimagepath file='next.gif'}' alt='{$navStrings.next}' align='absmiddle' border='0' width='8' height='11'>
							</button>
						{else}
							<button type='button' class='button' title='{$navStrings.next}' disabled>
								<img src='{sugar_getimagepath file='next_off.gif'}' alt='{$navStrings.next}' align='absmiddle' border='0' width='8' height='11'>
							</button>
						{/if}
						{if $pageData.urls.endPage  && $pageData.offsets.total != $pageData.offsets.lastOffsetOnPage}
							<button type='button' title='{$navStrings.end}' class='button' {if $prerow}onclick='return sListView.save_checks("end", "{$moduleString}")' {else} onClick='location.href="{$pageData.urls.endPage}"'{/if}>
								<img src='{sugar_getimagepath file='end.gif'}' alt='{$navStrings.end}' align='absmiddle' border='0' width='13' height='11'>							
							</button>
						{elseif !$pageData.offsets.totalCounted || $pageData.offsets.total == $pageData.offsets.lastOffsetOnPage}
							<button type='button' class='button' disabled title='{$navStrings.end}'>
							 	<img src='{sugar_getimagepath file='end_off.gif'}' alt='{$navStrings.end}' align='absmiddle' border='0' width='13' height='11'>
							</button>
						{/if}
					</td>
				</tr>
			</table>
		</td>
	</tr>
