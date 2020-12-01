# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 2.9.1 - 2020-12-01

### Fixed

- [#19](https://github.com/laminas/laminas-server/pull/19) fixes a scenario whereby calling `Reflection::reflectionFunction()` or `new ReflectMethod()` with `null` or otherwise invalid `$argv` arguments could lead to fatal errors. These methods now either validate or cast on all invalid values.

- [#18](https://github.com/laminas/laminas-server/pull/18) fixes detection of array function and method parameters on PHP 8.


-----

### Release Notes for [2.9.1](https://github.com/laminas/laminas-server/milestone/3)

2.9.x bugfix release (patch)

### 2.9.1

- Total issues resolved: **0**
- Total pull requests resolved: **2**
- Total contributors: **1**

#### Bug

 - [19: Fixed error when receiving null $argv in Reflection::reflectClass/reflectFunction](https://github.com/laminas/laminas-server/pull/19) thanks to @mtorromeo
 - [18: Fix for deprecated use of ReflectionParameter::isArray() on PHP 8](https://github.com/laminas/laminas-server/pull/18) thanks to @mtorromeo

## 2.9.0 - 2020-11-23

### Added

- [#15](https://github.com/laminas/laminas-server/pull/15) adds support for PHP 8.

- [#8](https://github.com/laminas/laminas-server/pull/8) and [#9](https://github.com/laminas/laminas-server/pull/9) add `Laminas\Server\ClientInterface`, which extends `Laminas\Server\Client`, and which will replace that interface in version 3.0.

- [#8](https://github.com/laminas/laminas-server/pull/8) and [#9](https://github.com/laminas/laminas-server/pull/9) add `Laminas\Server\ServerInterface`, which extends `Laminas\Server\Server`, and which will replace that interface in version 3.0.

### Changed

- [#8](https://github.com/laminas/laminas-server/pull/8) adds a new method to `Laminas\Server\AbstractServer`, `buildSignature()`. The method brings in the content of `_buildSignature()`, which has been marked deprecated, and which now proxies to `buildSignature()`. If you are calling `_buildSignature()` in your own code, please update to use `buildSignature()` instead.

- [#8](https://github.com/laminas/laminas-server/pull/8) adds a new method to `Laminas\Server\AbstractServer`, `buildCallback()`. The method brings in the content of `_buildCallback()`, which has been marked deprecated, and which now proxies to `buildCallback()`. If you are calling `_buildCallback()` in your own code, please update to use `buildCallback()` instead.

### Deprecated

- [#8](https://github.com/laminas/laminas-server/pull/8) deprecates `Laminas\Server\Client`. The interface will be removed in version 3.0; please implement `Laminas\Server\ClientInterface` instead.

- [#8](https://github.com/laminas/laminas-server/pull/8) deprecates `Laminas\Server\Server`. The interface will be removed in version 3.0; please implement `Laminas\Server\ServerInterface` instead.

### Removed

- [#15](https://github.com/laminas/laminas-server/pull/15) removes support for PHP versions prior to 7.3.


-----

### Release Notes for [2.9.0](https://github.com/laminas/laminas-server/milestone/2)



### 2.9.0

- Total issues resolved: **1**
- Total pull requests resolved: **4**
- Total contributors: **3**

#### Enhancement

 - [16: Psalm integration](https://github.com/laminas/laminas-server/pull/16) thanks to @weierophinney
 - [15: Add PHP 8.0 support](https://github.com/laminas/laminas-server/pull/15) thanks to @bfoosness
 - [9: Change class inheritance path by having new interfaces extend current interfaces](https://github.com/laminas/laminas-server/pull/9) thanks to @arueckauer
 - [8: 3.0 Preparation](https://github.com/laminas/laminas-server/pull/8) thanks to @arueckauer

#### Documentation

 - [10: Add purpose to AbstractServer::fixType() method comment](https://github.com/laminas/laminas-server/issues/10) thanks to @arueckauer

## 2.8.1 - 2019-10-16

### Added

- [zendframework/zend-server#27](https://github.com/zendframework/zend-server/pull/27) adds support for PHP 7.3.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-server#30](https://github.com/zendframework/zend-server/pull/30) provides fixes to ensure the various Reflection classes can be safely de/serialized under PHP 7.4.

## 2.8.0 - 2018-04-30

### Added

- [zendframework/zend-server#26](https://github.com/zendframework/zend-server/pull/26) adds support for PHP 7.1 and 7.2.

- [zendframework/zend-server#19](https://github.com/zendframework/zend-server/pull/19) adds the ability to register any PHP callable with `Laminas\Server\Method\Callback`.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- [zendframework/zend-server#26](https://github.com/zendframework/zend-server/pull/26) removes support for HHVM.

### Fixed

- [zendframework/zend-server#20](https://github.com/zendframework/zend-server/pull/20) fixes how `Cache::save()` works when `Server::getFunctions()` returns an
  associative array instead of a `Definition`, ensuring it will also skip
  any blacklisted methods when used in this way.

## 2.7.0 - 2016-06-20

### Added

- [zendframework/zend-server#13](https://github.com/zendframework/zend-server/pull/13) adds and publishes
  the documentation to https://docs.laminas.dev/laminas-server
- [zendframework/zend-server#14](https://github.com/zendframework/zend-server/pull/14) adds support for
  laminas-code v3 (while retaining support for laminas-code v2).

### Deprecated

- [zendframework/zend-server#14](https://github.com/zendframework/zend-server/pull/14) deprecates all
  underscore-prefixed methods of `AbstractServer`; they will be renamed in
  version 3 to remove the prefix (though, in the case of `_dispatch()`, it will
  be renamed entirely, likely to `performDispatch()`).

### Removed

- [zendframework/zend-server#14](https://github.com/zendframework/zend-server/pull/14) removes support
  for PHP 5.5; the new minimum supported version of PHP is 5.6.

### Fixed

- Nothing.

## 2.6.1 - 2016-02-04

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-server#11](https://github.com/zendframework/zend-server/pull/11) updates the
  dependencies to use laminas-stdlib `^2.5 || ^3.0`.

## 2.6.0 - 2015-12-17

### Added

- [zendframework/zend-server#3](https://github.com/zendframework/zend-server/pull/3) adds support for
  resolving `{@inheritdoc}` annotations to the original parent during
  reflection.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-server#2](https://github.com/zendframework/zend-server/pull/2) fixes misleading
  exception in reflectFunction that referenced reflectClass.
