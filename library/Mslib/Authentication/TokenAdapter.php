<?php

namespace Mslib\Authentication;

use DateTime;
use Zend\Authentication\Adapter;
use Zend\Authentication\Result;
use Zend\Json;
use Zend\Crypt;
use Zend\Debug\Debug;

class TokenAdapter extends Adapter\AbstractAdapter
{
    protected $cipher;
    protected $key;
    protected $config;
    protected $currentTime;

    public function __construct(Crypt\Symmetric\SymmetricInterface $cipher,
        DateTime $now)
    {
        $this->cipher = $cipher;
        $this->currentTime = $now;
    }

    public function authenticate()
    {
        $expiry = $this->config['token_expiry'];
        $this->cipher->setKey($this->key);
        $result = $this->cipher->decrypt(base64_decode($this->identity));

        if (!$result)
            return new Result(Result::FAILURE_CREDENTIAL_INVALID,
                $this->identity, array());
        try {
        $result = Json\Json::decode($result);
        } catch (Json\Exception\RuntimeException $ex) {
        return new Result(Result::FAILURE_UNCATEGORIZED,
            $this->identity, array());
        }
        $dte = DateTime::createFromFormat(DateTime::ISO8601, $result->time);
        $diff = $this->currentTime->diff($dte);
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
