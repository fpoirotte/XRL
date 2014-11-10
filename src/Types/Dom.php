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

namespace fpoirotte\XRL\Types;

/**
 * \brief
 *      The XML-RPC "base64" type.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class Dom extends \fpoirotte\XRL\Types\AbstractType
{
    public function __toString()
    {
        return $this->value->asXML();
    }

    /// \copydoc fpoirotte::XRL::Types::AbstractType::set()
    public function set($value)
    {
        if (!is_object($value)) {
            throw new \InvalidArgumentException('Expected an object');
        }

        if ($value instanceof \DOMNode) {
            $value = simplexml_import_dom($value);
        }

        if ($value instanceof \XMLWriter) {
            $value = new \SimpleXMLElement($value->outputMemory(), LIBXML_NONET);
        }

        if (!($value instanceof \SimpleXMLElement)) {
            throw new \InvalidArgumentException('Expected a SimpleXMLElement object');
        }

        $this->value = $value;
    }

    /// \copydoc fpoirotte::XRL::Types::AbstractType::write()
    public function write(\XMLWriter $writer, \DateTimeZone $timezone, $stringTag)
    {
        $xml = $this->value->asXML();

        // Not all combinations of PHP/libxml support stripping
        // the XML declaration. So, we do it ourselves here.
        if (strlen($xml) >= 6 && !strncmp($xml, '<?xml', 5) &&
            strpos(" \t\r\n", $xml[5]) !== false) {
            $xml = (string) substr($xml, strpos($xml, '?>') + 2);
        }

        $writer->startElementNS(
            'ex',
            'dom',
            'http://ws.apache.org/xmlrpc/namespaces/extensions'
        );
        $writer->writeRaw($xml);
        $writer->fullEndElement();
    }

    /// \copydoc fpoirotte::XRL::Types::AbstractType::parse()
    protected static function parse($value, \DateTimeZone $timezone = null)
    {
        return simplexml_load_string($value, '\\SimpleXMLElement', LIBXML_NONET);
    }
}
