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
require_once ('modules/ModuleBuilder/MB/AjaxCompose.php') ;
require_once ('modules/ModuleBuilder/Module/StudioModule.php') ;



require_once ('include/MVC/View/SugarView.php') ;

class ModuleBuilderViewWizard extends SugarView
{
	private $view = null ; // the wizard view to display
	private $actions ;
	private $buttons ;
	private $question ;
	private $title ;
	private $help ;

	private $editModule ;

	function __construct ()
	{
		if ( isset ( $_REQUEST [ 'view' ] ) )
			$this->view = $_REQUEST [ 'view' ] ;

		$this->editModule = (! empty ( $_REQUEST [ 'view_module' ] ) ) ? $_REQUEST [ 'view_module' ] : null ;
		$this->buttons = array(); // initialize so that modules without subpanels for example don't result in this being unset and causing problems in the smarty->assign
	}

	function display ()
	{
		$this->ajax = new AjaxCompose ( ) ;
		$smarty = new Sugar_Smarty ( ) ;

		if (isset ( $_REQUEST [ 'MB' ] ))
		{
			$this->processMB ( $this->ajax ) ;
		} else
		{








				$this->processStudio ( $this->ajax ) ;




		}

		$smarty->assign ( 'buttons', $this->buttons ) ;
		$smarty->assign ( 'image_path', $GLOBALS [ 'image_path' ] ) ;
		$smarty->assign ( "title", $this->title ) ;
		$smarty->assign ( "question", $this->question ) ;
		$smarty->assign ( "defaultHelp", $this->help ) ;
		$smarty->assign ( "actions", $this->actions ) ;

		$this->ajax->addSection ( 'center', $this->title, $smarty->fetch ( 'modules/ModuleBuilder/tpls/wizard.tpl' ) ) ;
		echo $this->ajax->getJavascript () ;
	}

	function processStudio ( $ajax )
	{

		$this->ajax->addCrumb ( translate( 'LBL_STUDIO' ), 'ModuleBuilder.main("studio")' ) ;

		if (! isset ( $this->editModule ))
		{
			//Studio Select Module Page
			$this->generateStudioModuleButtons () ;
			$this->question = translate('LBL_QUESTION_EDIT') ;
			$this->title = translate( 'LBL_STUDIO' );
			global $current_user;
			if (is_admin($current_user))
				$this->actions = "<input class=\"button\" type=\"button\" id=\"exportBtn\" name=\"exportBtn\" onclick=\"ModuleBuilder.getContent('module=ModuleBuilder&action=exportcustomizations');\" value=\"" . translate ( 'LBL_BTN_EXPORT' ) . '">' ;

			$this->help = 'studioHelp' ;
		} else
		{
			$module = new StudioModule ( $this->editModule ) ;
			$this->ajax->addCrumb ( $module->name, 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&view_module=' . $this->editModule . '")' ) ;
			switch ( $this->view )
			{
				case 'layouts':
					//Studio Select Layout page
					$this->buttons = $module->getLayouts() ;
					$this->title = $module->name . " " . translate('LBL_LAYOUTS') ;
					$this->question = translate( 'LBL_QUESTION_LAYOUT' ) ;
					$this->help = 'layoutsHelp' ;
					$this->ajax->addCrumb ( translate( 'LBL_LAYOUTS' ), 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&view=layouts&view_module=' . $this->editModule . '")' ) ;
					break;












				case 'subpanels':
					//Studio Select Subpanel page.
					$this->buttons = $module->getSubpanels() ;
					$this->title = $module->name . " " . translate( 'LBL_SUBPANELS' ) ;
					$this->question = translate( 'LBL_QUESTION_SUBPANEL' ) ;
					$this->ajax->addCrumb ( translate( 'LBL_SUBPANELS' ), 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&view=subpanels&view_module=' . $this->editModule . '")' ) ;
					$this->help = 'subpanelHelp' ;
					break;

				case 'search':
					//Studio Select Search Layout page.
					$this->buttons = $module->getSearch() ;
					$this->title = $module->name . " " . translate('LBL_SEARCH');
					$this->question = translate( 'LBL_QUESTION_SEARCH' ) ;
					$this->ajax->addCrumb ( translate( 'LBL_LAYOUTS' ), 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&view=layouts&view_module=' . $this->editModule . '")' ) ;
					$this->ajax->addCrumb ( translate( 'LBL_SEARCH' ), 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&view=search&view_module=' . $this->editModule . '")' ) ;
					$this->help = 'searchHelp' ;
					break;

				default:
					//Studio Edit Module Page
					$this->buttons = $module->getModule () ;
					$this->question = translate( 'LBL_QUESTION_MODULE' ) ;
					$this->title = translate( 'LBL_EDIT' ) . " " . $module->name ;
					$this->help = 'moduleHelp' ;
			}
		}
	}

	function processMB ( $ajax )
	{
		if (! isset ( $_REQUEST [ 'view_package' ] ))
		{
			sugar_die ( "no ModuleBuilder package set" ) ;
		}

		$this->editModule = $_REQUEST [ 'view_module' ] ;
		$this->package = $_REQUEST [ 'view_package' ] ;

		$ajax->addCrumb ( translate ( 'LBL_MODULEBUILDER', 'ModuleBuilder' ), 'ModuleBuilder.main("mb")' ) ;
		$ajax->addCrumb ( $this->package, 'ModuleBuilder.getContent("module=ModuleBuilder&action=package&view_package=' . $this->package . '")' ) ;
		$ajax->addCrumb ( $this->editModule, 'ModuleBuilder.getContent("module=ModuleBuilder&action=module&view_module=' . $this->editModule . '&view_package=' . $this->package . '")' ) ;

		switch ( $this->view )
		{
			case 'search':
				//MB Select Search Layout page.
				$this->generateMBSearchButtons () ;
				$this->title = $this->editModule . " " . translate( 'LBL_SEARCH' ) ;
				$this->question = translate( 'LBL_QUESTION_SEARCH' ) ;
				$ajax->addCrumb ( translate( 'LBL_LAYOUTS' ), 'ModuleBuilder.getContent("module=ModuleBuilder&MB=true&action=wizard&view_module=' . $this->editModule . '&view_package=' . $this->package . '")' ) ;
				$ajax->addCrumb ( translate( 'LBL_SEARCH_FORMS' ), 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&MB=1&view=search&view_module=' . $this->editModule . '&view_package=' . $this->package . '")' ) ;
				$this->help = "searchHelp" ;
				break;

			case 'subpanel':
				//ModuleBuilder Select Subpanel
				$ajax->addCrumb ( $this->editModule, 'ModuleBuilder.getContent("module=ModuleBuilder&action=module&view_module=' . $this->editModule . '&view_package=' . $this->package . '")' ) ;
				$ajax->addCrumb ( translate( 'LBL_SUBPANELS' ), 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&MB=1&view=subpanels&view_module=' . $this->editModule . '&view_package=' . $this->package . '")' ) ;
				$this->question = translate( 'LBL_QUESTION_SUBPANEL' ) ;
				$this->help = 'subpanelHelp' ;
				break;

			case 'dashlet':
				$this->generateMBDashletButtons ();
				$this->title = $this->editModule ." " .translate('LBL_DASHLET');
				$this->question = translate( 'LBL_QUESTION_DASHLET' ) ;
				//$this->ajax->addCrumb ( translate('LBL_QUESTION_DASHLET'), 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&view_module=' . $this->editModule . '")' ) ;
				$this->ajax->addCrumb ( translate( 'LBL_LAYOUTS' ), 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&view=layouts&MB=1&view_package='.$this->package.'&view_module=' . $this->editModule . '")' ) ;
				$this->ajax->addCrumb ( translate( 'LBL_DASHLET' ), 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&view=dashlet&MB=1&view_package='.$this->package.'&view_module=' . $this->editModule . '")' ) ;
				$this->help = 'dashletHelp' ;
				break;
























			default:
				$ajax->addCrumb ( translate( 'LBL_LAYOUTS' ), 'ModuleBuilder.getContent("module=ModuleBuilder&MB=true&action=wizard&view_module=' . $this->editModule . '&view_package=' . $this->package . '")' ) ;
				$this->generateMBViewButtons () ;
				$this->title = $this->editModule . " " . translate( 'LBL_LAYOUTS' ) ;
				$this->question = translate( 'LBL_QUESTION_LAYOUT' ) ;
				$this->help = "layoutsHelp" ;
		}
	}





































	function generateStudioModuleButtons ()
	{
		require_once ('modules/ModuleBuilder/Module/StudioBrowser.php') ;
		$sb = new StudioBrowser ( ) ;
		$sb->loadModules () ;
		$nodes = $sb->getNodes () ;
		$this->buttons = array ( ) ;
		//$GLOBALS['log']->debug(print_r($nodes,true));
		foreach ( $nodes as $module )
		{
			$this->buttons [ $module [ 'name' ] ] = array ( 'action' => $module [ 'action' ] , 'imageTitle' => ucfirst ( $module [ 'module' ] ) , 'size' => '48' ) ;
		}
	}















































	function generateMBViewButtons ()
	{
		$this->buttons [ $GLOBALS [ 'mod_strings' ] [ 'LBL_EDITVIEW' ] ] = array ( 'action' => "module=ModuleBuilder&MB=true&action=editLayout&view=".MB_EDITVIEW."&view_module={$this->editModule}&view_package={$this->package}" , 'imageTitle' => 'EditView', 'help'=>'viewBtnEditView' ) ;
		$this->buttons [ $GLOBALS [ 'mod_strings' ] [ 'LBL_DETAILVIEW' ] ] = array ( 'action' => "module=ModuleBuilder&MB=true&action=editLayout&view=".MB_DETAILVIEW."&view_module={$this->editModule}&view_package={$this->package}" , 'imageTitle' => 'DetailView', 'help'=>'viewBtnListView'  ) ;
		$this->buttons [ $GLOBALS [ 'mod_strings' ] [ 'LBL_LISTVIEW' ] ] = array ( 'action' => "module=ModuleBuilder&MB=true&action=editLayout&view=".MB_LISTVIEW."&view_module={$this->editModule}&view_package={$this->package}" , 'imageTitle' => 'ListView', 'help'=>'viewBtnListView' ) ;
		$this->buttons [ $GLOBALS [ 'mod_strings' ] [ 'LBL_SEARCH' ] ] = array ( 'action' => "module=ModuleBuilder&MB=true&action=wizard&view=search&view_module={$this->editModule}&view_package={$this->package}" , 'imageTitle' => 'SearchForm' , 'help'=> 'searchBtn' ) ;
		$this->buttons [ $GLOBALS [ 'mod_strings' ] [ 'LBL_DASHLET' ] ] = array ( 'action' => "module=ModuleBuilder&MB=true&action=wizard&view=dashlet&view_module={$this->editModule}&view_package={$this->package}" , 'imageTitle' => 'Dashlet', 'help'=>'viewBtnDashlet' ) ;

	}

	function generateMBDashletButtons() {
		$this->buttons [ $GLOBALS [ 'mod_strings' ][ 'LBL_DASHLETLISTVIEW' ] ] = array('action'=> "module=ModuleBuilder&MB=true&action=editLayout&view=dashlet&view_module={$this->editModule}&view_package={$this->package}", 'imageTitle'=> $GLOBALS ['mod_strings']['LBL_DASHLETLISTVIEW'], 'imageName'=>'ListView', 'help'=>'DashletListViewBtn');
		$this->buttons [ $GLOBALS [ 'mod_strings' ][ 'LBL_DASHLETSEARCHVIEW' ] ] = array('action'=> "module=ModuleBuilder&MB=true&action=editLayout&view=dashletsearch&view_module={$this->editModule}&view_package={$this->package}", 'imageTitle'=> $GLOBALS ['mod_strings']['LBL_DASHLETSEARCHVIEW'], 'imageName'=>'BasicSearch','help'=> 'DashletSearchViewBtn');
	}
	function generateMBSearchButtons ()
	{
		$this->buttons [ $GLOBALS [ 'mod_strings' ] [ 'LBL_BASIC' ] ] = array ( 'action' => "module=ModuleBuilder&MB=true&action=editLayout&view_module={$this->editModule}&view_package={$this->package}&view=SearchView&searchlayout=basic_search" , 'imageTitle' => $GLOBALS [ 'mod_strings' ] [ 'LBL_BASIC_SEARCH' ] , 'imageName' => 'BasicSearch','help' => "BasicSearchBtn" ) ;
		$this->buttons [ $GLOBALS [ 'mod_strings' ] [ 'LBL_ADVANCED' ] ] = array ( 'action' => "module=ModuleBuilder&MB=true&action=editLayout&view_module={$this->editModule}&view_package={$this->package}&view=SearchView&searchlayout=advanced_search" , 'imageTitle' => $GLOBALS [ 'mod_strings' ] [ 'LBL_ADVANCED_SEARCH' ] , 'imageName' => 'AdvancedSearch','help' => "AdvancedSearchBtn" ) ;
	}
}
?>
