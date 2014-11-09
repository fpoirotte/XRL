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

class Faults implements \Serializable
{
    protected static $faults = array(
        'NOT_WELL_FORMED' => array(
            'code'  => -32700,
            'msg'   => 'parse error. not well formed',
        ),
        'UNSUPPORTED_ENCODING' => array(
            'code'  => -32701,
            'msg'   => 'parse error. unsupported encoding',
        ),
        'INVALID_CHARACTER' => array(
            'code'  => -32702,
            'msg'   => 'parse error. invalid character for encoding',
        ),
        'INVALID_XML_RPC' => array(
            'code'  => -32600,
            'msg'   => 'server error. invalid xml-rpc. not conforming to spec',
        ),
        'METHOD_NOT_FOUND' => array(
            'code'  => -32601,
            'msg'   => 'server error. requested method not found',
        ),
        'INVALID_PARAMETERS' => array(
            'code'  => -32602,
            'msg'   => 'server error. invalid method parameters',
        ),
        'INTERNAL_ERROR' => array(
            'code'  => -32603,
            'msg'   => 'server error. internal xml-rpc error',
        ),
        'APPLICATION_ERROR' => array(
            'code'  => -32500,
            'msg'   => 'application error',
        ),
        'SYSTEM_ERROR' => array(
            'code'  => -32400,
            'msg'   => 'system error',
        ),
        'TRANSPORT_ERROR' => array(
            'code'  => -32300,
            'msg'   => 'transport error',
        ),
    );

    const NOT_WELL_FORMED       = 'NOT_WELL_FORMED';
    const UNSUPPORTED_ENCODING  = 'UNSUPPORTED_ENCODING';
    const INVALID_CHARACTER     = 'INVALID_CHARACTER';
    const INVALID_XML_RPC       = 'INVALID_XML_RPC';
    const METHOD_NOT_FOUND      = 'METHOD_NOT_FOUND';
    const INVALID_PARAMETERS    = 'INVALID_PARAMETERS';
    const INTERNAL_ERROR        = 'INTERNAL_ERROR';
    const APPLICATION_ERROR     = 'APPLICATION_ERROR';
    const SYSTEM_ERROR          = 'SYSTEM_ERROR';
    const TRANSPORT_ERROR       = 'TRANSPORT_ERROR';

    final private function __construct()
    {
    }

    final public function serialize()
    {
        return null;
    }

    final public function unserialize($serialized)
    {
    }

    public static function get($name, $exc = null)
    {
        if (!isset(self::$faults[$fault])) {
            throw new \InvalidArgumentException('Unknown interoperability fault');
        }
        $params = self::$faults[$fault];
        return new \Exception($params['msg'], $params['code'], $exc);
    }
}
