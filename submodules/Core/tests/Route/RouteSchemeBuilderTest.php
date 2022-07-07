<?php
namespace Bpm\Test\Core\Route;

use Bpm\Core\Route\Builder\RouteSchemeBuilder;
use Bpm\Core\Route\Builder\RouteSchemeBuilderInterface;
use Bpm\Core\Route\HttpGet;
use Bpm\Core\Route\HttpPost;
use Bpm\Core\Route\HttpPut;
use Bpm\Core\Route\HttpDelete;
use Bpm\Core\Router\Http\Method;
use PHPUnit\Framework\TestCase;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

class RouteSchemeBuilderTest extends TestCase
{
    public function testBuilderInstanceOfHttpRouteSchemeBuilderInterface()
    {
        $builder = new RouteSchemeBuilder(new \Ds\Vector());

        $this->assertInstanceOf(RouteSchemeBuilderInterface::class, $builder);
    }

    public function testBuilderHttpGetRoute()
    {
        $routes = new \Ds\Vector();
        $builder = new RouteSchemeBuilder($routes);

        $this->assertArrayNotHasKey('get', $builder->build());

        $get = new HttpGet('get');
        $get->setActionName('get');

        $routes = new \Ds\Vector([$get]);

        $builder = new RouteSchemeBuilder($routes);
        $scheme = $builder->build();
        $this->assertArrayHasKey('get', $scheme);

        $this->assertEquals(Method::class, $scheme['get']['type']);
        $this->assertEquals('get', $scheme['get']['options']['verb']);
    }

    public function testBuilderHttpPostRoute()
    {
        $routes = new \Ds\Vector();
        $builder = new RouteSchemeBuilder($routes);

        $this->assertArrayNotHasKey('post', $builder->build());

        $post = new HttpPost('post');
        $post->setActionName('post');

        $routes = new \Ds\Vector([$post]);
        $builder = new RouteSchemeBuilder($routes);
        $scheme = $builder->build();

        $this->assertArrayHasKey('post', $scheme);
        $this->assertEquals(Method::class, $scheme['post']['type']);
        $this->assertEquals('post', $scheme['post']['options']['verb']);
    }

    public function testBuilderHttpPutRoute()
    {
        $routes = new \Ds\Vector();
        $builder = new RouteSchemeBuilder($routes);

        $this->assertArrayNotHasKey('put', $builder->build());

        $put = new HttpPut('put');
        $put->setActionName('put');
        $routes = new \Ds\Vector([$put]);

        $builder = new RouteSchemeBuilder($routes);
        $scheme = $builder->build();

        $this->assertArrayHasKey('put', $scheme);
        $this->assertEquals(Method::class, $scheme['put']['type']);
        $this->assertEquals('put', $scheme['put']['options']['verb']);
    }

    public function testBuilderHttpDeleteRoute()
    {
        $routes = new \Ds\Vector();
        $builder = new RouteSchemeBuilder($routes);

        $this->assertArrayNotHasKey('delete', $builder->build());

        $delete = new HttpDelete('delete');
        $delete->setActionName('delete');

        $routes = new \Ds\Vector([$delete]);
        $builder = new RouteSchemeBuilder($routes);
        $scheme = $builder->build();

        $this->assertArrayHasKey('delete', $scheme);
        $this->assertEquals(Method::class, $scheme['delete']['type']);
        $this->assertEquals('delete', $scheme['delete']['options']['verb']);
    }

    public function testBuilderChildRoutes()
    {
        $get1 = new HttpGet('get/{id:int}');
        $get1->setActionName('getAction');
        $get2 = new HttpGet('get/{id:int}/test');
        $get2->setActionName('getTestAction');

        $post1 = new HttpPost('post');
        $post1->setActionName('postAction');
        $post2 = new HttpPost('test');
        $post2->setActionName('testAction');

        $put1 = new HttpPut('put');
        $put1->setActionName('putAction');
        $put2 = new HttpPut('test');
        $put2->setActionName('testAction');

        $delete = new HttpDelete('delete/{id:int}');
        $delete->setActionName('deleteAction');

        $routes = new \Ds\Vector([
            $get1,
            $get2,
            $post1,
            $post2,
            $put1,
            $put2,
            $delete
        ]);
        $builder = new RouteSchemeBuilder($routes);

        $scheme = $builder->build();


        $this->assertCount(2, $scheme['get']['child_routes']);

        $this->assertArrayHasKey('getAction', $scheme['get']['child_routes']);
        $this->assertArrayHasKey('getTestAction', $scheme['get']['child_routes']);

        $this->assertEquals(Segment::class, $scheme['get']['child_routes']['getAction']['type']);
        $this->assertEquals(Segment::class, $scheme['get']['child_routes']['getTestAction']['type']);

        $this->assertEquals('get/:id', $scheme['get']['child_routes']['getAction']['options']['route']);
        $this->assertEquals('getAction', $scheme['get']['child_routes']['getAction']['options']['defaults']['action']);
        $this->assertCount(1, $scheme['get']['child_routes']['getAction']['options']['constraints']);
        $this->assertEquals('\d+', $scheme['get']['child_routes']['getAction']['options']['constraints']['id']);

        $this->assertEquals('get/:id/test', $scheme['get']['child_routes']['getTestAction']['options']['route']);
        $this->assertEquals('getTestAction', $scheme['get']['child_routes']['getTestAction']['options']['defaults']['action']);
        $this->assertCount(1, $scheme['get']['child_routes']['getTestAction']['options']['constraints']);
        $this->assertEquals('\d+', $scheme['get']['child_routes']['getTestAction']['options']['constraints']['id']);



        $this->assertCount(2, $scheme['post']['child_routes']);

        $this->assertArrayHasKey('postAction', $scheme['post']['child_routes']);
        $this->assertArrayHasKey('testAction', $scheme['post']['child_routes']);

        $this->assertEquals(Literal::class, $scheme['post']['child_routes']['postAction']['type']);
        $this->assertEquals(Literal::class, $scheme['post']['child_routes']['testAction']['type']);

        $this->assertEquals('post', $scheme['post']['child_routes']['postAction']['options']['route']);
        $this->assertEquals('postAction', $scheme['post']['child_routes']['postAction']['options']['defaults']['action']);
        $this->assertArrayNotHasKey('constraints', $scheme['post']['child_routes']['postAction']['options']);

        $this->assertEquals('test', $scheme['post']['child_routes']['testAction']['options']['route']);
        $this->assertEquals('testAction', $scheme['post']['child_routes']['testAction']['options']['defaults']['action']);
        $this->assertArrayNotHasKey('constraints', $scheme['post']['child_routes']['testAction']['options']);





        $this->assertCount(2, $scheme['put']['child_routes']);
        $this->assertArrayHasKey('putAction', $scheme['put']['child_routes']);
        $this->assertArrayHasKey('testAction', $scheme['put']['child_routes']);

        $this->assertEquals(Literal::class, $scheme['put']['child_routes']['putAction']['type']);
        $this->assertEquals(Literal::class, $scheme['put']['child_routes']['testAction']['type']);

        $this->assertEquals('put', $scheme['put']['child_routes']['putAction']['options']['route']);
        $this->assertEquals('putAction', $scheme['put']['child_routes']['putAction']['options']['defaults']['action']);
        $this->assertArrayNotHasKey('constraints', $scheme['put']['child_routes']['putAction']['options']);

        $this->assertEquals('test', $scheme['put']['child_routes']['testAction']['options']['route']);
        $this->assertEquals('testAction', $scheme['put']['child_routes']['testAction']['options']['defaults']['action']);
        $this->assertArrayNotHasKey('constraints', $scheme['put']['child_routes']['testAction']['options']);




        $this->assertCount(1, $scheme['delete']['child_routes']);
        $this->assertArrayHasKey('deleteAction', $scheme['delete']['child_routes']);
        $this->assertEquals(Segment::class, $scheme['delete']['child_routes']['deleteAction']['type']);
        $this->assertEquals('delete/:id', $scheme['delete']['child_routes']['deleteAction']['options']['route']);
        $this->assertEquals('deleteAction', $scheme['delete']['child_routes']['deleteAction']['options']['defaults']['action']);
        $this->assertCount(1, $scheme['delete']['child_routes']['deleteAction']['options']['constraints']);
        $this->assertEquals('\d+', $scheme['delete']['child_routes']['deleteAction']['options']['constraints']['id']);

    }
}