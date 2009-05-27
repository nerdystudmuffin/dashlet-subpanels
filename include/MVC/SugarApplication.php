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
 * *******************************************************************************/
/*
 * Created on Mar 21, 2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require_once('include/MVC/Controller/ControllerFactory.php');
require_once('include/MVC/View/ViewFactory.php');

class SugarApplication
{ 	
 	var $controller = null;
 	var $headerDisplayed = false;
 	var $default_module = 'Home';
 	var $default_action = 'index';
 	
 	function SugarApplication()
 	{}
 	
 	/**
 	 * Perform execution of the application. This method is called from index2.php
 	 */
	function execute(){
		global $sugar_config;
		if(!empty($sugar_config['default_module']))
			$this->default_module = $sugar_config['default_module'];
		$module = $this->default_module;
		if(!empty($_REQUEST['module']))$module = $_REQUEST['module'];
		insert_charset_header();
		$this->setupPrint();
		$this->controller = ControllerFactory::getController($module);
        // if the entry point is defined to not need auth, then don't authenicate
		if( empty($_REQUEST['entryPoint']) 
                || $this->controller->checkEntryPointRequiresAuth($_REQUEST['entryPoint']) ){
            $this->loadUser();
            $this->ACLFilter();
            $this->preProcess();
            $this->controller->preProcess();
        }

		if(ini_get('session.auto_start') !== false) {
		   
		   $_SESSION['breadCrumbs'] = new BreadCrumbStack($GLOBALS['current_user']->id);
		} 
        

        if(ini_get('session.auto_start') !== false) {
		    
		    $_SESSION['breadCrumbs'] = new BreadCrumbStack($GLOBALS['current_user']->id);
		}        
        SugarThemeRegistry::buildRegistry();
        $this->loadLanguages();



		$this->checkDatabaseVersion();



		$this->loadDisplaySettings();
		$this->loadLicense();
		$this->loadGlobals();
		$this->setupResourceManagement($module);		
		$this->controller->execute();
		sugar_cleanup();
	}
		
	/**
	 * Load the authenticated user. If there is not an authenticated user then redirect to login screen.
	 */
	function loadUser(){
		global $authController, $sugar_config;
		// Double check the server's unique key is in the session.  Make sure this is not an attempt to hijack a session
		$user_unique_key = (isset($_SESSION['unique_key'])) ? $_SESSION['unique_key'] : '';
		$server_unique_key = (isset($sugar_config['unique_key'])) ? $sugar_config['unique_key'] : '';
		$allowed_actions = (!empty($this->controller->allowed_actions)) ? $this->controller->allowed_actions : $allowed_actions = array('Authenticate', 'Login',);
		
		if(($user_unique_key != $server_unique_key) && (!in_array($this->controller->action, $allowed_actions)) && 
		   (!isset($_SESSION['login_error']))) 
		   {
			session_destroy();
			$post_login_nav = '';
			
			if(!empty($this->controller->module)){
				$post_login_nav .= '&login_module='.$this->controller->module;
			}
			if(!empty($this->controller->action)){
				$post_login_nav .= '&login_action='.$this->controller->action;
			}
			if(!empty($this->controller->record)){
				$post_login_nav .= '&login_record='.$this->controller->record;
			}
			if(in_array(strtolower($this->controller->action), array('save', 'delete')) || isset($_REQUEST['massupdate'])
					|| isset($_GET['massupdate']) || isset($_POST['massupdate']))
				$post_login_nav = '';
		
			header('Location: index.php?action=Login&module=Users'.$post_login_nav);
			exit ();
		}
		
		$authController = new AuthenticationController((!empty($GLOBALS['sugar_config']['authenticationClass'])? $GLOBALS['sugar_config']['authenticationClass'] : 'SugarAuthenticate'));
		$GLOBALS['current_user'] = new User();
		if(isset($_SESSION['authenticated_user_id'])){ 
			// set in modules/Users/Authenticate.php
			if(!$authController->sessionAuthenticate()){
				 // if the object we get back is null for some reason, this will break - like user prefs are corrupted
				$GLOBALS['log']->fatal('User retrieval for ID: ('.$_SESSION['authenticated_user_id'].') does not exist in database or retrieval failed catastrophically.  Calling session_destroy() and sending user to Login page.');
				session_destroy();
				SugarApplication::redirect('index.php?action=Login&module=Users');
				die();
			}//fi
		}elseif(!($this->controller->module == 'Users' && in_array($this->controller->action, $allowed_actions))){
			session_destroy();
			SugarApplication::redirect('index.php?action=Login&module=Users');
			die();
		}
		$GLOBALS['log']->debug('Current user is: '.$GLOBALS['current_user']->user_name);
		
		//set cookies
		if(isset($_SESSION['authenticated_user_id'])){
			$GLOBALS['log']->debug("setting cookie ck_login_id_20 to ".$_SESSION['authenticated_user_id']);
			setcookie('ck_login_id_20', $_SESSION['authenticated_user_id'], time() + 86400 * 90);
		}
		if(isset($_SESSION['authenticated_user_theme'])){
			$GLOBALS['log']->debug("setting cookie ck_login_theme_20 to ".$_SESSION['authenticated_user_theme']);
			setcookie('ck_login_theme_20', $_SESSION['authenticated_user_theme'], time() + 86400 * 90);
		}
		if(isset($_SESSION['authenticated_user_theme_color'])){
			$GLOBALS['log']->debug("setting cookie ck_login_theme_color_20 to ".$_SESSION['authenticated_user_theme_color']);
			setcookie('ck_login_theme_color_20', $_SESSION['authenticated_user_theme_color'], time() + 86400 * 90);
		}
		if(isset($_SESSION['authenticated_user_theme_font'])){
			$GLOBALS['log']->debug("setting cookie ck_login_theme_font_20 to ".$_SESSION['authenticated_user_theme_font']);
			setcookie('ck_login_theme_font_20', $_SESSION['authenticated_user_theme_font'], time() + 86400 * 90);
		}
		if(isset($_SESSION['authenticated_user_language'])){
			$GLOBALS['log']->debug("setting cookie ck_login_language_20 to ".$_SESSION['authenticated_user_language']);
			setcookie('ck_login_language_20', $_SESSION['authenticated_user_language'], time() + 86400 * 90);
		}
		//check if user can access
			
	}
	
	function ACLFilter(){
		ACLController :: filterModuleList($GLOBALS['moduleList']);
		ACLController :: filterModuleList($GLOBALS['modInvisListActivities']);
	}
	
	/**
	 * setupResourceManagement
	 * This function initialize the ResourceManager and calls the setup method
	 * on the ResourceManager instance.
	 * 
	 */
	function setupResourceManagement($module) {
		require_once('include/resource/ResourceManager.php');
		$resourceManager = ResourceManager::getInstance();
		$resourceManager->setup($module);		
	}
	
	function setupPrint() {
		$GLOBALS['request_string'] = '';

		// merge _GET and _POST, but keep the results local
		// this handles the issues where values come in one way or the other
		// without affecting the main super globals
		$merged = array_merge($_GET, $_POST);
		foreach ($merged as $key => $val) 
		{
		   if(is_array($val)) 
		   {
		       foreach ($val as $k => $v) 
		       {
		           $GLOBALS['request_string'] .= $val[$k].'='.urlencode($v).'&';
		       }
		   } 
		   else 
		   {
		       $GLOBALS['request_string'] .= $key.'='.urlencode($val).'&';
		   }
		}
		$GLOBALS['request_string'] .= 'print=true';
	}
		
	function preProcess(){
		if(!empty($_SESSION['authenticated_user_id'])){ 
			$ut = $GLOBALS['current_user']->getPreference('ut');
			if(empty($ut) && $this->controller->action != 'SaveTimezone' && $this->controller->action != 'Logout') {
				$this->controller->module = 'Users';
				$this->controller->action = 'SetTimezone';
				$record = $GLOBALS['current_user']->id;
			}else{
				$this->handleOfflineClient();
			}
			
			if(isset($_SESSION['hasExpiredPassword']) && $_SESSION['hasExpiredPassword'] == '1'){
				if( $this->controller->action!= 'Save' && $this->controller->action != 'Logout') {
	                $this->controller->module = 'Users';
	                $this->controller->action = 'ChangePassword';
	                $record = $GLOBALS['current_user']->id;
	            }else{
	                $this->handleOfflineClient();
	            }
			}	
		$this->handleAccessControl();
		}
		
	}
	
	function handleOfflineClient(){
		if(isset($GLOBALS['sugar_config']['disc_client']) && $GLOBALS['sugar_config']['disc_client']){
			if(isset($_REQUEST['action']) && $_REQUEST['action'] != 'SaveTimezone'){
				if (!file_exists('modules/Sync/file_config.php')){
					if($_REQUEST['action'] != 'InitialSync' && $_REQUEST['action'] != 'Logout' && 
					   ($_REQUEST['action'] != 'Popup' && $_REQUEST['module'] != 'Sync')){
						//echo $_REQUEST['action'];
						//die();	
					   		$this->controller->module = 'Sync';
							$this->controller->action = 'InitialSync';
						}
		    	}else{
		    		require_once ('modules/Sync/file_config.php');
		    		if(isset($file_sync_info['is_first_sync']) && $file_sync_info['is_first_sync']){
		    			if($_REQUEST['action'] != 'InitialSync' && $_REQUEST['action'] != 'Logout' && 
		    			   ( $_REQUEST['action'] != 'Popup' && $_REQUEST['module'] != 'Sync')){
								$this->controller->module = 'Sync';
								$this->controller->action = 'InitialSync';
						}
		    		}
		    	}
			}
			global $moduleList, $sugar_config, $sync_modules;
			require_once('modules/Sync/SyncController.php');
			$GLOBALS['current_user']->is_admin = '0'; //No admins for disc client
		}
	}
	
	/**
	 * Handles everything related to authorization.
	 */
	function handleAccessControl(){
		if(is_admin($GLOBALS['current_user']) || is_admin_for_any_module($GLOBALS['current_user']))
			return;
	    if(!empty($_REQUEST['action']) && $_REQUEST['action']=="RetrieveEmail")
            return;
		if(!is_admin($GLOBALS['current_user']) && !empty($GLOBALS['adminOnlyList'][$this->controller->module])
		&& !empty($GLOBALS['adminOnlyList'][$this->controller->module]['all'])
		&& (empty($GLOBALS['adminOnlyList'][$this->controller->module][$this->controller->action]) || $GLOBALS['adminOnlyList'][$this->controller->module][$this->controller->action] != 'allow')) {
			$this->controller->hasAccess = false;
			return;
		}	

		if(!empty($GLOBALS['current_user']) && empty($GLOBALS['modListHeader']))
			$GLOBALS['modListHeader'] = query_module_access_list($GLOBALS['current_user']);
			
		if(in_array($this->controller->module, $GLOBALS['modInvisList']) &&
			((in_array('Activities', $GLOBALS['moduleList'])              &&	
			in_array('Calendar',$GLOBALS['moduleList']))                 &&	
			in_array($this->controller->module, $GLOBALS['modInvisListActivities']))
			){
				$this->controller->hasAccess = false;
				return;
		}
	}
	
	/**
	 * Load application wide languages as well as module based languages so they are accessible
	 * from the module.
	 */
	function loadLanguages(){
		if(!empty($_SESSION['authenticated_user_language'])) {
			$GLOBALS['current_language'] = $_SESSION['authenticated_user_language'];
		}
		else {
			$GLOBALS['current_language'] = $GLOBALS['sugar_config']['default_language'];
		}
		$GLOBALS['log']->debug('current_language is: '.$GLOBALS['current_language']);
		//set module and application string arrays based upon selected language
		$GLOBALS['app_strings'] = return_application_language($GLOBALS['current_language']);
		if(empty($GLOBALS['current_user']->id))$GLOBALS['app_strings']['NTC_WELCOME'] = '';
		if(!empty($GLOBALS['system_config']->settings['system_name']))$GLOBALS['app_strings']['LBL_BROWSER_TITLE'] = $GLOBALS['system_config']->settings['system_name'];
		$GLOBALS['app_list_strings'] = return_app_list_strings_language($GLOBALS['current_language']);
		$GLOBALS['mod_strings'] = return_module_language($GLOBALS['current_language'], $this->controller->module);
	}
	
	/**
	* checkDatabaseVersion
	* Check the db version sugar_version.php and compare to what the version is stored in the config table.
	* Ensure that both are the same.
	*/
 	function checkDatabaseVersion(){
		global $sugar_db_version;
		$version_query = 'SELECT count(*) as the_count FROM config WHERE category=\'info\' AND name=\'sugar_version\'';
		
		if($GLOBALS['db']->dbType == 'oci8'){



		}
		else if ($GLOBALS['db']->dbType == 'mssql'){
			$version_query .= " AND CAST(value AS varchar(8000)) = '$sugar_db_version'";
		}
		else {
			$version_query .= " AND value = '$sugar_db_version'";
		}

		$result = $GLOBALS['db']->query($version_query);
		$row = $GLOBALS['db']->fetchByAssoc($result, -1, true);
		$row_count = $row['the_count'];

		if($row_count == 0 && empty($GLOBALS['sugar_config']['disc_client'])){
			$sugar_version = $GLOBALS['sugar_version'];
			sugar_die("Sugar CRM $sugar_version Files May Only Be Used With A Sugar CRM $sugar_db_version Database.");
		}
	}
	
	/**
	 * Load the themes/images.
	 */
	function loadDisplaySettings()
    {
        global $theme;
        
        if(isset($_REQUEST['usertheme'])) {
            $theme = clean_string($_REQUEST['usertheme']);
            if ( !isset($_REQUEST['noThemeSave']) ) {
                $_SESSION['theme_changed'] = true;
                $_SESSION['authenticated_user_theme'] = $theme;
            }
		}
        else {
			$theme = !empty($_SESSION['authenticated_user_theme'])
                ? $_SESSION['authenticated_user_theme']
                : $GLOBALS['sugar_config']['default_theme'];
			if(isset($_SESSION['authenticated_user_theme']) && $_SESSION['authenticated_user_theme'] != '') {
				$_SESSION['theme_changed'] = false;
			}
		}
        if ( isset($_REQUEST['usercolor']) )
            $_SESSION['authenticated_user_theme_color'] = clean_string($_REQUEST['usercolor']);
        if ( isset($_REQUEST['userfont']) )
            $_SESSION['authenticated_user_theme_font'] = clean_string($_REQUEST['userfont']);
        
		
        SugarThemeRegistry::set($theme);
        require_once('include/utils/layout_utils.php');
        $GLOBALS['image_path'] = SugarThemeRegistry::current()->getImagePath().'/';
        if ( defined('TEMPLATE_URL') ) 
            $GLOBALS['image_path'] = TEMPLATE_URL . '/'. $GLOBALS['image_path'];
		
        if ( isset($GLOBALS['current_user']) )
            $GLOBALS['gridline'] = (int) ($GLOBALS['current_user']->getPreference('gridline') == 'on');
	}
			
	function loadLicense(){
		loadLicense();
		global $user_unique_key, $server_unique_key;
		$user_unique_key = (isset($_SESSION['unique_key'])) ? $_SESSION['unique_key'] : '';
		$server_unique_key = (isset($sugar_config['unique_key'])) ? $sugar_config['unique_key'] : '';
	}
	
	function loadGlobals(){
		global $currentModule;
		$currentModule = $this->controller->module;
		if($this->controller->module == $this->default_module){
			$_REQUEST['module'] = $this->controller->module;
			if(empty($_REQUEST['action']))
			$_REQUEST['action'] = $this->default_action;
		}
	}
		
	function startSession(){
		if(isset($_REQUEST['MSID'])) {
			session_id($_REQUEST['MSID']);
			session_start();
			if(isset($_SESSION['user_id']) && isset($_SESSION['seamless_login'])){
				unset ($_SESSION['seamless_login']);
			}else{
				if(isset($_COOKIE['PHPSESSID'])){
	       			setcookie('PHPSESSID', '', time()-42000, '/');
        		}
	    		sugar_cleanup(false);
	    		session_destroy();
	    		exit('Not a valid entry method');
			}
		}else{
			if(can_start_session()){
				session_start();
			}
		}
























	}
	
	function endSession(){









		session_destroy();
	}
 	/**
	 * Redirect to another URL
	 *
	 * @access	public
	 * @param	string	$url	The URL to redirect to
	 */
 	function redirect( $url)
	{
		/*
		 * If the headers have been sent, then we cannot send an additional location header
		 * so we will output a javascript redirect statement.
		 */
		if (headers_sent()) {
			echo "<script>document.location.href='$url';</script>\n";
		} else {
			//@ob_end_clean(); // clear output buffer
			session_write_close();
			header( 'HTTP/1.1 301 Moved Permanently' );
			header( "Location: ". $url );
		}
		exit();
	}
 }
?>
