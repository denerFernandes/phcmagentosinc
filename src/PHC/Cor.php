<?php
/**
 * Classe that manage sequence numbers for export files
 *
 * @author   jose pinto <bluecor@gmail.com>
 */
namespace PHC;

class Cor
{
	// properties
	private $cor;

	public function setCor($cor)
	{
		$this->cor = $cor;
	}
	
	public function getCor()
	{
		return trim($this->cor);
	}	
}