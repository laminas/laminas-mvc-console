# Version 2 to Version 3

laminas-mvc-console ports all console functionality from laminas-mvc and laminas-view v2
releases to a single component. As such, a number of classes were renamed that
may impact end-users.

## laminas-mvc Functionality

### AbstractConsoleController

`Laminas\Mvc\Controller\AbstractConsoleController` becomes
`Laminas\Mvc\Console\Controller\AbstractConsoleController`. Otherwise, all
functionality remains the same.

Update your code to import the `AbstractConsoleController` under its new
namespace.

### Routing

The namespace `Laminas\Mvc\Router\Console` becomes `Laminas\Mvc\Console\Router`. All
classes retain existing functionality. If you were using default routes
(`Simple`) or using the short names to refer to console routes, no changes will
be necessary. Otherwise, update your code to refer to the new namespace.

### ResponseSender

`Laminas\Mvc\ResponseSender\ConsoleResponseSender` becomes
`Laminas\Mvc\Console\ResponseSender\ConsoleResponseSender`. As this is an
implementation detail, it should have no impact on the end-user.

### Listeners

The `Laminas\Mvc\View\Console` namespace becomes `Laminas\Mvc\Console\View`, but all
existing listeners retain their names and functionality. As these were all
managed by the console-specific `ViewManager`, this change should have no impact
on the end-user unless:

- any of these classes were being extended
- any custom factories were being used to provide the services (specifically the
  `ConsoleRouteNotFoundStrategy`, `ConsoleExceptionStrategy`, and
  `ConsoleDefaultRenderingStrategy`).

In such cases, you will need to update your code to reference the new namespace.

## laminas-view Functionality

### ViewModel

laminas-view provided a `Laminas\View\Model\ConsoleModel` class. This is now
`Laminas\Mvc\Console\View\ViewModel`. If you were returning `ConsoleModel`
previously, update your code to return the new version.

### Renderer

laminas-view provided a `Laminas\View\Renderer\ConsoleRenderer` class. This is now
`Laminas\Mvc\Console\View\Renderer`. Additionally, the console-specific
`DefaultRenderingStrategy` now consumes the renderer (it did not in version 2).
