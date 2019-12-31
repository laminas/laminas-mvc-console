<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Mvc\Console\Service;

use Interop\Container\ContainerInterface;
use Laminas\Mvc\Console\Service\ConsoleRouteNotFoundStrategyFactory;
use Laminas\Mvc\Console\View\RouteNotFoundStrategy;
use PHPUnit_Framework_TestCase as TestCase;

class ConsoleRouteNotFoundStrategyFactoryTest extends TestCase
{
    public function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->factory = new ConsoleRouteNotFoundStrategyFactory();
    }

    public function testDisplaysRouteNotFoundReasonByDefault()
    {
        $this->container->has('config')->willReturn(false);

        $strategy = $this->factory->__invoke($this->container->reveal(), RouteNotFoundStrategy::class);
        $this->assertInstanceOf(RouteNotFoundStrategy::class, $strategy);
        $this->assertTrue($strategy->displayNotFoundReason());
    }

    public function overrideDisplayNotFoundReasonConfig()
    {
        return [
            'console' => [[
                'console' => ['view_manager' => [
                    'display_not_found_reason' => false,
                ]],
            ]],
            'default' => [[
                'view_manager' => [
                    'display_not_found_reason' => false,
                ],
            ]],
        ];
    }

    /**
     * @dataProvider overrideDisplayNotFoundReasonConfig
     */
    public function testCanToggleDisplayRouteNotFoundReasonFlagViaConfiguration($config)
    {
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn($config);

        $strategy = $this->factory->__invoke($this->container->reveal(), RouteNotFoundStrategy::class);
        $this->assertInstanceOf(RouteNotFoundStrategy::class, $strategy);
        $this->assertFalse($strategy->displayNotFoundReason());
    }
}
