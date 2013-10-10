<?php

namespace Gji;

use Zend\Http\Client;

class RpcClient extends Client {
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

	public function setResource($resource)
	{
		$this->resource = $resource;
		return $this;
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
		$headers = $request->getHeaders();

        $this->setOptions(array(
            //'sslcapath'	=> '/var/www/SSL/myaccount.jjrichards.com.au/',
            'sslverifypeer' => false,
            //'ssltransport' => 'ssl',
            //'sslallowselfsigned' => false
            //'sslpassphrase'	=>	'QuXhTw8V'
        ));

		if ($this->acceptHeader) {
			$headers->addHeaderLine('Accept', $this->acceptHeader);
		}
		$request->setHeaders($headers);
		if (isset($this->xconfig->auth) && !empty($this->xconfig->auth->username)) {
			$this->setAuth($this->xconfig->auth->username, $this->xconfig->auth->password);
		}
		return $request;
	}

	function __call($name, $args)
	{
		$this->resource = "$this->resource/$name";
		$this->setParameterGet(current($args));
		return $this->sendGet();
	}
}
