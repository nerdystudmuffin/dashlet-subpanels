<?php
if (! defined ( 'sugarEntry' ) || ! sugarEntry)
	die ( 'Not A Valid Entry Point' ) ;
/**
 * Subpanel definition classes to ease the use of metadata/subpaneldefs.php
 *
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




//input
//	module directory
//constructor
//	open the layout_definitions file.
//
class aSubPanel
{
	
	var $name ;
	var $_instance_properties ;
	
	var $mod_strings ;
	var $panel_definition ;
	var $sub_subpanels ;
	var $parent_bean ;
	
	//module's table name and column fields.
	var $table_name ;
	var $db_fields ;
	var $bean_name ;
	var $template_instance ;
	
	function aSubPanel ( $name , $instance_properties , $parent_bean , $reload = false , $original_only = false )
	{
		
		$this->_instance_properties = $instance_properties ;
		$this->name = $name ;
		$this->parent_bean = $parent_bean ;
		
		//set language
		global $current_language ;
		if (! isset ( $parent_bean->mbvardefs ))
		{
			$mod_strings = return_module_language ( $current_language, $parent_bean->module_dir ) ;
		}
		$this->mod_strings = $mod_strings ;
		
		if ($this->isCollection ())
		{
			$this->load_sub_subpanels () ; //load sub-panel definition.
		} else
		{
			$def_path = 'modules/' . $this->_instance_properties [ 'module' ] . '/metadata/subpanels/' . $this->_instance_properties [ 'subpanel_name' ] . '.php' ;
			require ($def_path) ;
			
			if (! $original_only && isset ( $this->_instance_properties [ 'override_subpanel_name' ] ) && file_exists ( 'custom/modules/' . $this->_instance_properties [ 'module' ] . '/metadata/subpanels/' . $this->_instance_properties [ 'override_subpanel_name' ] . '.php' ))
			{
				$cust_def_path = 'custom/modules/' . $this->_instance_properties [ 'module' ] . '/metadata/subpanels/' . $this->_instance_properties [ 'override_subpanel_name' ] . '.php' ;
				
				require ($cust_def_path) ;
			}
			
			// check that the loaded subpanel definition includes a $subpanel_layout section - some, such as projecttasks/default do not...
			$this->panel_definition = array () ;
			if (isset($subpanel_layout))
			{
				$this->panel_definition = $subpanel_layout ;
				



			}
			$this->load_module_info () ; //load module info from the module's bean file.
		}
	
	}
	
	function distinct_query ()
	{
		if (isset ( $this->_instance_properties [ 'get_distinct_data' ] ))
		{
			
			if (! empty ( $this->_instance_properties [ 'get_distinct_data' ] ))
				return true ; else
				return false ;
		}
		return false ;
	}
	
	//return the translated header value.
	function get_title ()
	{
		if (empty ( $this->mod_strings [ $this->_instance_properties [ 'title_key' ] ] ))
		{
			return translate ( $this->_instance_properties [ 'title_key' ], $this->_instance_properties [ 'module' ] ) ;
		}
		return $this->mod_strings [ $this->_instance_properties [ 'title_key' ] ] ;
	}
	
	//return the definition of buttons. looks for buttons in 2 locations.
	function get_buttons ()
	{
		$buttons = array ( ) ;
		if (isset ( $this->_instance_properties [ 'top_buttons' ] ))
		{
			//this will happen only in the case of sub-panels with multiple sources(activities).
			$buttons = $this->_instance_properties [ 'top_buttons' ] ;
		} else
		{
			$buttons = $this->panel_definition [ 'top_buttons' ] ;
		}
		
		// permissions. hide SubPanelTopComposeEmailButton from activities if email module is disabled.
		//only email is  being tested becuase other submodules in activites/history such as notes, tasks, meetings and calls cannot be disabled.
		//as of today these are the only 2 sub-panels that use the union clause. 
		$mod_name = $this->get_module_name () ;
		if ($mod_name == 'Activities' or $mod_name = 'History')
		{
			global $modListHeader ;
			global $modules_exempt_from_availability_check ;
			if (isset ( $modListHeader ) && (! (array_key_exists ( 'Emails', $modListHeader ) or array_key_exists ( 'Emails', $modules_exempt_from_availability_check ))))
			{
				foreach ( $buttons as $key => $button )
				{
					foreach ( $button as $property => $value )
					{
						if ($value == 'SubPanelTopComposeEmailButton' or $value == 'SubPanelTopArchiveEmailButton')
						{
							//remove this button from the array.
							unset ( $buttons [ $key ] ) ;
						}
					}
				}
			}
		}
		
		return $buttons ;
	}
	
	//call this function for sub-panels that have unions.
	function load_sub_subpanels ()
	{
		
		global $modListHeader ;
		// added a check for security of tabs to see if an user has access to them
		// this prevents passing an "unseen" tab to the query string and pulling up its contents
		if (! isset ( $modListHeader ))
		{
			global $current_user ;
			if (isset ( $current_user ))
			{
				$modListHeader = query_module_access_list ( $current_user ) ;
			}
		}
		
		global $modules_exempt_from_availability_check ;
		
		if (empty ( $this->sub_subpanels ))
		{
			$panels = $this->get_inst_prop_value ( 'collection_list' ) ;
			foreach ( $panels as $panel => $properties )
			{
				if (array_key_exists ( $properties [ 'module' ], $modListHeader ) or array_key_exists ( $properties [ 'module' ], $modules_exempt_from_availability_check ))
				{
					$this->sub_subpanels [ $panel ] = new aSubPanel ( $panel, $properties, $this->parent_bean ) ;
				}
			}
		
		}
	}
	
	function isDatasourceFunction ()
	{
		if (strpos ( $this->get_inst_prop_value ( 'get_subpanel_data' ), 'function' ) === false)
		{
			return false ;
		}
		return true ;
	}
	function isCollection ()
	{
		if ($this->get_inst_prop_value ( 'type' ) == 'collection')
			return true ; else
			return false ;
	}
	
	//get value of a property defined at the panel instance level.
	function get_inst_prop_value ( $name )
	{
		if (isset ( $this->_instance_properties [ $name ] ))
			return $this->_instance_properties [ $name ] ; else
			return null ;
	}
	//get value of a property defined at the panel definition level.
	function get_def_prop_value ( $name )
	{
		if (isset ( $this->panel_definition [ $name ] ))
		{
			return $this->panel_definition [ $name ] ;
		} else
		{
			return null ;
		}
	}
	
	//if datasource is of the type function then return the function name
	//else return the value as is.
	function get_function_parameters ()
	{
		$parameters = array ( ) ;
		if ($this->isDatasourceFunction ())
		{
			$parameters = $this->get_inst_prop_value ( 'function_parameters' ) ;
		}
		return $parameters ;
	}
	
	function get_data_source_name ( $check_set_subpanel_data = false )
	{
		$prop_value = null ;
		if ($check_set_subpanel_data)
		{
			$prop_value = $this->get_inst_prop_value ( 'set_subpanel_data' ) ;
		}
		if (! empty ( $prop_value ))
		{
			return $prop_value ;
		} else
		{
			//fall back to default behavior.
		}
		if ($this->isDatasourceFunction ())
		{
			return (substr_replace ( $this->get_inst_prop_value ( 'get_subpanel_data' ), '', 0, 9 )) ;
		} else
		{
			return $this->get_inst_prop_value ( 'get_subpanel_data' ) ;
		}
	}
	
	//returns the where clause for the query.
	function get_where ()
	{
		return $this->get_def_prop_value ( 'where' ) ;
	}
	
	function is_fill_in_additional_fields ()
	{
		// do both. inst_prop returns values from metadata/subpaneldefs.php and def_prop returns from subpanel/default.php
		$temp = $this->get_inst_prop_value ( 'fill_in_additional_fields' ) || $this->get_def_prop_value ( 'fill_in_additional_fields' ) ;
		return $temp ;
	}
	
	function get_list_fields ()
	{
		if (isset ( $this->panel_definition [ 'list_fields' ] ))
		{
			return $this->panel_definition [ 'list_fields' ] ;
		} else
		{
			return array ( ) ;
		}
	}
	
	function get_module_name ()
	{
		return $this->get_inst_prop_value ( 'module' ) ;
	}
	
	function get_name ()
	{
	    return $this->name ;
	}
	
	//load subpanel mdoule's table name and column fields.
	function load_module_info ()
	{
		global $beanList ;
		global $beanFiles ;
		
		$module_name = $this->get_module_name () ;
		if (! empty ( $module_name ))
		{
			
			$bean_name = $beanList [ $this->get_module_name () ] ;
			
			$this->bean_name = $bean_name ;
			
			include_once ($beanFiles [ $bean_name ]) ;
			$this->template_instance = new $bean_name ( ) ;
			$this->template_instance->force_load_details = true ;
			$this->table_name = $this->template_instance->table_name ;
			//$this->db_fields=$this->template_instance->column_fields;
		}
	}
	//this function is to be used only with sub-panels that are based 
	//on collections.
	function get_header_panel_def ()
	{
		if (! empty ( $this->sub_subpanels ))
		{
			if (! empty ( $this->_instance_properties [ 'header_definition_from_subpanel' ] ) && ! empty ( $this->sub_subpanels [ $this->_instance_properties [ 'header_definition_from_subpanel' ] ] ))
			{
				return $this->sub_subpanels [ $this->_instance_properties [ 'header_definition_from_subpanel' ] ] ;
			} else
			{
				reset ( $this->sub_subpanels ) ;
				return current ( $this->sub_subpanels ) ;
			}
		}
		return null ;
	}
	
	/**
	 * Returns an array of current properties of the class.
	 * It will simply give the class name for instances of classes.
	 */
	function _to_array ()
	{
		return array ( '_instance_properties' => $this->_instance_properties , 'db_fields' => $this->db_fields , 'mod_strings' => $this->mod_strings , 'name' => $this->name , 'panel_definition' => $this->panel_definition , 'parent_bean' => get_class ( $this->parent_bean ) , 'sub_subpanels' => $this->sub_subpanels , 'table_name' => $this->table_name , 'template_instance' => get_class ( $this->template_instance ) ) ;
	}
}
;

class SubPanelDefinitions
{
	
	var $_focus ;
	var $_visible_tabs_array ;
	var $panels ;
	var $layout_defs ;
	
	/**
	 * Enter description here...
	 *
	 * @param BEAN $focus - this is the bean you want to get the data from
	 * @param STRING $layout_def_key - if you wish to use a layout_def defined in the default metadata/subpaneldefs.php that is not keyed off of $bean->module_dir pass in the key here
	 * @param ARRAY $layout_def_override - if you wish to override the default loaded layout defs you pass them in here.
	 * @return SubPanelDefinitions
	 */
	function SubPanelDefinitions ( $focus , $layout_def_key = '' , $layout_def_override = '' )
	{
		$this->_focus = $focus ;
		if (! empty ( $layout_def_override ))
		{
			$this->layout_defs = $layout_def_override ;
		
		} else
		{
			$this->open_layout_defs ( false, $layout_def_key ) ;
		}
	}
	
	/**
	 * This function returns an ordered list of the tabs.
	 */
	function get_available_tabs ($FromGetModuleSubpanels=false)
	{
		global $modListHeader ;
		global $modules_exempt_from_availability_check ;
		
		if (isset ( $this->_visible_tabs_array ))
			return $this->_visible_tabs_array ;
		
		if (empty($modListHeader))
		    $modListHeader = query_module_access_list($GLOBALS['current_user']); 	
		$this->_visible_tabs_array = array ( ) ; // bug 16820 - make sure this is an array for the later ksort
		if (isset ( $this->layout_defs [ 'subpanel_setup' ] )) // bug 17434 - belts-and-braces - check that we have some subpanels first
		{
			foreach ( $this->layout_defs [ 'subpanel_setup' ] as $key => $values_array )
			{
				//check permissions.
				

				$result = '' ;
				if (array_key_exists ( $values_array [ 'module' ], $modules_exempt_from_availability_check ))
					$result .= 'exempt ' ;
				if (array_key_exists ( $values_array [ 'module' ], $modListHeader ))
				{
					$result .= "not exempt " ;
					if (! ACLController::moduleSupportsACL ( $values_array [ 'module' ] ) || ACLController::checkAccess ( $values_array [ 'module' ], 'list', true ))
					{
						$result .= "ACL ok" ;
					}
				}
				$GLOBALS [ 'log' ]->debug ( "SubPanelDefinitions->get_available_tabs(): " . $key . "=" . $result ) ;
				
				if (array_key_exists ( $values_array [ 'module' ], $modules_exempt_from_availability_check ) or (! ACLController::moduleSupportsACL ( $values_array [ 'module' ] ) || ACLController::checkAccess ( $values_array [ 'module' ], 'list', true )))
				{
					while ( ! empty ( $this->_visible_tabs_array [ $values_array [ 'order' ] ] ) )
					{
						$values_array [ 'order' ] ++ ;
					}
				    if($FromGetModuleSubpanels){
                        $this->_visible_tabs_array [$values_array ['order']] = array($key=>$values_array['title_key']);
                    }
                    else{
                        $this->_visible_tabs_array [$values_array ['order']] = $key;
                    }
				}
			}
		}
		//		$GLOBALS['log']->debug("SubPanelDefinitions->get_available_tabs(): visible_tabs_array = ".print_r($this->_visible_tabs_array,true));							
		ksort ( $this->_visible_tabs_array ) ;
		return $this->_visible_tabs_array ;
	}
	
	/**
	 * Load the definition of the a sub-panel.
	 * Also the sub-panel is added to an array of sub-panels.
	 * use of reload has been deprecated, since the subpanel is initialized every time.
	 */
	function load_subpanel ( $name , $reload = false , $original_only = false )
	{
		$panel = new aSubPanel ( $name, $this->layout_defs [ 'subpanel_setup' ] [ strtolower ( $name ) ], $this->_focus, $reload, $original_only ) ;
		return $panel ;
	}
	
	/**
	 * Load the layout def file and associate the definition with a variable in the file.
	 */
	function open_layout_defs ( $reload = false , $layout_def_key = '' , $original_only = false )
	{
		$layout_defs [ $this->_focus->module_dir ] = array ( ) ;
		$layout_defs [ $layout_def_key ] = array ( ) ;
		if (empty ( $this->layout_defs ) || $reload || (! empty ( $layout_def_key ) && ! isset ( $layout_defs [ $layout_def_key ] )))
		{
			if (file_exists ( 'modules/' . $this->_focus->module_dir . '/metadata/subpaneldefs.php' ))
			{
				require ('modules/' . $this->_focus->module_dir . '/metadata/subpaneldefs.php') ;
			}
			if (! $original_only && file_exists ( 'custom/modules/' . $this->_focus->module_dir . '/Ext/Layoutdefs/layoutdefs.ext.php' ))
			{
				
				require ('custom/modules/' . $this->_focus->module_dir . '/Ext/Layoutdefs/layoutdefs.ext.php') ;
			
			}
			
			if (! empty ( $layout_def_key ))
			{
				$this->layout_defs = $layout_defs [ $layout_def_key ] ;
			} else if (isset($_REQUEST['subpanel']) && !empty($_REQUEST['subpanel']) && $_REQUEST['subpanel'] == 'aclroles' && $this->_focus->module_dir == 'Users')
			{
				$this->layout_defs = $layout_defs['UserRoles'];
			} else
			{
				$this->layout_defs = $layout_defs [ $this->_focus->module_dir ] ;
			}
		
		}
	
	}
	
	/**
	 * Removes a tab from the list of loaded tabs.
	 * Returns true if successful, false otherwise.
	 * Hint: Used by Campaign's DetailView.
	 */
	function exclude_tab ( $tab_name )
	{
		$result = false ;
		//unset layout definition
		if (! empty ( $this->layout_defs [ 'subpanel_setup' ] [ $tab_name ] ))
		{
			unset ( $this->layout_defs [ 'subpanel_setup' ] [ $tab_name ] ) ;
		}
		//unset instance from _visible_tab_array
		if (! empty ( $this->_visible_tabs_array ))
		{
			$key = array_search ( $tab_name, $this->_visible_tabs_array ) ;
			if ($key !== false)
			{
				unset ( $this->_visible_tabs_array [ $key ] ) ;
			}
		}
		return $result ;
	}
}
?>
