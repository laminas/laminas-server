# Migration to version 3.0

However, starting in this version, we offer a few changes affecting the
following that you should be aware of, and potentially update your application
to adopt:

- [PHP 7.2 support](#php-7.2-support)
- [Strict typing](#strict-typing)

## PHP 7.2 support

Starting in 3.0 this component supports only PHP 7.2+.

## Strict Typing

Argument type declarations have been added. Passing a wrong argument type which
previously might have worked, will now raise a TypeError.

## Signature changes

The following signatures changed that could affect class extension and/or
consumers.

### Server

Namespacing is not required by all `Server` implementations and thus optional.
Method argument type declarations have been updated to reflect this state.
`addFunction` and `setClass` methods now accept namespace variables of nullable
strings, where previously they suggested a `string`-only usage.
