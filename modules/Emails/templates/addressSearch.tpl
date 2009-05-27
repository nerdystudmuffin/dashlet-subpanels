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
<form id="searchForm" method="get" action="#">
    <table id="searchTable" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr id="peopleTableSearchRow">
            <td id="searchNameFirst" scope="row" nowrap="NOWRAP">
                {$app_strings.LBL_EMAIL_ADDRESS_BOOK_FIRST_NAME}: <input name="first_name" id="input_searchNameFirst" type="text">
            </td>
            <td id="searchNameLast" scope="row" nowrap="NOWRAP">
                {$app_strings.LBL_EMAIL_ADDRESS_BOOK_LAST_NAME}: <input name="last_name" id="input_searchNameLast" type="text">
            </td>
            <td id="searchEmail" scope="row" nowrap="NOWRAP">
                {$mod_strings.LBL_EMAIL} <input name="email_address" id="input_searchEmail" type="text">
            </td>
			<td>
			     <select name="person" id="input_searchPerson">
			         {$listOfPersons}
			     </select>
            </td>
            <td id="searchSubmit" scope="row" nowrap="NOWRAP">
                <input class="button" onclick="SUGAR.email2.addressBook.searchContacts();" value="   {$app_strings.LBL_SEARCH_BUTTON_LABEL}   " id="input_searchSubmit" type="button">
            </td>
        </tr>
    </table>
</form>
