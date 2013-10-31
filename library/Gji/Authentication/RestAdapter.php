<?php

namespace Gji\Authentication;

use Zend\Authentication\Adapter;
use Zend\Authentication\Result;
use Zend\Json\Json;
use Zend\Http\Response;
use Zend\Debug\Debug;
use Gji\Http;

class RestAdapter extends Adapter\AbstractAdapter
{
    protected $key;
    protected $restClient;
    protected $resource;

    public function __construct()
    {
        $this->restClient = new Http\RestClient;
    }

    public function authenticate()
    {
        if (!$this->resource)
            throw new \Exception('$this->resource is not set');
        $this->restClient->setParameterPost(array(
            'Username' => $this->identity,
            'Password' => $this->credential));
        $rsp = $this->restClient->sendPost();
/*
Debug::dump($rsp->getStatusCode());
Debug::dump($rsp->getBody());exit;
*/

        switch ($rsp->getStatusCode()) {
        case Response::STATUS_CODE_200:
            $response = Json::decode($rsp->getBody());
            $this->restClient->setResource('users/' .
                $response->User->id);
            $params = array(
                'AuthKey' => $response->User->strAuthKey,
                'CustomerNumber' => $response->User->customers[0]->fk_Customer->strCustomerNumber);
            $this->restClient->setParameterGet($params);
            $rsp = $this->restClient->sendGet();
            $response = Json::decode($rsp->getBody());
            $identity = array(
                'loggedIn' => true,
                'authKey' => $params['AuthKey'],
                'name' => $response->username,
                'fullname' => ucwords("$response->firstname $response->lastname"),
                'userId' => $response->id,
                'customerId' => $response->customers[0]->id,
                'customerName' => $response->customers[0]->name,
                'roleId' => $response->role->id,
                'static_profile' => $response->static_profile,
                'active_profile' => $response->active_profile,
                );
            $code = Result::SUCCESS;
            $message = array();
            break;
        case Response::STATUS_CODE_401:
            $code = Result::FAILURE_CREDENTIAL_INVALID;
            $identity = $this->identity;
            $message = array('Wrong email address or password.');
            break;
        case Response::STATUS_CODE_403:
        case Response::STATUS_CODE_429:
            $code = Result::FAILURE_UNCATEGORIZED;
            $identity = $this->identity;
            $message = array('Wrong email address or password.');
            break;
        case Response::STATUS_CODE_500:
        default:
            $code = Result::FAILURE_UNCATEGORIZED;
            $identity = $this->identity;
            if (!$this->identity)
                $message = array('Please enter a login email.');
            else
                $message = array('An unknown error occurred.');
            break;
        }
        return new Result($code, $identity, $message);
    }

    public function setConsumerKey($key)
    {
        $this->key = $key;
        return $this;
    }

    public function setConfig($config)
    {
        $this->restClient->setConfig($config);
        return $this;
    }

    public function setResource($resource)
    {
        $this->resource = $resource;
        $this->restClient->setResource($resource);
        return $this;
    }
}
