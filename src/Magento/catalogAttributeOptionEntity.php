<?php
namespace Magento;

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