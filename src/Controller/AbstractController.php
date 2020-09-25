<?php

namespace App\Controller;

use JMS\Serializer\SerializerBuilder;
use FOS\RestBundle\Controller\AbstractFOSRestController;

Abstract class AbstractController extends AbstractFOSRestController
{
    /**
     * string
     */
    const FORMAT = 'json';
    
    /**
     * int
     */
    const SUCCESS_HTTP_CODE = 200;

    /**
     * int
     */
    const BAD_REQUEST_HTTP_CODE = 400;

    /**
     * get Serialize data
     * 
     * @param $data
     * 
     * return string
     */
    protected function getSerialize($data)
    {
        $serializer = SerializerBuilder::create()->build();
        return $serializer->serialize($data, self::FORMAT);
    }  
}
