<?php
/**
 * Classe com a configuração da aplicação
*
* @author   jose pinto <bluecor@gmail.com>
*/
namespace Base;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SwiftMailerHandler;

class Config {

	private static $_singleton;
	private $_config;

	private function __construct()
	{
		// read app config
		$this->_config = parse_ini_file("config.ini");
	}
	
	public static function getInstance()
	{
		if (is_null(self::$_singleton)) {
			self::$_singleton = new Config();
		}
		return self::$_singleton;
	}
	
	public function getValue($param) {
		if (isset($this->_config[$param])) {
			return $this->_config[$param];
		} else {
			throw new \Exception('esse parametro nao está na configuração');
		}
	}
}
