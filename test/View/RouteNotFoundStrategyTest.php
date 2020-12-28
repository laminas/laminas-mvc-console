<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-console/blob/master/LICENSE.md New BSD License
 */
namespace LaminasTest\Mvc\Console\View;

use Interop\Container\ContainerInterface;
use Laminas\Console\Adapter\AdapterInterface;
use Laminas\Console\ColorInterface;
use Laminas\Console\Request;
use Laminas\Console\Response;
use Laminas\EventManager\EventManager;
use Laminas\EventManager\Test\EventListenerIntrospectionTrait;
use Laminas\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Laminas\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Laminas\ModuleManager\ModuleManager;
use Laminas\Mvc\Application;
use Laminas\Mvc\Console\Exception\RuntimeException;
use Laminas\Mvc\Console\View\RouteNotFoundStrategy;
use Laminas\Mvc\Console\View\ViewModel;
use Laminas\Mvc\MvcEvent;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use ReflectionClass;
use ReflectionMethod;

class RouteNotFoundStrategyTest extends TestCase
{
    use EventListenerIntrospectionTrait;
    use ProphecyTrait;

    /**
     * @var RouteNotFoundStrategy
     */
    protected $strategy;

    public function setUp() : void
    {
        $this->strategy = new RouteNotFoundStrategy();
    }

    public function mockLoadedModules()
    {
        $first = $this->prophesize(ConsoleBannerProviderInterface::class);
        $first->willImplement(ConsoleUsageProviderInterface::class);
        $first->getConsoleBanner(Argument::type(AdapterInterface::class))->willReturn('');
        $first->getConsoleUsage(Argument::type(AdapterInterface::class))->willReturn(['FIRST USAGE']);

        $second = $this->prophesize(ConsoleBannerProviderInterface::class);
        $second->getConsoleBanner(Argument::type(AdapterInterface::class))->willReturn('SECOND BANNER');

        $third = $this->prophesize(TestAsset\ConsoleModule::class);
        $third->getConsoleBanner(Argument::type(AdapterInterface::class))->willReturn('THIRD BANNER');
        $third->getConsoleUsage(Argument::type(AdapterInterface::class))->willReturn('THIRD USAGE');

        $fourth = $this->prophesize(TestAsset\ConsoleModule::class);
        $fourth->getConsoleBanner(Argument::type(AdapterInterface::class))->willReturn('');
        $fourth->getConsoleUsage(Argument::type(AdapterInterface::class))->willReturn([
            '--foo' => 'BAR',
            ['--bar', 'Just another flag'],
        ]);

        return [
            'First'  => $first->reveal(),
            'Second' => $second->reveal(),
            'Third'  => $third->reveal(),
            'Fourth' => $fourth->reveal(),
        ];
    }

    public function testAttachesToEventManagerAtExpectedPriority()
    {
        $events = new EventManager();
        $this->strategy->attach($events);

        $this->assertListenerAtPriority(
            [$this->strategy, 'handleRouteNotFoundError'],
            1,
            MvcEvent::EVENT_DISPATCH_ERROR,
            $events,
            'Route not found listener not attached at expected priority'
        );
    }

    public function testRenderTableConcatenateAndInvalidInputDoesNotThrowException()
    {
        $reflection = new ReflectionClass(RouteNotFoundStrategy::class);
        $method = $reflection->getMethod('renderTable');
        $method->setAccessible(true);
        $result = $method->invokeArgs($this->strategy, [[[]], 1, 0]);
        $this->assertSame('', $result);
    }

    public function testListenerDoesNothingIfEventHasNoError()
    {
        $event = $this->prophesize(MvcEvent::class);
        $event->getError()->willReturn(null);
        $event->getResponse()->shouldNotBeCalled();
        $event->getRequest()->shouldNotBeCalled();

        $this->assertNull($this->strategy->handleRouteNotFoundError($event->reveal()));
    }

    public function testReturnsEarlyForErrorsItDoesNotHandle()
    {
        $event = $this->prophesize(MvcEvent::class);
        $event->getError()->willReturn('unknown-error-type');
        $event->getResponse()->willReturn(null);
        $event->getRequest()->willReturn(null);
        $event->getResult()->shouldNotBeCalled();

        $this->assertNull($this->strategy->handleRouteNotFoundError($event->reveal()));
    }

    public function validErrorTypes()
    {
        // @codingStandardsIgnoreStart
        return [
            'controller-not-found' => [Application::ERROR_CONTROLLER_NOT_FOUND, 'Could not match to a controller'],
            'controller-invalid'   => [Application::ERROR_CONTROLLER_INVALID, 'Invalid controller specified'],
            'router-no-match'      => [Application::ERROR_ROUTER_NO_MATCH, 'Invalid arguments or no arguments provided'],
        ];
        // @codingStandardsIgnoreEnd
    }

    /**
     * @dataProvider validErrorTypes
     */
    public function testSetsResponseErrorMetadataAndReasonToError($type)
    {
        $response = $this->prophesize(Response::class);
        $response->setMetadata('error', $type)->shouldBeCalled();

        $event = $this->prophesize(MvcEvent::class);
        $event->getError()->willReturn($type);
        $event->getResponse()->willReturn($response->reveal());
        $event->getRequest()->willReturn(null);
        $event->getResult()->willReturn($response->reveal());

        $this->assertNull($this->strategy->handleRouteNotFoundError($event->reveal()));

        $reason = new \ReflectionProperty($this->strategy, 'reason');
        $reason->setAccessible(true);
        $this->assertSame($type, $reason->getValue($this->strategy));
    }

    /**
     * @dataProvider validErrorTypes
     */
    public function testLackOfConsoleAdapterRaisesException($type)
    {
        $response = $this->prophesize(Response::class);
        $response->setMetadata('error', $type)->shouldBeCalled();

        $event = $this->prophesize(MvcEvent::class);
        $event->getError()->willReturn($type);
        $event->getResponse()->willReturn($response->reveal());
        $event->getRequest()->willReturn(null);
        $event->getResult()->willReturn(null);

        $container = $this->prophesize(ContainerInterface::class);
        $container->get('ModuleManager')->willReturn(null);
        $container->get('console')->willReturn(null);

        $app = $this->prophesize(Application::class);
        $app->getServiceManager()->willReturn($container->reveal());
        $event->getApplication()->willReturn($app->reveal());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Console adapter');
        $this->strategy->handleRouteNotFoundError($event->reveal());
    }

    /**
     * @dataProvider validErrorTypes
     */
    public function testSetsResultToPopulatedViewModelWhenSuccessful($type, $reasonMessage)
    {
        $request = $this->prophesize(Request::class);
        $request->getScriptName()->willReturn('laminas-mvc-console-test');

        $response = $this->prophesize(Response::class);
        $response->setMetadata('error', $type)->shouldBeCalled();

        $event = $this->prophesize(MvcEvent::class);
        $event->getError()->willReturn($type);
        $event->getResponse()->willReturn($response->reveal());
        $event->getRequest()->willReturn($request->reveal());
        $event->getResult()->willReturn(null);
        $event->getParam('exception', false)->willReturn(false);

        $moduleManager = $this->prophesize(ModuleManager::class);
        $moduleManager->getLoadedModules(false)->willReturn($this->mockLoadedModules())->shouldBeCalledTimes(2);

        $console = $this->prophesize(AdapterInterface::class);
        $console->colorize('SECOND BANNER', ColorInterface::BLUE)->willReturn('SECOND BANNER');
        $console->colorize('THIRD BANNER', ColorInterface::BLUE)->willReturn('THIRD BANNER');
        $console->getWidth()->willReturn(80);
        $console->colorize(Argument::containingString('First'), ColorInterface::RED)->willReturn('First');
        $console->colorize(Argument::containingString('Third'), ColorInterface::RED)->willReturn('Third');
        $console->colorize(Argument::containingString('Fourth'), ColorInterface::RED)->willReturn('Fourth');
        $console
            ->colorize('laminas-mvc-console-test --foo', ColorInterface::GREEN)
            ->willReturn('laminas-mvc-console-test --foo');

        $container = $this->prophesize(ContainerInterface::class);
        $container->get('ModuleManager')->willReturn($moduleManager->reveal());
        $container->get('console')->willReturn($console->reveal());

        $app = $this->prophesize(Application::class);
        $app->getServiceManager()->willReturn($container->reveal());
        $event->getApplication()->willReturn($app->reveal());
        $event->setResult(Argument::that(function ($argument) use ($reasonMessage) {
            if (! $argument instanceof ViewModel) {
                return false;
            }

            $result = $argument->getResult();
            if (! strstr($result, $reasonMessage)) {
                return false;
            }

            if (! strstr($result, 'BAR')) {
                return false;
            }

            if (! strstr($result, '--bar')) {
                return false;
            }

            if (! strstr($result, 'Just another flag')) {
                return false;
            }

            return true;
        }))->shouldBeCalled();

        $this->assertNull($this->strategy->handleRouteNotFoundError($event->reveal()));
    }

    public function throwables()
    {
        $throwables = ['exception' => [\Exception::class]];

        if (version_compare(PHP_VERSION, '7.0.0', '>=')) {
            $throwables['error'] = [\Error::class];
        }

        return $throwables;
    }

    /**
     * @dataProvider throwables
     */
    public function testWillTraceAnyThrowableWhenAllowedToReportNotFoundReason($throwable)
    {
        $event = new MvcEvent();
        $event->setParam('exception', new $throwable('EXCEPTION THROWN'));

        $r = new ReflectionMethod($this->strategy, 'reportNotFoundReason');
        $r->setAccessible(true);

        $report = $r->invoke($this->strategy, $event);
        $this->assertStringContainsString('Reason for failure: Unknown', $report);
        $this->assertStringContainsString('Exception: EXCEPTION THROWN', $report);
    }
}
