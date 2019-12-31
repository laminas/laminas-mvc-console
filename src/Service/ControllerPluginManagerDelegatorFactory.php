<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-console/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Mvc\Console\Service;

use Interop\Container\ContainerInterface;
use Laminas\Mvc\Console\Controller\Plugin\CreateConsoleNotFoundModel;
use Laminas\ServiceManager\DelegatorFactoryInterface;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ControllerPluginManagerDelegatorFactory implements DelegatorFactoryInterface
{
    /**
     * Add console-specific plugins to the controller PluginManager.
     *
     * @param ContainerInterface $container
     * @param string $name
     * @param callable $callback
     * @param null|array $options
     * @return \Laminas\Mvc\Controller\PluginManager
     */
    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        $plugins = $callback();

        $plugins->setAlias('CreateConsoleNotFoundModel', CreateConsoleNotFoundModel::class);
        $plugins->setAlias('createConsoleNotFoundModel', CreateConsoleNotFoundModel::class);
        $plugins->setAlias('createconsolenotfoundmodel', CreateConsoleNotFoundModel::class);
        $plugins->setFactory(CreateConsoleNotFoundModel::class, InvokableFactory::class);

        return $plugins;
    }

    /**
     * Add console-specific plugins to the controller PluginManager. (v2)
     *
     * @param ServiceLocatorInterface $container
     * @param string $name
     * @param string $requestedName
     * @param callable $callback
     * @return \Laminas\Mvc\Controller\PluginManager
     */
    public function createDelegatorWithName(ServiceLocatorInterface $container, $name, $requestedName, $callback)
    {
        return $this($container, $requestedName, $callback);
    }
}
