<?php
/**
 * DCManager is the Dashlet Container Manager (DCM) and provides the primary API for the SugarCRM application to interact with Dashlet Containers and Dashlets. 
 * It allows for SugarViews to render the initial layout of the DashletContainer using the getLayout function as well as responding to AJAX Requests for layout 
 * updates using the getAJAXResponse function. The Dashlet Container Manager (DCM) is the SugarCRM applications method for interaction with Dashlet Containters 
 * and Dashlets. 
 * 
 * @author mitani
 *
 */
class DCManager{
	
	/**
	 * The container instance being used
	 * @var DashletContainer container
	 */
	private $container;
	
	/**
	 * Constructor for creating a Dashlet Container Manager. It will load the appropriate container. If a focus bean is passed in 
	 * it will set the focus bean as well. 
	 * @param string $dashletMetaDataFile - file path to the meta-data specificying the Dashlets used in this container
	 * @param string $container  - name of the Dashlet Container to use if not specified it will use the system default
	 * @param string $layout - name of the Dashlet Container Layout to use if not specified it will use the system default
	 * @param SugarBean $bean - the primary focus bean to be used for this Dashlet Container
	 * 
	 */
	function __construct($dashletMetaDataFile, $container=null, $layout=null, $bean= null ){
		$this->container = DCFactory::getInstance($dashletMetaDataFile, $container, $layout);
		if($bean)$this->setFocusBean($bean);		
	}
	
	
	/**
	 * Sets the focus bean for the container
	 * @param SugarBean $bean - the primary focus bean to be used in the Dashlet Container 
	 * 
	 */
	public function setFocusBean($bean){
		$this->container->setFocusBean($bean);
	}
	
	/**
	 * Returns the focus bean used in the contianer
	 * @return SugarBean
	 */
	public function getFocusBean(){
		return $this->container->getFocusBean();
	}
	
	/**
	 * Returns the Dashlet Container 
	 * @return DashletContainer
	 */
	public function getContainer(){
		return $this->container;
	}
	
	/**
	 * Returns an associative array containing the HTML code as well as any JS files that need to be loaded in order to render
	 * the container. If there is a function to call onload it should be handled inside one of the JS files using the YUI Event Handler
	 * 
	 * @return Associative Array ('html'=>html code , 'jsfiles'=>array('js1', 'js2', ...));
	 */
	public function getLayout(){
		return $this->container->getLayout();
	}
	
	/**
	 * Responds to any AJAX response made by JSDCManager. 
	 * 
	 * It expects requests in the following format
	 * 
	 * array(
	 * 	'dashlets'=>array(
	 * 		'dashlet1-id'=>array('method'=>function to call on, 'data'=>data to pass into the function),
	 * 		'dashlet2-id'=>array('method'=>function to call on, 'data'=>data to pass into the function),	
	 * 		...
	 * 	)
	 * )
	 * 
	 * 
	 * It will return the data in the following format 
	 * 
	 * array(
	 * 'dashlets'=>
	 * 		array( 	
	 * 				'dashlet1-id'=> response <string | array of data>
	 * 				'dashlet2-id'=>response <string | array of data> 
	 * 				...
	 * 		)
	 * 'DCM'=>array(
	 * 		'status'=>200 - uses sames responses as  HTML status response
	 * 		'response'=> string or array of data for Dashlet Container Manager to process on the JS side
	 * 		
	 * )
	 * 
	 * 
	 * )
	 * @return JSON Data
	 */
	public function getAJAXResponse(){
		
	}
	
	/**
	 * converts a file path into a GUID which can be used for things such as accessing meta-data in system settings or user preferences
	 * @param $filePath
	 * @static
	 * @return GUID 
	 */
	static public function getFilePathGUID($filePath){
		return 'DC-' .md5($filepath);
	
	}
	
	
	
	
}