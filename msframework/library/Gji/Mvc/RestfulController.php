<?php

namespace Gji;

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

	protected function basicResponse($code)
	{
		$response = $this->getResponse();
		$response->setStatusCode($code);
		return $response;
	}

	protected function createdResponse($id)
	{
		$location = $this->getRequest()->getUriString() . "/$id";
		$response = $this->getResponse();
		$response->setStatusCode(Response::STATUS_CODE_201);
		$response->getHeaders()
			->addHeaderLine("Content-Type: application/json")
			->addHeaderLine("Location: $location");
		$response->setContent(Json::encode(array('id' => $id,
			'location' => $location)));
		return $response;
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

	protected function badRequestResponse()
	{
		return $this->basicResponse(Response::STATUS_CODE_400);
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
		$response = $this->getResponse();
		$response->setStatusCode(Response::STATUS_CODE_405);
		$response->getHeaders()
			->addHeaderLine('Access-Control-Allow-Methods', $methods);
		return $response;
	}

	protected function conflictResponse()
	{
		return $this->basicResponse(Response::STATUS_CODE_409);
	}

	protected function goneResponse()
	{
		return $this->basicResponse(Response::STATUS_CODE_410);
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
