<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Mvc\Console;

use Interop\Container\ContainerInterface;
use Laminas\EventManager\Test\EventListenerIntrospectionTrait;
use Laminas\Mvc\Console\ResponseSender\ConsoleResponseSender;
use Laminas\Mvc\Console\Service\ConsoleResponseSenderDelegatorFactory;
use Laminas\Mvc\ResponseSender\SendResponseEvent;
use Laminas\Mvc\SendResponseListener;
use Laminas\Mvc\Service\SendResponseListenerFactory;
use Laminas\Mvc\Service\ServiceManagerConfig;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit_Framework_TestCase as TestCase;
use ReflectionProperty;

class ServiceManagerTest extends TestCase
{
    use EventListenerIntrospectionTrait;

    /**
     * @group 10
     * @group 11
     * @group 12
     */
    public function testEventManagerOverridden()
    {
        $minimalConfig = [
            'aliases' => [
                'SendResponseListener' => SendResponseListener::class,
            ],
            'factories' => [
                SendResponseListener::class => SendResponseListenerFactory::class,
            ],
            'delegators' => [
                SendResponseListener::class => [
                    ConsoleResponseSenderDelegatorFactory::class,
                ],
            ]
        ];

        $smConfig = new ServiceManagerConfig($minimalConfig);

        $serviceManager = new ServiceManager();
        $smConfig->configureServiceManager($serviceManager);

        $sendResponseListener = $serviceManager->get('SendResponseListener');
        $eventManager = $sendResponseListener->getEventManager();
        $this->assertEvents($eventManager);
    }

    protected function assertEvents($eventManager)
    {
        $count = 0;
        $found = false;

        foreach ($this->getListenersForEvent(SendResponseEvent::EVENT_SEND_RESPONSE, $eventManager, true) as $priority => $listener) {
            $count++;
            if ($priority === -2000
                && $listener instanceof ConsoleResponseSender
            ) {
                $found = true;
            }
        }

        $this->assertEquals(4, $count);
        $this->assertTrue($found, 'ConsoleResponseSender was not found in listeners');
    }
}
