<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Mvc\Console\Service;

use Interop\Container\ContainerInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\EventsCapableInterface;
use Laminas\Mvc\Console\ResponseSender\ConsoleResponseSender;
use Laminas\Mvc\Console\Service\ConsoleResponseSenderDelegatorFactory;
use Laminas\Mvc\ResponseSender\SendResponseEvent;
use PHPUnit_Framework_TestCase as TestCase;
use Prophecy\Argument;

class ConsoleResponseSenderDelegatorFactoryTest extends TestCase
{
    public function testAttachesConsoleResponseSenderToSendResponseListener()
    {
        $events = $this->prophesize(EventManagerInterface::class);
        $events->attach(
            SendResponseEvent::EVENT_SEND_RESPONSE,
            Argument::type(ConsoleResponseSender::class),
            -2000
        )->shouldBeCalled();

        $listener = $this->prophesize(EventsCapableInterface::class);
        $listener->getEventManager()->willReturn($events->reveal());

        $container = $this->prophesize(ContainerInterface::class)->reveal();

        $callback = function () use ($listener) {
            return $listener->reveal();
        };

        $factory = new ConsoleResponseSenderDelegatorFactory();
        $this->assertSame(
            $listener->reveal(),
            $factory($container, 'SendResponseListener', $callback)
        );
    }
}
