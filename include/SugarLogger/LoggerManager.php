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
/*********************************************************************************
 * $Id$
 * Description:  Defines the English language pack for the base application.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

class LoggerManager{
	//this the the current log level
	private $level = 'fatal';

	//this is a list of different loggers that have been loaded
	private $loggers = array();

	//this is the instance of the LoggerManager
	private static $instance = NULL;

	//these are the mappings for levels to different log types
	private $logMapping = array(
		'default'=>'SugarLogger',
	);

	//these are the log level mappings anything with a lower value than your current log level will be logged
	private  $levelMapping = array(
		'debug'=>100,
		'info'=>70,
		'warn'=>50,
		'error'=>25,
		'fatal'=>10,
		'security'=>5,
		'off'=>0,
	);

	//only let the getLogger instantiate this object
	private function __construct(){
		$level = SugarConfig::getInstance()->get('logger.level', $this->level);
		if (!empty($level)) {
			$this->setLevel($level);
		}
	}

 	public function __call($method, $message){
        if ( !isset($this->levelMapping[$method]) )
            $method = $this->level;
 		//if the method is a direct match to our level let's let it through this allows for custom levels
 		if($method == $this->level
 			//otherwise if we have a level mapping for the method and that level is less than or equal to the current level let's let it log
 			|| (!empty($this->levelMapping[$method]) && $this->levelMapping[$this->level] >= $this->levelMapping[$method])){
 			//now we get the logger type this allows for having a file logger an email logger, a firebug logger or any other logger you wish you can set different levels to log differently
 			$logger = (!empty($this->logMapping[$method]))?$this->logMapping[$method]:$this->logMapping['default'];
 			//if we haven't instantiated that logger let's instantiate
 			if(!isset($this->loggers[$logger])){
 				require('include/SugarLogger/'. $logger . '.php');
 				$this->loggers[$logger] = new $logger();
 			}
 			//tell the logger to log the message
 			$this->loggers[$logger]->log($method, $message);
 		}
 	}

	public function security($message){
		$this->__call('debug', array($message));
	}
 
 	public function setLevel($name){
        if ( isset($this->levelMapping[$name]) )
            $this->level = $name;
 	}

	public function getLogger(){
		if(!LoggerManager::$instance){
			LoggerManager::$instance = new LoggerManager();
		}
		return LoggerManager::$instance;
	}

}
