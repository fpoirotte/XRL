Extensions
==========

XRL supports several extensions to the original XML-RPC specification.
These extensions are known to be widely supported by other implementations
and generally do not conflict with the original specification.


Supported extensions
--------------------

getCapabilities
~~~~~~~~~~~~~~~

The `getCapabilities extension
<http://tech.groups.yahoo.com/group/xml-rpc/message/2897>`_ has been designed
for two reasons:

*   To let XML-RPC servers announce (non-standard) features they support.
*   To provide an easy way for XML-RPC clients to adapt their behaviour
    depending on the non-standard features supported by a server.

XRL servers implement the following additional methods when this extension
is enabled:

*   ``system.getCapabilities``


introspection
~~~~~~~~~~~~~

The `introspection <http://xmlrpc-c.sourceforge.net/introspection.html>`_
extension makes it possible for a client to retrieve information
about a remote method by querying the XML-RPC server providing it.

XRL servers implement the following additional methods when this extension
is enabled:

*   ``system.listMethods``
*   ``system.methodSignature``
*   ``system.methodHelp``


multicall
~~~~~~~~~

The `multicall <http://mirrors.talideon.com/articles/multicall.html>`_
extension has been designed to avoid the latency incurred by HTTP round-trips
when making several method calls against the same XML-RPC server.

XRL servers implement the following additional methods when this extension
is enabled:

*   ``system.multicall``


faults_interop
~~~~~~~~~~~~~~

The `faults_interop
<http://xmlrpc-epi.sourceforge.net/specs/rfc.fault_codes.php>`_ extension
contains specifications for a set of standard error conditions (faults),
to promote interoperability between XML-RPC implementations.

This extension is always enabled and does not add any additional methods
to an XML-RPC server. A developer willing to use the interoperability faults
defined in this extension can call ``\fpoirotte\XRL\Faults::get()``
with the name of the fault to create.

..  sourcecode:: inline-php

    $server->error = function () {
        throw \fpoirotte\XRL\Faults::get(\fpoirotte\XRL\Faults::SYSTEM_ERROR);
    };

The following interoperability fault names are recognized:

*   ``NOT_WELL_FORMED``
*   ``UNSUPPORTED_ENCODING``
*   ``INVALID_CHARACTER``
*   ``INVALID_XML_RPC``
*   ``METHOD_NOT_FOUND``
*   ``INVALID_PARAMETERS``
*   ``INTERNAL_ERROR``
*   ``APPLICATION_ERROR``
*   ``SYSTEM_ERROR``
*   ``TRANSPORT_ERROR``


Apache types
~~~~~~~~~~~~

The `Apache types <http://ws.apache.org/xmlrpc/types.html>`_ extension
is kind of special. It does not define any additional methods,
but instead focuses on defining additional XML-RPC types.

This extension is always enabled. See also the documentation on
:ref:`supported XML-RPC types <types>` for more information on these types
and how they are used in XRL.


Enabling the extensions
-----------------------

By default, XRL enables only a few extensions (namely, the ``faults_interop``
and ``Apache types`` extensions).

To enable the rest of the extensions, you must call
``\fpoirotte\XRL\CapableServer::enable()`` on the server:

..  sourcecode:: inline-php

    // Create a regular XML-RPC server.
    $server = new \fpoirotte\XRL\Server();

    // Enable additional extensions (capabilities) for that server.
    \fpoirotte\XRL\CapableServer::enable($server);

..  note::

    It is not currently possible to enable each extension separately
    when using ``\fpoirotte\XRL\CapableServer::enable()``.
    It's an all-or-nothing kind of situation.


..  : End of document.
..  : vim: ts=4 et
