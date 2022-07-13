<?php


namespace Bpm\Test\Core\Controller;


use Bpm\Core\Controller\BaseController;
use Bpm\Core\Controller\Exception\ArgumentCountError;
use PHPUnit\Framework\TestCase;
use Zend\Http\Header\ContentType;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\Exception\InvalidArgumentException;
use Zend\Mvc\MvcEvent;
use Zend\Router\RouteMatch;
use Zend\Stdlib\Parameters;

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

    public function testOnDispatchCallMethodWithBuiltinArguments()
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

    public function testOnDispatchCallMethodWithNonExistArgumentInRouteMatch()
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

    public function testOnDispatchCallMethodNonBuiltinArgumentWithOutParameterAttribute()
    {
        $this->expectException(\Bpm\Core\Controller\Exception\InvalidArgumentException::class);

        $routeMatchMock = $this->createMock(RouteMatch::class);
        $routeMatchMock->expects($this->any())
            ->method('getParam')
            ->will($this->returnValueMap([
                ['action', null, 'withoutParameterAttribute']
            ]));

        $event = new MvcEvent();
        $event->setRouteMatch($routeMatchMock);

        $controller = $this->getMockBuilder(MockController::class)
            ->onlyMethods(['withoutParameterAttribute'])
            ->getMock();

        $controller->onDispatch($event);
    }

    /**
     * @dataProvider onDispatchCallMethodFromArgumentProvider
     */
    public function testOnDispatchCallMethodFromArgument(
        array $routeMatchReturnValue,
        string $contentType,
        Parameters $queryParameters,
        Parameters $postParameters,
        string $bobyParameters,
        string $action,
        mixed $expectedRequest
    )
    {
        $routeMatchMock = $this->createMock(RouteMatch::class);
        $routeMatchMock->expects($this->any())
            ->method('getParam')
            ->will($this->returnValueMap($routeMatchReturnValue));

        $event = new MvcEvent();
        $event->setRouteMatch($routeMatchMock);


        $headers = new Headers();
        $headers->addHeader(new ContentType($contentType));

        $requestMock = new Request();
        $requestMock->setQuery($queryParameters);
        $requestMock->setPost($postParameters);
        $requestMock->setContent($bobyParameters);
        $requestMock->setHeaders($headers);

        $controller = $this->getMockBuilder(MockController::class)
            ->onlyMethods([$action, 'getRequest'])
            ->getMock();

        $controller->method('getRequest')->willReturn($requestMock);

        $controller->expects($this->once())
            ->method($action)
            ->with(...$expectedRequest);

        $controller->onDispatch($event);
    }

    private function onDispatchCallMethodFromArgumentProvider()
    {
        return [
            [
                [
                    ['action', null, 'testFromQuery'],
                    ['id', null,  1]
                ],
                'text/plain',
                new Parameters(['name' => 'some string']),
                new Parameters(),
                json_encode([]),
                'testFromQuery',
                [1, MockFromQueryRequest::initExpected('some string')]
            ],
            [
                [
                    ['action', null, 'testFromPost'],
                    ['id', null,  123]
                ],
                'multipart/form-data',
                new Parameters(),
                new Parameters(['data' => 'post some string']),
                json_encode([]),
                'testFromPost',
                [123, MockFromPostRequest::initExpected('post some string')]
            ],
            [
                [
                    ['action', null, 'testFromPost'],
                    ['id', null,  345]
                ],
                'application/json',
                new Parameters(),
                new Parameters(),
                json_encode(['data' => 'post json some string']),
                'testFromPost',
                [345, MockFromPostRequest::initExpected('post json some string')]
            ],
            [
                [
                    ['action', null, 'testFromBody'],
                    ['id', null,  87]
                ],
                'application/json',
                new Parameters(),
                new Parameters(),
                json_encode(['bodydata' => 'body json some string']),
                'testFromBody',
                [87, MockFromBodyRequest::initExpected('body json some string')]
            ],
            [
                [
                    ['action', null, 'testFromBody'],
                    ['id', null,  32]
                ],
                'text/plain',
                new Parameters(),
                new Parameters(),
                'bodydata=body+some+string',
                'testFromBody',
                [32, MockFromBodyRequest::initExpected('body some string')]
            ]
        ];
    }

    public function testOnDispatchCallMethodWithoutArgumentType()
    {
        $this->expectException(\Bpm\Core\Controller\Exception\InvalidArgumentException::class);

        $routeMatchMock = $this->createMock(RouteMatch::class);
        $routeMatchMock->expects($this->any())
            ->method('getParam')
            ->will($this->returnValueMap([
                ['action', null, 'withoutArgumentType']
            ]));

        $event = new MvcEvent();
        $event->setRouteMatch($routeMatchMock);

        $controller = $this->getMockBuilder(MockController::class)
            ->onlyMethods(['withoutArgumentType'])
            ->getMock();

        $controller->onDispatch($event);
    }

}