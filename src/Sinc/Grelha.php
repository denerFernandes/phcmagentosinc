<?php
/**
 *
 * @author   jose pinto <bluecor@gmail.com>
 */
namespace Sinc;

use Base\Filesystem;
use Base\Config;
use Base\Log;
use Base\DB;
use Magento\OptionListFactory;
use Magento\CorListMatch;
use Magento\TamanhoListMatch;
use Magento\catalogAttributeOptionEntity;
use PHC\CorList;

class Grelha
{
    // log handle Monolog\Logger
    private $log;
    
    /**
     * Prepare task to run: read ini file, set log and connect to database
     */
    public function setUp()
    {
        // get app instances
        $this->log = Log::getInstance()->getLogChannel();
    }

    /**
     */
    public function perform()
    {
		$this->Cores();
		$this->Tamanhos();

    }
    
    public function Tamanhos()
    {
    	try {
    		$this->log->addInfo('Inicio exportação tamanhos');

    		$magentoOptionList = OptionListFactory::create(134);	//  hardcoded to magento - PHC tamanho
    		
    		$phcOptionList = new TamanhoListMatch();
    		$phcOptionList->fetchAll();
    			
    		$this->log->addInfo('Registos encontrados', array("n" => $phcOptionList->getListCount()));
    	
    		// ver se á novas opçoes acrescentar-las
    		foreach ($phcOptionList->getList() as $opcao) {
    			 
    			// add option if doesnt exists
    			if (!($magentoOptionList->existsOption($opcao->getTam()))) {
    				// acrescentar ao magento
    				$magentoOptionList->addOption($opcao->getTam());
    				$this->log->addInfo('marcado para enviar', array("label" => $opcao->getTam()));
    			}
    	
    		};
    	
    		// apagar do magento as que nao existem
    		foreach ($magentoOptionList->getList() as $magOption) {
    	
    			// apagar a opcao do magento se nao existir
    			if (!($phcOptionList->existsOption($magOption->label))) {
    				$magentoOptionList->delOption($magOption);
    				$this->log->addInfo('marcado para apagar', array("label" => $magOption->label));
    			}
    	
    		}
    	
    		// actualizar no magento
    		$this->log->addInfo('enviando dados');
    		$magentoOptionList->save();
    	
    		$this->log->addInfo('Fim exportação tamanhos');
    		 
    	} catch (PDOException $e) {
    		$this->log->addError(
    				"erro a carregar cores de SQL",
    				array('PDOException' => $e->getMessage()));
    	}
    }
    
    public function Cores()
    {
    	try {
    		$this->log->addInfo('Inicio exportacao cores');
    			
    		$magentoOptionList = OptionListFactory::create(135);	// cor
    		$phcOptionList = new CorListMatch();
    		$phcOptionList->fetchAll();
    			
    		$this->log->addInfo('Registos encontrados', array("n" => $phcOptionList->getListCount()));
    	
    		// ver se á novas opçoes acrescentar-las
    		foreach ($phcOptionList->getList() as $opcao) {
    			 
    			// add option if doesnt exists
    			if (!($magentoOptionList->existsOption($opcao->getCor()))) {
    				// acrescentar ao magento
    				$magentoOptionList->addOption($opcao->getCor());
    				$this->log->addInfo('marcado para enviar', array("label" => $opcao->getCor()));
    			}
    	
    		};
    	
    		// apagar do magento as que nao existem
    		foreach ($magentoOptionList->getList() as $magOption) {
    	
    			// apagar a opcao do magento se nao existir
    			if (!($phcOptionList->existsOption($magOption->label))) {
    				$magentoOptionList->delOption($magOption);
    				$this->log->addInfo('marcado para apagar', array("label" => $magOption->label));
    			}
    	
    		}
    	
    		// actualizar no magento
    		$this->log->addInfo('enviando dados');
    		$magentoOptionList->save();
    	
    		$this->log->addInfo('Fim exportação cores');
    		 
    	} catch (PDOException $e) {
    		$this->log->addError(
    				"erro a carregar cores de SQL",
    				array('PDOException' => $e->getMessage()));
    	}
    }
     
}
