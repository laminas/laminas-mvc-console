<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Mvc\Console\Service;

use Interop\Container\ContainerInterface;
use Laminas\Mvc\Console\Service\ViewManagerDelegatorFactory;
use PHPUnit\Framework\TestCase;

class ViewManagerDelegatorFactoryTest extends TestCase
{
    use FactoryEnvironmentTrait;

    public function setUp()
    {
        $this->factory = new ViewManagerDelegatorFactory();
    }

    public function testReturnsReturnValueOfCallbackWhenNotInConsoleEnvironment()
    {
        $this->setConsoleEnvironment(false);

        $callback = function () {
            return 'FOO';
        };

        $this->assertSame(
            $callback(),
            $this->factory->__invoke($this->createContainer(), 'ViewManager', $callback)
        );
    }

    public function testReturnsConsoleViewManagerWhenInConsoleEnvironment()
    {
        $this->setConsoleEnvironment(true);

        $viewManager = (object) ['view' => true];
        $container = $this->prophesize(ContainerInterface::class);
        $container->has('ConsoleViewManager')->willReturn(true);
        $container->get('ConsoleViewManager')->willReturn($viewManager);

        $callback = function () {
            return 'FOO';
        };

        $result = $this->factory->__invoke($container->reveal(), 'ViewManager', $callback);
        $this->assertSame($viewManager, $result);
    }
}
