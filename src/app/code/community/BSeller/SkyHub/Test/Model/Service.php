<?php

class BSeller_SkyHub_Test_Model_Service extends EcomDev_PHPUnit_Test_Case
{

    use BSeller_SkyHub_Trait_Service;


    /**
     * @test
     */
    public function checkInstanceGettersAreCorrect()
    {
        $this->assertInstanceOf(BSeller_SkyHub_Model_Service::class, $this->service());
        $this->assertInstanceOf(\SkyHub\Api::class, $this->service()->api());
        $this->assertInstanceOf(\SkyHub\Api\Service\ServiceInterface::class, $this->service()->apiService());
    }

}
