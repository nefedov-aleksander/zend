<?php

namespace Bpm\Crm\Controller;

use Bpm\Core\Route\ApiVersion;
use Bpm\Core\Route\Route;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

#[ApiVersion(1)]
#[Route('v<version:apiVersion>/index')]
class IndexController extends AbstractRestfulController
{
    public function indexAction()
    {
        return new JsonModel([]);
    }
}
