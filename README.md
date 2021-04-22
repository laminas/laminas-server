# laminas-server

[![Build Status](https://github.com/laminas/laminas-server/workflows/Continuous%20Integration/badge.svg)](https://github.com/laminas/laminas-server/actions?query=workflow%3A"Continuous+Integration")

The laminas-server family of classes provides functionality for the various server
classes, including
[laminas-json-server](https://docs.laminas.dev/laminas-json-server/),
[laminas-soap](https://docs.laminas.dev/laminas-soap/) and
[laminas-xmlrpc](https://docs.laminas.dev/laminas-xmlrpc/).
`Laminas\Server\Server` provides an interface that mimics PHP’s `SoapServer` class;
all RPC-style server classes should implement this interface in order to provide a
standard server API.

- File issues at https://github.com/laminas/laminas-server/issues
- Documentation is at https://docs.laminas.dev/laminas-server/
