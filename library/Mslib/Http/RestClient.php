<?php

namespace Mslib\Http;

use Zend\Http\Client;
use Zend\Http\Request;
use Mslib\Authentication;

class RestClient extends Client
{
    protected $xconfig;
    protected $acceptHeader;
    protected $rangeHeader;
    protected $headers;
    protected $resource;

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

    public function sendPost()
    {
        return $this->sendWithMethod(Request::METHOD_POST);
    }

    public function sendPut()
    {
        return $this->sendWithMethod(Request::METHOD_PUT);
    }

    public function sendDelete()
    {
        return $this->sendWithMethod(Request::METHOD_DELETE);
    }

    public function sendGet()
    {
        return $this->sendWithMethod(Request::METHOD_GET);
    }

    public function sendPatch()
    {
        return $this->sendWithMethod(Request::METHOD_PATCH);
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
        if (isset($this->xconfig->auth)
            && !empty($this->xconfig->auth->username))
            $this->setAuth($this->xconfig->auth->username,
                $this->xconfig->auth->password);

        if (isset($this->xconfig->oauth)) {
            $signer = new Authentication\OauthSignature(
                $this->xconfig->oauth->consumerSecret,
                $this->xconfig->oauth->tokenSecret,
                'sha1');
            $signer->setConsumerKey($this->xconfig->oauth->consumerKey)
                ->setAccessToken('370773112-GmHxMAgYyLbNEtIKZeRNFsMKPR9EyMZeS9weJAEb');
        }
        $headers = $request->getHeaders();
        if ($this->acceptHeader)
            $headers->addHeaderLine('Accept', $this->acceptHeader);
        if (isset($this->xconfig->oauth))
            $headers->addHeaderLine('Authorization',
                $signer->sign($request));
        $request->setHeaders($headers);
        return $request;
    }
}
