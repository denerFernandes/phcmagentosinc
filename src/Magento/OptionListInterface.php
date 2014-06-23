<?php
namespace Magento;

/**
 * Classe que mantem uma lista de opcoes de um attributo do magento
 * 
 * @author   jose pinto <bluecor@gmail.com>
 */
interface OptionListInterface
{
    /**
     * Le do magento todas as opcoes do $attribute
     * @param string $attribute 
     */
	public function fetchAll($attribute);
	
	/**
	 * Testa a existencia de uma opcao neste attributo
	 * @param \Magento\catalogAttributeOptionEntity[] $option
	 * @return boolean se existe ou nao a opção
	 */
	public function existsOption($option); 

	/**
	 * retorna a lista de opcoes nesta instancia
	 * @return \Magento\catalogAttributeOptionEntity[] lista de opcoes
	 */
	public function getList();
	
	/**
	 * retorna o numero de registos na lista
	 * @return int
	 */
	public function getListCount();
	
	/**
	 * Actualiza a base de dados de opcoes no magento
	 */
	public function save();
	
	/**
	 * marca esta opcao para acrescentar
	 * @param \Magento\catalogAttributeOptionEntity[] $Option
	 */
	private function addOption($Option);
	
	/**
	 * marca esta opcao para apagar
	 * @param \Magento\catalogAttributeOptionEntity[] $option $Option
	 */
	private function delOption($option);
	
	public function getIdByValue($value);
}