.. :  © copyright XRL Team, 2012. All rights reserved.
.. :
.. :  This file is part of XRL.
.. :
.. :  XRL is free software: you can redistribute it and/or modify
.. :  it under the terms of the GNU General Public License as published by
.. :  the Free Software Foundation, either version 3 of the License, or
.. :  (at your option) any later version.
.. :
.. :  XRL is distributed in the hope that it will be useful,
.. :  but WITHOUT ANY WARRANTY; without even the implied warranty of
.. :  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
.. :  GNU General Public License for more details.
.. :
.. :  You should have received a copy of the GNU General Public License
.. :  along with XRL.  If not, see <http://www.gnu.org/licenses/>.

A simple XML-RPC client and server written in PHP
=================================================

Features
--------

XRL provides the following features:

*   Simple servers, using the property operator ``->`` to indicate
    what methods the server provides.

*   Simple clients, using the regular method call syntax
    ``$client->procedure(42)`` to call a procedure
    provided by the remote XML-RPC server.

*   Intuitive XML-RPC calls, with automatic type conversion between
    PHP types and their XML-RPC counterparts.

*   Extensibility, using the array operator ``[]`` to manipulate
    the factories used by either the client or server.

*   Optional validation of XML-RPC messages (both requests and responses).

*   Two XML output formats (compact or indented), making it easy
    to debug potential issues.

*   Many different types of installations are possible
    (sorted from easiest to most complex):

    -   Using a PHAR archive.
    -   Using a git clone.
    -   From sources.

*   A CLI script (for git/source installs, but see below)
    that can be used to query a remote XML-RPC server,
    display traffic (for debugging purposes), etc.

*   A ``.phar`` archive that contains all of XRL's source code
    and can also be used in place of the regular CLI script.

*   Compatibility with PHP versions from PHP 5.2.1 onward.


Installation
------------

Several types of installations are possible:

*   Using a ``.phar`` archive:

        $ wget --no-check-certificate https://pear.erebot.net/get/XRL-dev-master.phar

*   From a git clone:

        $ git clone git://github.com/fpoirotte/XRL.git

*   From sources:

    -   As a `.tar.gz archive <https://github.com/fpoirotte/XRL/tarball/master>`_:

            $ wget -O XRL-sources.tar.gz https://github.com/fpoirotte/XRL/tarball/master

    -   As a `.zip archive <https://github.com/fpoirotte/XRL/zipball/master>`_:

            $ wget -O XRL-sources.zip https://github.com/fpoirotte/XRL/zipball/master


Contributions
-------------

If you want to contribute to this project:

* `Fork it <https://github.com/fpoirotte/XRL/fork_select>`_.
* Change the code.
* Send us a pull request.

Please note that we ask contributors to assign the copyright in their
contributions to the collective name "XRL Team".
To make things easier to maintain, we also ask that you keep the same license
in your contributions as the global one (GPL v3+).

The copyright and licensing information should be reproduced at the top of
every new file (as a special comment).
For example, every new PHP source file should begin with the following
paragraph:

    <?php
    /**
     * \file
     *
     * \copyright XRL Team, 2012. All rights reserved.
     *
     *  This file is part of XRL.
     *
     *  XRL is free software: you can redistribute it and/or modify
     *  it under the terms of the GNU General Public License as published by
     *  the Free Software Foundation, either version 3 of the License, or
     *  (at your option) any later version.
     *
     *  XRL is distributed in the hope that it will be useful,
     *  but WITHOUT ANY WARRANTY; without even the implied warranty of
     *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *  GNU General Public License for more details.
     *
     *  You should have received a copy of the GNU General Public License
     *  along with XRL.  If not, see <http://www.gnu.org/licenses/>.
     */


License
-------

XRL is released under the GPLv3 license. A copy of the license is available
at https://raw.github.com/fpoirotte/XRL/master/LICENSE.


Other resources
---------------

In addition to the information above, you may find the following
resources useful:

*   http://fpoirotte.github.io/XRL/ |---| Complete documentation for XRL.

*   http://travis-ci.org/fpoirotte/XRL |---| XRL's status on
    Travis Continuous Integration.

    Current status: |travis-ci|

..  |travis-ci| image:: https://api.travis-ci.org/fpoirotte/xrl.png
    :alt: unknown
    :target: http://travis-ci.org/fpoirotte/xrl

*   https://buildbot.erebot.net/ |---| Our Continuous Integration server.


..  |---| unicode:: U+02014 .. em dash
    :trim:

.. vim: ts=4 et
