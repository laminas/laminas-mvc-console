<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Mvc\Console\Controller\Plugin;

use Laminas\Mvc\Console\Controller\Plugin\CreateConsoleNotFoundModel;
use Laminas\Mvc\Console\View\ViewModel as ConsoleModel;
use PHPUnit_framework_TestCase as TestCase;

class CreateConsoleNotFoundModelTest extends TestCase
{
    public function testCanReturnModelWithErrorMessageAndErrorLevel()
    {
        $plugin = new CreateConsoleNotFoundModel();
        $model  = $plugin();

        $this->assertInstanceOf(ConsoleModel::class, $model);
        $this->assertSame('Page not found', $model->getResult());
        $this->assertSame(1, $model->getErrorLevel());
    }
}
