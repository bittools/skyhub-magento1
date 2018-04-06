<?php

/**
 * BSeller Platform | B2W - Companhia Digital
 *
 * Do not edit this file if you want to update this module for future new versions.
 *
 * @category  BSeller
 * @package   BSeller_SkyHub
 *
 * @copyright Copyright (c) 2018 B2W Digital - BSeller Platform. (http://www.bseller.com.br)
 *
 * @author    Tiago Sampaio <tiago.sampaio@e-smart.com.br>
 */

trait BSeller_SkyHub_Trait_Customer
{


    /**
     * @param string $fullname
     *
     * @return Varien_Object
     */
    protected function breakName($fullname)
    {
        $fullnametmp = (array) explode(' ', $fullname);
    
        $firstname  = ucwords(array_shift($fullnametmp));
        $lastname   = ucwords(array_pop($fullnametmp));
        if(!$lastname) {
            $lastname = $firstname;
        }
        $middlename = ucwords(implode(' ', $fullnametmp));
        
        return new Varien_Object([
            'firstname' => $firstname,
            'middlename' => $middlename,
            'lastname' => $lastname,
            'fullname' => $fullname,
        ]);
    }

    /**
     * @param string $phone
     * @return string
     */
    protected function formatPhone($phone)
    {
        if (!$phone) {
            return '(00) 0000-0000';
        }
        return $phone;
    }

    /**
     * @param $address
     * @param $addressSize
     * @return string
     */
    protected function formatAddress($address, $addressSize)
    {
        $street = $address->getData('street');
        $number = $address->getData('number');
        $complement = implode(' ', [$address->getData('reference'), $address->getData('detail')]);
        $neighborhood = $address->getData('neighborhood');

        return $this->_formatAddress(
            [
                $street,
                $number,
                $complement,
                $neighborhood,
            ], $addressSize
        );
    }

    /**
     * @param array $address
     * @param $addressSize
     * @return string
     */
    private function _formatAddress(array $address, $addressSize)
    {
        if ($addressSize == 1) {
            return implode(' - ', $address);
        }

        return (array_shift($address) . "\n" . $this->_formatAddress($address, $addressSize - 1));
    }
}
