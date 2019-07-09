<?php

namespace tests\Filters;

use BlastCloud\Chassis\Expectation;
use BlastCloud\Chassis\Filters\WithCallback;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use tests\testFiles\ChassisChild;

class WithCallbackTest extends TestCase
{
    /** @var ChassisChild */
    public $chassis;

    public function setUp(): void
    {
        parent::setUp();

        $this->chassis = new ChassisChild($this);
    }

    public function testWithCallback()
    {
        $this->chassis->setHistory([
           ['first'], ['second']
        ]);

        $this->chassis->assertFirst(function (Expectation $e) {
            return $e->withCallback(function ($history) {
                return true;
            });
        });

        $this->chassis->assertNone(function (Expectation $e) {
            return $e->withCallback(function ($history) {
                return false;
            });
        });
    }

    public function testFailureString()
    {
        $this->chassis->setHistory([
            ['first']
        ]);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage((new WithCallback())->__toString());

        $this->chassis->assertAll(function (Expectation $e) {
            return $e->withCallback(function ($history) {
                return false;
            });
        });
    }

    public function testFailureUserString()
    {
        $this->chassis->setHistory([
            ['first']
        ]);

        $message = 'My custom callback message.';

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage($message);

        $this->chassis->assertAll(function (Expectation $e) use ($message) {
            return $e->withCallback(function ($history) {
                return false;
            }, $message);
        });
    }
}
