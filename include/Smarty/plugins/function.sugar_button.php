<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * This is a Smarty plugin to handle the creation of HTML form buttons under the
 * metat-data framework.  The buttons may be defined using either the set of built-in
 * buttons or custom code.
 * 
 * For example, to add the 'SAVE' and 'CANCEL' buttons to the editviewdefs.php meta-data file,
 * you will create a key/value pair where the key is of value 'form' and value is another array
 * with a 'buttons' key. 
 * 
 * ...
 * $viewdefs['Accounts']['EditView'] = array(
 * 'templateMeta' => array(
 *                           'form' => array('buttons'=>array('SAVE','CANCEL')),
 * ...
 * 
 * The supported types are: CANCEL, DELETE, DUPLICATE, EDIT, FIND_DUPLICATES and SAVE. 
 * If you need to create a custom button or the button is very specific to the module and not
 * provided as a supported type, then you'll need to use custom code.  Instead of providing the
 * key, you'll have to create an array with a 'customCode' key.  
 * 
 * ...
 * $viewdefs['Accounts']['EditView'] = array(
 * 'templateMeta' => array(
 *	'form' => array('buttons'=>array('SAVE',
 *	                                 array('customCode'=>'<input title="{$APP.LBL_CLOSE_AND_CREATE_BUTTON_TITLE}" ' .
 *	                                 		'                    accessKey="{$APP.LBL_CLOSE_AND_CREATE_BUTTON_KEY}" ' .
 *	                                 		'                    class="button" ' .
 *	                                 		'					 onclick="alert(\'hello {$id} \')"; ' .
 *	                                 		'                    type="submit" ' .
 *	                                 		'                    name="button" ' .
 *	                                 		'                    value="{$APP.LBL_CLOSE_AND_CREATE_BUTTON_LABEL}">')
 *	                                 )),
 * ...
 * 
 * Please note that you should ensure that your customCode is generic in the sense that there are no 
 * instance specific values created because it will cause failures should other instances also use
 * the button's code.  The key to remember is that we are rendering a generic template for each
 * module's view and, as such, the meta-data definition should also be generic enough to support
 * variable instance values for the module.
 * 
 * In our examples, the resulting metatdata definition is passed to EditView's header.tpl 
 * file and the Smarty plugin (this file) is invoked as follows:
 * {{sugar_button module='{{$module}}' id='{{$form.buttons[$id]}}' view='EditView'}}
 * 
 * 
 * @author Collin Lee {clee@sugarcrm.com}
 */
 
/**
 * smarty_function_sugar_button
 * This is the constructor for the Smarty plugin.
 * 
 * @param $params The runtime Smarty key/value arguments
 * @param $smarty The reference to the Smarty object used in this invocation 
 */
function smarty_function_sugar_button($params, &$smarty)
{
   if(empty($params['module'])) {
   	  $smarty->trigger_error("sugar_button: missing required param (module)");
   } else if(empty($params['id'])) {
   	  $smarty->trigger_error("sugar_button: missing required param (id)");
   } else if(empty($params['view'])) {
   	  $smarty->trigger_error("sugar_button: missing required param (view)");
   }

   
   
   $type = $params['id'];
   if(!is_array($type)) {
   	  $module = $params['module'];
   	  $view = $params['view'];
   	  switch(strtoupper($type)) {
			case "CANCEL":
			$cancelButton  = '{if !empty($smarty.request.return_action) && ($smarty.request.return_action == "DetailView" && !empty($fields.id.value))}';
			$cancelButton .= '<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="button" onclick="this.form.action.value=\'DetailView\'; this.form.module.value=\'{$smarty.request.return_module}\'; this.form.record.value=\'{$smarty.request.return_id}\';" type="submit" name="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}"> ';
			$cancelButton .= '{elseif !empty($smarty.request.return_action) && ($smarty.request.return_action == "DetailView" && !empty($smarty.request.return_id))}';
			$cancelButton .= '<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="button" onclick="this.form.action.value=\'DetailView\'; this.form.module.value=\'{$smarty.request.return_module}\'; this.form.record.value=\'{$smarty.request.return_id}\';" type="submit" name="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}"> ';
			$cancelButton .= '{else}';
			$cancelButton .= '<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="button" onclick="this.form.action.value=\'index\'; this.form.module.value=\'{$smarty.request.return_module}\'; this.form.record.value=\'{$smarty.request.return_id}\';" type="submit" name="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}"> ';      
			$cancelButton .= '{/if}';
			return $cancelButton;
			break;
			
			case "DELETE":
			return '{if $bean->aclAccess("delete")}<input title="{$APP.LBL_DELETE_BUTTON_TITLE}" accessKey="{$APP.LBL_DELETE_BUTTON_KEY}" class="button" onclick="this.form.return_module.value=\'' . $module . '\'; this.form.return_action.value=\'ListView\'; this.form.action.value=\'Delete\'; return confirm(\'{$APP.NTC_DELETE_CONFIRMATION}\');" type="submit" name="Delete" value="{$APP.LBL_DELETE_BUTTON_LABEL}">{/if} ';
			break;
			
			case "DUPLICATE":
			return '{if $bean->aclAccess("edit")}<input title="{$APP.LBL_DUPLICATE_BUTTON_TITLE}" accessKey="{$APP.LBL_DUPLICATE_BUTTON_KEY}" class="button" onclick="this.form.return_module.value=\''. $module . '\'; this.form.return_action.value=\'DetailView\'; this.form.isDuplicate.value=true; this.form.action.value=\'' . $view . '\'; this.form.return_id.value=\'{$id}\';" type="submit" name="Duplicate" value="{$APP.LBL_DUPLICATE_BUTTON_LABEL}" id="duplicate_button">{/if} ';
			break;
			  
			case "EDIT";
			return '{if $bean->aclAccess("edit")}<input title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" class="button" onclick="this.form.return_module.value=\'' . $module . '\'; this.form.return_action.value=\'DetailView\'; this.form.return_id.value=\'{$id}\'; this.form.action.value=\'' . $view . '\';" type="submit" name="Edit" id="edit_button" value="{$APP.LBL_EDIT_BUTTON_LABEL}">{/if} '; 
			break;
			
			case "FIND_DUPLICATES":
			return '{if $bean->aclAccess("edit")}<input title="{$APP.LBL_DUP_MERGE}" accessKey="M" class="button" onclick="this.form.return_module.value=\'' . $module . '\'; this.form.return_action.value=\'DetailView\'; this.form.return_id.value=\'{$id}\'; this.form.action.value=\'Step1\'; this.form.module.value=\'MergeRecords\';" type="submit" name="Merge" value="{$APP.LBL_DUP_MERGE}">{/if} ';
			break;
					
			case "SAVE":
				$view = ($_REQUEST['action'] == 'EditView') ? 'EditView' : (($view == 'EditView') ? 'EditView' : $view);
				return '{if $bean->aclAccess("save")}<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="button" onclick="{if $isDuplicate}this.form.return_id.value=\'\'; {/if}this.form.action.value=\'Save\'; return check_form(\'' . $view . '\');" type="submit" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}">{/if} ';
			break;
			
			case "SUBPANELSAVE":
				$view = $view == 'QuickCreate' ? "QuickCreate_{$module}" : $view;
				return '{if $bean->aclAccess("save")}<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="button" onclick="this.form.action.value=\'Save\';if(check_form(\''.$view.'\'))return SUGAR.subpanelUtils.inlineSave(this.form.id, \''.buttonGetSubanelId($module) . '\');return false;" type="submit" name="' . $params['module'] . '_subpanel_save_button" id="' . $params['module'] . '_subpanel_save_button" value="{$APP.LBL_SAVE_BUTTON_LABEL}">{/if} ';
			case "SUBPANELCANCEL":
				return '<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="button" onclick="return SUGAR.subpanelUtils.cancelCreate(\'subpanel_' . buttonGetSubanelId($module) . '\');return false;" type="submit" name="' . $params['module'] . '_subpanel_cancel_button" id="' . $params['module'] . '_subpanel_cancel_button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}"> ';
		    case "SUBPANELFULLFORM":
				$html = '<input title="{$APP.LBL_FULL_FORM_BUTTON_TITLE}" accessKey="{$APP.LBL_FULL_FORM_BUTTON_KEY}" class="button" onclick="this.form.return_action.value=\'DetailView\'; this.form.action.value=\'EditView\'; if(typeof(this.form.to_pdf)!=\'undefined\') this.form.to_pdf.value=\'0\';" type="submit" name="' . $params['module'] . '_subpanel_full_form_button" id="' . $params['module'] . '_subpanel_full_form_button" value="{$APP.LBL_FULL_FORM_BUTTON_LABEL}"> ';
				$html .= '<input type="hidden" name="full_form" value="full_form">';
		        return $html;
			case "AUDIT":
	            $popup_request_data = array(
			        'call_back_function' => 'set_return',
			        'form_name' => 'EditView',
			        'field_to_name_array' => array(),
			    );
	            $json = getJSONobj();
	            
	            require_once('include/SugarFields/Parsers/MetaParser.php');
	            $encoded_popup_request_data = MetaParser::parseDelimiters($json->encode($popup_request_data));
	 			$audit_link = '<input title="{$APP.LNK_VIEW_CHANGE_LOG}" class="button" onclick=\'open_popup("Audit", "600", "400", "&record={$fields.id.value}&module_name=' . $params['module'] . '", true, false, ' . $encoded_popup_request_data . '); return false;\' type="submit" value="{$APP.LNK_VIEW_CHANGE_LOG}">';
				$view = '{if $bean->aclAccess("detail")}{if !empty($fields.id.value) && $isAuditEnabled}'.$audit_link.'{/if}{/if}';
				return $view;


















   	  } //switch
   	  
   } else if(is_array($type) && isset($type['customCode'])) {
   	  return $type['customCode'];
   } 
   
}

function buttonGetSubanelId($module){
	$subpanel = strtolower($module);
	$activities = array('Calls', 'Meetings', 'Tasks', 'Emails');
	if($module == 'Notes'){
		$subpanel = 'history';
	}
	if(in_array($module, $activities)){
		$subpanel = 'activities';
	}		
	return $subpanel;
}

?>
