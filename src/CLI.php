<?php
/**
 * \file
 *
 * Copyright (c) 2012, XRL Team
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the <organization> nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace fpoirotte\XRL;

/**
 * \brief
 *      A class that implements a simple CLI script
 *      to send XML-RPC queries and to display their
 *      result.
 */
class CLI
{
    /**
     * Return XRL's version.
     *
     * \retval string
     *      XRL's version, or "dev" if the version
     *      information could not be retrieved.
     */
    public static function getVersion()
    {
        // From a phar release.
        if (!strncmp('phar://', __FILE__, 7)) {
            $phar = new \Phar(__FILE__);
            $md = $phar->getMetadata();
            return $md['version'];
        }

        // From a composer install.
        $getver = dirname(__DIR__) .
                    DIRECTORY_SEPARATOR . 'vendor' .
                    DIRECTORY_SEPARATOR . 'erebot' .
                    DIRECTORY_SEPARATOR . 'buildenv' .
                    DIRECTORY_SEPARATOR . 'get_version.php';
        if (file_exists($getver)) {
            return trim(shell_exec($getver));
        }

        // Default guess.
        return 'dev';
    }

    /**
     * Display this script's usage.
     *
     * \param fpoirotte::XRL::Output $output
     *      Output where usage information
     *      will be sent.
     *
     * \param string $prog
     *      The name of this script, ie.
     *      \c $_SERVER['argv'][0]
     */
    public function printUsage($output, $prog)
    {
        $usageFile  = dirname(__DIR__) .
                        DIRECTORY_SEPARATOR . 'data' .
                        DIRECTORY_SEPARATOR . 'usage.txt';
        $usage      = @file_get_contents($usageFile);
        $usage      = str_replace(array("\r\n", "\r"), "\n", $usage);
        $usage      = trim($usage);
        $output->write($usage, $prog, 'http://xmlrpc.example.com/');
    }

    /**
     * Parse a boolean out of some text.
     * "0", "off" or "false" can be used to represent
     * \c false while "1", "on" or "true" can used to
     * represent \c true.
     *
     * \param string $value
     *      Some text that's supposed to represent
     *      a boolean value.
     *
     * \retval bool
     *      The value that was parsed.
     *
     * \throw Exception
     *      The given text did not contain
     *      a boolean value.
     */
    protected function parseBool($value)
    {
        $value = strtolower($value);
        if (in_array($value, array('0', 'off', 'false'))) {
            return false;
        }
        if (in_array($value, array('1', 'on', 'true'))) {
            return true;
        }
        throw new \Exception('Invalid value "'.$value.'" for type "bool"');
    }

    /**
     * Return the content of a file.
     *
     * \param string $value
     *      Name of the file to read.
     *
     * \retval string
     *      The content of that file.
     *
     * \throw Exception
     *      The file did not exist or its content
     *      could not be read.
     */
    protected function parseFile($value)
    {
        $content = @file_get_contents($value);
        if ($content === false) {
            throw new \Exception('Could not read content of "'.$value.'"');
        }
        return $content;
    }

    /**
     * Parse a date/time value and return a
     * \c DateTime object for it.
     *
     * \param string $value
     *      A date/time value, using any of the compound
     *      formats supported by PHP.
     *
     * \retval DateTime
     *      An object representing that date/time.
     *
     * \throw Exception
     *      The given value did not refer to a valid
     *      date/time.
     *
     * \note
     *      See http://php.net/datetime.formats.compound.php
     *      for a list of compound formats PHP supports.
     */
    protected function parseTimestamp($value)
    {
        $result = new \DateTime($value);
        // Older versions of PHP returned false for invalid
        // values instead of throwing an exception.
        if (!$result) {
            throw new \Exception('Invalid datetime value "'.$value.'"');
        }
        return $result;
    }

    /**
     * Parse this script's arguments and extract
     * parameters.
     *
     * \param array $args
     *      Arguments that contain parameters for an XML-RPC
     *      request, using the notation this script expects.
     *
     * \param DateTimeZone $timezone
     *      Timezone information (used to parse date/times).
     *
     * \retval array
     *      The parameters for an XML-RPC request that were
     *      parsed by this method.
     *
     * \throw Exception
     *      Some error occurred during the parsing.
     *      See the exception's message for more information.
     *
     * \note
     *      The list of arguments passed to this method
     *      is modified as it is parsed.
     */
    protected function parseParam(array &$args, \DateTimeZone $timezone)
    {
        if (!count($args)) {
            throw new \Exception('Not enough arguments.');
        }

        $type = strtolower(array_shift($args));
        $parseFunc = null;
        switch ($type) {
            case 'null':
            case 'nil':
            case 'n':
                return null;
                break;

            case 'boolean':
            case 'bool':
            case 'b':
                $parseFunc = array($this, '_parseBool');
                break;

            case 'integer':
            case 'int':
            case 'i4':
            case 'i':
                $parseFunc = 'intval';
                break;

            case 'double':
            case 'float':
            case 'f':
                $parseFunc = 'floatval';
                break;

            case 'string':
            case 'str':
            case 's':
                $parseFunc = 'strval';
                break;

            case 'file':
            case '@':
                $parseFunc = array($this, '_parseFile');
                break;

            case 'timestamp':
            case 'datetime':
            case 'date':
            case 'time':
            case 'ts':
            case 'dt':
            case 't':
            case 'd':
                $parseFunc = array($this, '_parseTimestamp');
                break;

            case 'hash':
            case 'h':
                $result = array();
                while (true) {
                    if (!count($args)) {
                        throw new \Exception('Not enough arguments for "hash".');
                    }

                    if (in_array(strtolower($args[0]), array('endhash', 'eh'))) {
                        break;
                    }

                    $key = $this->parseParam($args, $timezone);
                    if (!is_int($key) && !is_string($key)) {
                        throw new \Exception(
                            'Invalid type "'.gettype($key).'" for hash key. '.
                            'Only integer and string keys may be used.'
                        );
                    }
                    $value = $this->parseParam($args, $timezone);
                    $result[$key] = $value;
                }
                // Pop the ending "endhash".
                array_shift($args);
                return $result;

            case 'list':
            case 'l':
                $result = array();
                while (true) {
                    if (!count($args)) {
                        throw new \Exception('Not enough arguments for "list".');
                    }

                    if (in_array(strtolower($args[0]), array('endlist', 'el'))) {
                        break;
                    }

                    $result[] = $this->parseParam($args, $timezone);
                }
                // Pop the ending "endlist".
                array_shift($args);
                return $result;

            default:
                throw new \Exception('Unknown type "'.$type.'".');
        }

        if (!count($args)) {
            throw new \Exception('Not enough arguments.');
        }

        $value = array_shift($args);
        $value = call_user_func($parseFunc, $value);
        if ($value instanceof \DateTime) {
            $value->setTimezone($timezone);
        }
        return $value;
    }

    /**
     * Parse the arguments passed to this script.
     *
     * \param array $args
     *      A list with the arguments passed to this
     *      script.
     *
     * \retval array
     *      An array of <tt>($options,$params)</tt>
     *      for this script.
     *
     * \throw Exception
     *      Some error occurred during the parsing.
     *      See the exception's message for more
     *      information.
     */
    protected function parse(array $args)
    {
        $params     = array(
            'serverURL'     => null,
            'procedure'     => null,
            'additional'    => array(),
        );
        $options    = array(
            'd' => false,
            'h' => false,
            'n' => false,
            't' => new \DateTimeZone(@date_default_timezone_get()),
            'v' => false,
            'x' => false,
        );

        while (count($args)) {
            $v = array_shift($args);

            if ($params['serverURL'] === null) {
                if (substr($v, 0, 1) == '-') {
                    $p = array();
                    $v = (string) substr($v, 1);
                    foreach (str_split($v) as $o) {
                        if (!array_key_exists($o, $options)) {
                            throw new \Exception(
                                'Unknown option "'.$o.'". '.
                                'Use -h to get help.'
                            );
                        }

                        if (is_bool($options[$o])) {
                            $options[$o] = true;
                        } else {
                            $p[] = $o;
                        }
                    }

                    foreach ($p as $o) {
                        if (!count($args)) {
                            throw new \Exception(
                                'Not enough arguments for option "'.$o.'".'
                            );
                        }
                        $v = array_shift($args);
                        if (!($options[$o] instanceof \DateTimeZone)) {
                            $options[$o] = new \DateTimeZone($v);
                        }
                    }
                } else {
                    $params['serverURL'] = $v;
                }
                continue;
            }

            if ($params['procedure'] === null) {
                $params['procedure'] = $v;
                break;
            }
        }

        while (count($args)) {
            $params['additional'][] = $this->parseParam($args, $options['t']);
        }

        return array($options, $params);
    }

    /**
     * Run this CLI script.
     *
     * \param array $args
     *      A list of arguments passed to this script.
     *
     * \retval int
     *      Exit code. \c 0 is used to indicate a
     *      success, while any other code indicates
     *      an error.
     *
     * \note
     *      In case of an error, additional messages
     *      may be sent to STDERR by this script.
     */
    public function run(array $args)
    {
        $prog = array_shift($args);
        try {
            list($options, $params) = $this->parse($args);
        } catch (\Exception $e) {
            fprintf(STDERR, '%s: %s'.PHP_EOL, $prog, $e->getMessage());
            return 2;
        }

        // Show help.
        if ($options['h']) {
            $this->printUsage(new \fpoirotte\XRL\Output(STDOUT), $prog);
            return 0;
        }

        // Show version.
        if ($options['v']) {
            $version = self::getVersion();
            echo 'XRL v. '.$version.' -- copyright the XRL Team'.PHP_EOL;
            echo 'Licensed under the new 3-clause BSD License'.PHP_EOL;
            echo 'Visit https://github.com/fpoirotte/XRL for more'.PHP_EOL;
            return 0;
        }

        // Do we have enough arguments to do something?
        if ($params['serverURL'] === null || $params['procedure'] === null) {
            $this->printUsage(new \fpoirotte\XRL\Output(STDERR), $prog);
            return 2;
        }

        // Then let's do it!
        $encoder    = new \fpoirotte\XRL\Encoder($options['t'], true);
        $decoder    = new \fpoirotte\XRL\Decoder($options['t'], $options['x']);
        $request    = new \fpoirotte\XRL\Request(
            $params['procedure'],
            $params['additional']
        );

        $xml = $encoder->encodeRequest($request);
        if ($options['d']) {
            echo "Request:".PHP_EOL;
            echo trim($xml).PHP_EOL.PHP_EOL;
        }

        if ($options['n']) {
            echo 'Not sending the actual query due to dry run mode.'.PHP_EOL;
            return 0;
        }

        $context    = stream_context_get_default();
        $ctxOptions = array(
            'http' => array(
                'method'    => 'POST',
                'content'   => $xml,
                'header'    => 'Content-Type: text/xml',
            ),
        );
        stream_context_set_option($context, $ctxOptions);

        $data = file_get_contents($params['serverURL'], false, $context);
        if ($data === false) {
            fprintf(
                STDERR,
                'Could not query "%s"'.PHP_EOL,
                $params['serverURL']
            );
            return 1;
        }

        if ($options['d']) {
            echo 'Response:'.PHP_EOL;
            echo trim($data).PHP_EOL.PHP_EOL;
        }

        try {
            $result = $decoder->decodeResponse($data);
        } catch (\Exception $result) {
            // Nothing to do.
        }

        echo 'Result:'.PHP_EOL;
        print_r($result);
        return 0;
    }
}