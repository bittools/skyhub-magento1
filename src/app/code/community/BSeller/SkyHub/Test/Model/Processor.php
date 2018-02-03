<?php

class BSeller_SkyHub_Test_Model_Processor extends EcomDev_PHPUnit_Test_Case
{

    use BSeller_SkyHub_Trait_Processors;


    /**
     * @test
     *
     * @throws Mage_Core_Model_Store_Exception
     */
    public function checkIfTheCurrentStoreIsTheDefaultStoreWhenProcessorStarts()
    {
        /** @var BSeller_SkyHub_Model_Processor_Catalog_Product_Attribute $processor */
        $processor = $this->catalogProductAttributeProcessor();
        $this->assertInstanceOf(BSeller_SkyHub_Model_Processor_Catalog_Product_Attribute::class, $processor);
        $this->assertEquals(Mage::app()->getDefaultStoreView()->getCode(), Mage::app()->getStore()->getCode());
    }

}
