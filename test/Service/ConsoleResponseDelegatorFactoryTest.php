<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Mvc\Console\Service;

use Laminas\Console\Response;
use Laminas\Mvc\Console\Service\ConsoleResponseDelegatorFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ConsoleResponseDelegatorFactoryTest extends TestCase
{
    use FactoryEnvironmentTrait;
    use ProphecyTrait;

    public function setUp() : void
    {
        $this->factory = new ConsoleResponseDelegatorFactory();
    }

    public function testReturnsReturnValueOfCallbackWhenNotInConsoleEnvironment()
    {
        $this->setConsoleEnvironment(false);

        $callback = function () {
            return 'FOO';
        };

        $this->assertSame(
            $callback(),
            $this->factory->__invoke($this->createContainer(), 'Response', $callback)
        );
    }

    public function testReturnsConsoleResponseWhenInConsoleEnvironment()
    {
        $this->setConsoleEnvironment(true);

        $callback = function () {
            return 'FOO';
        };

        $result = $this->factory->__invoke($this->createContainer(), 'Response', $callback);
        $this->assertInstanceOf(Response::class, $result);
    }
}
