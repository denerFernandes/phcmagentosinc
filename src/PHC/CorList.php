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
 * Classe que mapea uma lista de cores
 *
 * @author   jose pinto <bluecor@gmail.com>
 */
class CorList 
{
    // database handle
    private $dbh;
    
    // log instance
    private $log;
    
    // resulta data
    private $data;
    
    // record count
    private $recordCount;
    
    // iterator position
    private $position;
    
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
     * retira todas as cores do PHC 
     */
    public function searchAllActive()
    {
        try {
        	if ($this->recordCount == 0) {
        	    // @todo falta passar para view
    	    	$query = "SELECT cor FROM sgc WHERE ref in (SELECT ref FROM st WHERE u_nanet = 1) GROUP BY cor";
    	    	$sth = $this->dbh->prepare($query);
    	    	$sth->execute();
    	    	$this->data = $sth->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'PHC\Cor');
    	    	$this->recordCount = count($this->data);
        	}
        } catch (PDOException $e) {
    		$this->log->addError(
    				"erro a carregar cores do SQL PHC",
    				array('PDOException' => $e->getMessage()));
    	}	        	
    }
    
    /**
     * Retorna lista de cores em uso
     * @return \PHC\Cor[] array de objecos
     */
    public function getList()
    {
        return $this->data;
    }
    
    /**
     * Retorna o numero de registos nesta lista
     * @return int 
     */
    public function getListCount()
    {
        return $this->recordCount;
    }

}