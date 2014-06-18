<?php
/**
 * Classe com a configuração da aplicação
*
* @author   jose pinto <bluecor@gmail.com>
*/
namespace Base;


use Base\Config;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SwiftMailerHandler;

class Log {

	private static $_singleton;
	private $_log;
	
	private function __construct()
	{
		$config = Config::getInstance();
		
		// SwiftMaller Intsance
		$transport = \Swift_SmtpTransport::newInstance(
				$config->getValue('SMTPServer'), $config->getValue('SMTPPort'))
			->setUsername($config->getValue('SMTPUsername'))
			->setPassword($config->getValue('SMTPPassword'));
		
		$mailer = \Swift_Mailer::newInstance($transport);
		
		$message = \Swift_Message::newInstance()
			->setSubject($config->getValue('ErrorSubject'))
			->setFrom($config->getValue('ErrorMailFrom'))
			->setTo(explode(",", $config->getValue('ErrorMailTo')));
		 
		// create a log channel
		$this->_log = new Logger($config->getValue('appname'));
		$this->_log->pushHandler(new StreamHandler($config->getValue('logfile')), Logger::DEBUG);
		$this->_log->pushHandler(new SwiftMailerHandler($mailer, $message), Logger::ERROR);
		
	}
	
	public static function getInstance()
	{
		if (is_null(self::$_singleton)) {
			self::$_singleton = new Log();
		}
		return self::$_singleton;
	}
	
	public function getLogChannel()	
	{
		return $this->_log;	
	}
	
}
