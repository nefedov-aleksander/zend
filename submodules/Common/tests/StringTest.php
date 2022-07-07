<?php
namespace Bpm\Test\Common;

use Bpm\Common\Str;
use PHPUnit\Framework\TestCase;

class StringTest extends TestCase
{

    public function testStringIsEmptyOrWhiteSpace()
    {
        $this->assertTrue(Str::isEmptyOrWhiteSpace(""));
        $this->assertTrue(Str::isEmptyOrWhiteSpace("     "));
        $this->assertFalse(Str::isEmptyOrWhiteSpace("str"));
    }

    public function testStringIsNullOrEmpty()
    {
        $this->assertTrue(Str::isNullOrEmpty(null));
        $this->assertTrue(Str::isNullOrEmpty(""));
        $this->assertFalse(Str::isNullOrEmpty("str"));
    }

    public function testStringIsMatch()
    {
        $this->assertTrue(Str::isMatch('12345', '~^\d+$~'));
        $this->assertFalse(Str::isMatch('qwert', '~^\d+$~'));

        $this->assertFalse(Str::isNotMatch('12345', '~^\d+$~'));
        $this->assertTrue(Str::isNotMatch('qwert', '~^\d+$~'));
    }

    public function testStringReplace()
    {
        $this->assertEquals('test_qwerty_123', Str::replace('test/qwerty/123', '/', '_'));
    }

}