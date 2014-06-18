<?php
/**
 *
* @author   jose pinto <bluecor@gmail.com>
*/
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