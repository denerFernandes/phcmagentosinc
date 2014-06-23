<?php
namespace PHC;

/**
 * Classe para mapear tabela de cores do PHC 
 *
 * @author   jose pinto <bluecor@gmail.com>
 */
class Cor
{
	// unica propriedade, cor em texto
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