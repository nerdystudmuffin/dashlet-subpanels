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

class Dashlet {
   /**
     * Id of the Dashlet
     * @var guid
     */ 
    var $id; 
    /**
     * Title of the Dashlet
     * @var string
     */
    var $title = 'Generic Dashlet';
    /**
     * true if the Dashlet has configuration options. 
     * @var bool
     */
    var $isConfigurable = false;
    /**
     * true if the Dashlet is refreshable (ie charts that provide their own refresh) 
     * @var bool
     */
    var $isRefreshable = true;
    /**
     * true if the Dashlet contains javascript 
     * @var bool
     */
    var $hasScript = false;
    /**
     * Language strings, must be loaded at the Dashlet level w/ loadLanguage
     * @var array
     */
    var $dashletStrings;
    
    function Dashlet($id) {
        $this->id = $id;
    }
    
    function setConfigureIcon(){
    	
    	
        if($this->isConfigurable) 
            $additionalTitle = '<td nowrap width="1%"><div style="width: 100%;text-align:right"><a href="#" onclick="SUGAR.mySugar.configureDashlet(\'' 
                               . $this->id . '\'); return false;">'    
                               . SugarThemeRegistry::current()->getImage('edit','title="' . translate('LBL_DASHLET_EDIT', 'Home') . '" alt="' . translate('LBL_DASHLET_EDIT', 'Home') . '"  border="0"  align="absmiddle"').'</a> ' 
                               . '';
        else 
            $additionalTitle = '<td nowrap width="1%"><div style="width: 100%;text-align:right">';    	
    	
    	return $additionalTitle;
    }
    
    function setRefreshIcon(){
    	
    	
    	$additionalTitle = '';
        if($this->isRefreshable)
            $additionalTitle .= '<a href="#" onclick="SUGAR.mySugar.retrieveDashlet(\'' 
                                . $this->id . '\'); return false;">'
                                . SugarThemeRegistry::current()->getImage('refresh','border="0" align="absmiddle" title="' . translate('LBL_DASHLET_REFRESH', 'Home') . '" alt="' . translate('LBL_DASHLET_REFRESH', 'Home') . '"')
                                . '</a> ';
        return $additionalTitle;
    }
    
    function setDeleteIcon(){
    	
    	
    	$additionalTitle = '';
        $additionalTitle .= '<a href="#" onclick="SUGAR.mySugar.deleteDashlet(\'' 
                            . $this->id . '\'); return false;">'
                            . SugarThemeRegistry::current()->getImage('close_dashboard','border="0" align="absmiddle" title="' . translate('LBL_DASHLET_DELETE', 'Home') . '" alt="' . translate('LBL_DASHLET_DELETE', 'Home') . '"')
                            . '</a></div></td></tr></table>';
		return $additionalTitle;                                	
    }
    
    /**
     * Called when Dashlet is displayed
     * 
     * @param string $text text after the title
     * @return string title html
     */
    function getTitle($text) {
        global $app_strings, $sugar_config;
        
        $title = '<table width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td width="99%">' . $text . '</td>';
        $title .= $this->setConfigureIcon();
        $title .= $this->setRefreshIcon();
        $title .= $this->setDeleteIcon();
            
        if(!function_exists('get_form_header')) {
            global $theme;
            
        }
        
        $str = '<div ';
        if(empty($sugar_config['lock_homepage']) || $sugar_config['lock_homepage'] == false) $str .= ' onmouseover="this.style.cursor = \'move\';"';
        $str .= 'id="dashlet_header_' . $this->id . '">' . get_form_header($this->title, $title, false) . '</div>';
        
        return $str;
    }
    /**
     * Called when Dashlet is displayed, override this
     * 
     * @param string $text text after the title
     * @return string title html
     */
    function display($text = '') {
        return $this->getTitle($text);
    }
    
    /**
     * Called when Dashlets configuration options are called
     * 
     */
    function displayOptions() {
    }
    
    /**
     * override if you need to do pre-processing before display is called
     * 
     */
    function process() {
    }    
    
    function save() {
    }
    
    /**
     * Override this if your dashlet is configurable (this is called when the the configureDashlet form is shown)
     * Filters the array for only the parameters it needs to save
     * 
     * @param array $req the array to pull options from
     * 
     * @return array options array
     */
    function saveOptions($req) {
    }
    
    /**
     * Sets the language strings
     * 
     * @param string $dashletClassname classname of the dashlet
     * 
     */
    function loadLanguage($dashletClassname, $dashletDirectory = 'modules/Home/Dashlets/') {
        global $current_language, $dashletStrings;
        
        if(!isset($dashletStrings[$dashletClassname])) {
            // load current language strings for current language, else default to english
            if(is_file($dashletDirectory . $dashletClassname . '/' . $dashletClassname . '.' . $current_language . '.lang.php')) 
                require_once($dashletDirectory . $dashletClassname . '/' . $dashletClassname . '.' . $current_language . '.lang.php');
            else 
                require_once($dashletDirectory . $dashletClassname . '/' . $dashletClassname . '.en_us.lang.php');
        }

        $this->dashletStrings = $dashletStrings[$dashletClassname];
    }
    
    /**
     * Generic way to store an options array into UserPreferences
     * 
     * @param array $optionsArray the array to save
     */
    function storeOptions($optionsArray) {
        global $current_user;
        $dashletDefs = $current_user->getPreference('dashlets', 'Home'); // load user's dashlets config
        $dashletDefs[$this->id]['options'] = $optionsArray;
        $current_user->setPreference('dashlets', $dashletDefs, 0, 'Home');   
    }
    
    /**
     * Generic way to retrieve options array from UserPreferences
     * 
     * @return array options array stored in UserPreferences
     */
    function loadOptions() {
        global $current_user;
        $dashletDefs = $current_user->getPreference('dashlets', 'Home'); // load user's dashlets config
        if(isset($dashletDefs[$this->id]['options']))
            return $dashletDefs[$this->id]['options'];
        else 
            return array();   
    }
    
    /**
     * Override this in the subclass. It is used to determine whether the dashlet can be displayed.
     * 
     * @return bool indicating whether or not the current user has access to display this Dashlet.
     */
    function hasAccess(){
    	return true;
    }
    
    /**
     * Generic way call a proxy, pass in the parameters url (and optionally postData) to fetch a url
     * use the parameter method for post or get.
     * @return array options array stored in UserPreferences
     */    
    /**
     * ON VACATION WILL BE BACK SOON
    function proxyCall() {
        if(!empty($_REQUEST['method']) && !empty($_REQUEST['url'])) {
            if(strtolower($_REQUEST['method']) == 'get') {
                // taken from PHP Cookbook pg 283
                $page = '';
                $fh = fopen($_REQUEST['url'], 'r');
                while(!feof($fh)) {
                    $page .= fread($fh, 1048576);
                }
                fclose($fh);
                echo $page;
            }
            else if(strtolower($_REQUEST['method']) == 'post') {
                //
            }
        }
        
    }
    **/
}
?>
