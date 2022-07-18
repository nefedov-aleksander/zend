<?php


namespace Bpm\Crm\Controller;


use Bpm\Core\Controller\BaseController;
use Bpm\Core\Route\ApiVersion;
use Bpm\Core\Route\HttpGet;
use Bpm\Core\Route\HttpPost;
use Bpm\Core\Route\Route;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

#[ApiVersion(1)]
#[Route('v<version:apiVersion>/test')]
class TestController extends BaseController
{
    #[HttpGet('get')]
    public function getList()
    {
//        die('asdfasdfa');
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

    #[HttpGet('testget/{id:int}')]
    public function testget()
    {
        return new JsonModel([
            'c' => 'd'
        ]);
    }
}