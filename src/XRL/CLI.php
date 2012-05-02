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

/**
 * \brief
 *      A class that implements a simple CLI script
 *      to send XML-RPC queries and to display their
 *      result.
 */
class XRL_CLI
{
    /**
     * Return XRL's version.
     *
     * \retval string
     *      XRL's version, or "dev" if the version
     *      information could not be retrieved.
     */
    static public function getVersion()
    {
        // Test for installation via pear/pyrus.
        $version = '@PACKAGE_VERSION@';
        if ($version != '@'.'PACKAGE_VERSION'.'@')
            return $version;

        // We're running from a repository checkout.
        // Default to a generic "dev"elopment version.
        $version = 'dev';

        // But try to figure out an actual version from our layout.
        $dir    = dirname(dirname(dirname(__FILE__)));
        $files  = array();
        $iter   = new RegexIterator(
            new DirectoryIterator($dir),
            '/^RELEASE\-(.+)$/',
            RegexIterator::GET_MATCH
        );
        foreach ($iter as $file)
            $files[$file[1]] = $file;

        if (!count($files))
            return $version;

        uksort($files, 'version_compare');
        list($file, $version) = array_pop($files);
        return $version;
    }

    /**
     * Display this script's usage.
     *
     * \param XRL_Output $o
     *      An outputter where usage information
     *      will be sent.
     *
     * \param string $prog
     *      The name of this script, ie.
     *      \c $_SERVER['argv'][0]
     */
    public function printUsage($o, $prog)
    {
        $dataDir = '@data_dir@';
        // Running from sources/clone.
        if ($dataDir == '@'.'data_dir'.'@') {
            $usageDir = dirname(dirname(dirname(__FILE__))) .
                            DIRECTORY_SEPARATOR . 'data';
        }
        else
            $usageDir = $dataDir;

        $usageDir .= DIRECTORY_SEPARATOR;
        if ($dataDir != '@'.'data_dir'.'@' || !strncmp(__FILE__, 'phar://', 7))
            $usageDir .= 'pear.erebot.net' . DIRECTORY_SEPARATOR . 'XRL';

        $usageFile  = $usageDir . DIRECTORY_SEPARATOR . 'usage.txt';
        $usage      = @file_get_contents($usageFile);
        $usage      = str_replace(array("\r\n", "\r"), "\n", $usage);
        $usage      = trim($usage);
        $o->_($usage, $prog, 'http://xmlrpc.example.com/');
    }

    /**
     * Parse a boolean out of some text.
     * "0", "off" or "false" can be used to represent
     * \c FALSE while "1", "on" or "true" can used to
     * represent \c TRUE.
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
    protected function _parseBool($value)
    {
        $value = strtolower($value);
        if (in_array($value, array('0', 'off', 'false')))
            return FALSE;
        if (in_array($value, array('1', 'on', 'true')))
            return TRUE;
        throw new Exception('Invalid value "'.$value.'" for type "bool"');
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
    protected function _parseFile($value)
    {
        $content = @file_get_contents($value);
        if ($content === FALSE)
            throw new Exception('Could not read content of "'.$value.'"');
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
    protected function _parseTimestamp($value)
    {
        $result = new DateTime($value);
        // Older versions of PHP returned FALSE for invalid
        // values instead of throwing an exception.
        if (!$result)
            throw new Exception('Invalid datetime value "'.$value.'"');
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
    protected function _parseParam(array &$args, DateTimeZone $timezone)
    {
        if (!count($args))
            throw new Exception('Not enough arguments.');

        $type = strtolower(array_shift($args));
        $parseFunc = NULL;
        switch ($type) {
            case 'null':
            case 'nil':
            case 'n':
                return NULL;
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
                while (TRUE) {
                    if (!count($args))
                        throw new Exception('Not enough arguments for "hash".');

                    if (in_array(strtolower($args[0]), array('endhash', 'eh')))
                        break;

                    $key = $this->_parseParam($args, $timezone);
                    if (!is_int($key) && !is_string($key)) {
                        throw new Exception(
                            'Invalid type "'.gettype($key).'" for hash key. '.
                            'Only integer and string keys may be used.'
                        );
                    }
                    $value = $this->_parseParam($args, $timezone);
                    $result[$key] = $value;
                }
                // Pop the ending "endhash".
                array_shift($args);
                return $result;

            case 'list':
            case 'l':
                $result = array();
                while (TRUE) {
                    if (!count($args))
                        throw new Exception('Not enough arguments for "list".');

                    if (in_array(strtolower($args[0]), array('endlist', 'el')))
                        break;

                    $result[] = $this->_parseParam($args, $timezone);
                }
                // Pop the ending "endlist".
                array_shift($args);
                return $result;

            default:
                throw new Exception('Unknown type "'.$type.'".');
        }

        if (!count($args))
            throw new Exception('Not enough arguments.');

        $value = array_shift($args);
        $value = call_user_func($parseFunc, $value);
        if ($value instanceof DateTime)
            $value->setTimezone($timezone);
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
    protected function _parse(array $args)
    {
        $params     = array(
            'serverURL'     => NULL,
            'procedure'     => NULL,
            'additional'    => array(),
        );
        $options    = array(
            'd' => FALSE,
            'h' => FALSE,
            'n' => FALSE,
            't' => new DateTimeZone(@date_default_timezone_get()),
            'v' => FALSE,
            'x' => FALSE,
        );

        while (count($args)) {
            $v = array_shift($args);

            if ($params['serverURL'] === NULL) {
                if (substr($v, 0, 1) == '-') {
                    $p = array();
                    $v = (string) substr($v, 1);
                    foreach (str_split($v) as $o) {
                        if (!array_key_exists($o, $options)) {
                            throw new Exception(
                                'Unknown option "'.$o.'". '.
                                'Use -h to get help.'
                            );
                        }

                        if (is_bool($options[$o]))
                            $options[$o] = TRUE;
                        else
                            $p[] = $o;
                    }

                    foreach ($p as $o) {
                        if (!count($args)) {
                            throw new Exception(
                                'Not enough arguments for option "'.$o.'".'
                            );
                        }
                        $v = array_shift($args);
                        if ($options[$o] instanceof DateTimeZone)
                            $options[$o] = new DateTimeZone($v);
                    }
                }
                else
                    $params['serverURL'] = $v;
                continue;
            }

            if ($params['procedure'] === NULL) {
                $params['procedure'] = $v;
                break;
            }
        }

        while (count($args)) {
            $params['additional'][] = $this->_parseParam($args, $options['t']);
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
            list($options, $params) = $this->_parse($args);
        }
        catch (Exception $e) {
            fprintf(STDERR, '%s: %s'.PHP_EOL, $prog, $e->getMessage());
            return 2;
        }

        // Show help.
        if ($options['h']) {
            $this->printUsage(new XRL_Output(STDOUT), $prog);
            return 0;
        }

        // Show version.
        if ($options['v']) {
            $version = self::getVersion();
            echo 'XRL v. '.$version.' -- copyright the XRL Team'.PHP_EOL;
            echo 'https://github.com/fpoirotte/XRL'.PHP_EOL;
            return 0;
        }

        // Do we have enough arguments to do something?
        if ($params['serverURL'] === NULL || $params['procedure'] === NULL) {
            $this->printUsage(new XRL_Output(STDERR), $prog);
            return 2;
        }

        // Then let's do it!
        $encoder    = new XRL_Encoder($options['t'], TRUE);
        $decoder    = new XRL_Decoder($options['t'], $options['x']);
        $request    = new XRL_Request(
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

        $data = file_get_contents($params['serverURL'], FALSE, $context);
        if ($data === FALSE) {
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
        }
        catch (Exception $result) {
            // Nothing to do.
        }

        echo 'Result:'.PHP_EOL;
        var_dump($result);
        return 0;
    }
}

