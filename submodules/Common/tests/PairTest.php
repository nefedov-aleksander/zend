<?php


namespace Bpm\Test\Common;


use Bpm\Common\Pair;
use PHPUnit\Framework\TestCase;

class PairTest extends TestCase
{

    public function testPair()
    {
        $pair = new Pair('test', 'tset');

        $this->assertEquals('test', $pair->key);
        $this->assertEquals('tset', $pair->value);
    }

}