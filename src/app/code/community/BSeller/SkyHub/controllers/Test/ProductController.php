<?php

class BSeller_SkyHub_Test_ProductController extends BSeller_SkyHub_Controller_Front_Action
{

    use BSeller_SkyHub_Trait_Integrators;


    public function attributesAction()
    {
        /** @var array $attributes */
        $attributes = Mage::getModel('bseller_skyhub/catalog_product')->getAttributes();

        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
        foreach ($attributes as $attribute) {
            $result = $this->catalogProductAttributeIntegrator()->create($attribute);
            
            if (!$result) {
                continue;
            }
            
            break;
        }
    }
    
    
    public function entityAction()
    {
        $productId = $this->getRequest()->getParam('id');
        
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('bseller_skyhub/catalog_product')->load($productId);
        
        /** @var \SkyHub\Api\Handler\Response\HandlerInterface $response */
        $response = $this->catalogProductIntegrator()->product($product->getSku());
    
        /** @var \SkyHub\Api\Handler\Response\HandlerException $response */
        if ($response && $response->exception()) {
            $this->getResponse()
                ->setHttpResponseCode($response->code())
                ->setBody($response->message());
            
            return;
        }
    }
    
    
    public function listAction()
    {
        /** @var \SkyHub\Api\Handler\Response\HandlerInterface $response */
        $response = $this->catalogProductIntegrator()->products();
    
        /** @var \SkyHub\Api\Handler\Response\HandlerException $response */
        if ($response && $response->exception()) {
            $this->getResponse()
                ->setHttpResponseCode($response->code())
                ->setBody($response->message());
            
            return;
        }
    }

}
