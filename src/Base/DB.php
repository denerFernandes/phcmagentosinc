<?php
/**
 * Classe que controla o interface com Huawei
 *
 * @author   jose pinto <bluecor@gmail.com>
 */
namespace Base;

use PDO;
use PDOException;
use PDOPDOMssql;
use Base\Config;

class DB {
	
	// unique instance
	private static $_singleton;
	
	// db connection
	private $_connection;
	
	// log channel
	private $log;
	
	// database properties
	private $hostname;
	private $dbname;
	private $username;
	private $pw;
	private $appname;
	
	private $connected = false;
	
	private function __construct()
	{
		$this->log = Log::getInstance();
			
		// get app config
		$config = Config::getInstance();
			
		$this->hostname = $config->getValue('hostname');
		$this->dbname =  $config->getValue('database');
		$this->username =  $config->getValue('username');
		$this->pw =  $config->getValue('password');
		$this->appname =  $config->getValue('appname');
	}
	
	private function connect()
	{
		// open database
		try {
			$this->_connection = new PDOMssql(
					"dblib:host=" . $this->hostname . ";dbname=" . $this->dbname . ";appname=" . $this->appname,
					$this->username,
					$this->pw);
		
			$this->_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$this->connected = true;
		
		} catch (PDOException $e) {
			$this->log->getLogChannel()->addDebug(
					"Impossivel ligar a base dados, aplicacao terminada",
					array('PDOException' => $e->getMessage()));
			die();
		}		
	}
	
	public static function getInstance()
	{
		if (is_null(self::$_singleton)) {
			self::$_singleton = new DB();
		}
		return self::$_singleton;
	}
	
	
	public function getConnection()
	{
		if (!$this->connected) {
			$this->connect();
		}
		return $this->_connection;
	}	
}