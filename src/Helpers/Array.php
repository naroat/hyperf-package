<?php
declare(strict_types=1);

namespace Taoran\HyperfPackage\Helpers;

/**
 * xml to array 转换.
 * @param type $xml
 * @return type
 */
if (! function_exists('xml2array')) {
    function xml2array($xml)
    {
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }
}