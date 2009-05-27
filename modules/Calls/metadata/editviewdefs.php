<?php
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
$viewdefs['Calls']['EditView'] = array(
    'templateMeta' => array('maxColumns' => '2',
                            'form' => array('hidden'=>array('<input type="hidden" name="isSaveAndNew" value="false">',
															'<input type="hidden" name="send_invites">',
															'<input type="hidden" name="user_invitees">',
															'<input type="hidden" name="lead_invitees">',
															'<input type="hidden" name="contact_invitees">'), 
                                            'buttons' => array(
                                                array('customCode'=>'<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="button" onclick="fill_invitees();this.form.action.value=\'Save\'; this.form.return_action.value=\'DetailView\'; {if isset($smarty.request.isDuplicate) && $smarty.request.isDuplicate eq "true"}this.form.return_id.value=\'\'; {/if}return check_form(\'EditView\') && isValidDuration();" type="submit" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}">'), 
                                             'CANCEL',
                                             array('customCode'=>'<input title="{$MOD.LBL_SEND_BUTTON_TITLE}" class="button" onclick="this.form.send_invites.value=\'1\';fill_invitees();this.form.action.value=\'Save\';this.form.return_action.value=\'EditView\';this.form.return_module.value=\'{$smarty.request.return_module}\';return check_form(\'EditView\') && isValidDuration();" type="submit" name="button" value="{$MOD.LBL_SEND_BUTTON_LABEL}">'),
                                             array('customCode'=>'{if $fields.status.value != "Held"}' .
                                             		'<input title="{$APP.LBL_CLOSE_AND_CREATE_BUTTON_TITLE}" ' .
                                             		'accessKey="{$APP.LBL_CLOSE_AND_CREATE_BUTTON_KEY}" ' .
                                             		'class="button" ' .
                                             		'onclick="fill_invitees(); this.form.status.value=\'Held\'; this.form.action.value=\'Save\'; this.form.return_module.value=\'Calls\'; this.form.isDuplicate.value=true; this.form.isSaveAndNew.value=true; this.form.return_action.value=\'EditView\'; this.form.return_id.value=\'{$fields.id.value}\'; return check_form(\'EditView\') && isValidDuration();" ' .
                                             		'type="submit" name="button" ' .
                                             		'value="{$APP.LBL_CLOSE_AND_CREATE_BUTTON_LABEL}">{/if}'),
                                            ),
                                            'footerTpl'=>'modules/Calls/tpls/footer.tpl'),
                            'widths' => array(
                                            array('label' => '10', 'field' => '30'), 
                                            array('label' => '10', 'field' => '30')
                                            ),
    						'javascript' => '<script type="text/javascript" src="' . getJSPath('include/JSON.js') . '"></script>
<script type="text/javascript" src="' . getJSPath('include/jsolait/init.js') . '"></script>
<script type="text/javascript" src="' . getJSPath('include/jsolait/lib/urllib.js') . '"></script>
<script type="text/javascript">{$JSON_CONFIG_JAVASCRIPT}</script>
<script type="text/javascript" src="' . getJSPath('include/javascript/jsclass_base.js') . '"></script>
<script type="text/javascript" src="' . getJSPath('include/javascript/jsclass_async.js') . '"></script>
<script type="text/javascript" src="' . getJSPath('modules/Meetings/jsclass_scheduler.js') . '"></script>
<script>toggle_portal_flag();function toggle_portal_flag()  {ldelim} {$TOGGLE_JS} {rdelim} </script>',
),
 'panels' =>array (
  'default' => 
  array (
    
    array (
      array('name'=>'name', 'displayParams'=>array('required'=>true)),
      
      array (
        'name' => 'status',
        'displayParams' => array('required'=>true),
        'fields' => 
        array (
          array('name'=>'direction'),
          array('name'=>'status'),
        ),
      ),
    ),
    
    array (
      array('name'=>'date_start',       
            'type'=>'datetimecombo',
            'displayParams'=>array('required' => true, 'updateCallback'=>'SugarWidgetScheduler.update_time();'),
            'label'=>'LBL_DATE_TIME'),





    ),
    
    array (
      'assigned_user_name',
      NULL,
    ),
    
    array (
      array (
        'name' => 'duration_hours',
        'label' => 'LBL_DURATION',
        'customCode' => '{literal}<script type="text/javascript">function isValidDuration() { form = document.getElementById(\'EditView\'); if ( form.duration_hours.value + form.duration_minutes.value <= 0 ) { alert(\'{/literal}{$MOD.NOTICE_DURATION_TIME}{literal}\'); return false; } return true; }</script>{/literal}<input name="duration_hours" tabindex="1" size="2" maxlength="2" type="text" value="{$fields.duration_hours.value}" onkeyup="SugarWidgetScheduler.update_time();"/>{$fields.duration_minutes.value}&nbsp;<span class="dateFormat">{$MOD.LBL_HOURS_MINUTES}',
        'displayParams' => array('required'=>true),
      ),
      
      'parent_name',
    ),
    
    array (
      array('name' => 'reminder_time',
            'customCode' => '{if $fields.reminder_checked.value == "1"}' .
            	 	        '{assign var="REMINDER_TIME_DISPLAY" value="inline"}' .
            	 	        '{assign var="REMINDER_CHECKED" value="checked"}' .
            	 	        '{else}' .
            	 	        '{assign var="REMINDER_TIME_DISPLAY" value="none"}' .
            	 	        '{assign var="REMINDER_CHECKED" value=""}' .
            	 	        '{/if}' .
            	 	        '<input name="reminder_checked" type="hidden" value="0"><input name="reminder_checked" onclick=\'toggleDisplay("should_remind_list");\' type="checkbox" class="checkbox" value="1" {$REMINDER_CHECKED}><div id="should_remind_list" style="display:{$REMINDER_TIME_DISPLAY}">{$fields.reminder_time.value}</div>',
            'label' => 'LBL_REMINDER'),
    ),
    
    array (
      'description',
    ),
  ),
)


);
?>
