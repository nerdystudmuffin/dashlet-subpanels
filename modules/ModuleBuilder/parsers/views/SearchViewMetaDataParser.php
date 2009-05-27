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


require_once ('modules/ModuleBuilder/parsers/views/ListLayoutMetaDataParser.php') ;
require_once 'modules/ModuleBuilder/parsers/constants.php' ;

class SearchViewMetaDataParser extends ListLayoutMetaDataParser
{
    static $variableMap = array (
    						MB_BASICSEARCH => 'basic_search' ,
    						MB_ADVANCEDSEARCH => 'advanced_search' ,




    						) ;
    // Columns is used by the view to construct the listview - each column is built by calling the named function
    public $columns = array ( 'LBL_DEFAULT' => 'getDefaultFields' , 'LBL_HIDDEN' => 'getAvailableFields' ) ;
    protected $allowParent = true;

    /*
     * Constructor
     * Must set:
     * $this->columns   Array of 'Column LBL'=>function_to_retrieve_fields_for_this_column() - expected by the view
     * @param string searchLayout	The type of search layout, e.g., MB_BASICSEARCH or MB_ADVANCEDSEARCH
     * @param string moduleName     The name of the module to which this listview belongs
     * @param string packageName    If not empty, the name of the package to which this listview belongs
     */
    function __construct ($searchLayout, $moduleName , $packageName = '')
    {
        $GLOBALS [ 'log' ]->debug ( get_class ( $this ) . ": __construct( $searchLayout , $moduleName , $packageName )" ) ;

        // BEGIN ASSERTIONS
        if (! isset ( self::$variableMap [ $searchLayout ] ) )
        {
            sugar_die ( get_class ( $this ) . ": View $searchLayout is not supported" ) ;
        }
        // END ASSERTIONS

        $this->_searchLayout = $searchLayout ;

        // unsophisticated error handling for now...
        try
        {
        	if (empty ( $packageName ))
        	{
            	require_once 'modules/ModuleBuilder/parsers/views/DeployedMetaDataImplementation.php' ;
            	$this->implementation = new DeployedMetaDataImplementation ( $searchLayout, $moduleName ) ;
        	} else
        	{
            	require_once 'modules/ModuleBuilder/parsers/views/UndeployedMetaDataImplementation.php' ;
            	$this->implementation = new UndeployedMetaDataImplementation ( $searchLayout, $moduleName, $packageName ) ;
        	}
        } catch (Exception $e)
        {
        	throw $e ;
        }

        $this->_saved = array_change_key_case ( $this->implementation->getViewdefs () ) ; // force to lower case so don't have problems with case mismatches later
        if(isset($this->_saved['templatemeta'])) {
            $this->_saved['templateMeta'] = $this->_saved['templatemeta'];
            unset($this->_saved['templatemeta']);
        }

        if ( ! isset ( $this->_saved [ 'layout' ] [ self::$variableMap [ $this->_searchLayout ] ] ) )
        {
        	// attempt to fallback on a basic_search layout...

        	if ( ! isset ( $this->_saved [ 'layout' ] [ self::$variableMap [ MB_BASICSEARCH ] ] ) )
        		throw new Exception ( get_class ( $this ) . ": {$this->_searchLayout} does not exist for module $moduleName" ) ;

        	$this->_saved [ 'layout'] [ MB_ADVANCEDSEARCH ] = $this->_saved [ 'layout' ] [ MB_BASICSEARCH ] ;
        }

        // convert the search view layout (which has its own unique layout form) to the standard listview layout so that the parser methods and views can be reused
        $this->_viewdefs = $this->convertSearchViewToListView ( $this->_saved [ 'layout' ] [ self::$variableMap [ $this->_searchLayout ] ] ) ;
        $this->_fielddefs = $this->implementation->getFielddefs () ;
        $this->_standardizeFieldLabels( $this->_fielddefs );

    }

    /*
     * Save the modified searchLayout
     * Have to preserve the original layout format, which is array('metadata'=>array,'layouts'=>array('basic'=>array,'advanced'=>array))
     */
    function handleSave ($populate = true)
    {
        if ($populate)
            $this->_populateFromRequest() ;
            





            
        $this->_saved [ 'layout' ] [ self::$variableMap [ $this->_searchLayout ] ] = $this->convertSearchViewToListView($this->_viewdefs);;
        $this->implementation->deploy ( $this->_saved ) ;
    }

    private function convertSearchViewToListView ($viewdefs)
    {
        $temp = array ( ) ;
        foreach ( $viewdefs as $key => $value )
        {
            if (! is_array ( $value ))
            {
                $key = $value ;
                $def = array ( ) ;
                $def[ 'name' ] = $key;
                $value = $def ;
            }

            if (!isset ( $value [ 'name' ] ))
            {
                $value [ 'name' ] = $key;
            }
            else
            {
                $key = $value [ 'name' ] ; // override key with name, needed when the entry lacks a key
            }
            // now add in the standard listview default=>true
            $value [ 'default' ] = true ;
            $temp [ strtolower ( $key ) ] = $value ;
        }
        return $temp ;
    }
    
    function getOriginalViewDefs() {
        $defs = $this->implementation->getOriginalViewdefs ();
        $out = array();
        if (!empty($defs) && !empty($defs['layout']) && !empty($defs['layout'][$this->_searchLayout]))
        {
        	$defs = $defs['layout'][$this->_searchLayout];
	        foreach ($defs as $def)
	        {
	            if (is_array($def) && isset($def['name']))
	            {
	            	$out[strtolower($def['name'])] = $def;
	            }
	        }
        }

        return $out;
    }
}
?>