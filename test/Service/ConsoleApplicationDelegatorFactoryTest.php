<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Mvc\Console\Service;

use Interop\Container\ContainerInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\Mvc\ApplicationInterface;
use Laminas\Mvc\Console\Service\ConsoleApplicationDelegatorFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ConsoleApplicationDelegatorFactoryTest extends TestCase
{
    use FactoryEnvironmentTrait;
    use ProphecyTrait;

    public function setUp() : void
    {
        $this->factory = new ConsoleApplicationDelegatorFactory();
    }

    public function testFactoryReturnsApplicationUntouchedWhenNotInConsoleEnvironment()
    {
        $this->setConsoleEnvironment(false);

        $application = $this->prophesize(ApplicationInterface::class);
        $application->getEventManager()->shouldNotBeCalled();

        $callback = function () use ($application) {
            return $application->reveal();
        };

        $container = $this->prophesize(ContainerInterface::class);
        $container->get('ConsoleViewManager')->shouldNotBeCalled();

        $this->assertSame(
            $application->reveal(),
            $this->factory->__invoke($container->reveal(), 'Application', $callback)
        );
    }

    public function testFactoryPassesApplicationEventManagerToConsoleViewManager()
    {
        $this->setConsoleEnvironment(true);

        $events = $this->prophesize(EventManagerInterface::class)->reveal();

        $application = $this->prophesize(ApplicationInterface::class);
        $application->getEventManager()->willReturn($events);

        $callback = function () use ($application) {
            return $application->reveal();
        };

        $aggregate = $this->prophesize(ListenerAggregateInterface::class);
        $aggregate->attach($events)->shouldBeCalled();

        $container = $this->prophesize(ContainerInterface::class);
        $container->get('ConsoleViewManager')->willReturn($aggregate->reveal());

        $this->assertSame(
            $application->reveal(),
            $this->factory->__invoke($container->reveal(), 'Application', $callback)
        );
    }
}
