<?php

namespace Gji\Authentication;

use Zend\Authentication\Adapter;
use Zend\Authentication\Result;
use Zend\Json\Json;
use Zend\Http\Response;
use Zend\Debug\Debug;
use Gji\RestClient;

class RestAdapter extends RestClient implements Adapter\AdapterInterface {
	protected $username;
	protected $password;
	protected $key;

	public function authenticate()
	{
		if (!$this->resource)
			throw new \Exception('$this->resource is not set');
		$this->setParameterGet(array(
			'AppKey' => $this->key,
			'Username' => $this->username,
			'Password' => $this->password));
		$rsp = $this->sendGet();

		switch ($rsp->getStatusCode()) {
		case Response::STATUS_CODE_200:
			$response = Json::decode($rsp->getBody());
			$this->setResource("user/" .
				$response->Result->User->id);
			$params = array(
				'AppKey' => $this->key,
				'AuthKey' => $response->Result->User->strAuthKey,
				'CustomerNumber' => $response->Result->User->customers[0]->fk_Customer->strCustomerNumber);
			$this->setParameterGet($params);
			$rsp = $this->sendGet();
			$response = Json::decode($rsp->getBody());
			$identity = array(
				'loggedIn' => true,
				'authKey' => $params['AuthKey'],
				'name' => $response->username,
				'fullname' => ucwords("$response->firstname $response->lastname"),
				'userId' => $response->id,
				'customerNumber' => $response->customers[0]->customer_number,
				'customerName' => $response->customers[0]->name,
				'roleId' => $response->role->id);
			$code = Result::SUCCESS;
			$message = array();
			break;
		case Response::STATUS_CODE_401:
			$code = Result::FAILURE_CREDENTIAL_INVALID;
			$identity = $this->username;
			$message = array('Wrong email address or password.');
			break;
		case Response::STATUS_CODE_500:
		default:
			$code = Result::FAILURE_UNCATEGORIZED;
			$identity = $this->username;
                        $message = array('An unknown error occurred.');
			break;
		}
		return new Result($code, $identity, $message);
	}

	public function setUsername($username)
	{
		$this->username = $username;
		return $this;
	}

	public function setPassword($password)
	{
		$this->password = $password;
		return $this;
	}

	public function setConsumerKey($key)
	{
		$this->key = $key;
		return $this;
	}
}
