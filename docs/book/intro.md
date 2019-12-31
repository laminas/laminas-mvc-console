# Introduction

Laminas introduced the ability to write console applications via its MVC
layer. This ability integrates a number of components, including:

- laminas-console
- laminas-eventmanager
- laminas-modulemanager
- laminas-servicemanager
- laminas-stdlib
- laminas-text
- laminas-view

When correctly configured, the functionality allows you to execute console
applications via the same `public/index.php` script as used for HTTP requests:

```bash
$ php public/index.php <arguments...>
```
