<?php
namespace Bpm\Test\Core\Router\Http;

use Bpm\Test\Core\Router\FactoryTester;
use PHPUnit\Framework\TestCase;
use Zend\Http\Request;
use Bpm\Core\Router\Http\Method as HttpMethod;
use Zend\Router\Http\RouteMatch;
use Zend\Stdlib\Request as BaseRequest;

class MethodTest extends TestCase
{
    public static function routeProvider()
    {
        return [
            'simple-match' => [
                new HttpMethod('get'),
                'get'
            ],
            'match-comma-separated-verbs' => [
                new HttpMethod('get,post'),
                'get'
            ],
            'match-comma-separated-verbs-ws' => [
                new HttpMethod('get ,   post , put'),
                'post'
            ],
            'match-ignores-case' => [
                new HttpMethod('Get'),
                'get'
            ]
        ];
    }

    /**
     * @dataProvider routeProvider
     * @param    HttpMethod $route
     * @param    $verb
     * @internal param string $path
     * @internal param int $offset
     * @internal param bool $shouldMatch
     */
    public function testMatching(HttpMethod $route, $verb)
    {
        $request = new Request();
        $request->setUri('http://example.com');
        $request->setMethod($verb);

        $match = $route->match($request);
        $this->assertInstanceOf(RouteMatch::class, $match);
        $this->assertEquals(1, $match->getLength());
    }

    public function testNoMatchWithoutVerb()
    {
        $route   = new HttpMethod('get');
        $request = new BaseRequest();

        $this->assertNull($route->match($request));
    }

    public function testFactory()
    {
        $tester = new FactoryTester($this);
        $tester->testFactory(
            HttpMethod::class,
            [
                'verb' => 'Missing "verb" in options array'
            ],
            [
                'verb' => 'get'
            ]
        );
    }
}
