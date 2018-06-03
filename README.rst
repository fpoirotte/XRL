.. : This file is part of XRL, a simple XML-RPC Library for PHP.
.. :
.. : Copyright (c) 2012, XRL Team. All rights reserved.
.. : XRL is licensed under the 3-clause BSD License.
.. :
.. : For the full copyright and license information, please view the LICENSE
.. : file that was distributed with this source code.

A simple XML-RPC client and server written in PHP
=================================================

|travis-ci| |coveralls| |hhvm| |readthedocs|

Features
--------

XRL provides the following features:

*   Simple servers, using either the property operator ``->``
    or array accessors ``[]`` to provide methods.

*   Simple clients, using the regular method call syntax
    ``$client->procedure(42)`` to call a procedure
    provided by the remote XML-RPC server.

*   Intuitive XML-RPC calls, with automatic type conversion between
    PHP types and their XML-RPC counterparts.

*   Optional validation of XML-RPC messages (both requests and responses).

*   Two XML output formats (compact or indented), making it easy
    to debug potential issues.

*   Many different types of installations are possible
    (sorted from easiest to most complex):

    -   Using a PHAR archive
    -   Using `composer <http://getcomposer.org/>`_
    -   From sources

*   A CLI script (for composer/source installs, but see below)
    that can be used to query a remote XML-RPC server,
    display traffic (for debugging purposes), etc.

*   A ``.phar`` archive that contains all of XRL's source code
    and can also be used in place of the regular CLI script.

*   Compatibility with PHP versions from PHP 5.3.4 onward.


Installation
------------

Several types of installations are possible:

*   Using a ``.phar`` archive:

    Download the PHAR archive for the latest release from
    https://github.com/fpoirotte/XRL/releases/latest

*   Using composer:

        ..  sourcecode:: console

            $ php composer.phar require fpoirotte/xrl

*   From sources (stable version)

    -   As a git clone:

        ..  sourcecode:: console

                $ git clone -b master git://github.com/fpoirotte/XRL.git

    -   As a `.tar.gz archive <https://github.com/fpoirotte/XRL/tarball/master>`_:

        ..  sourcecode:: console

                $ wget --content-disposition https://github.com/fpoirotte/XRL/tarball/master

    -   As a `.zip archive <https://github.com/fpoirotte/XRL/zipball/master>`_:

        ..  sourcecode:: console

                $ wget --content-disposition https://github.com/fpoirotte/XRL/zipball/master

*   From sources (development version)

    -   As a git clone:

        ..  sourcecode:: console

                $ git clone -b develop git://github.com/fpoirotte/XRL.git

    -   As a `.tar.gz archive <https://github.com/fpoirotte/XRL/tarball/develop>`_:

        ..  sourcecode:: console

                $ wget --content-disposition https://github.com/fpoirotte/XRL/tarball/develop

    -   As a `.zip archive <https://github.com/fpoirotte/XRL/zipball/develop>`_:

        ..  sourcecode:: console

                $ wget --content-disposition https://github.com/fpoirotte/XRL/zipball/develop


Usage
-----

Client: look at the code in `client.php <./docs/example/client.php>`_.

Server: look at the code in `server.php <./docs/example/server.php>`_.


Contributions
-------------

If you want to contribute to this project:

* `Fork it <https://github.com/fpoirotte/XRL/fork>`_.
* Change the code.
* Send us a pull request.

Please read the section on copyright attribution and licensing below carefully
before sending your pull request.

Copyright and license
---------------------

XRL is released under the 3-clause BSD License. An online copy of the license
is available at https://raw.github.com/fpoirotte/XRL/develop/LICENSE.

We ask contributors to assign the copyright in their contributions
to the collective name "XRL Team".

To make things easier, we also ask that you keep the same license
in your contributions as the global one if possible (3-clause BSD License).

The copyright and licensing information should be reproduced at the top
of every file. A template is given below for PHP files.
For other types of files (RelaxNG schemae, reStructuredText pages, etc.),
adapt the template to fit that file's particular syntax requirements.

Since we want to retain credit for contributors where it's due, feel free
to add a Doxygen ``\authors`` command with your name and email in every class
where you made significant changes.

Example template (taken from XRL's autoloader) for PHP files containing
both copyright information, licensing information and contributor credits:

..  sourcecode:: php

    <?php
    /*
     * This file is part of XRL, a simple XML-RPC Library for PHP.
     *
     * Copyright (c) 2012, XRL Team. All rights reserved.
     * XRL is licensed under the 3-clause BSD License.
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code.
     */

    namespace fpoirotte\XRL;

    /**
     * \brief
     *      An helper class that wraps XRL's autoloader.
     *
     * \authors John Doe <john@doe.example.com>
     * \authors Jane Doe <jane@doe.example.com>
     */
    class Autoload
    {
        // Some code here...
    }



..  : End of page.
..  : The rest of this document are definitions for various macros.

..  |travis-ci| image:: https://api.travis-ci.org/fpoirotte/XRL.png
    :alt: unknown
    :target: http://travis-ci.org/fpoirotte/XRL

..  |coveralls| image:: https://coveralls.io/repos/fpoirotte/XRL/badge.svg?branch=develop&service=github
    :alt: unknown
    :target: https://coveralls.io/github/fpoirotte/XRL?branch=develop

..  |hhvm| image:: http://hhvm.h4cc.de/badge/fpoirotte/xrl.png
    :alt: unknown
    :target: http://hhvm.h4cc.de/package/fpoirotte/xrl

..  |readthedocs| image:: https://readthedocs.org/projects/xrl/badge/?version=latest
    :alt: unknown
    :target: https://readthedocs.org/projects/xrl/?badge=latest

..  |---| unicode:: U+02014 .. em dash
    :trim:

..  : vim: ts=4 et
