<?php


namespace Bpm\Test\Core\Response;


use Bpm\Core\Response\ApiDataError;
use Bpm\Core\Response\ApiDataInterface;
use Bpm\Core\Response\ApiDataNotFound;
use Bpm\Core\Response\ApiDataOk;
use Bpm\Core\Response\ApiDataResult;
use Bpm\Core\Response\ApiDataResultList;
use Bpm\Core\Response\Exception\BadMethodCallException;
use Bpm\Core\Response\Exception\InvalidArgumentException;
use Bpm\Core\Validation\Exception\ValidationException;
use Bpm\Core\Validation\Exception\ValidationListException;
use PHPUnit\Framework\TestCase;
use Zend\Http\Response;

class ApiDataTest extends TestCase
{

    public function testApiDataResult()
    {
        $result = new ApiDataResult((object) ['a' => 1, 'b' => 2]);

        $this->assertInstanceOf(ApiDataInterface::class, $result);

        $this->assertEquals(1, $result->getResult()->a);
        $this->assertEquals(2, $result->getResult()->b);

        $this->assertEquals(Response::STATUS_CODE_200, $result->getStatusCode());
    }

    public function testApiDataResultInvalidStatusCode()
    {
        $this->expectException(InvalidArgumentException::class);

        new ApiDataResult((object) ['a' => 1, 'b' => 2], Response::STATUS_CODE_405);
    }

    public function testApiDataOk()
    {
        $result = new ApiDataOk();

        $this->assertInstanceOf(ApiDataInterface::class, $result);

        $this->assertEquals(Response::STATUS_CODE_204, $result->getStatusCode());
    }

    public function testApiDataOkSetStatusCodeNotIn()
    {
        $this->expectException(BadMethodCallException::class);

        $ok = new ApiDataOk();
        $ok->setStatusCode(Response::STATUS_CODE_405);
    }

    public function testApiDataNotFound()
    {
        $result = new ApiDataNotFound();

        $this->assertInstanceOf(ApiDataInterface::class, $result);

        $this->assertEquals(Response::STATUS_CODE_404, $result->getStatusCode());
    }

    public function testApiDataNotFoundSetStatusCodeNotIn()
    {
        $this->expectException(BadMethodCallException::class);

        $ok = new ApiDataNotFound();
        $ok->setStatusCode(Response::STATUS_CODE_200);
    }

    public function testApiDataError()
    {
        $result = new ApiDataError(new ValidationException('Validation error'));
        $this->assertInstanceOf(ApiDataInterface::class, $result);

        $this->assertEquals(Response::STATUS_CODE_422, $result->getStatusCode());

        $this->assertCount(1, $result->getResult()->messages);
        $this->assertEquals('Validation error', $result->getResult()->messages[0]);

        $result = new ApiDataError(new ValidationListException([
            'Validation error 1',
            'Validation error 2',
            'Validation error 3'
        ]));

        $this->assertCount(3, $result->getResult()->messages);
        $this->assertEquals('Validation error 1', $result->getResult()->messages[0]);
        $this->assertEquals('Validation error 2', $result->getResult()->messages[1]);
        $this->assertEquals('Validation error 3', $result->getResult()->messages[2]);
    }

    public function testApiDataList()
    {
        $result = new ApiDataResultList([
            (object) ['a' => 1, 'b' => 10],
            (object) ['a' => 2, 'b' => 11],
            (object) ['a' => 3, 'b' => 12],
            (object) ['a' => 4, 'b' => 13],
            (object) ['a' => 5, 'b' => 14]
        ], 20, 10, 133);
        $this->assertInstanceOf(ApiDataInterface::class, $result);

        $this->assertEquals(Response::STATUS_CODE_200, $result->getStatusCode());

        $this->assertCount(5, $result->getResult()->data);
        $this->assertEquals(20, $result->getResult()->offset);
        $this->assertEquals(10, $result->getResult()->limit);
        $this->assertEquals(133, $result->getResult()->total);
    }

}