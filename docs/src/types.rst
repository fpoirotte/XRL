XML-RPC Types
=============

Supported types
---------------

XRL supports all the datatypes defined in the `official XML-RPC specification
<http://xmlrpc.scripting.com/spec.html>`_, namely:

*   ``int`` and ``i4``: 32-bit signed integer value
*   ``boolean``: your usual boolean type
*   ``string``: regular string
*   ``double``: double-precision signed floating point number
*   ``dateTime.iso8601``: date/time (without milliseconds/timezone information)
*   ``base64``: base64-encoded binary string
*   ``struct``: associative array
*   ``array``: numeric array

It also accepts the following types, which are pretty common,
despite them not being part of the official specification:

*   ``nil``: null value
*   ``i8``: 64-bit signed integer value

Last but not least, it supports the following namespaced types,
defined by the `Apache Foundation <http://ws.apache.org/xmlrpc/types.html>`_.
Please note that in this particular case, the types must belong
to the namespace URI http://ws.apache.org/xmlrpc/namespaces/extensions
to be correctly interpreted.

*   ``nil``: null value (same as the non-namespaced type)
*   ``i1``: 8-bit signed integer value
*   ``i2``: 16-bit signed integer value
*   ``i8``: 64-bits signed integer value (same as the non-namespaced type)
*   ``biginteger``: arbitrary-length integer
*   ``dom``: a DOM node, transmitted as an XML fragment
*   ``dateTime``: date/time with milliseconds and timezone information

When transmitting non-standard types, XRL always uses namespaced types.
See the next chapter for more information.


Type conversions
----------------

By default, XRL automatically converts values between PHP & XML-RPC types
where appropriate.

The following table shows how XRL converts PHP types to XML-RPC types.

..  list-table:: PHP to XML-RPC conversion
    :widths: 50 50
    :header-rows: 1

    *   -   PHP type
        -   XML-RPC type

    *   -   ``null``
        -   namespaced ``nil``

    *   -   ``boolean``
        -   ``boolean``

    *   -   ``integer``
        -   ``i4`` if it fits into 32 bits,
            namespaced ``i8`` [1]_ otherwise

    *   -   ``double``
        -   ``double``

    *   -   ``string``
        -   ``string`` if it is a valid UTF-8 string,
            ``base64`` otherwise

    *   -   ``array``
        -   ``array`` for numeric arrays,
            ``struct`` for associative arrays

    *   -   ``GMP integer`` resource (PHP < 5.6.0)
        -   ``i4`` if it fits into 32 bits,
            namespaced ``i8`` [1]_ if it fits into 64 bits,
            namespaced ``biginteger`` [1]_ otherwise

    *   -   ``\fpoirotte\XRL\Types\AbstractType`` object
        -   XML-RPC type it represents

    *   -   ``\GMP`` object (PHP >= 5.6.0)
        -   ``i4`` if it fits into 32 bits,
            namespaced ``i8`` [1]_ if it fits into 64 bits,
            namespaced ``biginteger`` [1]_ otherwise

    *   -   ``\DateTime`` object
        -   ``dateTime.iso8601`` (using local timezone information by default)

    *   -   ``\DOMNode`` object
        -   namespaced ``dom`` [1]_

    *   -   ``\XMLWriter`` object
        -   namespaced ``dom`` [1]_

    *   -   ``\SimpleXMLElement`` object
        -   namespaced ``dom`` [1]_

    *   -   ``\Exception`` object
        -   fault structure (derived from ``struct``)


The following table shows how XRL converts XML-RPC types to PHP types.

..  list-table:: XML-RPC to PHP conversion
    :widths: 50 50
    :header-rows: 1

    *   -   XML-RPC type
        -   PHP type

    *   -   ``boolean``
        -   ``boolean``

    *   -   ``i4``
        -   ``integer``

    *   -   ``int``
        -   ``integer``

    *   -   ``double``
        -   ``double``

    *   -   ``string``
        -   ``string``

    *   -   ``base64``
        -   ``string``

    *   -   ``array``
        -   numeric ``array``

    *   -   ``struct``
        -   ``\Exception`` object if the structure represents a fault [2]_,
            associative ``array`` otherwise

    *   -   ``dateTime.iso8601``
        -   ``\DateTime`` (using local timezone information by default)

    *   -   ``nil``
        -   ``null``

    *   -   namespaced ``nil`` [1]_
        -   ``null``

    *   -   namespaced ``i1`` [1]_
        -   integer

    *   -   namespaced ``i2`` [1]_
        -   integer

    *   -   ``i8``
        -   ``GMP integer`` resource (PHP < 5.6.0)
            or ``\GMP`` object (PHP >= 5.6.0)

    *   -   namespaced ``i8`` [1]_
        -   ``GMP integer`` resource (PHP < 5.6.0)
            or ``\GMP`` object (PHP >= 5.6.0)

    *   -   namespaced ``biginteger`` [1]_
        -   ``GMP integer`` resource (PHP < 5.6.0)
            or ``\GMP`` object (PHP >= 5.6.0)

    *   -   namespaced ``dom`` [1]_
        -   ``\SimpleXMLElement`` object

    *   -   namespaced ``datetime`` [1]_
        -   ``\DateTime`` object


..  [1]
    Using the namespace URI http://ws.apache.org/xmlrpc/namespaces/extensions
    for compatibility with other implementations.

..  [2]
    An XML-RPC ``struct`` representing a fault (ie. an error condition)
    gets converted to an exception that is automatically thrown.


Under the hood
--------------

Type conversions are handled by the ``\fpoirotte\XRL\NativeEncoder`` class
(for PHP to XML-RPC conversions) and ``\fpoirotte\XRL\NativeDecoder`` class
(for XML-RPC to PHP conversions), with support from classes in the
``\fpoirotte\XRL\Types\`` namespace.

You may override or disable the conversion by passing another encoder/decoder
to the XML-RPC client or server constructor.

..  note::

    If you change the default encoder/decoder, you will then be responsible
    for handling conversions to/from the ``\fpoirotte\XRL\Types\AbstractType``
    instances XRL uses internally.

..  warning::

    XML-RPC faults are handled specially and will always turn into
    a PHP ``\Exception`` that gets automatically raised, no matter
    what decoder has been passed to the client/server's constructor.


..  : End of document.
..  : vim: ts=4 et
