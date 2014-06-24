<?php
namespace Sinc;

use PHC\ArtigosList;
use Base\Filesystem;
use Base\Config;
use Base\Log;
use Base\DB;
use Magento\OptionListFactory;
use Magento\CorListMatch;
use Magento\TamanhoListMatch;
use Magento\catalogProductCreateEntity;
use Magento\catalogProductList;
use PHC\CorList;

/**
 * Classe que faz o sincronismo dos produtos do PHC para
 * os respectivos items do catalog Magento
 *
 * Esta classe está pronta para transformar colocar numa fila de trabalho REDIS
 *
 * @author   jose pinto <bluecor@gmail.com>
 */
class Produtos
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
	 * Sincroniza os produtos.
	 * 
	 * Funcionamento:
	 * 1 Retira o Catalog do Magento para memoria (usa cache)
	 * 2 Retira a Lista de produtos do PHC
	 * 3 Procura no PHC artigos de NAO EXISTAM no magento (acrescenta item à lista)
	 * 4 Procura no MAGENTO items que NAO EXISTEM no PHC (acrescenta item à lista de remover)
	 * 5 Guarda as alterações na base de dados
	 */
	public function perform()
	{
		$this->log->addInfo('Inicio exportação produtos');
		
		// sacar lista de produtos de magento
		$magCatalog = new catalogProductList();
		$magCatalog->fetchAll();
		$catalog = $magCatalog->getList();
		
		// sacar lista de produtos de phc
		$phcArtigos = new ArtigosList();
		$phcArtigos->searchAllActive();
		$artigos = $phcArtigos->getList();
		
		// saber novos produtos a inserir em magento
		foreach ($artigos as $item) {
			
			// add option if doesnt exists ou update
			if (!($magCatalog->existsProduct($item))) {
				// acrescentar ao magento
				$magCatalog->addProduct(new catalogProductCreateEntity($item));
				$this->log->addInfo('marcado para enviar', array("sku" => $item->getRef()));
			} else {
				// acrescentar ao magento
				$magCatalog->updateProduct(new catalogProductCreateEntity($item));
				$this->log->addInfo('marcado para actualizar', array("sku" => $item->getRef()));
			}
				
		}
		
		// saber produtos a apagar de mangento
		foreach ($catalog as $item) {
			// apagar a opcao do magento se nao existir
			if (!($phcArtigos->existsArtigo($item->sku))) {
				$magCatalog->removeProduct($item->sku);
				$this->log->addInfo('marcado para apagar', array("sku" => $item->sku));
			}
		}
		
		// guardar alterações
		$magCatalog->save();
		
		$this->log->addInfo('Fim exportação produtos');
	}

}