<?php

namespace Magento;

use PHC\CorList;
use Magento\catalogAttributeOptionEntity;

/**
 * Classe usada para fazer a comparação de attribute cor PHC com opção de Magento
 * extende a CorList com o metodo de comparação
 */
class CorListMatch extends CorList
{
	public function fetchAll() 
	{
		parent::searchAllActive();		
	}
	
	/**
	 * Faz a comparação de item a item
	 * @param string $option
	 * @return boolean Se a opção existe no phc
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