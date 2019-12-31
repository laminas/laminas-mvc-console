<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Mvc\Console\Service;

use Interop\Container\ContainerInterface;
use Laminas\Console\Adapter\AdapterInterface;
use Laminas\Mvc\Console\Controller\AbstractConsoleController;
use Laminas\Mvc\Console\Service\ControllerManagerDelegatorFactory;
use Laminas\Mvc\Controller\ControllerManager;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit_Framework_TestCase as TestCase;
use Prophecy\Argument;
use ReflectionClass;

class ControllerManagerDelegatorFactoryTest extends TestCase
{
    public function isV3ServiceManager()
    {
        $r = new ReflectionClass(ServiceManager::class);
        return $r->hasMethod('configure');
    }

    public function testInjectsConsoleInitializerIntoControllerManager()
    {
        $controllers = $this->prophesize(ControllerManager::class);
        $controllers->addInitializer(Argument::that(function ($argument) {
            if (! is_callable($argument)) {
                return false;
            }

            if (! is_array($argument) || 2 !== count($argument)) {
                return false;
            }

            $object = array_shift($argument);
            if (! $object instanceof ControllerManagerDelegatorFactory) {
                return false;
            }

            $method = array_shift($argument);
            if ($method !== 'injectConsole') {
                return false;
            }

            return true;
        }))->shouldBeCalled();

        $callback = function () use ($controllers) {
            return $controllers->reveal();
        };

        $factory = new ControllerManagerDelegatorFactory();

        $this->assertSame($controllers->reveal(), $factory(
            $this->prophesize(ContainerInterface::class)->reveal(),
            'ControllerManager',
            $callback
        ));
    }

    public function testv3InitializerDoesNothingForNonAbstractConsoleControllers()
    {
        if (! $this->isV3ServiceManager()) {
            $this->markTestSkipped(sprintf(
                '%s tests laminas-servicemanager v3-specific functionality',
                __FUNCTION__
            ));
        }

        $container = $this->prophesize(ContainerInterface::class);
        $container->get('Console')->shouldNotBeCalled();
        $instance = (object) [];

        $factory = new ControllerManagerDelegatorFactory();
        $this->assertNull($factory->injectConsole($container->reveal(), $instance));
    }

    public function testV3InitializerInjectsConsoleIntoAbstractConsoleControllers()
    {
        if (! $this->isV3ServiceManager()) {
            $this->markTestSkipped(sprintf(
                '%s tests laminas-servicemanager v3-specific functionality',
                __FUNCTION__
            ));
        }

        $console = $this->prophesize(AdapterInterface::class)->reveal();

        $controller = $this->prophesize(AbstractConsoleController::class);
        $controller->setConsole($console)->shouldBeCalled();

        // Using SM instance to allow testing against both v2 and v3
        $container = $this->prophesize(ContainerInterface::class);
        $container->get('Console')->willReturn($console);

        $factory = new ControllerManagerDelegatorFactory();
        $this->assertNull($factory->injectConsole($container->reveal(), $controller->reveal()));
    }

    public function testV2InitializerDoesNothingForNonAbstractConsoleControllers()
    {
        if ($this->isV3ServiceManager()) {
            $this->markTestSkipped(sprintf(
                '%s tests laminas-servicemanager v2-specific functionality',
                __FUNCTION__
            ));
        }

        $container = $this->prophesize(ContainerInterface::class);
        $container->get('Console')->shouldNotBeCalled();

        $controllers = $this->prophesize(ControllerManager::class);
        $controllers->getServiceLocator()->willReturn($container->reveal());

        $instance = (object) [];

        $factory = new ControllerManagerDelegatorFactory();
        $this->assertNull($factory->injectConsole($instance, $controllers->reveal()));
    }

    public function testV2InitializerInjectsConsoleIntoAbstractConsoleControllers()
    {
        if ($this->isV3ServiceManager()) {
            $this->markTestSkipped(sprintf(
                '%s tests laminas-servicemanager v2-specific functionality',
                __FUNCTION__
            ));
        }

        $console = $this->prophesize(AdapterInterface::class)->reveal();

        $controller = $this->prophesize(AbstractConsoleController::class);
        $controller->setConsole($console)->shouldBeCalled();

        $container = new ServiceManager();
        $container->setService('Console', $console);

        $controllers = $this->prophesize(ControllerManager::class);
        $controllers->getServiceLocator()->willReturn($container);

        $factory = new ControllerManagerDelegatorFactory();
        $this->assertNull($factory->injectConsole($controller->reveal(), $controllers->reveal()));
    }
}
