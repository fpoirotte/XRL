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

class HeadersProcessor
{
    /**
     * Process HTTP headers and return information
     * about the content's type and parameters.
     *
     * \param array $headers
     *      Headers to process.
     *
     * \retval array
     *      Metadata about the contents.
     *      This array includes the content's type
     *      under the "type" key (if available),
     *      as well as parameters for that type
     *      under the "params" key.
     */
    public static function process(array $headers)
    {
        $meta = array('type' => null, 'params' => array());

        foreach ($headers as $header) {
            if (strpos($header, ':') === false) {
                // Just a failsafe in case the input
                // is totally messed up.
                continue;
            }

            list($key, $value) = explode(':', $header, 2);
            if (strcasecmp($key, 'Content-Type')) {
                // We only care about the Content-Type header.
                continue;
            }

            // Overwrite metadata from a previous Content-Type header
            // and prepare the raw data.
            $meta = array('type' => null, 'params' => array());
            $value  = trim($value, " \t");
            $parts  = explode(';', $value, 2);

            // Retrieve content type & prepare parameters.
            $type = rtrim($parts[0], " \t");
            if (count($parts) === 2) {
                $value = ';' . $parts[1];
            } else {
                $meta['type'] = $parts[0];
                continue;
            }

            // From RFC 2045.
            $tspecials  = "()<>@,;:\\\"/[]?=";
            // From RFC 822.
            $CTL        =   "\000\001\002\003\004\005\006\007" .
                            "\010\011\012\013\014\015\016\017" .
                            "\020\021\022\023\024\025\026\027" .
                            "\030\031\032\033\034\035\036\037\177";

            $params = array();
            while (strlen($value)) {
                // Parse ';' delimiter.
                if ($value[0] !== ';') {
                    break;
                }
                $value      = ltrim(substr($value, 1), " \t");

                // Parse attribute (token).
                $tokenLen   = strcspn($value, " $CTL$tspecials");
                $attribute  = substr($value, 0, $tokenLen);
                $value      = ltrim(substr($value, $tokenLen), " \t");
                if ($attribute === false || $value === '') {
                    break;
                }

                // Parse '=' delimiter.
                if ($value[0] !== '=') {
                    break;
                }
                $value      = ltrim(substr($value, 1), " \t");

                // Parse value.
                if ($value === '') {
                    break;
                }
                if ($value[0] === '"') {
                    // Parse quoted-string.
                    $attrValue = '"';
                    for ($i = 1, $len = strlen($value); $i < $len; $i++) {
                        if ($value[$i] === '\\') {
                            if (++$i >= $len) {
                                // Corrupt header (EOL after escape char).
                                // Process the next header.
                                continue 3;
                            }
                            $attrValue .= $value[$i];
                            continue;
                        } elseif ($value[$i] === "\r") {
                            // Corrupt header (unescaped CR).
                            // Process the next header.
                            continue 3;
                        } else {
                            $attrValue .= $value[$i];
                            if ($value[$i] === '"') {
                                $value = (string) substr($value, ++$i);
                                break; // Normal end of quoted-string.
                            }
                        }
                    }

                    if ($attrValue === '"' || substr($attrValue, -1) !== '"') {
                        // Corrupt header (unterminated quoted-string).
                        // Process the next header.
                        continue 2;
                    }
                    $attrValue  = (string) substr($attrValue, 1, -1);
                } else {
                    // Parse token.
                    $tokenLen   = strcspn($value, " $CTL$tspecials");
                    $attrValue  = substr($value, 0, $tokenLen);
                    $value      = ltrim(substr($value, $tokenLen), " \t");
                }

                // Parse (nested) comments.
                if (strlen($value) > 0 && $value[0] === '(') {
                    $depth = 1;
                    for ($i = 1, $len = strlen($value); $i < $len; $i++) {
                        if ($value[$i] === '\\') {
                            $i++; // Skip next character.
                        } elseif ($value[$i] === '(') {
                            $depth++; // Nested comment.
                        } elseif ($value[$i] === ')') {
                            $depth--; // Unnest.
                            if (!$depth) {
                                $value = ltrim(substr($value, $i + 1), " \t");
                                break; // End of comment(s).
                            }
                        }
                    }

                    if ($depth) {
                        // Corrupt header (unterminated comment).
                        // Process the next header.
                        continue 2;
                    }
                }

                $params[strtolower($attribute)] = $attrValue;
            }

            $meta['type']   = $type;
            $meta['params'] = $params;
        }
        return $meta;
    }
}
