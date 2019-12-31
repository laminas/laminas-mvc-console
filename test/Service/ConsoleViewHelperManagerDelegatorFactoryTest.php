<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Mvc\Console\Service;

use Interop\Container\ContainerInterface;
use Laminas\Mvc\Console\Service\ConsoleViewHelperManagerDelegatorFactory;
use Laminas\View\Helper;
use Laminas\View\HelperPluginManager;
use PHPUnit_Framework_TestCase as TestCase;
use Prophecy\Argument;

class ConsoleViewHelperManagerDelegatorFactoryTest extends TestCase
{
    use FactoryEnvironmentTrait;

    public function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class)->reveal();
        $this->plugins   = $this->prophesize(HelperPluginManager::class);
        $this->callback  = function () {
            return $this->plugins->reveal();
        };
        $this->factory   = new ConsoleViewHelperManagerDelegatorFactory();
    }

    public function testReturnsPluginsUnalteredWhenNotInConsoleEnvironment()
    {
        $this->setConsoleEnvironment(false);

        $this->plugins->setFactory(Helper\Url::class, Argument::type('callable'))->shouldNotBeCalled();
        $this->plugins->setFactory('laminasviewhelperurl', Argument::type('callable'))->shouldNotBeCalled();
        $this->plugins->setFactory(Helper\BasePath::class, Argument::type('callable'))->shouldNotBeCalled();
        $this->plugins->setFactory('laminasviewhelperbasepath', Argument::type('callable'))->shouldNotBeCalled();

        $this->assertSame(
            $this->plugins->reveal(),
            $this->factory->__invoke($this->container, 'ViewHelperManager', $this->callback)
        );
    }

    public function testInjectsPluginFactoriesWhenInConsoleEnvironment()
    {
        $this->setConsoleEnvironment(true);

        $this->plugins->setFactory(Helper\Url::class, Argument::type('callable'))->shouldBeCalled();
        $this->plugins->setFactory('laminasviewhelperurl', Argument::type('callable'))->shouldBeCalled();
        $this->plugins->setFactory(Helper\BasePath::class, Argument::type('callable'))->shouldBeCalled();
        $this->plugins->setFactory('laminasviewhelperbasepath', Argument::type('callable'))->shouldBeCalled();

        $this->assertSame(
            $this->plugins->reveal(),
            $this->factory->__invoke($this->container, 'ViewHelperManager', $this->callback)
        );
    }
}
