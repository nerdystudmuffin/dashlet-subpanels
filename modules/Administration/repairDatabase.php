<?php
if (!defined('sugarEntry') || !sugarEntry)
	die('Not A Valid Entry Point');
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

global $current_user, $beanFiles;
set_time_limit(3600);


$db = & DBManagerFactory :: getInstance();

if (is_admin($current_user) || isset ($from_sync_client)) {
	isset($_REQUEST['execute'])? $execute=$_REQUEST['execute'] : $execute= false;
	$export = false;

	if (sizeof($_POST)) {
		if (isset ($_POST['raction']) && strtolower($_POST['raction']) == "export") {
			//jc - output buffering is being used. if we do not clean the output buffer
			//the contents of the buffer up to the length of the repair statement(s)
			//will be saved in the file...
			ob_clean();

			header("Content-Disposition: attachment; filename=repairSugarDB.sql");
			header("Content-Type: text/sql; charset={$app_strings['LBL_CHARSET']}");
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Content-Length: " . strlen($_POST['sql']));

      //jc:7347 - for whatever reason, html_entity_decode is choking on converting
      //the html entity &#039; to a single quote, so we will use str_replace
      //instead
      $sql = str_replace(
        '&#039;',
        "'",
        $_POST['sql']
      );
      //echo html_entity_decode($_POST['sql']);
      echo $sql;
			die();
		}
		elseif (isset ($_POST['raction']) && strtolower($_POST['raction']) == "execute") {
			$sql = str_replace(
				array(
					"\n",
					'&#039;',
				),
				array(
					'',
					"'",
				),
				preg_replace('#(/\*.+?\*/\n*)#', '', $_POST['sql'])
			);
			foreach (split(";", $sql) as $stmt) {
				$stmt = trim($stmt);

				if (!empty ($stmt)) {
					$db->query($stmt,true,'Executing repair query: ');
				}
			}

			echo "<h3>{$mod_strings['LBL_REPAIR_DATABASE_SYNCED']}</h3>";
		}
		die();
	} else {

		if (!$export && empty ($_REQUEST['repair_silent'])) {
			echo get_module_title($mod_strings['LBL_REPAIR_DATABASE'], $mod_strings['LBL_REPAIR_DATABASE'], true);
			echo "<h1 id=\"rdloading\">{$mod_strings['LBL_REPAIR_DATABASE_PROCESSING']}</h1>";
			ob_flush();
		}

		$sql = '';

		VardefManager::clearVardef();

		foreach ($beanFiles as $bean => $file) {
			require_once ($file);
			$focus = new $bean ();
			$sql .= $db->repairTable($focus, $execute);

		}

		$olddictionary = $dictionary;

		unset ($dictionary);
		include ('modules/TableDictionary.php');

		foreach ($dictionary as $meta) {
			$tablename = $meta['table'];
			$fielddefs = $meta['fields'];
			$indices = $meta['indices'];
			$sql .= $db->repairTableParams($tablename, $fielddefs, $indices, $execute);
		}

		$dictionary = $olddictionary;

		echo "<script type=\"text/javascript\">document.getElementById('rdloading').style.display = \"none\";</script>";

		if (empty ($_REQUEST['repair_silent'])) {

			if (isset ($sql) && !empty ($sql)) {

				$qry_str = "";
				foreach (split("\n", $sql) as $line) {
					if (!empty ($line) && substr($line, -2) != "*/") {
						$line .= ";";
					}

					$qry_str .= $line . "\n";
				}

				echo "<h3>{$mod_strings['LBL_REPAIR_DATABASE_DIFFERENCES']}</h3>";
				echo "<p>{$mod_strings['LBL_REPAIR_DATABASE_TEXT']}</p>";

				echo "<form method=\"post\" action=\"index.php?module=Administration&amp;action=repairDatabase\">";
				echo "<textarea name=\"sql\" rows=\"24\" cols=\"150\" id=\"repairsql\">$qry_str</textarea>";
				echo "<input type=\"hidden\" name=\"raction\" id=\"raction\"/>";
				echo "<br /><input type=\"submit\" value=\"" . $mod_strings['LBL_REPAIR_DATABASE_EXECUTE'] . "\" onclick=document.getElementById('raction').value='Execute' /> " .
                     "<input type=\"submit\" value=\"" . $mod_strings['LBL_REPAIR_DATABASE_EXPORT'] . "\" onclick=document.getElementById('raction').value='Export' />";
			} else {
				echo "<h3>{$mod_strings['LBL_REPAIR_DATABASE_SYNCED']}</h3>";
			}
		}
	}

} else {
	die('Admin Only Section');
}
