<?php

namespace Gji\Http;

use Zend\Http\Client;
use Zend\Http\Request;

class RestClient extends Client
{
    protected $xconfig;
    protected $acceptHeader;
    protected $rangeHeader;
    protected $headers;
    protected $resource;
    protected $signingKey;

    public function setConfig($xconfig)
    {
        $this->xconfig = $xconfig;
        $this->setOptions($this->xconfig);
        return $this;
    }

    public function setHeader($name, $value)
    {
        if (!$this->headers)
            $this->headers = $this->getRequest()->getHeaders();
        $this->headers->addHeaderLine($name, $value);
        return $this;
    }

    public function setAcceptHeader($type)
    {
        $this->setHeader('Accept', $type);
        return $this;
    }

    public function setRangeHeader($range)
    {
        $this->setHeader('Range', $range);
        return $this;
    }

    public function setResource($resource)
    {
        $this->resource = $resource;
        return $this;
    }

    public function setSigningKey($key)
    {
        $this->signingKey = $key;
        return $this;
    }

    public function sendPost()
    {
        return $this->sendWithMethod('POST');
    }

    public function sendPut()
    {
        return $this->sendWithMethod('PUT');
    }

    public function sendDelete()
    {
        return $this->sendWithMethod('DELETE');
    }

    public function sendGet()
    {
        return $this->sendWithMethod('GET');
    }

    public function sendWithMethod($method)
    {
        $this->setMethod($method);
        $request = $this->preSend();
        return $this->send($request);
    }

    protected function preSend()
    {
        $request = $this->getRequest();
        $request->setUri($this->xconfig->uri . $this->resource);
        if (isset($this->xconfig->auth))
            $this->setAuth($this->xconfig->auth->username,
                $this->xconfig->auth->password);
        if (isset($this->signingKey))
            $authorization = $this->signRequest($request);
        $headers = $request->getHeaders();
        if ($this->acceptHeader)
            $headers->addHeaderLine('Accept', $this->acceptHeader);
        if (isset($this->signingKey))
            $headers->addHeaderLine('Authorization', $authorization);
        $request->setHeaders($headers);

        if (isset($this->xconfig->auth) && !empty($this->xconfig->auth->username)) {
            $this->setAuth($this->xconfig->auth->username, $this->xconfig->auth->password);
        }
        return $request;
    }

    protected function signRequest($request)
    {
        $params = $this->collectParameters($request);
        $base = $request->getMethod() . "&$params";
//\Zend\Debug\Debug::dump($base);exit;
/*
        $base = $request->getMethod() . '&'
            . rawurlencode($this->getUri()->toString()) . '&'
            . rawurlencode($params);
*/
        return base64_encode(hash_hmac('sha1', $base,
            $this->signingKey));
    }

    protected function collectParameters($request)
    {
        $ret = array();
/*
        $ret = array_merge($request->getQuery()->getArrayCopy(),
            $request->getUri()->getQueryAsArray());
        if ($request->getMethod() == Request::METHOD_POST)
            $ret = array_merge($ret,
                $request->getPost()->getArrayCopy());
*/
        $ret = array_merge($ret, array(
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => time(),
            'oauth_version' => '1.0'));
        $params = $ret;
/*
        $params = array();
        foreach ($ret as $key => $val)
            $params[rawurlencode($key)] = rawurlencode($val);
        ksort($params);
*/
        $ret = '';
        foreach ($params as $key => $val)
            if ($ret == '')
                $ret = "$key=$val";
            else
                $ret .= "&$key=$val";
        return $ret;
    }
}
