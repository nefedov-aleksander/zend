<?php

namespace Bpm\Test\Core\Controller;


use Bpm\Core\Controller\Parameter\Exception\InvalidContentException;
use Bpm\Core\Controller\Parameter\Exception\InvalidContentTypeException;
use Bpm\Core\Controller\Parameter\Exception\InvalidMapperException;
use Bpm\Core\Controller\Parameter\FromBody;
use Bpm\Core\Controller\Parameter\FromPost;
use Bpm\Core\Controller\Parameter\FromQuery;
use Bpm\Core\Controller\Parameter\ParameterInterface;
use PHPUnit\Framework\TestCase;
use Zend\Http\Header\ContentType;
use Zend\Http\Headers;
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

        $headers = new Headers();
        $headers->addHeader(new ContentType('multipart/form-data'));
        $request->setHeaders($headers);

        $result = $from->map($request, \stdClass::class);

        $this->assertEquals(11, $result->id);
        $this->assertEquals('some string', $result->str);
    }

    public function testFromPostNotFoundMapper()
    {
        $this->expectException(InvalidMapperException::class);

        $from = new FromQuery(EmptyMapper::class);

        $from->map(new Request(), \stdClass::class);
    }

    private function fromPostJsonContentTypeProvider()
    {
        return [
            [
                'application/json',
                json_encode(['id' => 54, 'name' => 'json name']),
                54,
                'json name'
            ],
            [
                'application/hal+json',
                json_encode(['id' => 78, 'name' => 'json hal name']),
                78,
                'json hal name'
            ],
            [
                'application/json; charset=utf-8',
                json_encode(['id' => 9, 'name' => 'json charset name']),
                9,
                'json charset name'
            ],
            [
                '  application/json ',
                json_encode(['id' => 67, 'name' => 'json trim name']),
                67,
                'json trim name'
            ],
            [
                ' application/json  ; charset=utf-8',
                json_encode(['id' => 187, 'name' => 'json trim charset name']),
                187,
                'json trim charset name'
            ]
        ];
    }

    /**
     * @dataProvider fromPostJsonContentTypeProvider
     */
    public function testFromPostJsonContentType(
        string $contentType,
        string $contentString,
        int $expectedId,
        string $expectedName
    )
    {
        $request = new Request();
        $request->setPost(new Parameters(['id' => 11, 'name' => 'some string']));

        $headers = new Headers();
        $headers->addHeader(new ContentType($contentType));
        $request->setHeaders($headers);
        $request->setContent($contentString);

        $from = new FromPost(Mapper::class);
        $result = $from->map($request, \stdClass::class);

        $this->assertEquals($expectedId, $result->id);
        $this->assertEquals($expectedName, $result->str);
    }

    public function testFromPostBadContentType()
    {
        $this->expectException(InvalidContentTypeException::class);

        $request = new Request();
        $headers = new Headers();
        $headers->addHeader(new ContentType('text/plain'));
        $request->setHeaders($headers);

        $from = new FromPost(Mapper::class);
        $from->map($request, \stdClass::class);
    }


    private function fromBodyProvider()
    {
        return [
            [
                'application/json',
                json_encode(['id' => 54, 'name' => 'json name']),
                54,
                'json name'
            ],
            [
                'application/hal+json',
                json_encode(['id' => 78, 'name' => 'json hal name']),
                78,
                'json hal name'
            ],
            [
                'application/json; charset=utf-8',
                json_encode(['id' => 9, 'name' => 'json charset name']),
                9,
                'json charset name'
            ],
            [
                '  application/json ',
                json_encode(['id' => 67, 'name' => 'json trim name']),
                67,
                'json trim name'
            ],
            [
                ' application/json  ; charset=utf-8',
                json_encode(['id' => 187, 'name' => 'json trim charset name']),
                187,
                'json trim charset name'
            ],
            [
                'text/plain',
                'id=755&name=body_name',
                755,
                'body_name'
            ],
        ];
    }

    /**
     * @dataProvider fromBodyProvider
     */
    public function testFromBody(
        string $contentType,
        string $contentString,
        int $expectedId,
        string $expectedName
    )
    {
        $from = new FromBody(Mapper::class);
        $this->assertInstanceOf(ParameterInterface::class, $from);

        $request = new Request();
        $headers = new Headers();
        $headers->addHeader(new ContentType($contentType));
        $request->setHeaders($headers);
        $request->setContent($contentString);

        $result = $from->map($request, \stdClass::class);

        $this->assertEquals($expectedId, $result->id);
        $this->assertEquals($expectedName, $result->str);
    }

    public function testFromBodyTextPlainBadContent()
    {
        $this->expectException(InvalidContentException::class);

        $request = new Request();
        $request->setPost(new Parameters([
            'id' => 11,
            'name' => 'some string'
        ]));

        $headers = new Headers();
        $headers->addHeader(new ContentType('text/plain'));
        $request->setHeaders($headers);
        $request->setContent('   bad_content_string');

        $from = new FromBody(Mapper::class);
        $from->map($request, \stdClass::class);
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