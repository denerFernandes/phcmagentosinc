<?php
/**
 * Classe that manage sequence numbers for export files
 *
 * @author   jose pinto <bluecor@gmail.com>
 */
namespace PHC;

class Article
{
	// properties
	private $ref;
	private $design;
	private $epv1;
	private $epv2;
	private $texteis;
	private $idfamilia;
	

	public function setRef($ref)
	{
		$this->ref = $ref;
	}
	
	public function setDesign($design)
	{
		$this->design = $design;
	}
	
	public function setEpv1($epv1)
	{
		$this->epv1 = $epv1;
	}
	
	public function setEpv2($epv2)
	{
		$this->epv2 = $epv2;
	}
	
}