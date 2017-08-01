<?php

namespace Blab\Database;

use Blab\Libs\Core;
use Blab\Database\Query\Sql;
use Blab\Database\Exception;

class Connector extends Core
{
	/**
	 * @var PDO
	 */
	protected $_service;

	/**
	 * @var object
	 */

	protected $_query;

	/**
	 * @var int
	 */

	protected $_lastInsertId;

	/**
	 * @var object
	 * @readwrite
	 */
	protected $_results;
	/**
	 * @var int
	 * @readwrite
	 */
	protected $_count;

	/**
	 * @var string
	 * @readwrite
	 */

	protected $_host;

	/**
	 * @var string
	 * @readwrite
	 */

	protected $_username;

	/**
	 * @var string
	 * @readwrite
	 */

	protected $_password;

	/**
	 * @var string
	 * @readwrite
	 */

	protected $_dbName;

	/**
	 * @var string
	 * @readwrite
	 */

	protected $_port= "3306";

	/**
	 * @var string
	 * @readwrite
	 */

	protected $_charset = "utf8";

	/**
	 * @var string 
	 * @readwrite
	 */

	protected $_engine = "InoDB";

	/**
	 * @var bool
	 * @readwrite
	 */

	protected $_isConnected = false;

	/**
     * Checks if connected to the database
     *
     * @return bool
     */
	protected function _isValidService(){

		$isInstance = $this->_service instanceof \PDO;

		if (!empty($this->_service) && $isInstance && $this->isConnected) {
			
			return true ;
		}

		return false;
	}

	/**
     * Prepare SQL Query
     *
     * @param  string    $sql
     * @param  array    $params
     * @return PDO
     */
	protected function _prepareSql($sql,$params = []){

		if ($_query = $this->_service->prepare($sql)) {
				
				$x=1;
				if (count($params)) {
					
					foreach ($params as $param) {
					
						$_query->bindValue($x,$param);

						$x++;
					}
				}

			return $_query;
		}
	}

	/**
     * Connect database with PDO
     *
     * @return object
     */
	public function connect(){

		if (!$this->_isValidService()) {

			$dsn = 'mysql:dbname='.$this->_dbName.';host='.$this->_host;
			$options = array(
					    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
					); 
			try{
				$this->_service = new \PDO($dsn,$this->_username,$this->_password,$options);
				$this->_isConnected = true;

			}catch(\PDOException $e){

				die($e->getMessage());
			}
		}

		return $this;
	}

	/**
     * Disconnect Database Connection
     *
     * @return object
     */
	public function close(){

		if ($this->_isValidService()) {
			
			$this->_isConnected = false;
			$this->_service=null;
		}

		return $this;
	}

	/**
     * returns a corresponding query instance
     *
     * @return PDO
     */
	public function query(){

		return new Sql(array(

				"connector"=>$this
			));
	}

	/**
     * Executes the provided SQL statement
     *
     * @param  string 	$sql
     * @param  array    $params
     * @return object
     */
	public function execute($sql,$params = []){

		if (!$this->_isValidService()) {
			
			throw new Exception\Service("Unable to connect service");
		}

			if ($this->_query = $this->_service->prepare($sql)) {
				
				$x=1;
				if (!empty($params)) {
					foreach ($params as $param) {
					
						$this->_query->bindValue($x,$param);

						$x++;
					}
				}

				if ($this->_query->execute()) {

					$this->_lastInsertId = $this->_service->lastInsertId();

					$this->_results = $this->_query->fetchAll(\PDO::FETCH_OBJ);

					$this->_count = $this->_query->rowCount();
			
					return $this;

				}else{

					return false;
				}
			}

			return false;
	}

	/**
     * Escapes the provided value to make it safe for queries
     *
     * @param  string    $value
     * @return string
     */
    public function escape($value)
    {
        if (!$this->_isValidService())
        {
            throw new Exception\Service("Not connected to a valid service");
        }
        
        return $this->_service->quote($value);
    }

    /**
     * Returns the ID of the last row to be inserted
     *
     * @return int
     */
    public function getLastInsertId()
    {
        if (!$this->_isValidService())
        {
            throw new Exception\Service("Not connected to a valid service");
        }
        
        return $this->_lastInsertId;
    }

    /**
     * Returns the number of rows affected
     * by the last SQL query executed
     *
     * @return int
     */
    public function getAffectedRows()
    {
        if (!$this->_isValidService())
        {
            throw new Exception\Service("Not connected to a valid service");
        }
        
        return $this->_count;
    }

    /**
     * Returns the last error of occur
     *
     * @return int
     */
    public function getLastError()
    {
        if (!$this->_isValidService())
        {
            throw new Exception\Service("Not connected to a valid service");
        }
        
        return $this->_service->errorInfo();
    }

    /**
     * Get All Data
     *
     * @return object
     */
	public function getResults(){

		return $this->_results;
	}
}