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

* Description: This file handles the Data base functionality for the application.
* It acts as the DB abstraction layer for the application. It depends on helper classes
* which generate the necessary SQL. This sql is then passed to PEAR DB classes.
* The helper class is chosen in DBManagerFactory, which is driven by 'db_type' in 'dbconfig' under config.php.
*
* All the functions in this class will work with any bean which implements the meta interface.
* The passed bean is passed to helper class which uses these functions to generate correct sql.
*
* The meta interface has the following functions:
* getTableName()	        	Returns table name of the object.
* getFieldDefinitions()	    	Returns a collection of field definitions in order.
* getFieldDefintion(name)		Return field definition for the field.
* getFieldValue(name)	    	Returns the value of the field identified by name.
*                           	If the field is not set, the function will return boolean FALSE.
* getPrimaryFieldDefinition()	Returns the field definition for primary key
*
* The field definition is an array with the following keys:
*
* name 		This represents name of the field. This is a required field.
* type 		This represents type of the field. This is a required field and valid values are:
*      		int
*      		long
*      		varchar
*      		text
*      		date
*      		datetime
*      		double
*      		float
*      		uint
*      		ulong
*      		time
*      		short
*      		enum
* length	This is used only when the type is varchar and denotes the length of the string.
*  			The max value is 255.
* enumvals  This is a list of valid values for an enum separated by "|".
*			It is used only if the type is ?enum?;
* required	This field dictates whether it is a required value.
*			The default value is ?FALSE?.
* isPrimary	This field identifies the primary key of the table.
*			If none of the fields have this flag set to ?TRUE?,
*			the first field definition is assume to be the primary key.
*			Default value for this field is ?FALSE?.
* default	This field sets the default value for the field definition.
*
*
* Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
* All Rights Reserved.
* Contributor(s): ______________________________________..
********************************************************************************/

include_once('include/database/MssqlManager.php');

class SqlsrvManager extends MssqlManager
{
    /**
     * @see DBManager::$backendFunctions
     */
    protected $backendFunctions = array(
        'free_result' => 'sqlsrv_free_stmt',
        'close'       => 'sqlsrv_close',
        );
	
	/**
	 * Cache of previous query results
	 */
	private $_selectResultCache = array();
	private $_insertResultCache = array();
	
	/**
     * @see DBManager::connect()
     */
    public function connect(
        array $configOptions = null,
        $dieOnError = false
        )
    {
        global $sugar_config;

        if (is_null($configOptions))
            $configOptions = $sugar_config['dbconfig'];

        //set the connections parameters
        $connect_param = '';
        $configOptions['db_host_instance'] = trim($configOptions['db_host_instance']);
        if (empty($configOptions['db_host_instance']))
            $connect_param = $configOptions['db_host_name'];
        else
            $connect_param = $configOptions['db_host_name']."\\".$configOptions['db_host_instance'];

        /*
         * Don't try to specifically use a persistent connection
         * since the driver will handle that for us
         */
        $this->database = sqlsrv_connect(
                $connect_param ,
                array(
                    "UID" => $configOptions['db_user_name'],
                    "PWD" => $configOptions['db_password'],
                    "Database" => $configOptions['db_name'],
                    )
                )
            or sugar_die("Could not connect to server ".$configOptions['db_host_name'].
                " as ".$configOptions['db_user_name'].".");

        //make sure connection exists
        if(!$this->database){
            sugar_die("Unable to establish connection");
        }

        if($this->checkError('Could Not Connect:', $dieOnError))
            $GLOBALS['log']->info("connected to db");

        $GLOBALS['log']->info("Connect:".$this->database);
    }

	/**
     * @see DBManager::checkError()
     */
    public function checkError(
        $msg = '',
        $dieOnError = false
        )
    {
        if (DBManager::checkError($msg, $dieOnError))
            return true;

        $sqlmsg = $this->_getLastErrorMessages();
        $sqlpos = strpos($sqlmsg, 'Changed database context to');
        if ( $sqlpos !== false )
            $sqlmsg = '';  // empty out sqlmsg if its 'Changed database context to'
        else {
            global $app_strings;
            //ERR_MSSQL_DB_CONTEXT: localized version of 'Changed database context to' message
            if (empty($app_strings)
					or !isset($app_strings['ERR_MSSQL_DB_CONTEXT'])
					or !isset($app_strings['ERR_MSSQL_WARNING']) ) {
                //ignore the message from sql-server if $app_strings array is empty. This will happen
                //only if connection if made before languge is set.
                $GLOBALS['log']->debug("Ignoring this database message: " . $sqlmsg);
                $sqlmsg = '';
            }
            else {
                $sqlpos = strpos($sqlmsg, $app_strings['ERR_MSSQL_DB_CONTEXT']);
                $sqlpos2 = strpos($sqlmsg, $app_strings['ERR_MSSQL_WARNING']);
				if ( $sqlpos !== false || $sqlpos2 !== false)
                    $sqlmsg = '';
            }
        }

        if ( strlen($sqlmsg) > 2 ) {
            $GLOBALS['log']->fatal("SQL Server error: " . $sqlmsg);
            return true;
        }

        return false;
	}

	/**
     * @see DBManager::query()
	 */
	public function query(
        $sql,
        $dieOnError = false,
        $msg = '',
        $suppress = false
        )
    {
		global $app_strings;
		
		// Flag if there are odd number of single quotes
        if ((substr_count($sql, "'") & 1))
            $GLOBALS['log']->error("SQL statement[" . $sql . "] has odd number of single quotes.");

        $this->countQuery($sql);
        $GLOBALS['log']->info('Query:' . $sql);
        $this->checkConnection();
        $this->query_time = microtime(true);
		
		if ($suppress) {








        }
        else {
            $result = @sqlsrv_query($this->database, $sql);
        }
		// the sqlsrv driver will sometimes return false from sqlsrv_query()
        // on delete queries, so we'll also check to see if we get an error
        // message as well.
        // see this forum post for more info
        // http://forums.microsoft.com/MSDN/ShowPost.aspx?PostID=3685918&SiteID=1
        if (!$result && ( $this->_getLastErrorMessages() != '' ) ) {
            // awu Bug 10657: ignoring mssql error message 'Changed database context to' - an intermittent
            // 				  and difficult to reproduce error. The message is only a warning, and does
            //				  not affect the functionality of the query
            
            $sqlmsg = $this->_getLastErrorMessages();
            $sqlpos = strpos($sqlmsg, 'Changed database context to');
			$sqlpos2 = strpos($sqlmsg, 'Warning:');
            
			if ($sqlpos !== false || $sqlpos2 !== false)		// if sqlmsg has 'Changed database context to', just log it
				$GLOBALS['log']->debug($sqlmsg . ": " . $sql );
			else {




				$GLOBALS['log']->fatal($sqlmsg . ": " . $sql );
				if($dieOnError)
					sugar_die('SQL Error : ' . $sqlmsg);
				else
					echo 'SQL Error : ' . $sqlmsg;
			}
        }
        $this->lastmysqlrow = -1;

        $this->query_time = microtime(true) - $this->query_time;
        $GLOBALS['log']->info('Query Execution Time:'.$this->query_time);







        $this->checkError($msg.' Query Failed:' . $sql . '::', $dieOnError);

		// Push on to result cache
		if ( stripos(trim($sql),'select') === 0 ) {
			$this->_selectResultCache[] = $result;
			// Clear out old items on the result cache
			if ( count($this->_selectResultCache) > 15 ) {
				$resource = array_shift($this->_selectResultCache);
				if ( is_resource($resource) )
					sqlsrv_free_stmt($resource);
			}
		}
		else {
			$this->_insertResultCache[] = $result;
			// Clear out old items on the result cache
			if ( count($this->_insertResultCache) > 3 ) {
				$resource = array_shift($this->_insertResultCache);
				if ( is_resource($resource) )
					sqlsrv_free_stmt($resource);
			}
		}
        return $result;
    }
    
	/**
     * @see DBManager::getFieldsArray()
     */
	public function getFieldsArray(
        &$result,
        $make_lower_case = false
        )
	{
        $field_array = array();

        if ( !is_resource($result) )
            return false;

        foreach ( sqlsrv_field_metadata($result) as $meta ) {
            if($make_lower_case==true)
                $meta['Name'] = strtolower($meta['Name']);

            $field_array[] = $meta['Name'];
        }

        return $field_array;
	}

    /**
     * @see DBManager::fetchByAssoc()
     */
    public function fetchByAssoc(
        &$result,
        $rowNum = -1,
        $encode = true
        )
    {
        if ( !is_resource($result) )
            return false;
		
		// move this result to the top of the result cache
		// to help it from being killed off
		$this->_selectResultCache[] = $result;
		
		if ($result && $rowNum < 0) {
            $row = $this->_fetchRowAssoc($result);
            //MSSQL returns a space " " when a varchar column is empty ("") and not null.
            //We need to iterate through the returned row array and strip empty spaces
            if(!empty($row)){
                foreach($row as $key => $column) {
                    //notice we only strip if one space is returned.  we do not want to strip
                    //strings with intentional spaces (" foo ")
                    if (!empty($column) && $column ==" ") {
                        $row[$key] = '';
                    }
                }
            }

            if($encode && $this->encode&& is_array($row))
                return array_map('to_html', $row);
            
            return $row;
		}

		if ($this->getRowCount($result) > $rowNum) {
            if ( $rowNum == -1 )
                $rowNum = 0;
            for ($i = 0; $i > $rowNum; $i++ )
                @sqlsrv_fetch($result);
        }

        $this->lastmysqlrow = $rowNum;
        $row = @$this->_fetchRowAssoc($result);
        if($encode && $this->encode && is_array($row)) 
            return array_map('to_html', $row);
        
        return $row;
	}

    /**
     * @see DBManager::getRowCount()
     */
    public function getRowCount(
        &$result
        )
    {
        return $this->getOne('SELECT @@ROWCOUNT');
	}
    
    /**
     * Emulates old mssql_get_last_message() behavior, giving us any error messages from the previous
     * function call
     *
     * @return string error message(s)
     */
    private function _getLastErrorMessages()
    {
        $message = '';
        
        if ( ($errors = sqlsrv_errors()) != null) 
            foreach ( $errors as $error ) 
                $message .= $error['message'] . '. ';
        
        return $message;
    }
    
    /**
     * Low level handling getting a row from a result set; automatically
     * makes all fetched values strings, just like the other PHP db functions.
     * We have to do this since the sqlsrv extension returns row values in thier
     * native types, which causes problems with how we handle things.
     *
     * @param  resource $result
     * @return array
     */
    private function _fetchRowAssoc(
        $result
        )
    {
        if ( !is_resource($result) )
            return false;
        
        $row = array();
        $fieldnames = $this->getFieldsArray($result);
        $fieldMetaData = sqlsrv_field_metadata($result);
		if ( sqlsrv_fetch($result) ) 
			for ( $i = 0; $i < sqlsrv_num_fields($result); $i++ )
				if ($fieldMetaData[$i]['Type'] == -9 
						|| ($fieldMetaData[$i]['Type'] >= SQLSRV_SQLTYPE_NVARCHAR(1) 
							&& $fieldMetaData[$i]['Type'] <= SQLSRV_SQLTYPE_NVARCHAR(8000))
						|| ($fieldMetaData[$i]['Type'] >= SQLSRV_SQLTYPE_NCHAR(1) 
							&& $fieldMetaData[$i]['Type'] <= SQLSRV_SQLTYPE_NCHAR(8000))
						|| $fieldMetaData[$i]['Type'] == SQLSRV_SQLTYPE_NVARCHAR('max') 
						|| $fieldMetaData[$i]['Type'] == SQLSRV_SQLTYPE_NCHAR('max'))
					$row[$fieldnames[$i]] = iconv("utf-16le", "utf-8", 
						sqlsrv_get_field($result,$i,SQLSRV_PHPTYPE_STRING(SQLSRV_ENC_BINARY))
						);
				else
                	$row[$fieldnames[$i]] = sqlsrv_get_field($result,$i,SQLSRV_PHPTYPE_STRING(SQLSRV_ENC_CHAR));
		else
			sqlsrv_free_stmt($result);
        
        return $row;
    }
} // end class definition

?>
