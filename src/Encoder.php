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
 *      An XML-RPC encoder that can produce either
 *      compact documents or pretty documents.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class Encoder implements \fpoirotte\XRL\EncoderInterface
{
    /// Whether the output should be indented (\c true) or not (\c false).
    protected $indent;

    /// Whether the "\<string\>" tag should be used (\c true) or not (\c false).
    protected $stringTag;

    /// Timezone used to encode date/times.
    protected $timezone;

    /**
     * Create a new XML-RPC encoder.
     *
     * \param DateTimeZone $timezone
     *      (optional) Information on the timezone for which
     *      date/times should be encoded.
     *      If omitted, the machine's current timezone is used.
     *
     * \param bool $indent
     *      (optional) Whether the XML produced should be
     *      indented (\c true) or not (\c false).
     *      Defaults to no indentation.
     *
     * \param bool $stringTag
     *      (optional) Whether strings should be encoded explicitly
     *      using the \<string\> tag (\c true) or implicitly (\c false).
     *      Defaults to not using such tags.
     *
     * \throw InvalidArgumentException
     *      An invalid value was passed for either the \c $indent
     *      or \c $stringTag argument.
     */
    public function __construct(
        \DateTimeZone $timezone = null,
        $indent = false,
        $stringTag = false
    ) {
        if (!is_bool($indent)) {
            throw new \InvalidArgumentException('$indent must be a boolean');
        }
        if (!is_bool($stringTag)) {
            throw new \InvalidArgumentException('$stringTag must be a boolean');
        }

        if ($timezone === null) {
            try {
                $timezone = new \DateTimeZone(@date_default_timezone_get());
            } catch (\Exception $e) {
                throw new \InvalidArgumentException($e->getMessage(), $e->getCode());
            }
        }

        $this->indent       = $indent;
        $this->stringTag    = $stringTag;
        $this->timezone     = $timezone;
    }

    /**
     * Return an XML writer that will be used
     * to produce XML-RPC requests and responses.
     *
     * \retval XMLWriter
     *      XML writer to use to produce documents.
     */
    protected function getWriter()
    {
        $writer = new \XMLWriter();
        $writer->openMemory();
        if ($this->indent) {
            $writer->setIndent(true);
            $writer->startDocument('1.0', 'UTF-8');
        } else {
            $writer->setIndent(false);
            $writer->startDocument();
        }
        return $writer;
    }

    /**
     * This method must be called when the document
     * is complete and returns the document.
     *
     * \param XMLWriter $writer
     *      XML writer used to produce the document.
     *
     * \retval string
     *      The XML document that was generated,
     *      as serialized XML.
     */
    protected function finalizeWrite(\XMLWriter $writer)
    {
        $writer->endDocument();
        $result = $writer->outputMemory(true);

        if (!$this->indent) {
            // Remove the XML declaration for an even
            // more compact result.
            if (!strncmp($result, '<'.'?xml', 5)) {
                $pos    = strpos($result, '?'.'>');
                if ($pos !== false) {
                    $result = (string) substr($result, $pos + 2);
                }
            }
            // Remove leading & trailing whitespace.
            $result = trim($result);
        }

        return $result;
    }

    /// \copydoc fpoirotte::XRL::EncoderInterface::encodeRequest()
    public function encodeRequest(\fpoirotte\XRL\Request $request)
    {
        $writer = $this->getWriter();
        $writer->startElement('methodCall');
        $writer->writeElement('methodName', $request->getProcedure());
        if (count($request->getParams())) {
            $writer->startElement('params');
            foreach ($request->getParams() as $param) {
                $writer->startElement('param');
                $writer->startElement('value');
                $param->write($writer, $this->timezone, $this->stringTag);
                $writer->endElement();
                $writer->endElement();
            }
            $writer->endElement();
        }
        $writer->endElement();
        $result = $this->finalizeWrite($writer);
        return $result;
    }

    /// \copydoc fpoirotte::XRL::EncoderInterface::encodeError()
    public function encodeError(\Exception $error)
    {
        $writer = $this->getWriter();
        $writer->startElement('methodResponse');
        $writer->startElement('fault');
        $writer->startElement('value');
        $exc = \fpoirotte\XRL\NativeEncoder::convert($error);
        $exc->write($writer, $this->timezone, $this->stringTag);
        $writer->endElement();
        $writer->endElement();
        $writer->endElement();
        $result = $this->finalizeWrite($writer);
        return $result;
    }

    /// \copydoc fpoirotte::XRL::EncoderInterface::encodeResponse()
    public function encodeResponse($response)
    {
        if (!($response instanceof \fpoirotte\XRL\Types\AbstractType)) {
            throw new \InvalidArgumentException('Invalid response');
        }

        $writer = $this->getWriter();
        $writer->startElement('methodResponse');
        $writer->startElement('params');
        $writer->startElement('param');
        $writer->startElement('value');
        $response->write($writer, $this->timezone, $this->stringTag);
        $writer->endElement();
        $writer->endElement();
        $writer->endElement();
        $writer->endElement();
        $result = $this->finalizeWrite($writer);
        return $result;
    }
}
