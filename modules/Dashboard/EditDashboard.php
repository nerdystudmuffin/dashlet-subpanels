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

 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/


global $mod_strings;



$focus = new Dashboard();

if(!isset($_REQUEST['chart_index']))
	sugar_die('no index is requested to move');

if(!isset($_REQUEST['record']))
	sugar_die('no index is requested to move');

global $current_user;

$focus->retrieve($_REQUEST['record']);

if ( empty($focus->id) || $focus->id == -1)
{
	sugar_die("there is no dashboard associated to this id:".$_REQUEST['record']);
}

if ( $current_user->id != $focus->assigned_user_id)
{
	sugar_die("why are you trying to edit someone else's dashboard?");
}

if ( $_REQUEST['dashboard_action'] == 'move_up')
{
	$focus->move('up',$_REQUEST['chart_index']);
} else if ($_REQUEST['dashboard_action'] == 'move_down')
{
  $focus->move('down',$_REQUEST['chart_index']);
} else if ($_REQUEST['dashboard_action'] == 'delete')
{   
	$focus->delete($_REQUEST['chart_index']);
} else if ($_REQUEST['dashboard_action'] == 'add')
{   
	$focus->add($_REQUEST['chart_type'],$_REQUEST['chart_id'],$_REQUEST['chart_index']);
}
else if ($_REQUEST['dashboard_action'] == 'arrange')
{   
	$focus->arrange(split('-',$_REQUEST['chartorder']));
}
header("Location: index.php?module=".$_REQUEST['return_module']."&action=".$_REQUEST['return_action']."&record=".$_REQUEST['return_id']);

exit;
?>
