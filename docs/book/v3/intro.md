# Introduction

The laminas-server family of classes provides functionality for the various server
classes, including
[laminas-json-server](https://docs.laminas.dev/laminas-json-server/),
[laminas-soap](https://docs.laminas.dev/laminas-soap/) and
[laminas-xmlrpc](https://docs.laminas.dev/laminas-xmlrpc/).
`Laminas\Server\Server` provides an interface that mimics PHP’s `SoapServer` class;
all RPC-style server classes should implement this interface in order to provide a
standard server API.

The `Laminas\Server\Reflection` tree provides a standard mechanism for performing
function and class introspection for use as callbacks with the server classes,
and provides data suitable for use with `Laminas\Server\Server`'s `getFunctions()`
and `loadFunctions()` methods.
