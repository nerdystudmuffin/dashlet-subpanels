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


// Contact is used to store customer information.
class iFrame extends SugarBean
{
	// Stored fields
	var $id;
	var $url;
	var $name;
	var $deleted;
	var $status = 1;
	var $placement='' ;
	var $date_entered;
	var $created_by;
	var $type;
	var $date_modified;
	var $table_name = "iframes";
	var $object_name = "iFrame";
	var $module_dir = 'iFrames';
	var $new_schema = true;
 
	function iFrame()
	{
		parent::SugarBean();



	}

	function get_xtemplate_data(){
		$return_array = array();
		global $current_user;
		foreach($this->column_fields as $field)
		{
			$return_array[strtoupper($field)] = $this->$field;
		}
				if(is_admin($current_user)){
					$select = translate('DROPDOWN_PLACEMENT', 'iFrames');
					$return_array['PLACEMENT_SELECT'] = get_select_options_with_id($select, $return_array['PLACEMENT'] );
				}else{
					$select = translate('DROPDOWN_PLACEMENT', 'iFrames');
					$shortcut = array('shortcut'=> $select['shortcut']);
					$return_array['PLACEMENT_SELECT'] = get_select_options_with_id($shortcut, '');
				}

				if(is_admin($current_user)){
					$select = translate('DROPDOWN_TYPE', 'iFrames');
					$return_array['TYPE_SELECT'] = get_select_options_with_id($select, $return_array['TYPE'] );
				}else{
					$select = translate('DROPDOWN_TYPE', 'iFrames');
					$personal = array('personal'=> $select['personal']);
					$return_array['TYPE_SELECT'] = get_select_options_with_id($personal, '');
				}
				if(!empty($select[$return_array['PLACEMENT']])){
					$return_array['PLACEMENT'] = $select[$return_array['PLACEMENT']];
				}

		return $return_array;
	}

		function get_list_view_data()
	{
		$ret_array = parent::get_list_view_array();
		if(!empty($ret_array['STATUS']) && $ret_array['STATUS'] > 0){
			 $ret_array['STATUS'] = '<input type="checkbox" class="checkbox" style="checkbox" checked disabled>';
		}else{
			$ret_array['STATUS'] = '<input type="checkbox" class="checkbox" style="checkbox" disabled>'	;
		}
		if(strlen($ret_array['URL']) > 63){
			$ret_array['URL'] = substr($ret_array['URL'], 0, 50) . '...' . substr($ret_array['URL'],-10);
		}
		$ret_array['CREATED_BY'] = get_assigned_user_name($this->created_by);
		$ret_array['PLACEMENT'] = translate('DROPDOWN_PLACEMENT', 'iFrames', $ret_array['PLACEMENT']);
				$ret_array['TYPE'] = translate('DROPDOWN_TYPE', 'iFrames', $ret_array['TYPE']);
		return $ret_array;

	}



	function lookup_frames($placement){
			global $current_user;
			$frames = array();
			if(!empty($current_user->id)){
				$id = $current_user->id;
			}else{
			    if(!empty($GLOBALS['sugar_config']['login_nav'])){
			        $id = -1;
			    }else{
				    return $frames;
			    }
			}
			$query = 'SELECT placement,name,id,url from '  .$this->table_name . " WHERE deleted=0 AND status=1 AND (placement='$placement' OR placement='all') AND (type='global' OR (type='personal' AND created_by='$id')) ORDER BY iframes.name";
			$res = $this->db->query($query);
			
			while($row = $this->db->fetchByAssoc($res)){
				$frames[$row['name']] = array($row['id'], $row['url'], $row['placement'],"iFrames",$row['name']);
			}
			return $frames;

	}

		function lookup_frame_by_record_id($record_id){
			global $current_user;
			if(isset($current_user)){
				$id = $current_user->id;
			}else{
				$id = -1;
			}
			$query = 'SELECT placement,name,id,url from '  .$this->table_name . " WHERE id = '$record_id' and  deleted=0 AND status=1 AND (placement='tab' OR placement='all') AND (type='global' OR (type='personal' AND created_by='$id'))";
			$res = $this->db->query($query);
			$frames = array();
			while($row = $this->db->fetchByAssoc($res)){
				$frames[$row['name']] = array($row['id'], $row['url'], $row['placement'],"iFrames",$row['name']);
			}
			return $frames;

	}
    
    function create_export_query($order_by, $where) {
        global $current_user;
        $user_id = $current_user->id;
        $custom_join = $this->custom_fields->getJOIN(true, true,$where);
        $query = "SELECT iframes.*";
        if($custom_join){
            $query .= $custom_join['select'];
        }
        $query .= " FROM iframes ";
        if($custom_join){
            $query .= $custom_join['join'];
        }

        $where_auto = " iframes.deleted = 0 AND (type='personal' AND created_by='$user_id')";

        if ($where != "")
            $query .= " WHERE $where AND ".$where_auto;
        else
            $query .= " WHERE ".$where_auto;

        if ($order_by != "")
            $query .= " ORDER BY $order_by";
        else
            $query .= " ORDER BY iframes.name";

        return $query;
    }

}


?>
