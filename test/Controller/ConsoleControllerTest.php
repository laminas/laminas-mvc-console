<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Mvc\Console\Controller;

use Laminas\Console\Request as ConsoleRequest;
use Laminas\Console\Response as ConsoleResponse;
use Laminas\Http\Request as HttpRequest;
use Laminas\Mvc\Console\Controller\Plugin\CreateConsoleNotFoundModel;
use Laminas\Mvc\Console\View\ViewModel;
use Laminas\Mvc\Controller\PluginManager;
use Laminas\Mvc\MvcEvent;
use Laminas\Router\RouteMatch;
use Laminas\ServiceManager\Factory\InvokableFactory;
use PHPUnit_Framework_TestCase as TestCase;
use Prophecy\Argument;

class ConsoleControllerTest extends TestCase
{
    /**
     * @var TestAsset\ConsoleController
     */
    public $controller;

    public function setUp()
    {
        $this->controller = new TestAsset\ConsoleController();

        $plugins = $this->controller->getPluginManager();
        $plugins->setAlias('createConsoleNotFoundModel', CreateConsoleNotFoundModel::class);
        $plugins->setFactory(CreateConsoleNotFoundModel::class, InvokableFactory::class);

        $routeMatch = new RouteMatch(['controller' => 'controller-sample']);
        $event      = new MvcEvent();
        $event->setRouteMatch($routeMatch);
        $this->controller->setEvent($event);
    }

    public function testDispatchCorrectRequest()
    {
        $request = new ConsoleRequest();
        $result = $this->controller->dispatch($request);

        $this->assertNotNull($result);
    }

    public function testDispatchIncorrectRequest()
    {
        $request = new HttpRequest();

        $this->setExpectedException('\Laminas\Mvc\Console\Exception\InvalidArgumentException');
        $this->controller->dispatch($request);
    }

    public function testGetNoInjectedConsole()
    {
        $console = $this->controller->getConsole();

        $this->assertNull($console);
    }

    public function testGetInjectedConsole()
    {
        $consoleAdapter = $this->getMock('\Laminas\Console\Adapter\AdapterInterface');

        $controller = $this->controller->setConsole($consoleAdapter);
        $console = $this->controller->getConsole();

        $this->assertInstanceOf('\Laminas\Mvc\Console\Controller\AbstractConsoleController', $controller);
        $this->assertInstanceOf('\Laminas\Console\Adapter\AdapterInterface', $console);
    }

    public function testNotFoundActionInvokesCreateConsoleNotFoundModelPlugin()
    {
        $routeMatch = $this->prophesize(RouteMatch::class);
        $routeMatch->setParam('action', 'not-found')->shouldBeCalled();

        $event = $this->prophesize(MvcEvent::class);
        $event->getRouteMatch()->willReturn($routeMatch);

        $plugin = new CreateConsoleNotFoundModel();
        $plugins = $this->prophesize(PluginManager::class);
        $plugins->setController(Argument::type(TestAsset\ConsoleController::class))->shouldBeCalled();
        $plugins->get('createConsoleNotFoundModel', Argument::any())->willReturn($plugin);

        $controller = new TestAsset\ConsoleController();
        $controller->setEvent($event->reveal());
        $controller->setPluginManager($plugins->reveal());

        $result = $controller->notFoundAction();
        $this->assertInstanceOf(ViewModel::class, $result);
    }
}
