<?php

class       XRL_Encoder
implements  XRL_EncoderInterface
{
    protected $_format;

    public function __construct($format = XRL_EncoderInterface::FORMAT_COMPACT)
    {
        if ($format != XRL_EncoderInterface::FORMAT_PRETTY &&
            $format != XRL_DecoderInterface::FORMAT_COMPACT)
            throw new InvalidArgumentException('Invalid format');

        $this->_format = $format;
    }

    protected function _getWriter()
    {
        $writer = new XMLWriter();
        $writer->openMemory();
        if ($this->_format == XRL_EncoderInterface::FORMAT_PRETTY)
            $writer->setIndent(TRUE);
        else
            $writer->setIndent(FALSE);
        $writer->startDocument('1.0', 'UTF-8');
        return $writer;
    }

    static protected function _writeValue(XMLWriter $writer, $value)
    {
        if (is_int($value))
            return $writer->writeElement('int', $value);

        if (is_bool($value))
            return $writer->writeElement('boolean', (int) $value);

        if (is_string($value)) {
            /// @TODO: base64 support.
            return $writer->text($value);
        }

        if (is_double($value))
            return $writer->writeElement('double', $value);

        if (is_array($value)) {
            $keys       = array_keys($value);
            $length     = count($value);

            // Empty arrays must be handled with care.
            if (!$length)
                $numeric = array();
            else {
                $numeric = range(0, $length - 1);
                sort($keys);
            }

            // Hash / associative array.
            if ($keys != $numeric) {
                $writer->startElement('struct');
                foreach ($value as $key => $val) {
                    $writer->startElement('member');
                    $writer->startElement('name');
                    $this->_writeValue($writer, $key);
                    $writer->endElement('name');

                    $writer->startElement('value');
                    $this->_writeValue($writer, $val);
                    $writer->endElement('value');
                    $writer->endElement('member');
                }
                $writer->endElement('struct');
                return;
            }

            // List / numerically-indexed array.
            $writer->startElement('array');
            $writer->startElement('data');
            foreach ($value as $val)
                $this->_writeValue($writer, $val);
            $writer->endElement('data');
            $writer->endElement('array');
            return;
        }

        if (!is_object($value))
            throw new InvalidArgumentException('Unsupported type');

        /// @TODO: special support for DateTime objects.

        if (($value instanceof Serializable) ||
            method_exists($value, '__sleep'))
            return $this->_writeValue($writer, serialize($value));

        throw new InvalidArgumentException('Could not serialize object');
    }

    static protected function _finalizeWrite(XMLWriter $writer)
    {
        $writer->endDocument();
        $result = $writer->outputMemory(TRUE);
        return $result;
    }

    public function encodeRequest(XRL_Request $request)
    {
        $writer = $this->_getWriter();
        $writer->startElement('methodCall');
        $writer->writeElement('methodName', $request->getProcedure());
        if (count($request->getParams())) {
            $writer->startElement('params');
            foreach ($request->getParams() as $param) {
                $writer->startElement('param');
                $writer->startElement('value');
                self::_writeValue($writer, $param);
                $writer->endElement();
                $writer->endElement();
            }
            $writer->endElement();
        }
        $writer->endElement();
        $result = self::_finalizeWrite($writer);
        return $result;
    }

    public function encodeError(Exception $error)
    {
        $writer = $this->_getWriter();
        $writer->startElement('methodResponse');
        $writer->startElement('fault');
        $writer->startElement('value');
        self::_writeValue(
            $writer,
            array(
                'faultCode'     => $error->getCode(),
                'faultString'   => $error->getMessage(),
            )
        );
        $writer->endElement();
        $writer->endElement();
        $writer->endElement();
        $result = self::_finalizeWrite($writer);
        return $result;
    }

    public function encodeResponse($response)
    {
        $writer = $this->_getWriter();
        $writer->startElement('methodResponse');
        $writer->startElement('params');
        $writer->startElement('param');
        $writer->startElement('value');
        self::_writeValue($writer, $response);
        $writer->endElement();
        $writer->endElement();
        $writer->endElement();
        $writer->endElement();
        $result = self::_finalizeWrite($writer);
        return $result;
    }
}

