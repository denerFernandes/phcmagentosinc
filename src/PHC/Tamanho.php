<?php
namespace PHC;

/**
 * Classe que mapeia a tabela de tamanhos do PHC
 * 
 * @author   jose pinto <bluecor@gmail.com>
 */
class Tamanho
{
	// unica propriedade da tabela
	private $tam;

	public function setTam($tam)
	{
		$this->tam = trim($tam);
	}
	
	public function getTam()
	{
		//return $this->tam;
		return preg_replace("/[^\w\d \.\*\/'-+]/", '', $this->tam);
	}	
}