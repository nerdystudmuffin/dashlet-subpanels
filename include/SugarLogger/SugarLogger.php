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

class SugarLogger {
	private $logfile = 'sugarcrm';
	private $ext = '.log';
	private $dateFormat = '%c';
	private $fp = false;
	private $logSize = '10MB';
	private $maxLogs = 10;
	private $filesuffix = "";

    private $initialized = false;
	public static $log_levels = array('debug'=>'Debug', 'info'=>'Info', 'error'=>'Error', 'fatal'=>'Fatal' , 'security'=>'Security', 'off'=>'Off');
	public static $filename_suffix= array("%m_%Y"=>"Month_Year", "%w_%m"=>"Week_Month","%m_%d_%y"=>"Month_Day_Year");

	public function getLogFileNameWithPath(){
		return $this->full_log_file;
	}
	public function getLogFileName(){
		return ltrim($this->full_log_file, "./");
	}

	function __construct() {
        $config = SugarConfig::getInstance();
        $this->ext = $config->get('logger.file.ext', $this->ext);
        $this->logfile = $config->get('logger.file.name', $this->logfile);
        $this->dateFormat = $config->get('logger.file.dateFormat', $this->dateFormat);
        $this->logSize = $config->get('logger.file.maxSize', $this->logSize);
        $this->maxLogs = $config->get('logger.file.maxLogs', $this->maxLogs);
        $this->filesuffix = $config->get('logger.file.suffix', $this->filesuffix);
        unset($config);
        $this->doInitialization();
	}

    private function doInitialization() {
        $this->full_log_file = $this->logfile . $this->ext;
        $this->initialized = $this->fileCanBeCreatedAndWrittenTo();
        $this->rollLog();
    }

    private function fileCanBeCreatedAndWrittenTo() {
        $this->attemptToCreateIfNecessary();
        return file_exists($this->full_log_file) && is_writable($this->full_log_file);
    }

    private function attemptToCreateIfNecessary() {
        if (file_exists($this->full_log_file)) {
            return;
        }
        @touch($this->full_log_file);
    }

	public function log($level,$message) {
        if (!$this->initialized) {
            return;
        }
		//lets get the current user id or default to -none- if it is not set yet
		$userID = (!empty($GLOBALS['current_user']->id))?$GLOBALS['current_user']->id:'-none-';

		//if we haven't opened a file pointer yet let's do that
		if (! $this->fp)$this->fp = fopen ($this->logfile . $this->ext , 'a' );

		//write out to the file including the time in the dateFormat the process id , the user id , and the log level as well as the message
		fwrite ( $this->fp, strftime ( $this->dateFormat ) . ' [' . getmypid () . '][' . $userID . '][' . strtoupper($level) . '] ' . $message [0] . "\n" );
	}

	private function rollLog($force = false) {
        if (!$this->initialized || empty($this->logSize)) {
            return;
        }
		// lets get the number of megs we are allowed to have in the file
		$megs = substr ( $this->logSize, 0, strlen ( $this->logSize ) - 2 );
		//convert it to bytes
		$rollAt = ( int ) $megs * 1024 * 1024;
		//check if our log file is greater than that or if we are forcing the log to roll
		if ($force || filesize ( $this->logfile . $this->ext ) >= $rollAt) {
			//now lets move the logs starting at the oldest and going to the newest
			for($i = $this->maxLogs - 2; $i > 0; $i --) {
				if (file_exists ( $this->logfile . $i . $this->ext )) {
					$to = $i + 1;
					$old_name = $this->logfile . $i . $this->ext;
					$new_name = $this->logfile . $to . $this->ext;
					//nsingh- Bug 22548  Win systems fail if new file name already exists. The fix below checks for that.
					//if/else branch is necessary as suggested by someone on php-doc ( see rename function ).
					sugar_rename($old_name, $new_name);

					//rename ( $this->logfile . $i . $this->ext, $this->logfile . $to . $this->ext );
				}
			}
			//now lets move the current .log file
			sugar_rename ($this->logfile . $this->ext, $this->logfile . '1' . $this->ext);

		}

	}

	function __destruct() {
		if ($this->fp)
			fclose ( $this->fp );
	}

}
