<?php
namespace Base;

use PDO;

/**
 * Wrapper experimental
 * 
 * @author   jose pinto <bluecor@gmail.com>
 *
 */
class PDOMssql extends PDO
{
    
    public function beginTransaction() 
    {
        $sth = $this->prepare("BEGIN TRAN");
        $sth->execute();	                    
    }
    
    public function commit()
    {
        $sth = $this->prepare("COMMIT");
        $sth->execute();	                    
    }
    
    public function rollBack()
    {
        if ($this->inTransaction()) {
            $sth = $this->prepare("ROLLBACK");
            $sth->execute();
        }
    }
    
    public function inTransaction()
    {
    	// testar se esta alguma transacao pendente
        $sth = $this->prepare("SELECT XACT_STATE() as trans");
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        return $result['trans'];
    }
}