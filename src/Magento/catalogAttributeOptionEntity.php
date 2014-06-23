<?php
namespace Magento;

/**
 * Classe para mapear attributos de um item de catalog Magento
 * 
 * @author   jose pinto <bluecor@gmail.com>
 *
 */
class catalogAttributeOptionEntity
{
	public $attribute;
	public $label;
	public $value;
	
	public function __construct($attribute, $label, $value)
	{
		$this->attribute = $attribute;
		$this->label= $label;
		$this->value = $value;
	}
	
}