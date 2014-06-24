<?php

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

/**
 * Classe que faz o sincronismo da GRELHA do PHC para 
 * os respectivos attributos do Magento
 * 
 * Esta classe está pronta para transformar colocar numa fila de trabalho REDIS
 * 
 * @author   jose pinto <bluecor@gmail.com>
 */
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
     * Sincroniza os dois tipos de attributos
     */
    public function perform()
    {
		$this->Cores();
		$this->Tamanhos();

    }
    
	/**
	 * Sincroniza a grelha de tamanho do PHC.
	 * 
	 * Funcionamento:
	 * 1 Retira o a lista de tamanhos do Magento para memoria (usa cache)
	 * 2 Retira a Lista de produtos do PHC
	 * 3 Procura no PHC artigos de NAO EXISTAM no magento (acrescenta item à lista)
	 * 4 Procura no MAGENTO items que NAO EXISTEM no PHC (acrescenta item à lista de remover)
	 * 5 Guarda as alterações na base de dados
	 */
    public function Tamanhos()
    {
    	try {
    		$this->log->addInfo('Inicio exportação tamanhos');

    		// @todo o id do atributo de magento está fixo, mudar para ficheiro de config ou procurar
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
    
    /**
     * Encaplusa a sincronização das cores
     */
    public function Cores()
    {
    	try {
    		$this->log->addInfo('Inicio exportacao cores');

    		// @todo o id do atributo de magento está fixo, mudar para ficheiro de config ou procurar
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
