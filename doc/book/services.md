# Default Services

laminas-mvc-console exists to enable legacy console tooling for laminas-mvc
applications. As such, one of its primary functions is providing services to the
MVC layer.

This chapter details the various services registered by laminas-mvc-console by
default, the classes they represent, and any configuration options available.

## Services Provided

The following is a list of service names and what the service returns.

Service Name                                     | Creates instance of
------------------------------------------------ | -------------------
ConsoleAdapter                                   | `Laminas\Console\Adapter\AdapterInterface`
ConsoleExceptionStrategy                         | `Laminas\Mvc\Console\View\ExceptionStrategy`
ConsoleRouteNotFoundStrategy                     | `Laminas\Mvc\Console\View\RouteNotFoundStrategy`
ConsoleRouter                                    | `Laminas\Mvc\Console\Router\SimpleRouteStack`
ConsoleViewManager                               | `Laminas\Mvc\Console\View\ViewManager`
`Laminas\Mvc\Console\View\DefaultRenderingStrategy` | `Laminas\Mvc\Console\View\DefaultRenderingStrategy`
`Laminas\Mvc\Console\View\Renderer`                 | `Laminas\Mvc\Console\View\Renderer`

## Aliases

The following is a list of service aliases.

Alias                           | Aliased to
------------------------------- | ----------
ConsoleDefaultRenderingStrategy | `Laminas\Mvc\Console\View\DefaultRenderingStrategy`
ConsoleRenderer                 | `Laminas\Mvc\Console\View\Renderer`

## Delegator factories

When operating in a console environment, several typical laminas-mvc services need
to operate differently, or require alternate services. To enable that,
laminas-mvc-console provides a number of [delegator
factories](http://docs.laminas.dev/laminas-servicemanager/delegators/). The
following is a list of those provided, the service they override, and a
description of what they do.

Service Name                    | Delegator Factory                                                   | Description
------------------------------- | ------------------------------------------------------------------- | -----------
Application                     | `Laminas\Mvc\Console\Service\ConsoleApplicationDelegatorFactory`       | In a console environment, attaches the `Laminas\Mvc\Console\View\ViewManager` to the application instance before returning it.
Request                         | `Laminas\Mvc\Console\Service\ConsoleRequestDelegatorFactory`           | If a console environment is detected, replaces the request with a `Laminas\Console\Request`.
Response                        | `Laminas\Mvc\Console\Service\ConsoleResponseDelegatorFactory`          | If a console environment is detected, replaces the response with a `Laminas\Console\Response`.
Router                          | `Laminas\Mvc\Console\Router\ConsoleRouterDelegatorFactory`             | If a console environment is detected, replaces the router with the `ConsoleRouter` service.
`Laminas\Mvc\SendResponseListener` | `Laminas\Mvc\Console\Service\ConsoleResponseSenderDelegatorFactory`    | If a console environment is detected, attaches the `Laminas\Mvc\Console\ResponseSender\ConsoleResponseSender` to the `SendResponseListener`.
ViewHelperManager               | `Laminas\Mvc\Console\Service\ConsoleViewHelperManagerDelegatorFactory` | If a console environment is detected, injects override factories for the `url` and `basePath` view helpers into the `HelperPluginManager`.

## Application Configuration Options

Console tooling provides several locations for configuration, primarily at the
service, routing, and view levels.

### Services

All services registered can be configured to use different factories; see the
above tables for details on what service names to override.

### Routing

Routing configuration is detailed in the [routing chapter](routing.md).

### ViewManager

`Laminas\Mvc\Console\View\ViewManager` acts similarly to its [laminas-mvc
equivalent](http://docs.laminas.dev/laminas-mvc/services/#viewmanager), and
will look for one or the other of the following configuration structures:

```php
return [
    'view_manager' => [
        'mvc_strategies' => $stringOrArrayOfMvcListenerServiceNames,
        'strategies' => $stringOrArrayOfViewListenerServiceNames,
    ],
    'console' => [
        'view_manager' => [
            'mvc_strategies' => $stringOrArrayOfMvcListenerServiceNames,
            'strategies' => $stringOrArrayOfViewListenerServiceNames,
        ],
    ],
];
```

Preference is given to those under the `console` top-level key (those under
`view_manager` are ignored if the `console.view_manager` structure exists).

`mvc_strategies` refers to view-related listeners that need to operate on the
`Laminas\Mvc\MvcEvent` context. `strategies` refers to view-related listeners that operate
on the `Laminas\View\ViewEvent` context.
