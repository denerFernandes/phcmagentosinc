<?php
/**
 *
 * @author   jose pinto <bluecor@gmail.com>
 */
namespace Magento;

use Base\Log;
use Base\DB;
use Base\MagentoConnection;

class catalogProductList
{
	const MEMCACHED_FETCH_ALL = 'catalogProductList::fetchAll';
	
    // log instance
    private $log;
    
    // resulta data array of catalogProductEntity
    private $data;
    
    // array of catalogProductEntity
    private $newProducts;
    private $removeProducts;
    private $updateProducts;
    
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
    
    /**
     * searchs and cores
     */
    public function fetchAll()
    {
    	// get from mem if available
    	$memcache = new \Memcached();
    	$memcache->addServer('localhost', 11211);
    	$key = md5(catalogProductList::MEMCACHED_FETCH_ALL);
    	$cache_data = $memcache->get($key);
    	if ($cache_data)   {
    		$this->log->addInfo('cache hit', array("key" => $key));
    		$this->data = $cache_data;
    	} else {
			$this->data = $this->client->catalogProductList($this->sessionid);
			$memcache->set($key, $this->data, 60*1);
    	}
    }
    
    public function existsProduct($product)
    {
    	$exists = false;
    	if (!empty($this->data)) {
    		foreach ($this->data as $item) 
    		{
    			if ($product->getRef() == $item->sku) {
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
    
	public function save() 
	{
		$this->saveNewProducts();
		$this->saveUpdateProducts();
		$this->saveRemoveProducts();			
		$this->destroyCache(); 
	}
	
	private function saveRemoveProducts()
	{
		if (count($this->removeProducts) > 0) {
		
			// remove product from Magento catalog
			foreach ($this->removeProducts as $product) {
			    $result = $this->client->catalogProductDelete($this->sessionid, $product->sku);
				$this->log->addInfo('removed', array("sku" => $product->sku, "result" => $result));
			}
		}
		
		$this->removeProducts = array();
	}
	
	private function saveNewProducts()
	{
		if (count($this->newProducts) > 0) {
			// create new product in catalog
			foreach ($this->newProducts as $product) {
			    
			    $result = $this->client->catalogProductCreate($this->sessionid, $product->getProductType(), $product->getAttributeSet(), $product->sku,  $product);
			    
				$this->log->addInfo('created', array("sku" => $product->sku, "result" => $result));
			}
		}
		$this->newProducts = array();		
	}
	
	private function saveUpdateProducts()
	{
		if (count($this->newProducts) > 0) {
			// inserir as novas
			foreach ($this->newProducts as $product) {
				$result = $this->client->catalogProductUpdate($this->sessionid, $product->sku,  $product);
				$this->log->addInfo('updated', array("sku" => $product->sku, "result" => $result));
			}
		}
		$this->newProducts = array();
	}
	
	private function destroyCache()
	{
		$memcache = new \Memcached();
		$memcache->addServer('localhost', 11211);
		$key = md5(catalogProductList::MEMCACHED_FETCH_ALL);
		$cache_data = $memcache->delete($key);
	}
	
	public function addProduct($product) 
	{
		$this->newProducts[] = $product;
	}
	
	public function removeProduct($product)
	{
		$this->removeProducts[] = $product;
	}
	
	public function updateProduct($product)
	{
		$this->updateProducts[] = $product;
	}
	    
}