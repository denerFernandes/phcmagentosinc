<?php
/**
 *
* @author   jose pinto <bluecor@gmail.com>
*/
namespace PHC;

use PDO;
use Base\Log;
use Base\DB;

class ArtigosList
{
	// database handle
	private $dbh;

	// log instance
	private $log;

	// resulta data array Artigo
	private $data;

	/**
	 *
	 * Get app instance for DB connection and Log
	 */
	function __construct()
	{
		// get app instances
		$this->log = Log::getInstance()->getLogChannel();
		$this->dbh = DB::getInstance()->getConnection();

		$this->recordCount = 0;
		$this->position = 0;
		$this->data = array();
	}
	
	
	/**
	 * searchs and cores
	 */
	public function searchAllActive()
	{
	    try {
       		if ($this->recordCount == 0) {
    			$query = "SELECT * FROM magentosinc ORDER BY 1";
    			$sth = $this->dbh->prepare($query);
    			$sth->execute();
    			$this->data = $sth->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'PHC\Artigo');
    		}
	   	} catch (PDOException $e) {
    		$this->log->addError(
    				"erro a carregar artigos do SQL PHC",
    				array('PDOException' => $e->getMessage()));
    	}	
	}    	
	
	public function existsArtigo($product)
	{
		$exists = false;
		foreach ($this->data as $item) {
			if ($item->getRef() == $product) {
				$exists = true;
				break;	// exit loop
			}
		}
		return $exists;
	}
	
	/**
	 * Retorna lista de cores em uso
	 * @return array of PHC\Cor objects
	 */
	public function getList()
	{
		return $this->data;
	}
	
}