<?php

use Mcprohosting\Retry\Retry;
use Mcprohosting\Retry\Runner;

class RetryTest extends PHPUnit_Framework_TestCase
{
    public function testStaticReturnsRunner()
    {
        $r = Retry::running(function () {});

        $this->assertInstanceOf('\Mcprohosting\Retry\Runner', $r);
        $this->assertInternalType('callable', $r->action);
    }

    public function testSetsAction()
    {
        $r = new Runner;
        $r->running(function () {
            return 'foo';
        });

        $this->assertInternalType('callable', $r->action);
        $this->assertEquals('foo', $r->action());
    }

    public function testSetsSoLongAs()
    {
        $r = new Runner;
        $this->assertEquals('foo', $r->condition('foo'));

        $r->soLongAs(function ($data) { return $data . 'bar'; });
        $this->assertEquals('foobar', $r->condition('foo'));

        $r->soLongAs('bar');
        $this->assertEquals('bar', $r->condition('foo'));
    }

    public function testSetsIs()
    {
        $r = new Runner;
        $r->is(true);
        $this->assertEquals(array('is', true), $r->value);
    }

    public function testSetsIsnt()
    {
        $r = new Runner;
        $r->isnt(true);
        $this->assertEquals(array('isnt', true), $r->value);
    }

    public function testSetsDelay()
    {
        $r = new Runner;
        $r->delay(500.5);
        $this->assertEquals(500500, $r->delay);
    }

    public function testGoes()
    {
        // Test that the runningner breaks when the runningning function returns a value equal to the testing value
        $r = new Runner;
        $i = 0;
        $r->running(function () use (&$i) {
            $i++;
            return $i === 7;
        })->whileIs(false)->go(50);
        $this->assertEquals(7, $i);

        // Test that the runningner breaks after a certain number of cycles regardless
        $r = new Runner;
        $i = 0;
        $r->running(function () use (&$i) {
            $i++;
            return false;
        })->whileIs(false)->go(7);
        $this->assertEquals(7, $i);

        // Test that delays are implemented correctly.
        $r = new Runner;
        $time_start = microtime(true);
        $r->running(function () {
            return false;
        })->whileIsnt(true)->delay(100)->go(5);
        $time_end = microtime(true);
        $this->assertTrue(($time_end - $time_start) > 0.49);

        // Test custom condition
        $r = new Runner;
        $i = 0;
        $r->running(function () use (&$i) {
            $i++;
            return 'foo';
        })->soLongAs(function ($data) {
            return strrev($data);
        })->is('oof')->go(7);
        $this->assertEquals(7, $i);
    }
} 
