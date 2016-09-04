..  _setup:

Setup
=====

Before you install XRL, make sure you have a working PHP installation.

XRL requires PHP 5.3.4 or later and the following PHP extensions:

*   XMLReader
*   XMLWriter
*   libxml
*   GMP
*   PCRE
*   SPL
*   Reflection

..  note::

    Use ``php -v`` and ``php -m`` to retrieve information about your PHP version
    and available extensions.

XRL can be installed using a :ref:`PHAR archive`, :ref:`Composer`
or from :ref:`sources`. The PHAR approach is recommended.


..  _`phar archive`:

PHAR archive
------------

Download the latest PHAR available on https://github.com/fpoirotte/XRL/releases
and save it to your computer.

(For Unix/Linux users) Optionally, make the file executable.


..  _composer:

Composer
--------

XRL can be installed using the `Composer dependency manager
<https://getcomposer.org/>`_. Just add ``fpoirotte/xrl``
to the dependencies in your :file:`composer.json` :

..  sourcecode:: console

    $ php composer.phar require fpoirotte/xrl


..  _sources:

Sources
-------

To install XRL from sources, use :program:`git` to clone the repository:

..  sourcecode:: console

    $ git clone https://github.com/fpoirotte/XRL.git /new/path/for/XRL


..  : End of document.
..  : vim: ts=4 et
