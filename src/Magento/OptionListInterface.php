<?php
namespace Magento;

interface OptionListInterface
{
	public function fetchAll($attribute);
	
	public function existsOption($option); 

	public function getList();
	
	public function getListCount();
	
	public function save();
	
	public function addOption($Option);
	
	public function delOption($option);
	
	public function getIdByValue($value);
}