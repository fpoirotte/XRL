Coding style
============

The XRL team closely follows the `PSR-2 <http://www.php-fig.org/psr/psr-2/>`_
coding style.

Also, when developing, the following command can be used to check various
aspects of the code quality:

..  sourcecode:: console

    $ vendor/bin/phing qa

It runs the following tools on XRL's code to detect possible issues:

*   PHP lint (``php -l``): checks PHP syntax
*   PHP_CodeSniffer: checks code compliance with coding standard
*   PDepend: identifies tight coupling between two pieces of code
*   PHPMD (PHP Mess Detector): detects high-risk code structures
*   PHPCPD (PHP Copy-Paste Detector): detects copy/paste abuse
*   PHPUnit: checks unit tests


..  : End of document.
..  : vim: ts=4 et
