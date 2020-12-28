<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Mvc\Console\Router;

use Interop\Container\ContainerInterface;
use Laminas\Mvc\Console\Router\ConsoleRouterFactory;
use Laminas\Mvc\Console\Router\SimpleRouteStack;
use Laminas\Router\RoutePluginManager;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ConsoleRouterFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function setUp() : void
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->factory = new ConsoleRouterFactory();
    }

    public function testReturnsASimpleRouteStackByDefaultWithNoConfig()
    {
        $container = $this->container;
        $container->has('config')->willReturn(false);
        $container->get('RoutePluginManager')->will(function () use ($container) {
            return new RoutePluginManager($container->reveal());
        });
        $router = $this->factory->__invoke($container->reveal(), 'ConsoleRouter');
        $this->assertInstanceOf(SimpleRouteStack::class, $router);
        $this->assertCount(0, $router->getRoutes());
    }

    public function testWillUseEmptyConfigToCreateSimpleRouteStackIfPresent()
    {
        $container = $this->container;
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([]);
        $container->get('RoutePluginManager')->will(function () use ($container) {
            return new RoutePluginManager($container->reveal());
        });
        $router = $this->factory->__invoke($container->reveal(), 'ConsoleRouter');
        $this->assertInstanceOf(SimpleRouteStack::class, $router);
        $this->assertCount(0, $router->getRoutes());
    }

    public function testWillUseEmptyRouterConfigToCreateSimpleRouteStackIfPresent()
    {
        $container = $this->container;
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn(['console' => ['router' => []]]);
        $container->get('RoutePluginManager')->will(function () use ($container) {
            return new RoutePluginManager($container->reveal());
        });
        $router = $this->factory->__invoke($container->reveal(), 'ConsoleRouter');
        $this->assertInstanceOf(SimpleRouteStack::class, $router);
        $this->assertCount(0, $router->getRoutes());
    }

    public function testWillUseRouterConfigToCreateSimpleRouteStack()
    {
        $container = $this->container;
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn(['console' => ['router' => ['routes' => [
            'test' => [
                'options' => [
                    'route' => 'test',
                ],
            ],
        ]]]]);
        $container->get('RoutePluginManager')->will(function () use ($container) {
            return new RoutePluginManager($container->reveal());
        });
        $router = $this->factory->__invoke($container->reveal(), 'ConsoleRouter');
        $this->assertInstanceOf(SimpleRouteStack::class, $router);
        $this->assertCount(1, $router->getRoutes());
    }
}
