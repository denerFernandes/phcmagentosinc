<?php
/**
 * @author   jose pinto <bluecor@gmail.com>
 */
namespace Base;

use Base\Config;

class MagentoConnection {
	
	// unique instance
	private static $_singleton;
	
	// SOAP Session
	private $_sessionid;
	private $_client;
	
	private $connected;
	
	// log channel
	private $log;
	
    // SOAP params
    private $SOAP_User;
    private $SOAP_Api;
    private $WDSL;
    
    private function __construct()
    {
        // get app instances
        $this->log = Log::getInstance()->getLogChannel();
        $this->dbh = DB::getInstance()->getConnection();
        
        $this->SOAP_User = Config::getInstance()->getValue('SOAP_User');
        $this->SOAP_Api = Config::getInstance()->getValue('SOAP_API_Key');
        $this->WDSL = Config::getInstance()->getValue('WDSL');
        
        $this->connected = false;
    }
    
    private function connect()
    {
		$this->_client = new \SoapClient($this->WDSL);
		$this->_sessionid = $this->_client->login($this->SOAP_User, $this->SOAP_Api);
		$this->connected = true;
    }
    
    public static function getInstance()
    {
    	if (is_null(self::$_singleton)) {
    		self::$_singleton = new MagentoConnection();
    	}
    	return self::$_singleton;
    }
    
    
    public function getClient()
    {
    	if (!$this->connected) {
    		$this->connect();
    	}
    	return $this->_client;
    }
    
    public function getSessionId()
    {
    	return $this->_sessionid;
    }
        
}