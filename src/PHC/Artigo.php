<?php
/**
 * Classe that manage sequence numbers for export files
 *
 * @author   jose pinto <bluecor@gmail.com>
 */
namespace PHC;

class Artigo
{
	// properties
	private $ref;
	private $design;
	private $epv1;
	private $epv2;
	private $texteis;
	private $idfamilia;
	private $cor;
	private $tam;
	private $sxepv1;
	private $sxepv2;

	public function setRef($ref)
	{
		$this->ref = $ref;
	}
	
	public function getRef()
	{
	     $ref = ($this->texteis == 1 ? (trim($this->ref) . trim($this->cor) . trim($this->tam)) : trim($this->ref));
	     $ref = mb_detect_encoding($ref, mb_detect_order(), true) === 'UTF-8' ? $ref : mb_convert_encoding($ref, 'UTF-8');
	     return $ref;
	}
	
	public function getBaseRef()
	{
	    $ref = mb_detect_encoding($this->ref, mb_detect_order(), true) === 'UTF-8' ? $this->ref : mb_convert_encoding($this->ref, 'UTF-8');
	    return $ref;
	}
	
	public function setDesign($design)
	{
		$this->design = $design;
	}
	
	public function getDesign()
	{
	     $value = preg_replace("/[^\w\d \.\*\/'-+]/", '', $this->design); 
	     $value = mb_detect_encoding($value, mb_detect_order(), true) === 'UTF-8' ? $value : mb_convert_encoding($value, 'UTF-8');
	     return $value;
	}
	
	public function getCor()
	{
	     return trim($this->cor);
	}
	
	public function getTam()
	{
	     return trim($this->tam);
	}
	
	public function setEpv1($epv1)
	{
		$this->epv1 = $epv1;
	}
	
	public function setEpv2($epv2)
	{
		$this->epv2 = $epv2;
	}
	
	public function getEpv1()
	{
	    $epv = ($this->texteis == 1 ? $this->epv1 : $this->sxepv1);
	    return $epv;
	}
	
	public function getEpv2()
	{
	    $epv = ($this->texteis == 1 ? $this->epv2 : $this->sxepv2);
	    return $epv;
	}

	public function getIdfamilia()
	{
	    return $this->idfamilia;
	}
	
	public function getTexteis()
	{
	    return $this->texteis;
	}
}