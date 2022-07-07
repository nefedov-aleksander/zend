<?php


namespace Bpm\Crm\Controller;


use Bpm\Core\Route\ApiVersion;
use Bpm\Core\Route\HttpGet;
use Bpm\Core\Route\HttpPost;
use Bpm\Core\Route\Route;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

#[ApiVersion(1)]
#[Route('v<version:apiVersion>/test')]
class TestController extends AbstractRestfulController
{
    #[HttpGet('get')]
    public function getList()
    {
        return new JsonModel([
            'a' => 'b'
        ]);
    }

    #[HttpPost('test/{id:int}')]
    public function test(int $id)
    {
        return new JsonModel([
            'c' => 'd'
        ]);
    }

    #[HttpGet('testget')]
    public function testget()
    {
        return new JsonModel([
            'c' => 'd'
        ]);
    }
}