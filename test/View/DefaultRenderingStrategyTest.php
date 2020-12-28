<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Mvc\Console\View;

use Laminas\Console\Adapter\AbstractAdapter;
use Laminas\EventManager\EventManager;
use Laminas\EventManager\Test\EventListenerIntrospectionTrait;
use Laminas\Mvc\ApplicationInterface;
use Laminas\Mvc\Console\View\DefaultRenderingStrategy;
use Laminas\Mvc\Console\View\Renderer;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Stdlib\Response;
use Laminas\View\Model;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class DefaultRenderingStrategyTest extends TestCase
{
    use EventListenerIntrospectionTrait;
    use ProphecyTrait;

    /**
     * @var Renderer
     */
    protected $renderer;

    /**
     * @var DefaultRenderingStrategy
     */
    protected $strategy;

    public function setUp() : void
    {
        $this->renderer = $this->prophesize(Renderer::class);
        $this->strategy = new DefaultRenderingStrategy($this->renderer->reveal());
    }

    public function testAttachesRendererAtExpectedPriority()
    {
        $events = new EventManager();
        $this->strategy->attach($events);
        $this->assertListenerAtPriority(
            [$this->strategy, 'render'],
            -10000,
            MvcEvent::EVENT_RENDER,
            $events,
            'Renderer listener not found'
        );
    }

    public function testCanDetachListenersFromEventManager()
    {
        $events = new EventManager();
        $this->strategy->attach($events);

        $listeners = $this->getArrayOfListenersForEvent(MvcEvent::EVENT_RENDER, $events);
        $this->assertCount(1, $listeners);

        $this->strategy->detach($events);
        $listeners = $this->getArrayOfListenersForEvent(MvcEvent::EVENT_RENDER, $events);
        $this->assertCount(0, $listeners);
    }

    public function testIgnoresNonConsoleModelNotContainingResultKeyWhenObtainingResult()
    {
        $console = $this->createMock(AbstractAdapter::class);
        $console
            ->expects($this->any())
            ->method('encodeText')
            ->willReturnArgument(0);

        //Register console service
        $sm = new ServiceManager();
        $sm->setService('console', $console);

        /* @var \PHPUnit_Framework_MockObject_MockObject|ApplicationInterface $mockApplication */
        $mockApplication = $this->createMock(ApplicationInterface::class);
        $mockApplication
            ->expects($this->any())
            ->method('getServiceManager')
            ->willReturn($sm);

        $event    = new MvcEvent();
        $event->setApplication($mockApplication);

        $model    = new Model\ViewModel(['content' => 'Page not found']);

        $this->renderer->render($model)->willReturn('');

        $response = new Response();
        $event->setResult($model);
        $event->setResponse($response);
        $this->strategy->render($event);
        $content = $response->getContent();
        $this->assertStringNotContainsString('Page not found', $content);
    }

    public function testIgnoresNonModel()
    {
        $console = $this->createMock(AbstractAdapter::class);
        $console
            ->expects($this->any())
            ->method('encodeText')
            ->willReturnArgument(0);

        //Register console service
        $sm = new ServiceManager();
        $sm->setService('console', $console);

        /* @var \PHPUnit_Framework_MockObject_MockObject|ApplicationInterface $mockApplication */
        $mockApplication = $this->createMock(ApplicationInterface::class);
        $mockApplication
            ->expects($this->any())
            ->method('getServiceManager')
            ->willReturn($sm);

        $event    = new MvcEvent();
        $event->setApplication($mockApplication);

        $model    = true;

        $this->renderer->render($model)->willReturn('');

        $response = new Response();
        $event->setResult($model);
        $event->setResponse($response);
        $this->assertSame($response, $this->strategy->render($event));
    }
}
