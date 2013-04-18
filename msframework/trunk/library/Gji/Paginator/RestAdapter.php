<?php

/*
 * $Id$
 */

namespace Gji\Paginator;

use Zend\Paginator\Adapter\AdapterInterface;
use Zend\Json\Json;
use Zend\Debug\Debug;
use Gji\RestClient;

class RestAdapter implements AdapterInterface {
	protected $client;
	protected $rowCount = null;

	public function __construct($clientConfig, $resourceName,
		$params = null)
	{
		$this->params = $params;
		$this->client = new RestClient;
		$this->client->setConfig($clientConfig)
			->setAcceptHeader('application/hal+json')
			->setResource($resourceName);
		$this->resource = $resourceName;
	}

	public function getItems($offset, $itemCountPerPage)
	{
		$this->params['offset'] = $offset;
		$this->params['limit'] = $itemCountPerPage;
		$this->client->setParameterGet($this->params);
		$response = $this->client->sendGet();

		if ($response->isOk()) {
			$collection = Json::decode($response->getBody());
			$resource = $this->resource;
			$ret = current((array) $collection->_embedded);
		} else
			$ret = array();
		return $ret;
	}

	public function count()
	{
		if ($this->rowCount !== null)
			return $this->rowCount;
		$this->client->setParameterGet($this->params);
		$response = $this->client->sendGet();

		if ($response->isOk()) {
			$collection = Json::decode($response->getBody());
			$this->rowCount = $collection->count;

		} else {
			$this->rowCount = -1;
			throw new \Exception('Got response with status: '
				. $response->getStatusCode());
		}
		return $this->rowCount;
	}
}
