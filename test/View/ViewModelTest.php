<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Mvc\Console\View;

use Laminas\Mvc\Console\View\ViewModel;
use PHPUnit_Framework_TestCase as TestCase;

class ViewModelTest extends TestCase
{
    public function setUp()
    {
        $this->model = new ViewModel();
    }

    public function testCaptureToIsNullByDefault()
    {
        $this->assertNull($this->model->captureTo());
    }

    public function testTerminalByDefault()
    {
        $this->assertTrue($this->model->terminate());
    }

    public function testErrorLevelIsNotSetByDefault()
    {
        $this->assertNull($this->model->getErrorLevel());
    }

    public function testCanSetErrorLevel()
    {
        $this->model->setErrorLevel(E_USER_DEPRECATED);
        $this->assertSame(E_USER_DEPRECATED, $this->model->getErrorLevel());
    }

    public function testResultIsNullByDefault()
    {
        $this->assertNull($this->model->getResult());
    }

    public function testCanSetResult()
    {
        $this->model->setResult('FOO');
        $this->assertEquals('FOO', $this->model->getResult());
    }
}
