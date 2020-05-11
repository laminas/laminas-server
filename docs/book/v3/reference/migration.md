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
