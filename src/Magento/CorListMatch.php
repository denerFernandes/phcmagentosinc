<?php

namespace Magento;

use PHC\CorList;
use Magento\catalogAttributeOptionEntity;

/**
 * Classe usada para fazer a compara��o de attribute cor PHC com op��o de Magento
 * extende a CorList com o metodo de compara��o
 */
class CorListMatch extends CorList
{
	public function fetchAll() 
	{
		parent::searchAllActive();		
	}
	
	/**
	 * Faz a compara��o de item a item
	 * @param string $option
	 * @return boolean Se a op��o existe no phc
	 */
	public function existsOption($option) 
	{
		$exists = false;
		$cores = parent::getList();
		
		foreach ($cores as $cor) {
			if ($cor->getCor() == $option) {
				$exists = true;
				break;	// exit loop
			}
		}
		return $exists;
	}

}