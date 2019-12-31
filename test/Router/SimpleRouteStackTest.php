<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Mvc\Console\Router;

use Laminas\Mvc\Console\Router\Catchall;
use Laminas\Mvc\Console\Router\Simple;
use Laminas\Mvc\Console\Router\SimpleRouteStack;
use PHPUnit_Framework_TestCase as TestCase;

class SimpleRouteStackTest extends TestCase
{
    public function routeTypeProvider()
    {
        $catchallOpts = ['defaults' => []];
        $simpleOpts   = ['route' => 'test'];

        return [
            'catchall' => ['catchall', $catchallOpts, Catchall::class],
            'catchAll' => ['catchAll', $catchallOpts, Catchall::class],
            'Catchall' => ['Catchall', $catchallOpts, Catchall::class],
            'CatchAll' => ['CatchAll', $catchallOpts, Catchall::class],
            'simple'   => ['simple', $simpleOpts, Simple::class],
            'Simple'   => ['Simple', $simpleOpts, Simple::class],

            Catchall::class => [Catchall::class, $catchallOpts, Catchall::class],
            Simple::class   => [Simple::class, $simpleOpts, Simple::class],
        ];
    }

    /**
     * @dataProvider routeTypeProvider
     */
    public function testExpectedAliasesAndFactoriesResolve($serviceName, array $options, $expected)
    {
        $router = new SimpleRouteStack();
        $routes = $router->getRoutePluginManager();
        $this->assertInstanceOf($expected, $routes->get($serviceName, $options));
    }
}
