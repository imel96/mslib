<?php

use Zend\Json\Json;
use Mslib;

class CallCentres extends Mslib\Http\RestClient
{
    public function getCallCentres()
    {
        $this->setResource('callcentres');
        $response = $this->sendGet();
        if (!$response->isOk())
            $centres = array('call_centres' => array(), 'total_count' => 0);
        else
            $centres = Json::decode($response->getBody(), Json::TYPE_ARRAY);
        return $centres;
    }
}
