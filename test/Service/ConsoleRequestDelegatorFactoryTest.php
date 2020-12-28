<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Mvc\Console\Service;

use Laminas\Console\Request;
use Laminas\Mvc\Console\Service\ConsoleRequestDelegatorFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ConsoleRequestDelegatorFactoryTest extends TestCase
{
    use FactoryEnvironmentTrait;
    use ProphecyTrait;

    public function setUp() : void
    {
        $this->factory = new ConsoleRequestDelegatorFactory();
    }

    public function testReturnsReturnValueOfCallbackWhenNotInConsoleEnvironment()
    {
        $this->setConsoleEnvironment(false);

        $callback = function () {
            return 'FOO';
        };

        $this->assertSame(
            $callback(),
            $this->factory->__invoke($this->createContainer(), 'Request', $callback)
        );
    }

    public function testReturnsConsoleRequestWhenInConsoleEnvironment()
    {
        $this->setConsoleEnvironment(true);

        $callback = function () {
            return 'FOO';
        };

        $result = $this->factory->__invoke($this->createContainer(), 'Request', $callback);
        $this->assertInstanceOf(Request::class, $result);
    }
}
