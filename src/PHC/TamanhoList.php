<?php
/**
 *
 * @author   jose pinto <bluecor@gmail.com>
 */
namespace PHC;

use PDO;
use Base\Log;
use Base\DB;

/**
 * Classe que mantem uma lista de tamanhos
 *
 * @author   jose pinto <bluecor@gmail.com>
 */
class TamanhoList
{
    // database handle
    private $dbh;
    
    // log instance
    private $log;
    
    // resulta data
    private $data;
    
    // record count
    private $recordCount;    
    
    /**
     * 
     * Get app instance for DB connection and Log
     */
    function __construct()
    {
        // get app instances
        $this->log = Log::getInstance()->getLogChannel();
        $this->dbh = DB::getInstance()->getConnection();

        $this->data = array();
    }
    
    /**
     * procura e retira todas os tamanhos do PHC
     */
    public function searchAllActive()
    {
        try {
        	if ($this->recordCount == 0) {
        	    // @todo falta passar para view
        		$query = "SELECT ltrim(rtrim(tam)) as tam FROM sgt WHERE ref in (SELECT ref FROM st WHERE u_nanet = 1) GROUP BY ltrim(rtrim(tam)) order by 1";
        		$sth = $this->dbh->prepare($query);
        		$sth->execute();
        		$this->data = $sth->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'PHC\Tamanho');
        		$this->recordCount = count($this->data);
        	}
    	} catch (PDOException $e) {
    	    $this->log->addError(
    	            "erro a carregar tamanhos do SQL PHC",
    	            array('PDOException' => $e->getMessage()));
    	}
        	 
    }
    
    /**
     * Retorna lista de tamanhos em uso
     * @return \PHC\Cor[]  
     */
    public function getList()
    {
        return $this->data;
    }
    
    /**
     * retorna o numero de items nesta lista
     * @return int
     */
    public function getListCount()
    {
    	return $this->recordCount;
    }    
 
}