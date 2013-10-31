<?php

namespace Mslib\Authentication;

use Zend\Authentication\Adapter;
use Zend\Authentication\Result;
use Zend\Json;
use Zend\Crypt\BlockCipher;
use Zend\Debug\Debug;

class TokenAdapter extends Adapter\AbstractAdapter
{
    protected $key;
    protected $config;

    public function authenticate()
    {
        $algo = $this->config['api_provider']['gji']['cipher'];
        $expiry = $this->config['api_provider']['gji']['token_expiry'];
        $blockCipher = BlockCipher::factory('mcrypt',
            array('algo' => $algo));
        $cipher = $blockCipher->getCipher();
        $cipher->setKey($this->key);
        $result = $cipher->decrypt(base64_decode($this->identity));

        if (!$result)
            return new Result(Result::FAILURE_CREDENTIAL_INVALID,
                $this->identity, array());
        try {
        $result = Json\Json::decode($result);
        } catch (Json\Exception\RuntimeException $ex) {
        return new Result(Result::FAILURE_UNCATEGORIZED,
            $this->identity, array());
        }
        $now = new \DateTime;
        $dte = \DateTime::createFromFormat(\DateTime::ISO8601,
            $result->time);
        $diff = $now->diff($dte);
        if ($diff->i > $expiry)
            return new Result(Result::FAILURE_CREDENTIAL_INVALID,
                $this->identity, array());
        return new Result(Result::SUCCESS, $result->username, array());
    }

    public function setConsumerKey($key)
    {
        $this->key = $key;
        return $this;
    }

    public function setAppKey($key)
    {
        $this->key = $key;
        return $this;
    }

    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }
}
