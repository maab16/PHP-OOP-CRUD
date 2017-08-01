<?php
namespace Blab\Libs;

use Blab\Database\Connector;

class DB{

	/**
     * @var PDO
     */
	protected $_db=null;

	/**
     * @var PDO\Instance
     */
	private static $_instance=null;

	/**
     * Initialize Database
     *
     * @return object 	PDO
     */
	public function __construct()
	{
		$database = new Connector(array(
				"host" 		=> 'localhost',
				"username" 	=> 'root',
				"password" 	=> '',
				"dbName" 	=> 'maab',
				"port" 		=> '3306',
				"engine"	=> 'InnoDB'
		));
		$this->_db = $database->connect();
	}

	/**
     * Get Database Instance
     *
     * @return object 	PDO
     */
	public static function getDBInstance(){

		if (!isset(self::$_instance)) {
				
			self::$_instance = new DB();
			return self::$_instance->_db;
		}

		return false;
	}
}