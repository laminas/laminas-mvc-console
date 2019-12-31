# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.1.0 - 2016-03-23

### Added

- [zendframework/zend-mvc-console#3](https://github.com/zendframework/zend-mvc-console/pull/3) adds the
  `CreateConsoleNotFoundModel` controller plugin from laminas-mvc. This also
  required adding `Laminas\Mvc\Console\Service\ControllerPluginManagerDelegatorFactory`
  to ensure it is present in the controller plugin manager when in a console
  context.
- [zendframework/zend-mvc-console#3](https://github.com/zendframework/zend-mvc-console/pull/3) adds
  `Laminas\Mvc\Console\Service\ControllerManagerDelegatorFactory`, to add an
  initializer for injecting a console adapter into `AbstractConsoleController`
  instances.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-mvc-console#3](https://github.com/zendframework/zend-mvc-console/pull/3) updates the
  `AbstractConsoleController` to override the `notFoundAction()` and always
  return the return value of the `CreateConsoleNotFoundModel` plugin.
- [zendframework/zend-mvc-console#3](https://github.com/zendframework/zend-mvc-console/pull/3) updates the
  `AbstractConsoleController` to mark it as abstract, as was always intended,
  but evidently never implemented, in laminas-mvc.

## 1.0.0 - 2016-03-23

First stable release.

This component replaces the various console utilities in laminas-mvc, laminas-router,
and laminas-view, and provides integration between each of those components and
laminas-console.

While this is a stable release, please wait to use it until a v3 release of
laminas-mvc, which will remove those features, to ensure everything works together
as expected.

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.
