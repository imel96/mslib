<?php

namespace Mslib\Test;

use Zend\Test\PHPUnit\Controller;
use Zend\Http\Response;
use Zend\Http\Request;
use Zend\Math\Rand;

class RestfulControllerTest extends Controller\AbstractHttpControllerTestCase
{
    protected $randomId;
    protected $resource;

    public function setUp()
    {
        parent::setUp();
        $this->randomId = rawurlencode(Rand::getString(8));
    }

    public function testGetList()
    {
        $this->dispatch("/$this->resource");
    }

    public function testGet()
    {
        $this->dispatch("/$this->resource/$this->randomId");
    }

    public function testHead()
    {
        $this->dispatch("/$this->resource", Request::METHOD_HEAD);
    }

    public function testUpdate()
    {
        $this->dispatch("/$this->resource/$this->randomId", Request::METHOD_PUT);
    }

    public function testReplaceList()
    {
        $this->dispatch("/$this->resource", Request::METHOD_PUT);
    }

    public function testDelete()
    {
        $this->dispatch("/$this->resource/$this->randomId",
            Request::METHOD_DELETE);
    }

    public function testDeleteList()
    {
        $this->dispatch("/$this->resource", Request::METHOD_DELETE);
    }

    public function testPatch()
    {
        $this->dispatch("/$this->resource/$this->randomId",
            Request::METHOD_PATCH);
    }

    public function testOptions()
    {
        $this->dispatch("/$this->resource", Request::METHOD_OPTIONS);
    }

    public function testCreate()
    {
        $this->dispatch("/$this->resource", Request::METHOD_POST);
    }
}
