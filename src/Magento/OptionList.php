<?php
/**
 *
 * @author   jose pinto <bluecor@gmail.com>
 */
namespace Magento;

use Base\Log;
use Base\DB;
use Base\MagentoConnection;

/**
 * Classe que mantem uma lista de opcoes do magento
 * 
 * @author   jose pinto <bluecor@gmail.com>
 */
class OptionList implements OptionListInterface
{
    // log instance
    private $log;
    
    // resulta data array of catalogAttributeOptionEntity
    private $data;
    
    private $attribute;
    
    // array of catalogAttributeOptionEntity
    // lista de operaçoes a efectuar no Magento
    private $newOptions;
    private $delOptions;
    
    // SOAP session
    private $client;
    private $sessionid;
    
    /**
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
     * Procura todas as opcoes de um attribute
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
     * Retorna array com a lista das opcoes
     * @return array items de catalogAttributeOptionEntity
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
	    // se existir novas opcoes inserir
		if (count($this->newOptions) > 0) {
		
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
		
		// e agora apagar as opcoes que nao existem
		if (count($this->delOptions) > 0) {
				
			foreach ($this->delOptions as $option) {
		
				$result = $this->client->catalogProductAttributeRemoveOption($this->sessionid, $this->attribute, $option->value);
		
				$this->log->addInfo('apagado', array("label" => $option->label, "result" => $result));
			}
				
		}
				
		// se houve modificacoes no magento, destruimos a cache
		if (count($this->delOptions) > 0 || count($this->newOptions) > 0) { $this->destroyCache(); }
	}
	
	/**
	 * Limpa a cache de opcoes deste attributo 
	 */
	private function destroyCache()
	{
		$memcache = new \Memcached();
		$memcache->addServer('localhost', 11211);
		$key = md5('OptionList::fetchAll' . $this->attribute);
		$cache_data = $memcache->delete($key);
	}
	
	private function addOption($Option) 
	{
		$this->newOptions[] = new catalogAttributeOptionEntity($this->attribute, $Option, '');
	}
	
	private function delOption($option)
	{
		$this->delOptions[] = $option;
	}
	    
}