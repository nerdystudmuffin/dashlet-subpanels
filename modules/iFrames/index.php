<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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
 /*********************************************************************************

 ********************************************************************************/








global $theme, $current_user;




global $mod_strings;
global $app_list_strings;
global $app_strings;
global $current_user, $focus;

echo get_module_title($mod_strings['LBL_MODULE_ID'], $mod_strings['LBL_MODULE_TITLE'], true); 

if(!empty($_REQUEST['record']) && empty($_REQUEST['edit'])){
	$iFrame = new iFrame();
	$iFrame->retrieve($_REQUEST['record']);
	$xtpl = new XTemplate('modules/iFrames/DetailView.html');
	$xtpl_data = $iFrame->get_xtemplate_data();
	$xtpl_data['URL'] = add_http($xtpl_data['URL']);
	$xtpl->assign('IFRAME', $xtpl_data);
	$xtpl->parse('main');
	$xtpl->out('main');
}
else
{
	if(!empty($_REQUEST['edit']))
	{
		$iFrame = new iFrame();
		$xtpl = new XTemplate('modules/iFrames/EditView.html');	

		if(!empty($_REQUEST['record']))
		{
			$iFrame->retrieve($_REQUEST['record']);
		}

		$xtpl_data = $iFrame->get_xtemplate_data();
		
		$xtpl->assign("MOD", $mod_strings);
		$xtpl->assign("APP", $app_strings);
		
		if (isset($_REQUEST['return_module']))
		{
			 $xtpl->assign("RETURN_MODULE", $_REQUEST['return_module']);
		}
		else
		{
			$xtpl->assign("RETURN_MODULE", 'iFrames');
		}
		
		if (isset($_REQUEST['return_action']))
		{
			 $xtpl->assign("RETURN_ACTION", $_REQUEST['return_action']);
		}
		else
		{
			 $xtpl->assign("RETURN_ACTION",'index');
		}
		
		if (isset($_REQUEST['return_id'])) 
		{
			$xtpl->assign("RETURN_ID", $_REQUEST['return_id']);
		}
		else if(!empty($_REQUEST['record']))
		{
			$xtpl->assign("RETURN_ID", $_REQUEST['record']);
		}
		
		if(!empty($xtpl_data['STATUS']) && $xtpl_data['STATUS'] > 0)
		{
			$xtpl_data['STATUS_CHECKED'] = 'checked';	
		}

		$xtpl->assign('IFRAME', $xtpl_data);
		$xtpl->parse('main');
		$xtpl->out('main');

		
		$javascript = new javascript();
		$javascript->setFormName('EditView');
		$javascript->setSugarBean($iFrame);
		$javascript->addAllFields('');
		echo $javascript->getScript();

	}
	else if(!empty($_REQUEST['delete']) || !empty($_REQUEST['listview']) || (empty($_REQUEST['record']) && empty($_REQUEST['edit'])) )
	{
		$button_title = $app_strings['LBL_NEW_BUTTON_LABEL'];
			
		$sugar_config['disable_export'] = true;
		$iFrame = new iFrame();
		$ListView = new ListView();
		$where = '';
			
		if(!is_admin($current_user))
		{
			$where = "created_by='$current_user->id'";
		}

		$ListView->initNewXTemplate( 'modules/iFrames/ListView.html',$mod_strings);
		$ListView->setHeaderTitle($mod_strings['LBL_LIST_FORM_TITLE']. '&nbsp;' );
		$ListView->setQuery($where, "", "name", "IFRAME");
		$ListView->processListView($iFrame, "main", "IFRAME");
		
		//special case redirect for refreshing shorcut listed sites that might have been deleted
		if(!empty($_REQUEST['delete'])) header("Location: index.php?module=iFrames&action=index");
	}
	else
	{
		$iFrame = new iFrame();
		$xtpl = new XTemplate('modules/iFrames/DetailView.html');
		$xtpl_data = array();
		$xtpl_data['URL'] = translate('DEFAULT_URL', 'iFrames');
		$xtpl->assign('IFRAME', $xtpl_data);
		$xtpl->parse('main');
		$xtpl->out('main');
	}
}



?>
