<?php
namespace Magento;

use PHC\ArtigosList;

/**
 * Classe para mapear as propriedades de um item do catalog Magento
 *
 * @author   jose pinto <bluecor@gmail.com>
 *
 */
class catalogProductCreateEntity 
{
    /**
     *  @todo mapear todas as constante do catalog
     */
    const CATALOG_SEARCH = 4;
    const NOT_VISIBLE_INDIVIDUALLY = 1;
    
    const CONFIGURABLE = 'configurable';
    const SIMPLE = 'simple';
    
    // propriedades a mapear com o Magento
    public $product_id;
    public $sku;
    public $name;
    public $description;
    public $short_description;
    public $set;
    public $status;
    public $weight;
    public $type;
    public $category_ids;
    public $website_ids;
    public $tax_class_id;
    public $price;
    public $special_price;
    public $additional_attributes;
     
    // private!! estas propriedades nao são mapeadas para o catalog
    private $attributeset;
    private $product_type;
    private $basesku;
     
    /**
     * constructor cria um item de catalog magento dado um artigo do PHC
     * deveria ter um Decorator
     */
    public function __construct(\PHC\Artigo $item)
    {
        $this->sku = $item->getRef();
        $this->basesku = $item->getBaseRef();
        $this->name = $item->getDesign();
        $this->description = $item->getDesign(); 
        $this->short_description = $item->getDesign();
        $this->category_ids = array($item->getIdfamilia());
        $this->weight = 1;
        $this->status = 1;
        $this->price = $item->getEpv2();
        $this->special_price = $item->getEpv1();
        $this->tax_class_id = 4;	
        $this->website_ids = array(1);
        $this->attributeset = ($item->getTexteis() == 0 ? 4 : 14);
        $this->visibility = (
                $item->getTexteis() == 1 ? 
                catalogProductCreateEntity::NOT_VISIBLE_INDIVIDUALLY : 
                catalogProductCreateEntity::CATALOG_SEARCH);
        
        $this->product_type = ($item->getTexteis() == 3 ? 
                catalogProductCreateEntity::CONFIGURABLE : 
                catalogProductCreateEntity::SIMPLE);
        
        // configurable products 
        if ($item->getTexteis() == 3) { 
            // link to other products
            $product = new ArtigosList();
            $product->searchAllActive();
            $skus = array();
            foreach ($product->getList() as $item) {
                if ($item->getTexteis() == 1 && $item->getBaseRef() == $this->basesku) {
                    $skus[] = $item->getRef();
                }
            }
            //$this->associated_skus = $skus;
        }
		
        if ($item->getTexteis() == 1) {
            $corOptions = OptionListFactory::create('cor');
            $tamOptions = OptionListFactory::create('tamanho');
              
            $productData = new \stdClass();
            $additionalAttrs = array();
              
            $cor = new \stdClass();
            $cor->key = "cor";
            $cor->value = $corOptions->getIdByValue($item->getCor());
            $additionalAttrs['single_data'][] = $cor;
              
            $tam = new \stdClass();
            $tam->key = "tamanho";
            $tam->value = $tamOptions->getIdByValue($item->getTam());
            $additionalAttrs['single_data'][] = $tam;
                            
            $this->additional_attributes = $additionalAttrs;
        }
	}
	
	public function getProductType()
	{
	    return $this->product_type;
	}
	
	public function getAttributeSet()
	{
	     return $this->attributeset;
	}
}