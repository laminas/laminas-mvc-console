<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Mvc\Console\Service;

use Interop\Container\ContainerInterface;
use Laminas\Mvc\Console\Controller\Plugin\CreateConsoleNotFoundModel;
use Laminas\Mvc\Console\Service\ControllerPluginManagerDelegatorFactory;
use Laminas\Mvc\Controller\PluginManager;
use Laminas\ServiceManager\Factory\InvokableFactory;
use PHPUnit_Framework_TestCase as TestCase;

class ControllerPluginManagerDelegatorFactoryTest extends TestCase
{
    public function testReturnsPluginManagerWithConfigurationForCreateConsoleNotFoundModelPlugin()
    {
        $plugins = $this->prophesize(PluginManager::class);
        $plugins->setAlias('CreateConsoleNotFoundModel', CreateConsoleNotFoundModel::class)->shouldBeCalled();
        $plugins->setAlias('createConsoleNotFoundModel', CreateConsoleNotFoundModel::class)->shouldBeCalled();
        $plugins->setAlias('createconsolenotfoundmodel', CreateConsoleNotFoundModel::class)->shouldBeCalled();
        $plugins->setFactory(CreateConsoleNotFoundModel::class, InvokableFactory::class)->shouldBeCalled();

        $callback = function () use ($plugins) {
            return $plugins->reveal();
        };

        $factory = new ControllerPluginManagerDelegatorFactory();
        $this->assertSame($plugins->reveal(), $factory(
            $this->prophesize(ContainerInterface::class)->reveal(),
            'ControllerPluginManager',
            $callback
        ));
    }
}
