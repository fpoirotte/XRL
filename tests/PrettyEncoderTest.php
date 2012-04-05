<?php

include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'EncoderTestCase.php');

class   PrettyEncoderTest
extends AbstractEncoder_TestCase
{
    protected $_format = XRL_EncoderInterface::OUTPUT_PRETTY;

    protected function _getXML($folder, $filename)
    {
        $content = file_get_contents(
            dirname(__FILE__) .
            DIRECTORY_SEPARATOR . 'testdata' .
            DIRECTORY_SEPARATOR . $folder .
            DIRECTORY_SEPARATOR . $filename . '.xml'
        );
        return $content;
    }
}

