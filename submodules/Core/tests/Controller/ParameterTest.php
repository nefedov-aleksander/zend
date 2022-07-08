<?php

namespace Bpm\Test\Core\Controller;


use Bpm\Core\Controller\Parameter\Exception\InvalidMapperException;
use Bpm\Core\Controller\Parameter\FromPost;
use Bpm\Core\Controller\Parameter\FromQuery;
use Bpm\Core\Controller\Parameter\ParameterInterface;
use PHPUnit\Framework\TestCase;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;

class ParameterTest extends TestCase
{
    public function testFromQuery()
    {
        $from = new FromQuery(Mapper::class);

        $this->assertInstanceOf(ParameterInterface::class, $from);

        $request = new Request();
        $request->setQuery(new Parameters([
            'id' => 11,
            'name' => 'some string'
        ]));

        $result = $from->map($request, \stdClass::class);

        $this->assertEquals(11, $result->id);
        $this->assertEquals('some string', $result->str);
    }

    public function testFromQueryNotFoundMapper()
    {
        $this->expectException(InvalidMapperException::class);

        $from = new FromQuery(EmptyMapper::class);
        $result = $from->map(new Request(), \stdClass::class);
        $from->map(new Request(), \stdClass::class);
    }

    public function testFromPost()
    {
        $from = new FromPost(Mapper::class);

        $this->assertInstanceOf(ParameterInterface::class, $from);

        $request = new Request();
        $request->setPost(new Parameters([
            'id' => 11,
            'name' => 'some string'
        ]));

        $result = $from->map($request, \stdClass::class);

        $this->assertEquals(11, $result->id);
        $this->assertEquals('some string', $result->str);
    }

    public function testFromPostNotFoundMapper()
    {
        $this->expectException(InvalidMapperException::class);

        $from = new FromQuery(EmptyMapper::class);
        $result = $from->map(new Request(), \stdClass::class);
        $from->map(new Request(), \stdClass::class);
    }
}

class Mapper
{
    public static function mapPost(Parameters $parameters): \stdClass
    {
        $request = new \stdClass();
        $request->id = $parameters->get('id');
        $request->str = $parameters->get('name');
        return $request;
    }

    public static function map(int $id): \stdClass
    {

    }
}

class EmptyMapper{}