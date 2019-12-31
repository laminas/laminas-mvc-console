<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Mvc\Console\Router;

use Laminas\Mvc\Console\Router\RouteMatch;
use PHPUnit_Framework_TestCase as TestCase;

class RouteMatchTest extends TestCase
{
    public function testConstructorCanAcceptArgumentLength()
    {
        $routeMatch = new RouteMatch(['foo' => true], 5);
        $this->assertEquals(5, $routeMatch->getLength());
    }

    public function testSettingMatchedRouteNameForFirstTimeSetsItVerbatim()
    {
        $routeMatch = new RouteMatch(['foo' => true], 5);
        $routeMatch->setMatchedRouteName('foo');
        $this->assertEquals('foo', $routeMatch->getMatchedRouteName());
        return $routeMatch;
    }

    /**
     * @depends testSettingMatchedRouteNameForFirstTimeSetsItVerbatim
     */
    public function testSettingMatchedRouteNameSubsequentTimePrependsNewName($routeMatch)
    {
        $routeMatch->setMatchedRouteName('bar');
        $this->assertEquals('bar/foo', $routeMatch->getMatchedRouteName());
    }

    public function testAllowsMergingWithAnotherInstance()
    {
        $first = new RouteMatch(['foo' => true], 5);
        $second = new RouteMatch(['bar' => 'baz'], 9);

        $merged = $first->merge($second);
        $this->assertSame($first, $merged);
        $this->assertEquals(14, $merged->getLength());
        $this->assertEquals($second->getMatchedRouteName(), $merged->getMatchedRouteName());
        $this->assertEquals([
            'foo' => true,
            'bar' => 'baz',
        ], $merged->getParams());
    }
}
