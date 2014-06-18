<?php

namespace Magento;

use PHC\TamanhoList;
use Magento\catalogAttributeOptionEntity;

class TamanhoListMatch extends TamanhoList
{
	public function fetchAll() 
	{
		parent::searchAllActive();		
	}
	
	public function existsOption($option) 
	{
		$exists = false;
		$cores = parent::getList();
		
		foreach ($cores as $cor) {
			if ($cor->getTam() == $option) {
				$exists = true;
				break;	// exit loop
			}
		}
		return $exists;
	}

}