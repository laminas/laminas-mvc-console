<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Mvc\Console\Router;

use Laminas\Mvc\Console\Router\Catchall;
use PHPUnit_Framework_TestCase as TestCase;

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
}
