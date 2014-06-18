<?php
/**
 *
 * @author   jose pinto <bluecor@gmail.com>
 */
namespace Magento;

use Base\Log;
use Base\DB;
use Base\MagentoConnection;

class OptionList implements OptionListInterface
{
    // log instance
    private $log;
    
    // resulta data array of catalogAttributeOptionEntity
    private $data;
    
    private $attribute;
    
    // array of catalogAttributeOptionEntity
    private $newOptions;
    private $delOptions;
    
    // SOAP session
    private $client;
    private $sessionid;
    
    /**
     * 
     * Get app instance for DB connection and Log
     */
    function __construct()
    {
        // get app instances
        $this->log = Log::getInstance()->getLogChannel();
        $this->client = MagentoConnection::getInstance()->getClient();
        $this->sessionid = MagentoConnection::getInstance()->getSessionId();
        
        $this->data = array();
    }
    
    public function getIdByValue($value)
    {
    	$id = false;
    	if (empty($this->data)) {
    		 throw new Exception('No data from attribute, fetch first');
    	} else {
    		foreach ($this->data as $optionEntity) 
    		{
    			if ($optionEntity->label == $value) {
    				$id = $optionEntity->value;
    			}
    		}
    	}
    	return $id;
    }
    
    /**
     * searchs and cores
     */
    public function fetchAll($attribute)
    {
    	$this->attribute = $attribute;
    	
    	// get from mem if available
    	$memcache = new \Memcached();
    	$memcache->addServer('localhost', 11211);
    	$key = md5('OptionList::fetchAll' . $attribute);
    	$cache_data = $memcache->get($key);
    	if ($cache_data)   {
    		$this->data = $cache_data;
    	} else {
			$this->data = $this->client->catalogProductAttributeOptions($this->sessionid, $attribute);
			$result = $memcache->set($key, $this->data, 60*10);
			if ($result === false) {
			    $this->log->addInfo('cache not set', array("result" => $memcache->getResultCode()));
			}
    	}
    }
    
    public function existsOption($option)
    {
    	$exists = false;
    	if (empty($this->data)) {
    		 throw new Exception('No data from attribute, fetch first');
    	} else {
    		foreach ($this->data as $optionEntity) 
    		{
    			if ($optionEntity->label == $option) {
    				$exists = true;
    			}
    		}
    	}
    	return $exists;
    } 
    
    /**
     * Retorna lista de cores em uso
     */
    public function getList()
    {
        return $this->data;
    }
    
    public function getListCount()
    {
        return $this->recordCount;
    }
    
	public function save() 
	{
		if (count($this->newOptions) > 0) {
			
			// inserir as novas
			foreach ($this->newOptions as $option) {
				
				$label = array(
							array(
								"store_id" => array("0"),
								"value" => $option->label
							)
				);
				
				$data = array(
						"label" => $label,
						"order" => "0",
						"is_default" => "0"
				);
				
				$result = $this->client->catalogProductAttributeAddOption($this->sessionid, $this->attribute, $data);
				
				$this->log->addInfo('enviado', array("label" => $option->label, "result" => $result));
			}
			
		}
		
		if (count($this->delOptions) > 0) {
				
			// inserir as novas
			foreach ($this->delOptions as $option) {
		
				$result = $this->client->catalogProductAttributeRemoveOption($this->sessionid, $this->attribute, $option->value);
		
				$this->log->addInfo('apagado', array("label" => $option->label, "result" => $result));
			}
				
		}
				
		if (count($this->delOptions) > 0 || count($this->newOptions) > 0) { $this->destroyCache(); }
	}
	
	private function destroyCache()
	{
		$memcache = new \Memcached();
		$memcache->addServer('localhost', 11211);
		$key = md5('OptionList::fetchAll' . $this->attribute);
		$cache_data = $memcache->delete($key);
	}
	
	public function addOption($Option) 
	{
		$this->newOptions[] = new catalogAttributeOptionEntity($this->attribute, $Option, '');
	}
	
	public function delOption($option)
	{
		$this->delOptions[] = $option;
	}
	    
}