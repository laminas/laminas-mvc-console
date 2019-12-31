<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-console/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Mvc\Console;

use Laminas\Mvc\SendResponseListener;
use Laminas\ServiceManager\Factory\InvokableFactory;

class ConfigProvider
{
    /**
     * Provide configuration for this component.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
        ];
    }

    /**
     * Provide dependency configuration for this component.
     *
     * @return array
     */
    public function getDependencyConfig()
    {
        return [
            'aliases' => [
                'ConsoleDefaultRenderingStrategy' => View\DefaultRenderingStrategy::class,
                'ConsoleRenderer'                 => View\Renderer::class,

                // Legacy Zend Framework aliases
            ],
            'delegator_factories' => [
                'Application'               => [ Service\ConsoleApplicationDelegatorFactory::class ],
                'ControllerManager'         => [ Service\ControllerManagerDelegatorFactory::class ],
                'ControllerPluginManager'   => [ Service\ControllerPluginManagerDelegatorFactory::class ],
                'Request'                   => [ Service\ConsoleRequestDelegatorFactory::class ],
                'Response'                  => [ Service\ConsoleResponseDelegatorFactory::class ],
                'Router'                    => [ Router\ConsoleRouterDelegatorFactory::class ],
                SendResponseListener::class => [ Service\ConsoleResponseSenderDelegatorFactory::class ],
                'ViewHelperManager'         => [ Service\ConsoleViewHelperManagerDelegatorFactory::class ],
            ],
            'factories' => [
                'ConsoleAdapter'               => Service\ConsoleAdapterFactory::class,
                'ConsoleExceptionStrategy'     => Service\ConsoleExceptionStrategyFactory::class,
                'ConsoleRouteNotFoundStrategy' => Service\ConsoleRouteNotFoundStrategyFactory::class,
                'ConsoleRouter'                => Router\ConsoleRouterFactory::class,
                'ConsoleViewManager'           => Service\ConsoleViewManagerFactory::class,
                View\DefaultRenderingStrategy::class => Service\DefaultRenderingStrategyFactory::class,
                View\Renderer::class           => InvokableFactory::class,
            ],
        ];
    }
}
