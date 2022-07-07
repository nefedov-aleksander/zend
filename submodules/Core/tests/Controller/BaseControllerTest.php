<?php


namespace Bpm\Test\Core\Controller;


use Bpm\Core\Controller\BaseController;
use Bpm\Core\Controller\Exception\ArgumentCountError;
use PHPUnit\Framework\TestCase;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\Exception\InvalidArgumentException;
use Zend\Mvc\MvcEvent;
use Zend\Router\RouteMatch;

class BaseControllerTest extends TestCase
{

    public function testDispatchBadRequest()
    {
        $this->expectException(InvalidArgumentException::class);

        $controller = $this->getMockForAbstractClass(BaseController::class);

        $controller->dispatch(new \Zend\Stdlib\Request());
    }

    public function testOnDispatchNotAllowedMethodIsEmptyAction()
    {
        $responseMock = $this->createMock(Response::class);
        $responseMock->expects($this->once())
            ->method('setStatusCode')
            ->with($this->equalTo(Response::STATUS_CODE_405));

        $event = new MvcEvent();
        $event->setRouteMatch($this->createMock(RouteMatch::class));

        $controller = $this->getMockBuilder(BaseController::class)
            ->onlyMethods(['getResponse'])
            ->getMockForAbstractClass(BaseController::class);

        $controller->method('getResponse')->willReturn($responseMock);

        $controller->onDispatch($event);
    }

    public function testOnDispatchNotAllowedMethodIsActionNotFound()
    {
        $routeMatchMock = $this->createMock(RouteMatch::class);
        $routeMatchMock->method('getParam')
            ->with($this->equalTo('action'))
            ->will($this->returnValue('test'));

        $responseMock = $this->createMock(Response::class);
        $responseMock->expects($this->once())
            ->method('setStatusCode')
            ->with($this->equalTo(Response::STATUS_CODE_405));

        $event = new MvcEvent();
        $event->setRouteMatch($routeMatchMock);

        $controller = $this->getMockBuilder(BaseController::class)
            ->onlyMethods(['getResponse'])
            ->getMockForAbstractClass(BaseController::class);

        $controller->method('getResponse')->willReturn($responseMock);

        $controller->onDispatch($event);
    }

    public function testOnDispatchCallMethod()
    {
        $routeMatch = $this->createMock(RouteMatch::class);
        $routeMatch->expects($this->once())
            ->method('getParam')
            ->with($this->equalTo('action'))
            ->will($this->returnValue('test'));

        $event = new MvcEvent();
        $event->setRouteMatch($routeMatch);

        $controller = $this->getMockBuilder(BaseController::class)
            ->addMethods(['test'])
            ->getMockForAbstractClass();

        $controller->expects($this->once())
            ->method('test');

        $controller->onDispatch($event);
    }

    public function testOnDispatchCallMethodWithArguments()
    {
        $routeMatchMock = $this->createMock(RouteMatch::class);
        $routeMatchMock->expects($this->any())
            ->method('getParam')
            ->will($this->returnValueMap([
                ['action', null, 'test'],
                ['id', null,  1],
                ['name', null,  'some string']
            ]));

        $event = new MvcEvent();
        $event->setRouteMatch($routeMatchMock);

        $controller = $this->getMockBuilder(MockController::class)
            ->onlyMethods(['test'])
            ->getMock();

        $controller->expects($this->once())
            ->method('test')
            ->with(1, 'some string');

        $controller->onDispatch($event);
    }

    public function testOnDispatchCallMethodWithNonExistArgument()
    {
        $this->expectException(ArgumentCountError::class);

        $routeMatchMock = $this->createMock(RouteMatch::class);
        $routeMatchMock->method('getParam')
            ->will($this->returnValueMap([
                ['action', null, 'test']
            ]));

        $event = new MvcEvent();
        $event->setRouteMatch($routeMatchMock);

        $controller = $this->getMockBuilder(MockController::class)
            ->onlyMethods(['test'])
            ->getMock();

        $controller->onDispatch($event);
    }

    /**
     * @dataProvider onDispatchCallGetAndDeleteMethodsProvider
     */
    public function testOnDispatchCallGetAndDeleteMethods(string $action, string $method, array $routeMap, array $exceptArgumetns)
    {
        $routeMatchMock = $this->createMock(RouteMatch::class);
        $routeMatchMock->expects($this->any())
            ->method('getParam')
            ->will($this->returnValueMap($routeMap));

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->once())
            ->method('getMethod')
            ->willReturn($method);


        $event = new MvcEvent();
        $event->setRouteMatch($routeMatchMock);

        $controller = $this->getMockBuilder(MockController::class)
            ->onlyMethods([$action, 'getRequest'])
            ->getMockForAbstractClass();

        $controller->method('getRequest')->willReturn($requestMock);

        $controller
            ->expects($this->once())
            ->method($action)
            ->with(...$exceptArgumetns);;

        $controller->onDispatch($event);
    }

    private function onDispatchCallGetAndDeleteMethodsProvider()
    {
        return [
            [
                'test',
                Request::METHOD_GET,
                [
                    ['action', null, 'test'],
                    ['id', null,  1],
                    ['name', null,  'some string']
                ],
                [1, 'some string']
            ],
            [
                'delete',
                Request::METHOD_DELETE,
                [
                    ['action', null, 'delete'],
                    ['id', null,  1]
                ],
                [1]
            ]
        ];
    }

    public function testOnDispatchCallPostAndPutMethods()
    {
        $this->assertTrue(false);
    }

}