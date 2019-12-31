# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

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
