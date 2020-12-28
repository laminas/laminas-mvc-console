<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Mvc\Console\Service;

use Interop\Container\ContainerInterface;
use Laminas\Mvc\Console\Service\DefaultRenderingStrategyFactory;
use Laminas\Mvc\Console\View\DefaultRenderingStrategy;
use Laminas\Mvc\Console\View\Renderer;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class DefaultRenderingStrategyFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testReturnsDefaultRenderingStrategyWithRendererInjected()
    {
        $renderer = $this->prophesize(Renderer::class)->reveal();
        $container = $this->prophesize(ContainerInterface::class);
        $container->get(Renderer::class)->willReturn($renderer);

        $factory = new DefaultRenderingStrategyFactory();
        $result = $factory($container->reveal(), DefaultRenderingStrategy::class);
        $this->assertInstanceOf(DefaultRenderingStrategy::class, $result);
    }
}
