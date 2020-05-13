# Migration to version 3.0

Starting in version 3.0, we offer a few changes affecting the
following that you should be aware of, and potentially update your application
to adopt:

## PHP 7.2 support

Starting in 3.0 this component supports only PHP 7.2+.

## Strict Typing

Argument type declarations have been added. Passing a wrong argument type which
previously might have worked will now raise a `TypeError`.

## Signature changes

The following signatures changed that could affect class extension and/or
consumers.

### Interface suffix

As of
[laminas-coding-standard](https://github.com/laminas/laminas-coding-standard)
version `2.0`, interface names must have an `Interface` suffix. The `Server`
interface has been renamed to `ServerInterface` and the `Client` interface to
`ClientInterface`.

### Reflection

Previously the non-represented state of `$argv` in the `reflectClass()` and
`reflectFunction()` methods was false. This changed to `null`.

To represent a non-present state of `$namespace` in the `reflectClass()` and
`reflectFunction()`, the default value changed from an empty string to `null`.

### Server

Namespacing is not required by all `Server` implementations and thus optional.
Method argument type declarations have been updated to reflect this state.
`addFunction` and `setClass` methods now accept namespace variables of nullable
strings, where previously they suggested a `string`-only usage.
