<?php
/**
 * @author   jose pinto <bluecor@gmail.com>
 */
namespace PHC;

class Tamanho
{
	// properties
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