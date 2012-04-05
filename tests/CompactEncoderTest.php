<?php

include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'EncoderTestCase.php');

class   CompactEncoderTest
extends AbstractEncoder_TestCase
{
    protected $_format = XRL_EncoderInterface::OUTPUT_COMPACT;

    protected function _getXML($folder, $filename)
    {
        $content = file_get_contents(
            dirname(__FILE__) .
            DIRECTORY_SEPARATOR . 'testdata' .
            DIRECTORY_SEPARATOR . $folder .
            DIRECTORY_SEPARATOR . $filename . '.xml'
        );

        // Remote all whitespaces.
        $content = str_replace(array(' ', "\n", "\r", "\t"), '', $content);

        // Use a bare XML declaration.
        $content = str_replace(
            '<'.'?xmlversion="1.0"encoding="UTF-8"?'.'>',
            '<'.'?xml version="1.0"?'.">\n",
            $content
        );

        // Add a trailing newline (this is what
        // libxml2 does when indent is disabled).
        $content .= "\n";

        return $content;
    }
}

