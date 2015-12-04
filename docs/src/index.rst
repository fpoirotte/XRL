XRL's documentation
===================

XRL (short for XML-RPC Library) is a collection of PHP classes that ease
the creation of XML-RPC clients or servers.

..  only:: devel

    ..  warning::

        This documentation was automatically built from the latest changes
        in |this_commit|_.
        It does not necessarily reflect features from any current or
        upcoming release. Check out https://readthedocs.org/projects/xrl/
        for documentation on supported versions. 

Overview
--------

*   **Simple setup** |---| grab the PHAR archive or add ``fpoirotte/xrl``
    as a dependency in your :file:`composer.json` and you're good to go.
*   **Very intuitive syntax** |---| write XML-RPC clients & servers like you
    would any other piece of code.
*   **Automatic type conversions** |---| use native PHP types without worrying
    about XML-RPC quirks.
*   **Support for many extensions** |---| want capabilities? introspection?
    multicalls?... yep, :ref:`we support them <extensions>`!


Setup & Usage
-------------

..  toctree::
    :maxdepth: 1

    setup
    quickstart
    hhvm


Features
--------

..  toctree::
    :maxdepth: 1

    types
    extensions
    cli


Contributing
------------

..  toctree::
    :maxdepth: 1

    contributing
    style
    credits


Licence
-------

XRL is released under the `3-clause BSD licence
<https://github.com/fpoirotte/XRL/blob/master/LICENSE>`_.


Other resources
---------------

*   `XRL on GitHub <https://github.com/fpoirotte/XRL/>`_ (source code and issue tracker)
*   `XRL on Packagist <https://packagist.org/packages/fpoirotte/XRL>`_ (Composer repository)
*   `XRL on Travis-CI <https://travis-ci.org/fpoirotte/XRL>`_ (continuous integration)
*   `XRL on Read The Docs <https://readthedocs.org/projects/xrl/>`_ (online documentation)
*   `Full API documentation <./apidoc/>`_ (hosted on Read The Docs)


..  : End of document.
..  |this_commit| replace:: XRL's code
..  |---| unicode:: U+02014 .. em dash
    :trim:
..  : vim: ts=4 et
