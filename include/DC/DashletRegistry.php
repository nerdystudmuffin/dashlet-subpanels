<?php 
/**
 * The DashletManager is a way for searching for Dashlets installed on the current system as well as providing a method for accessing 
 * a specific Dashlets information. It also allows for instantiating an instance of a Dashlet.
 * @author mitani
 *
 */
class DashletManager{
	
	/**
	 * All methods should be called statically prevent instantiation of this class
	 * 
	 */
	private function __construct(){
		
	}
	
	/**
	 * Allows for searching for a specific dashlet available on the installation to add to a given layout. Search may filter on name, category and type
	 * Returns an array in the format
	 * array(
	 * 	'dashlet1-id'=>array('icon'=>icon-image-path ,'name'=>name of dashlet,  'description'=>description of dashlet),
	 *	'dashlet2-id'=>array('icon'=>icon-image-path ,'name'=>name of dashlet,  'description'=>description of dashlet),
	 *	'dashlet3-id'=>array('icon'=>icon-image-path ,'name'=>name of dashlet,  'description'=>description of dashlet),
	 *	...
	 * );
	 * @param string $name - name to search for by default it searches returns all dashlets
	 * @param string $category - category of dashlet to search in by default it searches all categories
	 * @param string $type - type of dashlet to search for Standard, FocusBean by default it searches for both
	 * @static
	 * @return Associative Array containing search results keyed by dashlet id
	 * 
	 */
	static public function search($name=null, $category=null, $type = null){
		
	}
	
	/**
	 * Provides information for a given dashlet in the form of 
	 * array(
	 * 		'name'=> dashlet name
	 * 		'type'=> dashlet type
	 * 		'category'=> dashlet category
	 * 		'description'=> description
	 *		'author'=> author
	 *		'version'=>version
	 *		'date_published'=>date published
	 *		...
	 * ),
	 * @param GUID $dashletID - ID of the dashlet you wish to get information on
	 * @static
	 * @return Associative Array containg all meta-data about a given dashlet
	 */
	static public function info($dashletID){
		
	}
	
	/**
	 * Returns an instance of a Dashlet based on the provided DashletID
	 * @param GUID $dashletID - ID of Dashlet to be instantiated
	 * @return Dashlet
	 */
	
	static public function getDashlet($dashletID){
				
	}
	
	
}
?>