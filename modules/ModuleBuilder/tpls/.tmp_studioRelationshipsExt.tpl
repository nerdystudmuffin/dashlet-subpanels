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
 {[SUGAR.sugar_image(rhs_module,height=40,width=40)]}
*}

<table id='relTable' style="width:100%">
	<tr><td colspan='9'><input type='button' name='addrelbtn' value='{$mod_strings.LBL_BTN_ADD_RELATIONSHIP}' class='button' onclick='ModuleBuilder.moduleLoadRelationship2("");'></td></tr>
	<tr><td colspan='9'><hr><h1>{$mod_strings.LBL_RELATIONSHIPS}</h1></td></tr>
	<tr><th>Name</th><th>Left Module</th><th>Type</th><th>Right Module</th></tr>
	{literal}
	<tpl for="relationships">
		<tr class='{[xindex % 2 == 0 ? "even" : "odd"]}ListRowS1'><td>
				<a href='javascript:void(0)' onclick='ModuleBuilder.moduleLoadRelationship2("{name}");'>{name}</a></td>
				<!-- <td class='button' onclick='ModuleBuilder.moduleLoadRelationship2("{name}");'>{lhsicon}</td>
				<td align="center"><img src="themes/default/images/{relationship_type}.png"></td>
				<td class='button' onclick='ModuleBuilder.moduleLoadRelationship2("{name}");'>{rhsicon}</td> -->
			
				<td>{lhs_module}</td>
				<td>{relationship_type}</td>
				<td>{rhs_module}</td>
		</tr>
	</tpl>
</table>
<script>
ModuleBuilder.module = "{currentModule}";
ModuleBuilder.helpRegisterByID('relTable');
ModuleBuilder.helpSetup('studioWizard','relationshipsHelp');
</script>
{/literal}
