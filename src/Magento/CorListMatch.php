<?php

namespace Magento;

use PHC\CorList;
use Magento\catalogAttributeOptionEntity;

class CorListMatch extends CorList
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
			if ($cor->getCor() == $option) {
				$exists = true;
				break;	// exit loop
			}
		}
		return $exists;
	}

}