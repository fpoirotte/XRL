Command-line XML-RPC client
===========================

XRL provides a command-line XML-RPC client that makes it easy
to test a remote XML-RPC server.

For PHAR installations, this client is embedded with the PHAR archive itself.
To use it, just call the PHP interpreter on the PHAR archive:

..  sourcecode:: console

    $ php XRL.phar
    Usage: XRL.phar [options] <server URL> <procedure> [args...]

    Options:
     -h               Show this program's help.
     [...]

For other types of installations, call the PHP interpreter on :file:`bin/xrl`:

..  sourcecode:: console

    $ php ./bin/xrl 
    Usage: ./bin/xrl [options] <server URL> <procedure> [args...]

    Options:
     -h               Show this program's help.
     [...]


..  : End of document.
..  : vim: ts=4 et
