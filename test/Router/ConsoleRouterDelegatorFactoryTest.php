<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Mvc\Console\Router;

use Interop\Container\ContainerInterface;
use Laminas\Mvc\Console\Router\ConsoleRouterDelegatorFactory;
use LaminasTest\Mvc\Console\Service\FactoryEnvironmentTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ConsoleRouterDelegatorFactoryTest extends TestCase
{
    use FactoryEnvironmentTrait;
    use ProphecyTrait;

    public function environments()
    {
        return [
            'console' => [true],
            'http'    => [false],
        ];
    }

    /**
     * @dataProvider environments
     */
    public function testReturnsOriginalServiceWhenNotConsoleEnvironment($consoleFlag)
    {
        $this->setConsoleEnvironment($consoleFlag);
        $container = $this->prophesize(ContainerInterface::class);
        $container->get('ConsoleRouter')->shouldNotBeCalled();
        $factory   = new ConsoleRouterDelegatorFactory();

        $this->assertEquals('FOO', $factory(
            $container->reveal(),
            'not-a-router',
            function () {
                return 'FOO';
            }
        ));
    }

    /**
     * @dataProvider environments
     */
    public function testReturnsConsoleRouterServiceIfRequestedNameIsConsoleRouter($consoleFlag)
    {
        $this->setConsoleEnvironment($consoleFlag);
        $container = $this->prophesize(ContainerInterface::class);
        $container->get('ConsoleRouter')->willReturn('ConsoleRouter');

        $factory   = new ConsoleRouterDelegatorFactory();

        $this->assertEquals('ConsoleRouter', $factory(
            $container->reveal(),
            'ConsoleRouter',
            function () {
                return 'FOO';
            }
        ));
    }

    public function routerServiceNames()
    {
        return [
            ['router'],
            ['Router'],
            ['ROUTER'],
        ];
    }

    /**
     * @dataProvider routerServiceNames
     */
    public function testReturnsConsoleRouterServiceIfRequestedNameIsRouterAndInConsoleEnvironment($routerServiceName)
    {
        $this->setConsoleEnvironment(true);
        $container = $this->prophesize(ContainerInterface::class);
        $container->get('ConsoleRouter')->willReturn('ConsoleRouter');

        $factory   = new ConsoleRouterDelegatorFactory();

        $this->assertEquals('ConsoleRouter', $factory(
            $container->reveal(),
            $routerServiceName,
            function () {
                return 'FOO';
            }
        ));
    }

    /**
     * @dataProvider routerServiceNames
     */
    public function testReturnsOriginalServiceIfRequestedRoutingInterfaceAndNotInConsoleEnvironment($routerServiceName)
    {
        $this->setConsoleEnvironment(false);
        $container = $this->prophesize(ContainerInterface::class);
        $container->get('ConsoleRouter')->shouldNotBeCalled();

        $factory   = new ConsoleRouterDelegatorFactory();

        $this->assertEquals('FOO', $factory(
            $container->reveal(),
            $routerServiceName,
            function () {
                return 'FOO';
            }
        ));
    }
}
