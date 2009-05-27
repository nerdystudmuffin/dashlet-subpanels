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
 *********************************************************************************/
$viewdefs['Meetings']['SideQuickCreate'] = array(
    'templateMeta' => array('form'=>array('hidden'=>array('<input type="hidden" name="isSaveAndNew" value="false">',
                                                          '<input type="hidden" name="send_invites">',
                                                          '<input type="hidden" name="user_invitees">',
                                                          '<input type="hidden" name="contact_invitees">',
                                                          '<input type="hidden" name="duration_hours" value="0">',
                                                          '<input type="hidden" name="duration_minutes" value="15">',
                                                          '<input type="hidden" name="status" id="status" value="Planned">',
                                                     ),
                                          'headerTpl'=>'include/EditView/header.tpl',
                                          'footerTpl'=>'include/EditView/footer.tpl',
                                          'buttons'=>array('SAVE'),
    								      'button_location'=>'bottom'
                                          ),
							'maxColumns' => '1', 
							'panelClass'=>'none',
							'labelsOnTop'=>true,
                            'widths' => array(
                                            array('label' => '10', 'field' => '30'),
                                         ),
                            'javascript' => '<script type="text/javascript">document.getElementById(\'direction\').style.display=\'none\';</script>',
                        ),
 'panels' =>array (
  'DEFAULT' => 
  array (
    array(
        array('name'=>'name', 'label'=>'', 'customCode'=>'<div valign="top">{literal}<input type="radio" name="appttype" onchange="if(this.checked){this.form.module.value=\'Calls\'; this.form.return_module.value=\'Calls\'; this.form.direction.style.display = \'\';}">{/literal}{sugar_translate label="LBL_CALL"}{literal}<input type="radio" name="appttype" checked=true onchange="if(this.checked){this.form.module.value=\'Meetings\'; this.form.return_module.value=\'Meetings\'; this.form.direction.style.display=\'none\'}">{/literal}{sugar_translate label="LBL_MEETING"}</div>'),
    ),
    array (
      array('name'=>'name', 'displayParams'=>array('size'=>20, 'required'=>true)),
    ),  
    array (
      array('name'=>'date_start',       
            'type'=>'datetimecombo',
            'displayParams'=>array('required' => true, 'splitDateTime'=>true),
            'label'=>'LBL_DATE_TIME'),
    ),
   array (
      array('name'=>'parent_name', 'displayParams'=>array('size'=>11, 'selectOnly'=>true, 'split'=>true)),
    ), 
    array (
      array('name'=>'assigned_user_name', 'displayParams'=>array('required'=>true, 'size'=>11, 'selectOnly'=>true)),
    ),
  array (
      array (
        'name' => 'status',
        'displayParams' => array('required'=>true),
        'fields' => 
        array (
          array('name'=>'status'),
          array('name'=>'direction'),
        ),
      ),
    ),
  ),

 )


);
