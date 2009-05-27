<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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



require_once('include/ListView/ListViewData.php');
require_once('include/MassUpdate.php');

class ListViewDisplay {

	var $show_mass_update_form = false;
	var $rowCount;
	var $mass = null;
	var $seed;
	var $multi_select_popup;
	var $lvd;
	var $moduleString;
	var $export = true;
	var $multiSelect = true;
	var $mailMerge = true;
	var $should_process = true;

	/**
	 * Constructor
	 * @return null
	 */
	function ListViewDisplay() {
		$this->lvd = new ListViewData();
		$this->searchColumns = array () ;
	}
	function shouldProcess($moduleDir){
		if(!empty($GLOBALS['sugar_config']['save_query']) && $GLOBALS['sugar_config']['save_query'] == 'populate_only'){
		    if(empty($GLOBALS['displayListView']) && strcmp(strtolower($_REQUEST['action']), 'popup') != 0 &&  (!empty($_REQUEST['clear_query']) || $_REQUEST['module'] == $moduleDir && ((empty($_REQUEST['query']) || $_REQUEST['query'] == 'MSI' )&& (empty($_SESSION['last_search_mod']) || $_SESSION['last_search_mod'] != $moduleDir ) ))){
				$_SESSION['last_search_mod'] = $_REQUEST['module'] ;
				$this->should_process = false;
				return false;
			}
		}
		$this->should_process = true;
		return true;
	}

	/**
	 * Setup the class
	 * @param seed SugarBean Seed SugarBean to use
	 * @param file File Template file to use
	 * @param string $where
	 * @param offset:0 int offset to start at
	 * @param int:-1 $limit
	 * @param string[]:array() $filter_fields
	 * @param array:array() $params
	 * 	Potential $params are
		$params['distinct'] = use distinct key word
		$params['include_custom_fields'] = (on by default)
		$params['massupdate'] = true by default;
        $params['handleMassupdate'] = true by default, have massupdate.php handle massupdates?
	 * @param string:'id' $id_field
	 */
	function setup($seed, $file, $where, $params = array(), $offset = 0, $limit = -1,  $filter_fields = array(), $id_field = 'id') {
        $this->should_process = true;
        if(isset($seed->module_dir) && !$this->shouldProcess($seed->module_dir)){
        		return false;
        }
        if(isset($params['export'])) {
          $this->export = $params['export'];
        }
        if(!empty($params['multiSelectPopup'])) {
		  $this->multi_select_popup = $params['multiSelectPopup'];
        }
		if(!empty($params['massupdate']) && $params['massupdate'] != false) {
			$this->show_mass_update_form = true;
			$this->mass = new MassUpdate();
			$this->mass->setSugarBean($seed);
			if(!empty($params['handleMassupdate']) || !isset($params['handleMassupdate'])) {
                $this->mass->handleMassUpdate();
            }
		}
		$this->seed = $seed;

        // create filter fields based off of display columns
        if(empty($filter_fields)) {
            foreach($this->displayColumns as $columnName => $def) {
            $filter_fields[strtolower($columnName)] = true;
               if(!empty($def['related_fields'])) {
                    foreach($def['related_fields'] as $field) {
                        //id column is added by query construction function. This addition creates duplicates
                        //and causes issues in oracle. #10165
                        if ($field != 'id') {
                            $filter_fields[$field] = true;
                        }
                    }
                }
            }
            foreach ($this->searchColumns as $columnName => $def )
            {
                $filter_fields[strtolower($columnName)] = true;
            }
        }










        $data = $this->lvd->getListViewData($seed, $where, $offset, $limit, $filter_fields, $params, $id_field);
		foreach($this->displayColumns as $columnName => $def)
		{
			$seedName =  strtolower($columnName);
			if(empty($this->displayColumns[$columnName]['type']))
			{
		        if(!empty($this->lvd->seed->field_defs[$seedName]['type'])){
		               $seedDef = $this->lvd->seed->field_defs[$seedName];
		                $this->displayColumns[$columnName]['type'] = (!empty($seedDef['custom_type']))?$seedDef['custom_type']:$seedDef['type'];
				if(!empty($seedDef['options'])){
					$this->displayColumns[$columnName]['options'] = $seedDef['options'];
				}
		        //C.L. Fix for 11177
		        if($this->displayColumns[$columnName]['type'] == 'html') {
		            $cField = $this->seed->custom_fields;
		               if(isset($cField) && isset($cField->avail_fields[$seedName]['ext4'])) {
		                 	$seedName2 = strtoupper($columnName);
		                 	$htmlDisplay = html_entity_decode($cField->avail_fields[$seedName]['ext4']);
		                 	$count = 0;
		                 	while($count < count($data['data'])) {
		                 		$data['data'][$count][$seedName2] = &$htmlDisplay;
		                 	    $count++;
		                 	}
		            	}
		        }
		        }else{
		        	$this->displayColumns[$columnName]['type'] = '';
		        }
			}
			if (!empty($this->lvd->seed->field_defs[$seedName]['sort_on'])) {
		    	$this->displayColumns[$columnName]['orderBy'] = $this->lvd->seed->field_defs[$seedName]['sort_on'];
		    }
		}

		$this->process($file, $data, $seed->object_name);
		return true;
	}

	/**
	 * Any additional processing
	 * @param file File template file to use
	 * @param data array row data
	 * @param html_var string html string to be passed back and forth
	 */
	function process($file, $data, $htmlVar) {
		$this->rowCount = count($data['data']);
		$this->moduleString = $data['pageData']['bean']['moduleDir'] . '2_' . strtoupper($htmlVar) . '_offset';
	}

	/**
	 * Display the listview
	 * @return string ListView contents
	 */
	function display() {
		if(!$this->should_process) return '';
		$str = '';
		if($this->multiSelect == true && $this->show_mass_update_form)
			$str = $this->mass->getDisplayMassUpdateForm(true, $this->multi_select_popup).$this->mass->getMassUpdateFormHeader($this->multi_select_popup);

        return $str;
	}
	/**
	 * Display the select link
     * @return string select link html
	 * @param echo Bool set true if you want it echo'd, set false to have contents returned
	 */
	function buildSelectLink($id = 'select_link', $total=0, $pageTotal=0) {
		global $app_strings;
		if ($pageTotal < 0)
			$pageTotal = $total;
		$script = "<script>
			function select_overlib() {
				return overlib('<a style=\'width: 150px\' class=\'menuItem\' onmouseover=\'hiliteItem(this,\"yes\");\' onmouseout=\'unhiliteItem(this);\' onclick=\'if (document.MassUpdate.select_entire_list.value==1){document.MassUpdate.select_entire_list.value=0;sListView.check_all(document.MassUpdate, \"mass[]\", true, $pageTotal)}else {sListView.check_all(document.MassUpdate, \"mass[]\", true)};\' href=\'#\'>{$app_strings['LBL_LISTVIEW_OPTION_CURRENT']}&nbsp;({$pageTotal})</a>"
			. "<a style=\'width: 150px\' class=\'menuItem\' onmouseover=\'hiliteItem(this,\"yes\");\' onmouseout=\'unhiliteItem(this);\' onclick=\'sListView.check_entire_list(document.MassUpdate, \"mass[]\",true,{$total});\' href=\'#\'>{$app_strings['LBL_LISTVIEW_OPTION_ENTIRE']}&nbsp;({$total})</a>"
			. "<a style=\'width: 150px\' class=\'menuItem\' onmouseover=\'hiliteItem(this,\"yes\");\' onmouseout=\'unhiliteItem(this);\' onclick=\'sListView.clear_all(document.MassUpdate, \"mass[]\", false);\' href=\'#\'>{$app_strings['LBL_LISTVIEW_NONE']}</a>"
			. "', CENTER, '"
			. "', STICKY, MOUSEOFF, 3000, CLOSETEXT, '<img border=0 src=" . SugarThemeRegistry::current()->getImageURL('close_inline.gif')
			. ">', WIDTH, 150, CLOSETITLE, '" . $app_strings['LBL_ADDITIONAL_DETAILS_CLOSE_TITLE'] . "', CLOSECLICK, FGCLASS, 'olOptionsFgClass', "
			. "CGCLASS, 'olOptionsCgClass', BGCLASS, 'olBgClass', TEXTFONTCLASS, 'olFontClass', CAPTIONFONTCLASS, 'olOptionsCapFontClass', CLOSEFONTCLASS, 'olOptionsCloseFontClass');
			}
			</script>";
            $script .= "<a id='$id' onclick='return select_overlib();' href=\"#\">".$app_strings['LBL_LINK_SELECT']."&nbsp;<img src='".SugarThemeRegistry::current()->getImageURL('MoreDetail.png')."' width='8' height='7' border='0''>"."</a>";

		return $script;
	}
	/**
	 * Display the export link
     * @return string export link html
	 * @param echo Bool set true if you want it echo'd, set false to have contents returned
	 */
	function buildExportLink($id = 'export_link')  {
		global $app_strings;
		$script = '<input class="button" type="button" value="'.$app_strings['LBL_EXPORT'].'" ' .
				'onclick="return sListView.send_form(true, \''.$_REQUEST['module'].'\', \'index.php?entryPoint=export\',\''.$app_strings['LBL_LISTVIEW_NO_SELECTED'].'\')">';
		return $script;
	}

	function buildComposeEmailLink($totalCount) {
		global $app_strings;
		$userPref = $GLOBALS['current_user']->getPreference('email_link_type');
		$defaultPref = $GLOBALS['sugar_config']['email_default_client'];
		if($userPref != '') {
			$client = $userPref;
		} else {
			$client = $defaultPref;
		}
		if($client == 'sugar') {
			$script = '<input class="button" type="button" value="'.$app_strings['LBL_EMAIL_COMPOSE'].'" ' .
					'onclick="return sListView.send_form_for_emails(true, \''."Emails".'\', \'index.php?module=Emails&action=Compose&ListView=true\',\''.$app_strings['LBL_LISTVIEW_NO_SELECTED'].'\', \''.$_REQUEST['module'].'\', \''.$totalCount.'\', \''.$app_strings['LBL_LISTVIEW_LESS_THAN_TEN_SELECT'].'\')">';				
		} else {
			$script = '<input class="button" type="button" value="'.$app_strings['LBL_EMAIL_COMPOSE'].'" ' .
					'onclick="return sListView.use_external_mail_client(\''.$app_strings['LBL_LISTVIEW_NO_SELECTED'].'\');">';			
		}
		return $script;
	} // fn

	function buildFavoritesLink()  {
		global $app_strings;

		$script ="<input type='submit' name='Mark as Favorites' value='".$app_strings['LBL_MARK_AS_FAVORITES']."' onclick='this.form.massupdate.value = false; this.form.action.value = this.form.return_action.value; this.form.addtofavorites.value = true;' class='button'>";
		return $script;
	}
	function buildRemoveFavoritesLink()  {
		global $app_strings;

		$script = "<input type='submit' name='Add to Favorites' value='".$app_strings['LBL_REMOVE_FROM_FAVORITES']."' onclick='this.form.massupdate.value = false; this.form.action.value = this.form.return_action.value; this.form.removefromfavorites.value = true;' class='button'>";
		return $script;
	}

	function buildDeleteLink() {
		global $app_strings;
		//$string = '<input class="button" type="button" value="'.$app_strings['LBL_DELETE_BUTTON_LABEL'].'" onclick="return confirm(\'' . $app_strings['NTC_DELETE_CONFIRMATION_MULTIPLE'] . '\') && sListView.send_mass_update(\'selected\', \'Please select at least 1 record to proceed.\', 1)">';
		$string = '<input class="button" type="button" value="'.$app_strings['LBL_DELETE_BUTTON_LABEL'].'" onclick="return sListView.send_mass_update(\'selected\', \''.$app_strings['LBL_LISTVIEW_NO_SELECTED'].'\', 1)">';
		return $string;

	}
	/**
	 * Display the selected object span object
	 *
     * @return string select object span
	 */
	function buildSelectedObjectsSpan($echo = true, $total=0) {
		global $app_strings;

		$selectedObjectSpan = "{$app_strings['LBL_LISTVIEW_SELECTED_OBJECTS']}<input  style='border: 0px; background: transparent; font-size: inherit; color: inherit' type='text' id='selectCountTop' readonly name='selectCount[]' value='{$total}' />";

        return $selectedObjectSpan;
	}

    /**
     * Display merge duplicate links. The link can be disabled by setting module level duplicate_merge property to false
     * in the moudle's vardef file.
     */
     function buildMergeDuplicatesLink() {
        global $app_strings, $dictionary;
        $return_string='';

        $return_string.= isset($_REQUEST['module']) ? "&return_module={$_REQUEST['module']}" : "";
        $return_string.= isset($_REQUEST['action']) ? "&return_action={$_REQUEST['action']}" : "";
        $return_string.= isset($_REQUEST['record']) ? "&return_id={$_REQUEST['record']}" : "";
        //need delete and edit access.
		if (!(ACLController::checkAccess( $_REQUEST['module'], 'edit', true)) or !(ACLController::checkAccess( $_REQUEST['module'], 'delete', true))) {
			return '';
		}

        if (isset($dictionary[$this->seed->object_name]['duplicate_merge']) && $dictionary[$this->seed->object_name]['duplicate_merge']==true ) {
         //onclick='if (sugarListView.get_checks_count()> 1) {sListView.send_form(true, \"MergeRecords\", \"index.php\", \"{$app_strings['LBL_LISTVIEW_NO_SELECTED']}\", \"{$this->seed->module_dir}\",\"$return_string\");} else {alert(\"{$app_strings['LBL_LISTVIEW_TWO_REQUIRED']}\");return false;}' href=\"#\">".$app_strings['LBL_MERGE_DUPLICATES']."</a>");
			return "<input id='mergeduplicates_link' class='button' type='button'  ".
				"onclick='if (sugarListView.get_checks_count()> 1) {sListView.send_form(true, \"MergeRecords\", \"index.php\", \"{$app_strings['LBL_LISTVIEW_NO_SELECTED']}\", \"{$this->seed->module_dir}\",\"$return_string\");} else {alert(\"{$app_strings['LBL_LISTVIEW_TWO_REQUIRED']}\");return false;}'	".
					'value="'.$app_strings['LBL_MERGE_DUPLICATES'].'">';
        }
        else
        	return '';


     }
	/**
	 * Display the mail merge link
	 * @param echo Bool set true if you want it echo'd, set false to have contents returned
	 */
	function buildMergeLink() {
        
        require_once('modules/MailMerge/modules_array.php');
        global $current_user, $app_strings;

        $admin = new Administration();
        $admin->retrieveSettings('system');
        $user_merge = $current_user->getPreference('mailmerge_on');
       	$module_dir = (!empty($this->seed->module_dir) ? $this->seed->module_dir : '');
        $str = '';

        if ($user_merge == 'on' && isset($admin->settings['system_mailmerge_on']) && $admin->settings['system_mailmerge_on'] && !empty($modules_array[$module_dir])) {
            $str = '<input class="button" type="button" value="'.$app_strings['LBL_MAILMERGE'].'" ' .
					'onclick="if (document.MassUpdate.select_entire_list.value==1){document.location.href=\'index.php?action=index&module=MailMerge&entire=true\'} else {return sListView.send_form(true, \'MailMerge\',\'index.php\',\''.$app_strings['LBL_LISTVIEW_NO_SELECTED'].'\');}">';
        }
        return $str;
	}

	function buildTargetList() {
		$js = <<<EOF
			if ( document.forms['targetlist_form'] ) {
				var form = document.forms['targetlist_form'];
				form.reset;
			} else
				var form = document.createElement ( 'form' ) ;
			form.setAttribute ( 'name' , 'targetlist_form' );
			form.setAttribute ( 'method' , 'post' ) ;
			form.setAttribute ( 'action' , 'index.php' );
			document.body.appendChild ( form ) ;
			form.innerHTML = '<input type=\'hidden\' name=\'module\' value=\'{$_REQUEST['module']}\' /><input type=\'hidden\' name=\'action\' value=\'TargetListUpdate\' /><input type=\'hidden\' name=\'uids\' /><input type=\'hidden\' name=\'prospect_list\' /><input type=\'hidden\' name=\'return_module\' /><input type=\'hidden\' name=\'return_action\' />';
			open_popup('ProspectLists','600','400','',true,false,{ 'call_back_function':'set_return_and_save_targetlist','form_name':'targetlist_form','field_to_name_array':{'id':'prospect_list'} } );
EOF;
        return '<input class="button" type="button" value="'.$GLOBALS['app_strings']['LBL_ADD_TO_PROSPECT_LIST_BUTTON_LABEL'].'" onclick="'.$js.'" />' ;
	}

	/**
	 * Display the bottom of the ListView (ie MassUpdate
	 * @return string contents
	 */
	function displayEnd() {
		$str = '';
		if($this->show_mass_update_form) {
			$str .= $this->mass->getMassUpdateForm();
			$str .= $this->mass->endMassUpdateForm();
		}

		return $str;
	}

    /**
     * Display the multi select data box etc.
     * @return string contents
     */
	function getMultiSelectData() {
		$str = "<script>YAHOO.util.Event.addListener(window, \"load\", sListView.check_boxes);</script>\n";

		$massUpdateRun = isset($_REQUEST['massupdate']) && $_REQUEST['massupdate'] == 'true';
		$uids = empty($_REQUEST['uid']) || $massUpdateRun ? '' : $_REQUEST['uid'];
		$select_entire_list = isset($_REQUEST['select_entire_list']) && !$massUpdateRun ? $_REQUEST['select_entire_list'] : 0;

		$str .= "<textarea style='display: none' name='uid'>{$uids}</textarea>\n" .
				"<input type='hidden' name='select_entire_list' value='{$select_entire_list}'>\n".
				"<input type='hidden' name='{$this->moduleString}' value='0'>\n";
		return $str;
	}

}
?>
