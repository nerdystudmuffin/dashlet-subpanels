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
if(!empty($_SESSION['distribute_where']) && !empty($_REQUEST['distribute_method']) && !empty($_REQUEST['users']) && !empty($_REQUEST['use'])) {
	
	$focus = new Email();
		
	$emailIds = array();
	// CHECKED ONLY:
	if($_REQUEST['use'] == 'checked') {
		// clean up passed array
		$grabEx = explode('::',$_REQUEST['grabbed']);
		foreach($grabEx as $k => $emailId) {
			if($emailId != "undefined") {
				$emailIds[] = $emailId;
			}
		}
		
		// we have users and the items to distribute	
		if($_REQUEST['distribute_method'] == 'roundRobin') {
			if($focus->distRoundRobin($_REQUEST['users'], $emailIds)) {
				header('Location: index.php?module=Emails&action=ListViewGroup');
			}	
		} elseif($_REQUEST['distribute_method'] == 'leastBusy') {
			if($focus->distLeastBusy($_REQUEST['users'], $emailIds)) {
				header('Location: index.php?module=Emails&action=ListViewGroup');
			}
		} elseif($_REQUEST['distribute_method'] == 'direct') {
			// direct assignment
//			_ppd('count:'.count($_REQUEST['users']));
			if(count($_REQUEST['users']) > 1) {
				// only 1 user allowed in direct assignment
				$error = 1;
			} else {
				$user = $_REQUEST['users'][0];
				if($focus->distDirect($user, $emailIds)) {
					header('Location: index.php?module=Emails&action=ListViewGroup');
				}
			}
			
			header('Location: index.php?module=Emails&action=ListViewGroup&error='.$error);
		}
	} elseif($_REQUEST['use'] == 'all') {
		if($_REQUEST['distribute_method'] == 'direct') {
			// no ALL assignments to 1 user
			header('Location: index.php?module=Emails&action=ListViewGroup&error=2');
		}
		
		// we have the where clause that generated the view above, so use it
		$q = 'SELECT emails.id FROM emails WHERE '.$_SESSION['distribute_where'];
		$q = str_replace('&#039;', '"', $q);
		$r = $focus->db->query($q);
		$count = 0;
		while($a = $focus->db->fetchByAssoc($r)) {
			$emailIds[] = $a['id'];
			$count++;
		}
		// we have users and the items to distribute	
		if($_REQUEST['distribute_method'] == 'roundRobin') {
			if($focus->distRoundRobin($_REQUEST['users'], $emailIds)) {
				header('Location: index.php?module=Emails&action=ListViewGroup');
			}
		} elseif($_REQUEST['distribute_method'] == 'leastBusy') {
			if($focus->distLeastBusy($_REQUEST['users'], $emailIds)) {
				header('Location: index.php?module=Emails&action=ListViewGroup');
			}
		}
		
		if($count < 1) {
			$GLOBALS['log']->info('Emails distribute failed: query returned no results ('.$q.')');
			header('Location: index.php?module=Emails&action=ListViewGroup&error='.$error);
		}
	}

} else {
	// error
	header('Location: index.php?module=Emails&action=index');
}

?>
