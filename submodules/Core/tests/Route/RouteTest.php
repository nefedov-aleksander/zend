<?php

namespace Bpm\Test\Core\Route;

use Bpm\Core\Route\HttpDelete;
use Bpm\Core\Route\HttpGet;
use Bpm\Core\Route\HttpPost;
use Bpm\Core\Route\HttpPut;
use Bpm\Core\Route\Part\RouteAttribute;
use Bpm\Core\Route\Part\RouteConstraintInterface;
use Bpm\Core\Route\Part\RoutePart;
use Bpm\Core\Route\Part\RoutePartFactory;
use Bpm\Core\Route\Part\RoutePartFactoryInterface;
use Bpm\Core\Route\Part\RoutePartInterface;
use Bpm\Core\Route\RouteInterface;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
    public function testRoutePartInstanceOfRoutePartInterface()
    {
        $this->assertInstanceOf(RoutePartInterface::class, new RoutePart('test'));
        $this->assertInstanceOf(RoutePartInterface::class, new RouteAttribute("{id:int}"));
    }

    public function testRoutePart()
    {
        $part = new RoutePart('test');
        $this->assertEquals('test', (string) $part);
    }

    public function testRouteAttribute()
    {
        $attribute = new RouteAttribute("{id:int}");
        $this->assertInstanceOf(RouteConstraintInterface::class, $attribute);
        $this->assertEquals(':id', (string) $attribute);

        $constraint = $attribute->getConstraint();
        $this->assertEquals('id', array_key_first($constraint));
        $this->assertEquals('\d+', reset($constraint));

        $attribute = new RouteAttribute("{val:string}");
        $this->assertInstanceOf(RouteConstraintInterface::class, $attribute);
        $this->assertEquals(':val', (string) $attribute);

        $constraint = $attribute->getConstraint();
        $this->assertEquals('val', array_key_first($constraint));
        $this->assertEquals('.+', reset($constraint));
    }

    public function testEmptyRoutePart()
    {
        $this->expectException(\InvalidArgumentException::class);
        $part = new RoutePart('');
    }

    public function testEmptyRouteAttribute()
    {
        $this->expectException(\InvalidArgumentException::class);
        $part = new RouteAttribute('');
    }

    public function testEmptyNameRouteAttribute()
    {
        $this->expectException(\InvalidArgumentException::class);
        $part = new RouteAttribute('{:int}');
    }

    public function testEmptyConstraintRouteAttribute()
    {
        $this->expectException(\InvalidArgumentException::class);
        $part = new RouteAttribute('{id}');
    }

    public function testInvalidNameRouteAttribute()
    {
        $this->expectException(\InvalidArgumentException::class);
        $part = new RouteAttribute('{2asd:int}');
    }

    public function testInvalidConstraintRouteAttribute()
    {
        $this->expectException(\InvalidArgumentException::class);
        $part = new RouteAttribute('{tests:object}');
    }

    public function testRoutePartFactory()
    {
        $factory = new RoutePartFactory();
        $this->assertInstanceOf(RoutePartFactoryInterface::class, $factory);

        $this->assertInstanceOf(RoutePart::class, $factory->create('test'));

        $this->assertInstanceOf(RouteAttribute::class, $factory->create('{id:int}'));
    }

    public function testHttpGet()
    {
        $route = new HttpGet("test/{id:int}/{val:string}");
        $route->setActionName('action');
        $this->assertInstanceOf(RouteInterface::class, $route);

        $this->assertEquals('get', $route->getRouteTypeName());
        $this->assertEquals('action', $route->getActionName());
        $this->assertEquals('test/:id/:val', $route->compile());

        $this->assertTrue($route->hasConstraints());
        $constraints = $route->getConstraints();
        $this->assertCount(2, $constraints);
    }

    public function testHttpPost()
    {
        $route = new HttpPost("post/{id:int}/{val:string}");
        $route->setActionName('action');
        $this->assertInstanceOf(RouteInterface::class, $route);

        $this->assertEquals('post', $route->getRouteTypeName());
        $this->assertEquals('action', $route->getActionName());
        $this->assertEquals('post/:id/:val', $route->compile());

        $this->assertTrue($route->hasConstraints());
        $constraints = $route->getConstraints();
        $this->assertCount(2, $constraints);
    }

    public function testHttpPut()
    {
        $route = new HttpPut("put/{id:int}/{val:string}");
        $route->setActionName('action');
        $this->assertInstanceOf(RouteInterface::class, $route);

        $this->assertEquals('put', $route->getRouteTypeName());
        $this->assertEquals('action', $route->getActionName());
        $this->assertEquals('put/:id/:val', $route->compile());

        $this->assertTrue($route->hasConstraints());
        $constraints = $route->getConstraints();
        $this->assertCount(2, $constraints);
    }

    public function testHttpDelete()
    {
        $route = new HttpDelete("delete/{id:int}/{val:string}");
        $route->setActionName('action');
        $this->assertInstanceOf(RouteInterface::class, $route);

        $this->assertEquals('delete', $route->getRouteTypeName());
        $this->assertEquals('action', $route->getActionName());
        $this->assertEquals('delete/:id/:val', $route->compile());

        $this->assertTrue($route->hasConstraints());
        $constraints = $route->getConstraints();
        $this->assertCount(2, $constraints);
    }
}