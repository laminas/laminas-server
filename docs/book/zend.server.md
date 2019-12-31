# Introduction to Laminas\\Server

The `Laminas\Server` family of classes provides functionality for the various
server classes, including `Laminas\XmlRpc\Server` and `Laminas\Json\Server`.
`Laminas\Server\Server` provides an interface that mimics PHP 5’s `SoapServer`
class; all server classes should implement this interface in order to provide a
standard server API.

The `Laminas\Server\Reflection` tree provides a standard mechanism for performing
function and class introspection for use as callbacks with the server classes,
and provides data suitable for use with `Laminas\Server\Server`‘s `getFunctions()`
and `loadFunctions()` methods.
