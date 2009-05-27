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
/*
 * Implementation class (following a Bridge Pattern) for handling loading and saving deployed module metadata
 * For example, listview or editview viewdefs
 */

require_once 'modules/ModuleBuilder/parsers/views/AbstractMetaDataImplementation.php' ;
require_once 'modules/ModuleBuilder/parsers/views/MetaDataImplementationInterface.php' ;
require_once 'modules/ModuleBuilder/parsers/views/ListLayoutMetaDataParser.php' ;
require_once 'modules/ModuleBuilder/parsers/views/GridLayoutMetaDataParser.php' ;
require_once 'modules/ModuleBuilder/Module/StudioModule.php' ;
require_once 'modules/ModuleBuilder/parsers/constants.php' ;

class DeployedMetaDataImplementation extends AbstractMetaDataImplementation implements MetaDataImplementationInterface
{

	/*
	 * Constructor
	 * @param string $view
	 * @param string $moduleName
	 * @throws Exception Thrown if the provided view doesn't exist for this module
	 */
	function __construct ($view , $moduleName)
	{

		// BEGIN ASSERTIONS
		if (! isset ( $this->_fileVariables [ $view ] ))
		{
			sugar_die ( get_class ( $this ) . ": View $view is not supported" ) ;
		}
		if (! isset ( $GLOBALS [ 'beanList' ] [ $moduleName ] ))
		{
			sugar_die ( get_class ( $this ) . ": Modulename $moduleName is not a Deployed Module" ) ;
		}
		// END ASSERTIONS

		$this->_view = strtolower ( $view ) ;
		$this->_moduleName = $moduleName ;

		$module = new StudioModule ( $moduleName ) ;
		$fielddefs = $module->getFields();

		$loaded = null ;
		foreach ( array ( MB_BASEMETADATALOCATION , MB_CUSTOMMETADATALOCATION , MB_WORKINGMETADATALOCATION , MB_HISTORYMETADATALOCATION ) as $type )
		{
			$this->_sourceFilename = $this->getFileName ( $view, $moduleName, $type ) ;
			if ( null !== ($layout = $this->_loadFromFile ( $this->_sourceFilename ) ) )
			{
				// merge in the fielddefs from this layout
				$this->_mergeFielddefs ( $fielddefs , $layout ) ;
				$loaded = $layout ;
			}
		}

		if ($loaded === null)
		{
			switch ( $view )
			{
				case MB_QUICKCREATE:
					// Special handling for QuickCreates - if we don't have a QuickCreate definition in the usual places, then use an EditView

					$loaded = $this->_loadFromFile ( $this->getFileName ( MB_EDITVIEW, $this->_moduleName, MB_BASEMETADATALOCATION ) ) ;

					if ($loaded === null)
						throw new Exception( get_class ( $this ) . ": cannot convert from EditView to QuickCreate for Module $this->_moduleName - definitions for EditView are missing" ) ;

					// Now change the array index
					$temp = $loaded [ GridLayoutMetaDataParser::$variableMap [ MB_EDITVIEW ] ] ;
					unset ( $loaded [ GridLayoutMetaDataParser::$variableMap [ MB_EDITVIEW ] ] ) ;
					$loaded [ GridLayoutMetaDataParser::$variableMap [ MB_QUICKCREATE ] ] = $temp ;
					// finally, save out our new definition so that we have a base record for the history to work from
					$this->_sourceFilename = self::getFileName ( MB_QUICKCREATE, $this->_moduleName, MB_CUSTOMMETADATALOCATION ) ;
					$this->_saveToFile ( $this->_sourceFilename, $loaded ) ;
					$this->_mergeFielddefs ( $fielddefs , $loaded ) ;
					break;























				default:

			}
			if ( $loaded === null )
				throw new Exception( get_class ( $this ) . ": view definitions for View $this->_view and Module $this->_moduleName are missing" ) ;
		}

		$this->_viewdefs = $loaded ;
		// Set the original Viewdefs - required to ensure we don't lose fields from the base layout
		// Check the base location first, then if nothing is there (which for example, will be the case for some QuickCreates, and some mobile layouts - see above)
		// we need to check the custom location where the derived layouts will be
		foreach ( array ( MB_BASEMETADATALOCATION , MB_CUSTOMMETADATALOCATION ) as $type )
		{
			$sourceFilename = $this->getFileName ( $view, $moduleName, $type ) ;
			if ( null !== ($layout = $this->_loadFromFile ( $sourceFilename ) ) )
			{
				$this->_originalViewdefs = $layout ;
				break ;
			}
		}
		$this->_fielddefs = $fielddefs ;
		$this->_history = new History ( $this->getFileName ( $view, $moduleName, MB_HISTORYMETADATALOCATION ) ) ;

	}

	function getLanguage ()
	{
		return $this->_moduleName ;
	}


	/*
	 * Save a draft layout
	 * @param array defs    Layout definition in the same format as received by the constructor
	 */
	function save ($defs)
	{
		//If we are pulling from the History Location, that means we did a restore, and we need to save the history for the previous file.
		if ($this->_sourceFilename == $this->getFileName ( $this->_view, $this->_moduleName, MB_HISTORYMETADATALOCATION )) {
			foreach ( array ( MB_WORKINGMETADATALOCATION , MB_CUSTOMMETADATALOCATION , MB_BASEMETADATALOCATION ) as $type ) {
				if (file_exists($this->getFileName ( $this->_view, $this->_moduleName, $type ))) {
					$this->_history->append ( $this->getFileName ( $this->_view, $this->_moduleName, $type )) ;
					break;
				}
			}
		} else {
			$this->_history->append ( $this->_sourceFilename ) ;
		}

		$GLOBALS [ 'log' ]->debug ( get_class ( $this ) . "->save(): writing to " . $this->getFileName ( $this->_view, $this->_moduleName, MB_WORKINGMETADATALOCATION ) ) ;
		$this->_saveToFile ( $this->getFileName ( $this->_view, $this->_moduleName, MB_WORKINGMETADATALOCATION ), $defs ) ;
	}

	/*
	 * Deploy a layout
	 * @param array defs    Layout definition in the same format as received by the constructor
	 */
	function deploy ($defs)
	{
		if ($this->_sourceFilename == $this->getFileName ( $this->_view, $this->_moduleName, MB_HISTORYMETADATALOCATION )) {
			foreach ( array ( MB_WORKINGMETADATALOCATION , MB_CUSTOMMETADATALOCATION , MB_BASEMETADATALOCATION ) as $type ) {
				if (file_exists($this->getFileName ( $this->_view, $this->_moduleName, $type ))) {
					$this->_history->append ( $this->getFileName ( $this->_view, $this->_moduleName, $type )) ;
					break;
				}
			}
		} else {
			$this->_history->append ( $this->_sourceFilename ) ;
		}
		// when we deploy get rid of the working file; we have the changes in the MB_CUSTOMMETADATALOCATION so no need for a redundant copy in MB_WORKINGMETADATALOCATION
		// this also simplifies manual editing of layouts. You can now switch back and forth between Studio and manual changes without having to keep these two locations in sync
		$workingFilename = $this->getFileName ( $this->_view, $this->_moduleName, MB_WORKINGMETADATALOCATION ) ;

		if (file_exists ( $workingFilename ))
		unlink ( $this->getFileName ( $this->_view, $this->_moduleName, MB_WORKINGMETADATALOCATION ) ) ;
		$filename = $this->getFileName ( $this->_view, $this->_moduleName, MB_CUSTOMMETADATALOCATION ) ;
		$GLOBALS [ 'log' ]->debug ( get_class ( $this ) . "->deploy(): writing to " . $filename ) ;
		$this->_saveToFile ( $filename, $defs ) ;

		// now clear the cache so that the results are immediately visible
		include_once ('include/TemplateHandler/TemplateHandler.php') ;
		TemplateHandler::clearCache ( $this->_moduleName ) ;
	}

	/*
	 * Construct a full pathname for the requested metadata
	 * Can be called statically
	 * @param string view           The view type, that is, EditView, DetailView etc
	 * @param string modulename     The name of the module that will use this layout
	 * @param string type
	 */
	public static function getFileName ($view , $moduleName , $type = MB_CUSTOMMETADATALOCATION)
	{

		$pathMap = array ( MB_BASEMETADATALOCATION => '' , MB_CUSTOMMETADATALOCATION => 'custom/' , MB_WORKINGMETADATALOCATION => 'custom/working/' , MB_HISTORYMETADATALOCATION => 'custom/history/' ) ;
		$type = strtolower ( $type ) ;

		// BEGIN ASSERTIONS
		if (! isset ( $pathMap [ $type ] ))
		{
			sugar_die ( "DeployedMetaDataImplementation->getFileName(): Type $type is not recognized" ) ;
		}
		// END ASSERTIONS

		$filenames = array (
			MB_LISTVIEW => 'listviewdefs' ,
			MB_BASICSEARCH => 'searchdefs' ,
			MB_ADVANCEDSEARCH => 'searchdefs' ,
			MB_EDITVIEW => 'editviewdefs' ,
			MB_DETAILVIEW => 'detailviewdefs' ,
			MB_QUICKCREATE => 'quickcreatedefs',







		) ;

		// Construct filename
		return $pathMap [ $type ] . 'modules/' . $moduleName . '/metadata/' . $filenames [ $view ] . '.php' ;
	}

}
