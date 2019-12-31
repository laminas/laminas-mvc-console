<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Mvc\Console\View;

use Laminas\Console\Request;
use Laminas\EventManager\EventManager;
use Laminas\EventManager\Test\EventListenerIntrospectionTrait;
use Laminas\Mvc\Console\Router\RouteMatch;
use Laminas\Mvc\Console\View\InjectNamedConsoleParamsListener;
use Laminas\Mvc\MvcEvent;
use Laminas\Stdlib\Parameters;
use PHPUnit_Framework_TestCase as TestCase;

class InjectNamedConsoleParamsListenerTest extends TestCase
{
    use EventListenerIntrospectionTrait;

    public function setUp()
    {
        $this->listener = new InjectNamedConsoleParamsListener();
    }

    public function testAttachesToEventManagerAtExpectedPriority()
    {
        $events = new EventManager();
        $this->listener->attach($events);

        $this->assertListenerAtPriority(
            [$this->listener, 'injectNamedParams'],
            -80,
            MvcEvent::EVENT_DISPATCH,
            $events,
            'Listener not attached at expected priority'
        );
    }

    public function testReturnsEarlyIfNoRouteMatchPresentInEvent()
    {
        $event = $this->prophesize(MvcEvent::class);
        $event->getRouteMatch()->willReturn(null);
        $event->getRequest()->shouldNotBeCalled();

        $this->assertNull($this->listener->injectNamedParams($event->reveal()));
    }

    public function testReturnsEarlyIfRequestIsNotFromConsoleEnvironment()
    {
        $routeMatch = $this->prophesize(RouteMatch::class);
        $routeMatch->getParams()->shouldNotBeCalled();

        $event = $this->prophesize(MvcEvent::class);
        $event->getRouteMatch()->willReturn($routeMatch->reveal());
        $event->getRequest()->willReturn(null);

        $this->assertNull($this->listener->injectNamedParams($event->reveal()));
    }

    public function testInjectsRequestWithRouteMatchParams()
    {
        $requestParams = $this->prophesize(Parameters::class);
        $requestParams->toArray()->willReturn([
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => 'bat',
        ]);
        $requestParams->fromArray([
            'foo' => 'bar',
            'bar' => 'BAZ',
            'baz' => 'bat',
            'bat' => 'quz',
        ])->shouldBeCalled();

        $routeMatch = $this->prophesize(RouteMatch::class);
        $routeMatch->getParams()->willReturn([
            'bar' => 'BAZ',
            'bat' => 'quz',
        ]);

        $request = $this->prophesize(Request::class);
        $request->getParams()->willReturn($requestParams->reveal())->shouldBeCalledTimes(2);

        $event = $this->prophesize(MvcEvent::class);
        $event->getRouteMatch()->willReturn($routeMatch->reveal());
        $event->getRequest()->willReturn($request->reveal());

        $this->assertNull($this->listener->injectNamedParams($event->reveal()));
    }
}
