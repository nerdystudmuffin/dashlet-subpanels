<?php

/**
 * The Dashlet Container Factory (DCF) provides a facility for loading the appropriate Dashlet Container.
 * It will make the decision based on what container is requested as well as system and user settings.  
 * @author mitani
 *
 */
class DCFactory{
	/**
	 * Prevent Instantiation of DCFactory it should only be used statically
	 * 
	 */
	private function __construct(){
		
	}
	
	/**
	 * This function will make the decision for which container to load. 
	 * 
	 * If container is not specified
	 * 1. check if user has a default container they prefer load
	 * 
	 * @param string $dashletMetaDataFile - file path to the meta-data specificying the Dashlets used in this container
	 * @param string $container  - name of the Dashlet Container to use if not specified it will use the system default
	 * @param string $layout - name of the Dashlet Container Layout to use if not specified it will use the system default
	 * @static 
	 * @return DashletContainer
	 */
	static public function getContainer($dashletMetaDataFile, $container=null, $layout=null){
		
	}
	
	
}