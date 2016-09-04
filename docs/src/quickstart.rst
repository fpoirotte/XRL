Quick start
===========

Assuming XRL is correctly :ref:`installed <setup>` on your computer,
you can now write XML-RPC clients and servers.

Writing an XML-RPC client
-------------------------

1.  Load and register the autoloader [#note_composer]_

    ..  sourcecode:: inline-php

        require_once('/path/to/XRL/src/Autoload.php');
        \fpoirotte\XRL\Autoload::register();

2.  Create a new client configured to query the remote XML-RPC server

    ..  sourcecode:: inline-php

        $client = new \fpoirotte\XRL\Client("http://xmlrpc.example.com/server");

3.  Call a method provided by that server, like it were just any other code

    ..  sourcecode:: inline-php

        // Call the remote procedure named "hello",
        // with "world" as its parameter.
        $result = $client->hello('world');

        // $result now contains the remote procedure's result,
        // as a regular PHP type (integer, string, double, array, etc.)
        var_dump($result); // string(12) "hello world!"

        // Methods with names that are not valid PHP identifiers
        // can still be called!
        var_dump($client->{'string.up'}('game over')); // string(9) "GAME OVER"


Writing an XML-RPC server
-------------------------

1.  Load and register the autoloader [#note_composer]_

    ..  sourcecode:: inline-php

        require_once('/path/to/XRL/src/Autoload.php');
        \fpoirotte\XRL\Autoload::register();

2.  Create a new server instance

    ..  sourcecode:: inline-php

        $server = new \fpoirotte\XRL\Server();

3.  Attach some methods to that server

        -   You can register anonymous functions, closures,
            global functions, public methods on objects, etc.
            using the attribute access operator ``->``.
            You may even use invokable objects!

            ..  sourcecode:: inline-php

                class Simpson
                {
                    private $speech = array(
                        'Homer'     => 'Doh!',
                        'Marge'     => 'Hmm...',
                        'Bart'      => 'Aie, caramba!',
                        'Lisa'      => M_PI,
                        'Maggie'    => null,
                    );
                    private $character;

                    public function __construct($character)
                    {
                        if (!array_key_exists($character, $this->speech)) {
                            throw new InvalidArgumentException("Who's that?");
                        }
                        $this->character = $character;
                    }

                    public function __invoke()
                    {
                        return $this->speech[$this->character];
                    }
                }
                $server->homer  = new Simpson('Homer');
                $server->marge  = new Simpson('Marge');
                $server->bart   = new Simpson('Bart');
                $server->lisa   = new Simpson('Lisa');
                $server->maggie = new Simpson('Maggie');

        -   Alternatively, you can use the array syntax ``[]`` instead.
            This is recommended as it avoids potential conflicts
            with XRL's own attributes and it makes things easier
            when the method's name is not a valid PHP identifier.

            ..  sourcecode:: inline-php

                $server['hello'] = function ($s) { return "Hello $s!"; };
                $server['string.up'] = 'strtoupper';

4.  Handle incoming XML-RPC requests and publish the results

    ..  sourcecode:: inline-php

        $server->handle()->publish();


..  [#note_composer] Users of the `Composer dependency manager
    <https://getcomposer.org/>`_ should load the regular autoloader
    found in ``vendor/autoload.php`` instead.

..  : End of document.
..  : vim: ts=4 et
