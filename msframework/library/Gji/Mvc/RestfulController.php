<?php

namespace Gji\Mvc;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Http\Response;
use Zend\Json\Json;

class RestfulController extends AbstractRestfulController {

    public function get($id)
    {
        return $this->notImplemented();
    }
    
    public function getList()
    {
        return $this->notImplemented();
    }
    
    public function create($data)
    {
        return $this->notImplemented();
    }
    
    public function update($id, $data)
    {
        return $this->notImplemented();
    }
    
    public function delete($id)
    {
        return $this->notImplemented();
    }

	public function forward($request, $method, $client)
	{
		$sHeaders = $request->getHeaders();
		$dHeaders = $this->response->getHeaders();
		foreach ($sHeaders as $header)
			if (!$header instanceOf \Zend\Http\Header\Host)
				$dHeaders->addHeader($header);
		$client->setHeaders($dHeaders);
		$result = $client->sendWithMethod($method);
		$this->response->setContent($result->getBody())
			->setHeaders($result->getHeaders())
			->setStatusCode($result->getStatusCode());
		return $this->response;
	}

	protected function basicResponse($code, $message = null)
	{
		$this->response->setStatusCode($code);
		if ($message)
			$this->response->setContent($message);
		return $this->response;
	}

	protected function okResponse()
	{
		return $this->basicResponse(Response::STATUS_CODE_200);
	}

	protected function createdResponse($id)
	{
		$location = $this->getRequest()->getUriString() . "/$id";
		$this->response->setStatusCode(Response::STATUS_CODE_201);
		$this->response->getHeaders()
			->addHeaderLine("Content-Type: application/json")
			->addHeaderLine("Location: $location");
		$this->response->setContent(Json::encode(array('id' => $id,
			'location' => $location)));
		return $this->response;
	}

	protected function acceptedResponse()
	{
		return $this->basicResponse(Response::STATUS_CODE_202);
	}

	protected function noContentResponse()
	{
		return $this->basicResponse(Response::STATUS_CODE_204);
	}

	protected function resetResponse()
	{
		return $this->basicResponse(Response::STATUS_CODE_205);
	}

	protected function badRequestResponse($message = null)
	{
		return $this->basicResponse(Response::STATUS_CODE_400,
			$message);
	}

	protected function unauthorizedResponse()
	{
		return $this->basicResponse(Response::STATUS_CODE_401);
	}

	protected function forbiddenResponse()
	{
		return $this->basicResponse(Response::STATUS_CODE_403);
	}

	protected function notFoundResponse()
	{
		return $this->basicResponse(Response::STATUS_CODE_404);
	}

	protected function methodNotAllowedResponse($methods)
	{
		$this->response->setStatusCode(Response::STATUS_CODE_405)
			->getHeaders()
			->addHeaderLine('Allow', $methods);
		return $this->response;
	}

	protected function conflictResponse()
	{
		return $this->basicResponse(Response::STATUS_CODE_409);
	}

	protected function goneResponse()
	{
		return $this->basicResponse(Response::STATUS_CODE_410);
	}

	protected function preconditionFailedResponse($message = null)
	{
		return $this->basicResponse(Response::STATUS_CODE_412,
			$message);
	}

	protected function internalServerErrorResponse()
	{
		return $this->basicResponse(Response::STATUS_CODE_500);
	}

	protected function notImplementedResponse()
	{
		return $this->basicResponse(Response::STATUS_CODE_501);
	}
}
