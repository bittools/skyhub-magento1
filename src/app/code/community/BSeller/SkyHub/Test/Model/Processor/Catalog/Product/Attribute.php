<?php

class BSeller_SkyHub_Test_Model_Processor_Catalog_Product_Attribute extends EcomDev_PHPUnit_Test_Case
{

    use BSeller_SkyHub_Trait_Service,
        BSeller_SkyHub_Trait_Integrator;


    /**
     * @test
     */
    public function checkIfServiceApiIsACorrectInstance()
    {
        $this->assertInstanceOf(BSeller_SkyHub_Model_Service::class, $this->service());
        $this->assertInstanceOf(\SkyHub\Api::class, $this->service()->api());
    }
}
