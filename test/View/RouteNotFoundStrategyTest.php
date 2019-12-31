<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-console/blob/master/LICENSE.md New BSD License
 */
namespace LaminasTest\Mvc\Console\View;

use Laminas\Mvc\Console\View\RouteNotFoundStrategy;
use PHPUnit_Framework_TestCase as TestCase;
use ReflectionClass;

class RouteNotFoundStrategyTest extends TestCase
{
    /**
     * @var RouteNotFoundStrategy
     */
    protected $strategy;

    public function setUp()
    {
        $this->strategy = new RouteNotFoundStrategy();
    }

    public function testRenderTableConcatenateAndInvalidInputDoesNotThrowException()
    {
        $reflection = new ReflectionClass(RouteNotFoundStrategy::class);
        $method = $reflection->getMethod('renderTable');
        $method->setAccessible(true);
        $result = $method->invokeArgs($this->strategy, [[[]], 1, 0]);
        $this->assertSame('', $result);
    }
}
