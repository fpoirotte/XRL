HipHop Virtual Machine
=======================

    HipHop Virtual Machine (HHVM) is a process virtual machine
    based on just-in-time (JIT) compilation, serving as
    an execution engine for PHP.

    Source: http://en.wikipedia.org/wiki/HipHop_Virtual_Machine

There are currently issues that prevent XRL from running smoothly on HHVM.
Nonetheless, XRL is actively tested against HHVM in our
`Continuous Integration process <https://travis-ci.org/fpoirotte/XRL>`_.

We plan to write more tests in the future to isolate those problems
and to help make HHVM better. Meanwhile, users are advised to run
the stock PHP interpreter (either from http://php.net/ or from another
distributor) when using XRL.
