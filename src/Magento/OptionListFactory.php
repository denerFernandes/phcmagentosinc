<?php
namespace Magento;

class OptionListFactory
{
	/**
	 * Create a Option List Object from magento attribute value
	 * @param string $label
	 * @return \Magento\OptionList
	 */
	public static function create($label)
	{
		$optionlist = new OptionList();
		$optionlist->fetchAll($label);
		return $optionlist;		
	}		
}