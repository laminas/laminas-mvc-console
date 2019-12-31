<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Mvc\Console\Router;

use Laminas\Console\Request;
use Laminas\Mvc\Console\Router\Catchall;
use Laminas\Mvc\Console\Router\RouteMatch;
use Laminas\Stdlib\RequestInterface;
use PHPUnit\Framework\TestCase;

class CatchallTest extends TestCase
{
    public function provideFactoryOptions()
    {
        return [
            [[]],
            [['defaults' => []]]
        ];
    }

    /**
     * @dataProvider provideFactoryOptions
     */
    public function testFactoryReturnsInstanceForAnyOptionsArray($options)
    {
        $this->assertInstanceOf(Catchall::class, Catchall::factory($options));
    }

    public function testMatchReturnsEarlyForNonConsoleRequests()
    {
        $request = $this->prophesize(RequestInterface::class)->reveal();
        $route = new Catchall();
        $this->assertNull($route->match($request));
    }

    public function testMatchReturnsConstructorParamsForConsoleRequests()
    {
        $params = ['foo' => 'bar'];
        $request = $this->prophesize(Request::class)->reveal();
        $route = new Catchall($params);
        $result = $route->match($request);
        $this->assertInstanceOf(RouteMatch::class, $result);
        $this->assertEquals($params, $result->getParams());
    }

    public function testAssembleClearsAssembledParams()
    {
        $route = new Catchall();
        $route->assemble();
        $this->assertEquals([], $route->getAssembledParams());
    }
}
