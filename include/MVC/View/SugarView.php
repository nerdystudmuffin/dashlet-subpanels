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
class SugarView
{
    /**
     * This array is meant to hold an objects/data that we would like to pass between
     * the controller and the view.  The bean will automatically be set for us, but this
     * is meant to hold anything else.
     */
    var $view_object_map = array();
    /**
     * The name of the current module.
     */
	var $module = '';
    /**
     * The name of the current action.
     */
    var $action = '';
    /**
     */
    var $bean = null;
    /**
     * Sugar_Smarty. This is useful if you have a view and a subview you can
     * share the same smarty object.
     */
    var $ss = null;
    /**
     * Any errors that occured this can either be set by the view or the controller or the model
     */
    var $errors = array();
    /**
     * Options for what UI elements to hide/show/
     */
    var $options = array('show_header' => true, 'show_title' => true, 'show_subpanels' => false, 'show_search' => true, 'show_footer' => true, 'show_javascript' => true, 'view_print' => false,);
    var $type = null;
    var $responseTime;
    var $fileResources;
    /**
     * Constructor which will peform the setup.
     */
    function SugarView($bean = null, $view_object_map = array()){

    }

    function init($bean = null, $view_object_map = array())
    {
        $this->bean = &$bean;
        $this->view_object_map = $view_object_map;
        $this->action = $GLOBALS['action'];
        $this->module = $GLOBALS['module'];
        $this->_initSmarty();
    }
    
    private function _initSmarty(){
    	$this->ss = new Sugar_Smarty();
        $this->ss->assign('MOD', $GLOBALS['mod_strings']);
        $this->ss->assign('APP', $GLOBALS['app_strings']);
    }

    /**
     * This method will be called from the controller and is not meant to be overridden.
     */
    function process()
    {
        LogicHook::initialize();
        $this->checkModule();
        if ($this->_getOption('show_header')) {
            $this->displayHeader();
        } else {
            $this->renderJavascript();	
        }
        
        $this->trackView();
        $this->buildModuleList();
        $this->preDisplay();
        $this->displayErrors();
        $this->display();
        $GLOBALS['logic_hook']->call_custom_logic('', 'after_ui_frame');
        if ($this->_getOption('show_subpanels')) $this->displaySubPanels();
        if ($this->action === 'Login') {
            //this is needed for a faster loading login page ie won't render unless the tables are closed
            flush();
            ob_flush();
        }
        if ($this->_getOption('show_footer')) $this->displayFooter();
        $GLOBALS['logic_hook']->call_custom_logic('', 'after_ui_footer');
        //Do not track if there is no module or if module is not a String
        $this->track();
    }

    /**
     * This method will display the errors on the page.
     */
    function displayErrors()
    {
        foreach($this->errors as $error) {
            echo '<span class="error">' . $error . '</span><br>';
        }
    }

    /**
     * [OVERRIDE] - This method is meant to overidden in a subclass. The purpose of this method is
     * to allow a view to do some preprocessing before the display method is called. This becomes
     * useful when you have a view defined at the application level and then within a module
     * have a sub-view that extends from this application level view.  The application level
     * view can do the setup in preDisplay() that is common to itself and any subviews
     * and then the subview can just override display(). If it so desires, can also override
     * preDisplay().
     */
    function preDisplay()
    {
    }

    /**
     * [OVERRIDE] - This method is meant to overidden in a subclass. This method
     * will handle the actual display logic of the view.
     */
    function display()
    {
    }


    /**
     * trackView
     */
    private function trackView() {
    	$action = strtolower($this->action);
    	//Skip save, tracked in SugarBean instead
    	if($action == 'save') {
    	   return;
    	}

       
        $trackerManager = TrackerManager::getInstance();
        $timeStamp = gmdate($GLOBALS['timedate']->get_db_date_time_format());
        if($monitor = $trackerManager->getMonitor('tracker')){ 



	        $monitor->setValue('action', $action);
	        $monitor->setValue('user_id', $GLOBALS['current_user']->id);
	        $monitor->setValue('module_name', $this->module);
	        $monitor->setValue('date_modified', $timeStamp);
	        $monitor->setValue('visible', (($monitor->action == 'detailview') || ($monitor->action == 'editview')



	        								) ? 1 : 0);
	
	        if (!empty($this->bean->id)) {
	            $monitor->setValue('item_id', $this->bean->id);
	            $monitor->setValue('item_summary', $this->bean->get_summary_text());
	        }
	
	        //If visible is true, but there is no bean, do not track (invalid/unauthorized reference)
	        //Also, do not track save actions where there is no bean id
	        if($monitor->visible && empty($this->bean->id)) {
	           $trackerManager->unsetMonitor($monitor);
	           return;
	        }
	        $trackerManager->saveMonitor($monitor);
		}
    }


    /**
     * Determine whether or not to dispay the header on the page.
     */
    function displayHeader()
    {
        global $theme;
        global $max_tabs;
        global $app_strings;
        global $current_user;
        global $sugar_config;
        global $app_list_strings;

        $GLOBALS['app']->headerDisplayed = true;

        if (!function_exists('get_new_record_form') && (file_exists('modules/' . $this->module . '/metadata/sidecreateviewdefs.php') || file_exists('custom/modules/' . $this->module . '/metadata/sidecreateviewdefs.php'))) {
            require_once ('include/EditView/SideQuickCreate.php');
        }
        if (!$this->_menuExists($this->module) && !empty($GLOBALS['mod_strings']['LNK_NEW_RECORD'])) {
            $GLOBALS['module_menu'][] = Array("index.php?module=$this->module&action=EditView&return_module=$this->module&return_action=DetailView",
				$GLOBALS['mod_strings']['LNK_NEW_RECORD'],"{$GLOBALS['app_strings']['LBL_CREATE_BUTTON_LABEL']}$this->module" ,$this->module );
            $GLOBALS['module_menu'][] = Array("index.php?module=$this->module&action=index", $GLOBALS['mod_strings']['LNK_LIST'], $this->module, $this->module);
        }
        
        $themeObject = SugarThemeRegistry::current();
        $theme = $themeObject->__toString();
        
        $ss = new Sugar_Smarty();
        $ss->assign("APP", $app_strings);
        $ss->assign("THEME", $theme);
        $ss->assign("MODULE_NAME", $this->module);
        
        // get css
        $css = $themeObject->getCSS();
        if ($this->_getOption('view_print')) {
            $css .= '\n<link rel="stylesheet" type="text/css" href="'.$themeObject->getCSSURL('print.css').'" media="all" />';
        }
        $ss->assign("SUGAR_CSS",$css);
        
        // get javascript
        ob_start();
        $this->renderJavascript();

        $ss->assign("SUGAR_JS",ob_get_contents().$themeObject->getJS());
        ob_end_clean();
        
        // get favicon
        $user_module_favicon = $current_user->getPreference('module_favicon');
        if(!isset($user_module_favicon) && isset($GLOBALS['sugar_config']['default_module_favicon'])) 
            $user_module_favicon = $GLOBALS['sugar_config']['default_module_favicon'];
        elseif(!isset($user_module_favicon))
            $user_module_favicon = false;

        $favicon = $themeObject->getImageURL($this->module.'.gif',false);
        if ( !sugar_is_file($favicon) || !$user_module_favicon )
            $favicon = $themeObject->getImageURL('sugar_icon.ico',false);
        $ss->assign('FAVICON_URL',getJSPath($favicon));
        
        // get the module menu
        $shortcut_menu = array();
        $module_menu = load_menu("modules/".$this->module."/");
        foreach ( $module_menu as $key => $menu_item )
            $shortcut_menu[$key] = array(
                "URL"         => $menu_item[0],
                "LABEL"       => $menu_item[1],
                "MODULE_NAME" => $menu_item[2],
                "IMAGE"       => $themeObject
                    ->getImage($menu_item[2],"alt='".$menu_item[1]."'  border='0' align='absmiddle'"),
                );
        $ss->assign("SHORTCUT_MENU",$shortcut_menu);
        
        // handle rtl text direction
        if(isset($_REQUEST['RTL']) && $_REQUEST['RTL'] == 'RTL'){
            $_SESSION['RTL'] = true;
        }
        if(isset($_REQUEST['LTR']) && $_REQUEST['LTR'] == 'LTR'){
            unset($_SESSION['RTL']);
        }
        if(isset($_SESSION['RTL']) && $_SESSION['RTL']){
            $ss->assign("DIR", 'dir="RTL"');
        }
        
        // handle resizing of the company logo correctly on the fly
        $companyLogoURL = $themeObject->getImageURL('company_logo.png',false);
        $ss->assign("COMPANY_LOGO_URL",getJSPath($companyLogoURL));
        $company_logo_attributes = sugar_cache_retrieve('company_logo_attributes');
        if(!empty($company_logo_attributes) && md5_file($companyLogoURL) == $company_logo_attributes[0]) {
            $ss->assign("COMPANY_LOGO_WIDTH", $company_logo_attributes[1]);
            $ss->assign("COMPANY_LOGO_HEIGHT", $company_logo_attributes[2]);
        }
        else {
            list($width,$height) = getimagesize($companyLogoURL);
            if ( $width > 212 || $height > 40 ) {
                $resizePctWidth  = ($width - 212)/212;
                $resizePctHeight = ($height - 40)/40;
                if ( $resizePctWidth > $resizePctHeight )
                    $resizeAmount = $width / 212;
                else
                    $resizeAmount = $height / 40;
                $ss->assign("COMPANY_LOGO_WIDTH", round($width * (1/$resizeAmount)));
                $ss->assign("COMPANY_LOGO_HEIGHT", round($height * (1/$resizeAmount)));
                sugar_cache_put('company_logo_attributes',
                    array(
                        md5_file($companyLogoURL),
                        $ss->get_template_vars("COMPANY_LOGO_WIDTH"),
                        $ss->get_template_vars("COMPANY_LOGO_HEIGHT")
                        )
                    );
            }
            else {
                $ss->assign("COMPANY_LOGO_WIDTH", $width);
                $ss->assign("COMPANY_LOGO_HEIGHT", $height);
            }
        }
        
        // get the global links
        $gcls = array();
        $global_control_links = array();
        require("include/globalControlLinks.php");
        
        foreach($global_control_links as $key => $value) {
            if ($key == 'users')  {   //represents logout link.
                $ss->assign("LOGOUT_LINK", $value['linkinfo'][key($value['linkinfo'])]);  
                $ss->assign("LOGOUT_LABEL", key($value['linkinfo']));//key value for first element.
                continue;
            }
            
            foreach ($value as $linkattribute => $attributevalue) {
                // get the main link info
                if ( $linkattribute == 'linkinfo' )
                    $gcls[$key] = array(
                        "LABEL" => key($attributevalue),
                        "URL"   => current($attributevalue),
                        );
                // and now the sublinks
                if ( $linkattribute == 'submenu' && is_array($attributevalue) )
                    foreach ($attributevalue as $submenulinkkey => $submenulinkinfo)
                        $gcls[$key]['SUBMENU'][$submenulinkkey] = array(
                            "LABEL" => key($submenulinkinfo),
                            "URL"   => current($submenulinkinfo),
                        );
            }
        }
        $ss->assign("GCLS",$gcls);
        
        
        if (isset($_REQUEST['query_string'])) 
            $ss->assign("SEARCH", $_REQUEST['query_string']);
        
        if ($this->action == "EditView" || $this->action == "Login") 
            $ss->assign("ONLOAD", 'onload="set_focus()"');

        $ss->assign("AUTHENTICATED",isset($_SESSION["authenticated_user_id"]));
        if (isset($_SESSION["authenticated_user_id"])) {
            $ss->assign('selectThemeOptions',get_select_options_with_id(
                SugarThemeRegistry::availableThemes(), 
                SugarThemeRegistry::current()->__toString()
                ));
            
            // build json string of theme properties
            $allThemes = array();
            foreach ( SugarThemeRegistry::availableThemes() as $dirName => $themeName ) {
                $selectedTheme = SugarThemeRegistry::get($dirName);
                $allThemes[$dirName] = array(
                    'name' => $themeName,
                    'description' => $selectedTheme->description,
                    'previewImage' => $selectedTheme->getImageURL('themePreview.png'),
                    'colorSelect' => '',
                    'fontSelect' => '',
                    );
					
					
                // foreach color

                if(count($selectedTheme->colors) > 1) {
                    $startingColorName = $selectedTheme->colors[0];
                    if(!empty($_SESSION['authenticated_user_theme_color']) 
                            && in_array($_SESSION['authenticated_user_theme_color'],$themeObject->colors) )
                        $startingColorName = $_SESSION['authenticated_user_theme_color'];
                    $startingColorIcon = $selectedTheme->getImageURL("colors.{$startingColorName}.icon.gif");
                    $allThemes[$dirName]['colorSelect'] = <<<EOHTML
                    
<script type="text/javascript">

function change_color(color) {
		var callback = {
            success: function(o){
				SUGAR.themes.changeColor(color);
            },
            failure: function(o){
            alert("Ajax failure");
            }
        }   
		var postData = '&usercolor='+color;
        YAHOO.util.Connect.asyncRequest('POST','index.php?module=Home&action=index', callback, postData); 
}

function change_font(font) { 
	var callback = {
            success: function(o){
				SUGAR.themes.changeFont(font);
            },
            failure: function(o){
            alert("Ajax failure");
            }
        }   
		var postData = '&userfont='+font;
        YAHOO.util.Connect.asyncRequest('POST','index.php?module=Home&action=index', callback, postData);
}
</script>
<input type="hidden" name="usercolor" id="usercolor" value="">
<ul id="color_menu">
EOHTML;

                    foreach ( $selectedTheme->colors as $color ) {
                        $colorIcon = $selectedTheme->getImageURL("colors.{$color}.icon.gif");
                        $allThemes[$dirName]['colorSelect'] .= <<<EOHTML
<li style="background: url({$colorIcon})  no-repeat center;" onclick="change_color('{$color}');"></li>
EOHTML;
                    }
                    $allThemes[$dirName]['colorSelect'] .= '</ul></span>';
                }

                // foreach font
                if(count($selectedTheme->fonts) > 1) {
                    $startingFontName = $selectedTheme->fonts[0];
                    if(!empty($_SESSION['authenticated_user_theme_font']) 
                            && in_array($_SESSION['authenticated_user_theme_font'],$themeObject->fonts) )
                        $startingFontName = $_SESSION['authenticated_user_theme_font'];
                    $startingFontIcon = $selectedTheme->getImageURL("fonts.{$startingFontName}.icon.gif");
                    $allThemes[$dirName]['fontSelect'] = <<<EOHTML
<input type="hidden" name="userfont" id="userfont" value="">
<ul id="font_menu">
EOHTML;
                    foreach ( $selectedTheme->fonts as $font ) {
                        $fontIcon = $selectedTheme->getImageURL("fonts.{$font}.icon.gif");
                        $allThemes[$dirName]['fontSelect'] .= <<<EOHTML
<li style="background: url({$fontIcon}) no-repeat;" onclick="change_font('{$font}');"></li>
EOHTML;
                    }
                    $allThemes[$dirName]['fontSelect'] .= '</ul></span>';
                }
            }
            $json = getJSONobj();
            $ss->assign('allThemes',$json->encode($allThemes));
            
            $ss->assign("CURRENTTHEME_NAME",$themeObject->name);
            $ss->assign("CURRENTTHEME_DESCRIPTION",$themeObject->description);
            $ss->assign("CURRENTTHEME_FONTSELECT",$allThemes[$themeObject->dirName]['fontSelect']);
            $ss->assign("CURRENTTHEME_COLORSELECT",$allThemes[$themeObject->dirName]['colorSelect']);
            // get the current user name and id
            $ss->assign("CURRENT_USER", $current_user->first_name != '' 
                ? $current_user->first_name : $current_user->user_name);
            $ss->assign("CURRENT_USER_ID", $current_user->id);
            
            // get the last viewed records
            $tracker = new Tracker();
            $history = $tracker->get_recently_viewed($current_user->id);
            foreach ( $history as $key => $row ) {
                $history[$key]['item_summary_short'] = getTrackerSubstring($row['item_summary']);
                $history[$key]['image'] = SugarThemeRegistry::current()
                    ->getImage($row['module_name'],'border="0" align="absmiddle" alt="'.$row['item_summary'].'"');
            }
            $ss->assign("recentRecords",$history);
            
            // new record
            require_once("modules/".$this->module."/Forms.php");
            if ($this->action == "index" && function_exists('get_new_record_form'))
                $ss->assign("NEW_RECORD", get_new_record_form());
            
            // Get preference for shortcuts and last_viewed list placement
            $last_view_swap = $current_user->getPreference('swap_last_viewed');
            if(!isset($last_view_swap)) 
                $last_view_swap = $GLOBALS['sugar_config']['default_swap_last_viewed'];
            $ss->assign("LEFT_FORM_LAST_VIEWED",$last_view_swap);
            
            $shortcuts_swap = $current_user->getPreference('swap_shortcuts');
            if(!isset($shortcuts_swap)) 
                $shortcuts_swap = $GLOBALS['sugar_config']['default_swap_shortcuts'];
            $ss->assign("LEFT_FORM_SHORTCUTS",!$shortcuts_swap);
        }
        
        if (isset($_SESSION["authenticated_user_id"]) || $sugar_config['login_nav'] ) {
            // get the module list
            $moduleTopMenu = array();
            
            $max_tabs = $current_user->getPreference('max_tabs');
            if ( !isset($max_tabs) || $max_tabs <= 0 )
                $max_tabs = $GLOBALS['sugar_config']['default_max_tabs'];
            
            $user_navigation_paradigm = $current_user->getPreference('navigation_paradigm');
            if ( !isset($user_navigation_paradigm) ) 
                $user_navigation_paradigm = $GLOBALS['sugar_config']['default_navigation_paradigm'];
            
            if ( $user_navigation_paradigm == 'gm' ) {
                require_once('include/GroupedTabs/GroupedTabStructure.php');
                $groupedTabsClass = new GroupedTabStructure();
                
                $modules = query_module_access_list($current_user);
                $ss->assign('groupTabs',
                    $groupedTabsClass->get_tab_structure(get_val_array($modules)));
                $ss->assign('moduleNames',$app_list_strings['moduleList']);
                $ss->assign('defaultFirst',!in_array($this->module,$modules));
                $ss->assign('USE_GROUP_TABS',true);
            }
            else {
                $moduleExtraMenu = array();
                $i = 1;
                foreach ( query_module_access_list($current_user) as $module ) {
                    // Bug 25948 - Check for the module being in the moduleList
                    if ( isset($app_list_strings['moduleList'][$module]) ) {
                        if ( $i < $max_tabs )
                            $moduleTopMenu[$module] = $app_list_strings['moduleList'][$module];
                        else
                            $moduleExtraMenu[$module] = $app_list_strings['moduleList'][$module];
                        ++$i;
                    }
                }
                // Now, we'll push the current module into the end of top menu list if it's not
                // already there. In addition, we'll preserve this last entry for this session
                // until a new value is added there.
                if ( isset($moduleTopMenu[$this->module]) ) {
                    if ( isset($_SESSION['moreTab']) && isset($app_list_strings['moduleList'][$_SESSION['moreTab']])) {
                        $moduleTopMenu[$_SESSION['moreTab']] = $app_list_strings['moduleList'][$_SESSION['moreTab']];
                        unset($moduleExtraMenu[$_SESSION['moreTab']]);
                    }
                    else {
                        $moduleTopMenu += array_slice($moduleExtraMenu,0,1);
                        array_shift($moduleExtraMenu);
                    }
                }
                elseif ( isset($moduleExtraMenu[$this->module]) ) {
                    $_SESSION['moreTab'] = $this->module;
                    $moduleTopMenu[$_SESSION['moreTab']] = $app_list_strings['moduleList'][$_SESSION['moreTab']];
                    unset($moduleExtraMenu[$_SESSION['moreTab']]);
                }
                elseif ( isset($_SESSION['moreTab']) && isset($app_list_strings['moduleList'][$_SESSION['moreTab']])) {
                    $moduleTopMenu[$_SESSION['moreTab']] = $app_list_strings['moduleList'][$_SESSION['moreTab']];
                    unset($moduleExtraMenu[$_SESSION['moreTab']]);
                }
                $ss->assign("moduleTopMenu",$moduleTopMenu);
                $ss->assign("moduleExtraMenu",$moduleExtraMenu);
            }
        }
        $headerTpl = $themeObject->getTemplate('header.tpl');
        if ( isset($GLOBALS['sugar_config']['developerMode']) && $GLOBALS['sugar_config']['developerMode'] )
            $ss->clear_compiled_tpl($headerTpl);
        $ss->display($headerTpl);
        
        $this->includeClassicFile('modules/Administration/DisplayWarnings.php');
    }
    /**
     * If the view is classic then this method will include the file and
     * setup any global variables.
     */
    function includeClassicFile($file)
    {
        global $sugar_config, $theme, $current_user, $sugar_version, $sugar_flavor, $mod_strings, $app_strings, $app_list_strings, $action, $timezones;
        global $gridline, $request_string, $modListHeader, $module_menu, $dashletData, $authController, $locale, $currentModule, $import_bean_map, $license;
        global $user_unique_key, $server_unique_key, $barChartColors, $modules_exempt_from_availability_check, $dictionary, $current_language, $beanList, $beanFiles, $sugar_build, $sugar_codename;
        global $timedate, $login_error; // cn: bug 13855 - timedate not available to classic views.
        $currentModule = $this->module;
        require_once ($file);
    }

    function displayLoginJS()
    {
        if(isset($this->bean->module_dir)){
            echo "<script>var module_sugar_grp1 = '{$this->bean->module_dir}';</script>";
        }
        if(isset($_REQUEST['action'])){
            echo "<script>var action_sugar_grp1 = '{$_REQUEST['action']}';</script>";
        }
        echo '<script>jscal_today = ' . (1000*strtotime($GLOBALS['timedate']->handle_offset(gmdate($GLOBALS['timedate']->get_db_date_time_format()), $GLOBALS['timedate']->get_db_date_time_format()))) . '; if(typeof app_strings == "undefined") app_strings = new Array();</script>';
        echo '<script type="text/javascript" src="' . getJSPath('include/javascript/sugar_grp1_yui.js') . '"></script>';
        echo '<script type="text/javascript" src="' . getJSPath('include/javascript/sugar_grp1.js') . '"></script>';
        echo '<script type="text/javascript" src="' . getJSPath('jscalendar/lang/calendar-' . substr($GLOBALS['current_language'], 0, 2) . '.js') . '"></script>';
        echo <<<EOQ
		<script>
			if ( typeof(SUGAR) == 'undefined' ) {SUGAR = {}};
			if ( typeof(SUGAR.themes) == 'undefined' ) SUGAR.themes = {};
		</script>
EOQ;
    }

    /**
     * Called from process(). This method will display the correct javascript.
     */
    function displayJavascript()
    {
        global $locale;
    	//echo out the headers to enable caching
        if ($this->_getOption('show_header')) {
            echo "<html><head>";
        }






        if ($this->_getOption('show_javascript')) {
            echo "<script>var sugar_cache_dir = '{$GLOBALS['sugar_config']['cache_dir']}';</script>";
            echo "<script>var sugar_upload_dir = '{$GLOBALS['sugar_config']['upload_dir']}';</script>";
        
        	if(isset($this->bean->module_dir)){
                echo "<script>var module_sugar_grp1 = '{$this->bean->module_dir}';</script>";
            }
            if(isset($_REQUEST['action'])){
                echo "<script>var action_sugar_grp1 = '{$_REQUEST['action']}';</script>";
            }
            echo '<script>jscal_today = ' . (1000*strtotime($GLOBALS['timedate']->handle_offset(gmdate($GLOBALS['timedate']->get_db_date_time_format()), $GLOBALS['timedate']->get_db_date_time_format()))) . '; if(typeof app_strings == "undefined") app_strings = new Array();</script>';
            echo '<script type="text/javascript" src="' . getJSPath('include/javascript/sugar_grp1_yui.js') . '"></script>';
            echo '<script type="text/javascript" src="' . getJSPath('include/javascript/sugar_grp1.js') . '"></script>';
            echo '<script type="text/javascript" src="' . getJSPath('jscalendar/lang/calendar-' . substr($GLOBALS['current_language'], 0, 2) . '.js') . '"></script>';

            // cn: bug 12274 - prepare secret guid for asynchronous calls
            if (!isset($_SESSION['asynchronous_key']) || empty($_SESSION['asynchronous_key'])) {
                $_SESSION['asynchronous_key'] = create_guid();
            }
            $image_server = (defined('TEMPLATE_URL'))?TEMPLATE_URL . '/':'';
            echo '<script type="text/javascript">var asynchronous_key = "' . $_SESSION['asynchronous_key'] . '";SUGAR.themes.image_server="' . $image_server . '";</script>'; // cn: bug 12274 - create session-stored key to defend against CSRF
            echo '<script type="text/javascript"> var name_format = "' . $locale->getLocaleFormatMacro() . '";</script>';
            echo $GLOBALS['timedate']->get_javascript_validation();
            if (!is_file($GLOBALS['sugar_config']['cache_dir'] . 'jsLanguage/' . $GLOBALS['current_language'] . '.js')) {
                require_once ('include/language/jsLanguage.php');
                jsLanguage::createAppStringsCache($GLOBALS['current_language']);
            }
            echo '<script type="text/javascript" src="' . $GLOBALS['sugar_config']['cache_dir'] . 'jsLanguage/' . $GLOBALS['current_language'] . '.js?s=' . $GLOBALS['js_version_key'] . '&c=' . $GLOBALS['sugar_config']['js_custom_version'] . '&j=' . $GLOBALS['sugar_config']['js_lang_version'] . '"></script>';
            if (!is_file($GLOBALS['sugar_config']['cache_dir'] . 'jsLanguage/' . $this->module . '/' . $GLOBALS['current_language'] . '.js')) {
                require_once ('include/language/jsLanguage.php');
                jsLanguage::createModuleStringsCache($this->module, $GLOBALS['current_language']);
            }
            echo '<script type="text/javascript" src="' . $GLOBALS['sugar_config']['cache_dir'] . 'jsLanguage/' . $this->module . '/' . $GLOBALS['current_language'] . '.js?s=' . $GLOBALS['js_version_key'] . '&c=' . $GLOBALS['sugar_config']['js_custom_version'] . '&j=' . $GLOBALS['sugar_config']['js_lang_version'] . '"></script>';
        	
        }

        if (isset($_REQUEST['popup']) && !empty($_REQUEST['popup'])) {
            // cn: bug 12274 - add security metadata envelope for async calls in popups
            echo '<script type="text/javascript">var asynchronous_key = "' . $_SESSION['asynchronous_key'] . '";</script>'; // cn: bug 12274 - create session-stored key to defend against CSRF
        }
    }

    /**
     * Called from process(). This method will display the footer on the page.
     */
    function displayFooter()
    {
        if (empty($this->responseTime)) {
            $this->calculateFooterMetrics();
        }
        global $sugar_config;
        global $app_strings;
        
        //decide whether or not to show themepicker, default is to show
        $showThemePicker = true;
        if (isset($sugar_config['showThemePicker'])) {
            $showThemePicker = $sugar_config['showThemePicker'];
        }
        
        echo "<!-- crmprint -->";
        $jsalerts = new jsAlerts();
        if ( !isset($_SESSION['isMobile']) )
            echo $jsalerts->getScript();
        
        $ss = new Sugar_Smarty();
        $ss->assign("AUTHENTICATED",isset($_SESSION["authenticated_user_id"]));
        $ss->assign('MOD',return_module_language($GLOBALS['current_language'], 'Users'));
        
        if (SugarConfig::getInstance()->get('calculate_response_time', false))
            $ss->assign('STATISTICS',$this->_getStatistics());
        
        // Under the License referenced above, you are required to leave in all copyright statements in both
        // the code and end-user application.
    

        $copyright = '&copy; 2004-2009 SugarCRM Inc. The Program is provided AS IS, without warranty.  Licensed under <a href="LICENSE.txt" target="_blank" class="copyRightLink">GPLv3</a>.<br>This program is free software; you can redistribute it and/or modify it under the terms of the <br><a href="LICENSE.txt" target="_blank" class="copyRightLink"> GNU General Public License version 3</a> as published by the Free Software Foundation including the additional permission set forth in the source code header.<br>';

		











		
        
        
        
		

		
		
        // The interactive user interfaces in modified source and object code
        // versions of this program must display Appropriate Legal Notices, as
        // required under Section 5 of the GNU General Public License version
        // 3. In accordance with Section 7(b) of the GNU General Public License
        // version 3, these Appropriate Legal Notices must retain the display
        // of the "Powered by SugarCRM" logo. If the display of the logo is
        // not reasonably feasible for technical reasons, the Appropriate
        // Legal Notices must display the words "Powered by SugarCRM".
        $attribLinkImg = "<img style='margin-top: 2px' border='0' width='106' height='23' src='include/images/poweredby_sugarcrm.png' alt='Powered By SugarCRM'>\n";

		







        //rrs bug: 20923 - if this image does not exist as per the license, then the proper image will be displaye regardless, so no need
		//to display an empty image here.
		if(file_exists('include/images/poweredby_sugarcrm.png')){
			$copyright .= $attribLinkImg;
		}
        // End Required Image
        $ss->assign('COPYRIGHT',$copyright);
        $ss->display(SugarThemeRegistry::current()->getTemplate('footer.tpl'));
    }

    function displayBody()
    {
    }

    /**
     * Called from process(). This method will display subpanels.
     */
    function displaySubPanels()
    {
        if (isset($this->bean) && !empty($this->bean->id) && (file_exists('modules/' . $this->module . '/metadata/subpaneldefs.php') || file_exists('custom/modules/' . $this->module . '/metadata/subpaneldefs.php') || file_exists('custom/modules/' . $this->module . '/Ext/Layoutdefs/layoutdefs.ext.php'))) {
            $GLOBALS['focus'] = $this->bean;
            require_once ('include/SubPanel/SubPanelTiles.php');
            $subpanel = new SubPanelTiles($this->bean, $this->module);
            echo $subpanel->display();
        }
    }

    /**
     * Called from process(). This method will display the search form on the left of the page.
     */
    function displaySearch()
    {
        global $app_list_strings;
        
        $savedSearch = new SavedSearch();
        $json = getJSONobj();
        $savedSearchSelects = $json->encode(array($GLOBALS['app_strings']['LBL_SAVED_SEARCH_SHORTCUT'] . '<br>' . $savedSearch->getSelect($this->module)));
        $str = "<script>
		YAHOO.util.Event.addListener(window, 'load', SUGAR.util.fillShortcuts, $savedSearchSelects);
		</script>";
        echo $str;
    }
    function buildModuleList()
    {
        if (!empty($GLOBALS['current_user']) && empty($GLOBALS['modListHeader'])) $GLOBALS['modListHeader'] = query_module_access_list($GLOBALS['current_user']);
    }

    /**
     * private method used in process() to determine the value of a passed in option
     *
     * @param string option - the option that we want to know the valye of
     * @param bool default - what the default value should be if we do not find the option
     *
     * @return bool - the value of the option
     */
    function _getOption($option, $default = false)
    {
        if (!empty($this->options) && isset($this->options['show_all'])) {
            return $this->options['show_all'];
        } elseif (!empty($this->options) && isset($this->options[$option])) {
            return $this->options[$option];
        } else return $default;
    }

    /**
     * Check for the existence of a Menu.php file
     *
     * @param string $module - the module to check.
     * @return boolean - true if a Menu.php file exists, false otherwise.
     */
    protected function _menuExists($module)
    {
        if (!file_exists('modules/' . $module . '/Menu.php') && !file_exists('custom/modules/' . $module . 'Ext/Menus/menu.ext.php') && !file_exists('custom/application/Ext/Menus/menu.ext.php')) return false;
        else return true;
    }

    /**
     * track
     * Private function to track information about the view request
     */
    private function track()
    {
        if (empty($this->responseTime)) {
            $this->calculateFooterMetrics();
        }
        if (empty($GLOBALS['current_user']->id)) {
            return;
        }

        
        $trackerManager = TrackerManager::getInstance();






















	    $trackerManager->save();
		
    }

    protected function checkModule(){
    	if(!empty($this->module) && !file_exists('modules/'.$this->module)){
    		$error = str_replace("[module]", "$this->module", $GLOBALS['app_strings']['ERR_CANNOT_FIND_MODULE']);
        	$GLOBALS['log']->fatal($error);
        	echo $error;
        	die();
    	}
    }

    protected function renderJavascript() {
        if ($this->action !== 'Login') {
            $this->displayJavascript();
        } else {
            $this->displayLoginJS(); 
        }  	
    }    
    
    private function calculateFooterMetrics()
    {
        $endTime = microtime(true);
        $deltaTime = $endTime - $GLOBALS['startTime'];
        $this->responseTime = number_format(round($deltaTime, 2), 2);
        // Print out the resources used in constructing the page.
        $included_files = get_included_files();
        // take all of the included files and make a list that does not allow for duplicates based on case
        // I believe the full get_include_files result set appears to have one entry for each file in real
        // case, and one entry in all lower case.
        $list_of_files_case_insensitive = array();
        foreach($included_files as $key => $name) {
            // preserve the first capitalization encountered.
            $list_of_files_case_insensitive[mb_strtolower($name) ] = $name;
        }
        $this->fileResources = sizeof($list_of_files_case_insensitive);
    }
    
    private function _getStatistics()
    {
        $endTime = microtime(true);
        $deltaTime = $endTime - $GLOBALS['startTime'];
        $response_time_string = $GLOBALS['app_strings']['LBL_SERVER_RESPONSE_TIME'] . " " . number_format(round($deltaTime, 2), 2) . " " . $GLOBALS['app_strings']['LBL_SERVER_RESPONSE_TIME_SECONDS'];
        $return = $response_time_string;
        $return .= '<br />';















        if (!empty($GLOBALS['sugar_config']['show_page_resources'])) {
            // Print out the resources used in constructing the page.
            $included_files = get_included_files();

            // take all of the included files and make a list that does not allow for duplicates based on case
            // I believe the full get_include_files result set appears to have one entry for each file in real
            // case, and one entry in all lower case.
            $list_of_files_case_insensitive = array();
            foreach($included_files as $key => $name) {
                // preserve the first capitalization encountered.
                $list_of_files_case_insensitive[mb_strtolower($name) ] = $name;
            }
            $return .= $GLOBALS['app_strings']['LBL_SERVER_RESPONSE_RESOURCES'] . '(' . DBManager::getQueryCount() . ',' . sizeof($list_of_files_case_insensitive) . ')<br>';
            // Display performance of the internal and external caches....
            $return .= "External cache (hits/total=ratio) local ({$GLOBALS['external_cache_request_local_hits']}/{$GLOBALS['external_cache_request_local_total']}=" . round($GLOBALS['external_cache_request_local_hits']*100/$GLOBALS['external_cache_request_local_total'], 0) . "%)";
            if ($GLOBALS['external_cache_request_external_total']) {
                // Only display out of process cache results if there was at least one attempt to retrieve from the out of process cache (this signifies that it was enabled).
                $return .= " external ({$GLOBALS['external_cache_request_external_hits']}/{$GLOBALS['external_cache_request_external_total']}=" . round($GLOBALS['external_cache_request_external_hits']*100/$GLOBALS['external_cache_request_external_total'], 0) . "%)<br />";
            }
        }
        
        return $return;
    }
    
    /**
     * Returns the name of the current theme
     *
     * @return string name of the theme
     */
    public function getTheme()
    {
        return $this->theme;
    }
}

