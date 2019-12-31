<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Mvc\Console\Service;

use Laminas\Mvc\Console\Service\ConsoleViewManagerFactory;
use Laminas\Mvc\Console\View\ViewManager;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use PHPUnit\Framework\TestCase;

class ConsoleViewManagerFactoryTest extends TestCase
{
    use FactoryEnvironmentTrait;

    public function testRaisesExceptionWhenNotInConsoleEnvironment()
    {
        $this->setConsoleEnvironment(false);

        $factory = new ConsoleViewManagerFactory();
        $this->expectException(ServiceNotCreatedException::class);
        $this->expectExceptionMessage('requires a Console environment');
        $factory($this->createContainer(), 'ConsoleViewManager');
    }

    public function testReturnsViewManagerWhenInConsoleEnvironment()
    {
        $this->setConsoleEnvironment(true);

        $factory = new ConsoleViewManagerFactory();
        $result = $factory($this->createContainer(), 'ConsoleViewManager');
        $this->assertInstanceOf(ViewManager::class, $result);
    }
}
