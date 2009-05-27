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

$popupMeta = array('moduleMain' => 'Lead',
						'varName' => 'LEAD',
						'orderBy' => 'last_name, first_name',
						'whereClauses' => 
							array('first_name' => 'leads.first_name', 
									'last_name' => 'leads.last_name',
									'lead_source' => 'leads.lead_source',
									'status' => 'leads.status'),
						'selectDoms' =>
							array('LEAD_SOURCE_OPTIONS' => 
											array('dom' => 'lead_source_dom', 'searchInput' => 'lead_source'),
									'STATUS_OPTIONS' => 
											array('dom' => 'lead_status_dom', 'searchInput' => 'status')
									),
						'searchInputs' =>
							array('first_name', 'last_name', 'lead_source', 'status'),
						'listviewdefs' => array(
											'NAME' => array(
												'width' => '30',
												'label' => 'LBL_LIST_NAME',
										        'link' => true,
										        'default' => true,
										        'related_fields' => array('first_name', 'last_name', 'salutation')), 
											'LEAD_SOURCE' => array(
										        'width' => '10', 
										        'label' => 'LBL_LEAD_SOURCE',
										        'default' => true,),
											'STATUS' => array(
												'width' => '10', 
												'label' => 'LBL_LIST_STATUS',
										        'default' => true),
										     'ASSIGNED_USER_NAME' => array(
										        'width' => '10', 
										        'label' => 'LBL_LIST_ASSIGNED_USER',
										        'default' => true), 
											),
						'searchdefs'   => array(
										 	'first_name',
										 	'last_name', 
											'lead_source', 
											'status',
											array('name' => 'assigned_user_id', 'type' => 'enum', 'label' => 'LBL_ASSIGNED_TO', 'function' => array('name' => 'get_user_array', 'params' => array(false))),
										  )
						);


?>
 
 
