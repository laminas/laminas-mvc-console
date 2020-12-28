<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Mvc\Console\Service;

use Interop\Container\ContainerInterface;
use Laminas\Mvc\Application;
use Laminas\Mvc\Console\Service\ConsoleViewHelperManagerDelegatorFactory;
use Laminas\Mvc\MvcEvent;
use Laminas\Router\RouteMatch;
use Laminas\Router\RouteStackInterface;
use Laminas\View\Helper;
use Laminas\View\HelperPluginManager;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use ReflectionMethod;

class ConsoleViewHelperManagerDelegatorFactoryTest extends TestCase
{
    use FactoryEnvironmentTrait;
    use ProphecyTrait;

    public function setUp() : void
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

    public function testCreateUrlHelperFactoryInjectsHelperWithRouterAndRouteMatchWhenPresent()
    {
        $container = $this->prophesize(ContainerInterface::class);

        $router = $this->prophesize(RouteStackInterface::class);
        $container->get('HttpRouter')->will([$router, 'reveal']);

        $routeMatch = $this->prophesize(RouteMatch::class);

        $mvcEvent = $this->prophesize(MvcEvent::class);
        $mvcEvent->getRouteMatch()->will([$routeMatch, 'reveal']);

        $application = $this->prophesize(Application::class);
        $application->getMvcEvent()->will([$mvcEvent, 'reveal']);
        $container->get('Application')->will([$application, 'reveal']);

        $r = new ReflectionMethod($this->factory, 'createUrlHelperFactory');
        $r->setAccessible(true);
        $factory = $r->invoke($this->factory, $container->reveal());
        $helper = $factory();

        $reflectionClass = new \ReflectionClass(get_class($helper));
        $reflectionPropertyRouter = $reflectionClass->getProperty('router');
        $reflectionPropertyRouter->setAccessible(true);

        $reflectionPropertyRouteMatch = $reflectionClass->getProperty('routeMatch');
        $reflectionPropertyRouteMatch->setAccessible(true);

        $this->assertSame($router->reveal(), $reflectionPropertyRouter->getValue($helper));
        $this->assertSame($routeMatch->reveal(), $reflectionPropertyRouteMatch->getValue($helper));
    }
}
