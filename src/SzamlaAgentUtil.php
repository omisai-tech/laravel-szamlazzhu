<?php

namespace Omisai\Szamlazzhu;

class SzamlaAgentUtil
{
    public const DEFAULT_ADDED_DAYS = 8;

    /**
     * @throws \ReflectionException
     */
    public static function getXmlFileName(string $prefix, string $name, SzamlaAgent $agent, object $entity)
    {
        if (!empty($name) && !empty($entity)) {
            $name .= '-'.(new \ReflectionClass($entity))->getShortName();
        }

        $hash = $agent->getSingleton() ? '' : spl_object_hash($agent);

        return $prefix.'-'.strtolower($name).'-'.self::getDateTimeWithMilliseconds().'.xml';

        return $prefix.'-'.strtolower($name).'-'.$hash.'-'.self::getDateTimeWithMilliseconds().'.xml';
    }

    public static function getDateTimeWithMilliseconds(): string
    {
        return date('Y-m-d_H:i:s').substr(microtime(false), 2, 5);
    }

    public static function formatXml(\SimpleXMLElement $simpleXMLElement): \DOMDocument
    {
        $xmlDocument = new \DOMDocument('1.0');
        $xmlDocument->preserveWhiteSpace = false;
        $xmlDocument->formatOutput = true;
        $xmlDocument->loadXML($simpleXMLElement->asXML());

        return $xmlDocument;
    }

    public static function formatResponseXml(string $response): \DOMDocument
    {
        $xmlDocument = new \DOMDocument('1.0');
        $xmlDocument->preserveWhiteSpace = false;
        $xmlDocument->formatOutput = true;
        $xmlDocument->loadXML($response);

        return $xmlDocument;
    }

    public static function checkValidXml($xmlContent): array
    {
        libxml_use_internal_errors(true);

        $doc = new \DOMDocument('1.0', 'utf-8');
        $doc->loadXML($xmlContent);

        $result = libxml_get_errors();
        libxml_clear_errors();

        return $result;
    }

    public static function toJson($data): mixed
    {
        return json_encode($data);
    }

    public static function toArray($data): mixed
    {
        return json_decode(self::toJson($data), true);
    }

    public static function addChildArray(\SimpleXMLElement $xmlNode, string $name, array $data): void
    {
        $node = $xmlNode->addChild($name);

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                self::addChildArray($node, $key, $value);
            } else {
                $node->addChild($key, $value);
            }
        }
    }

    /**
     * @return \SimpleXMLElement $xmlNode
     */
    public static function removeNamespaces(\SimpleXMLElement $xmlNode)
    {
        $xmlString = $xmlNode->asXML();
        $cleanedXmlString = preg_replace('/(<\/|<)[a-z0-9]+:([a-z0-9]+[ =>])/i', '$1$2', $xmlString);

        return simplexml_load_string($cleanedXmlString);
    }

    /**
     * @throws SzamlaAgentException
     */
    public static function isValidJSON($string): mixed
    {
        // decode the JSON data
        $result = json_decode($string);
        // switch and check possible JSON errors
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                $error = '';
                break;
            case JSON_ERROR_DEPTH:
                $error = 'The maximum stack depth has been exceeded.';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $error = 'Invalid or malformed JSON.';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $error = 'Control character error, possibly incorrectly encoded.';
                break;
            case JSON_ERROR_SYNTAX:
                $error = 'Syntax error, malformed JSON.';
                break;
                // PHP >= 5.3.3
            case JSON_ERROR_UTF8:
                $error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
                break;
                // PHP >= 5.5.0
            case JSON_ERROR_RECURSION:
                $error = 'One or more recursive references in the value to be encoded.';
                break;
                // PHP >= 5.5.0
            case JSON_ERROR_INF_OR_NAN:
                $error = 'One or more NAN or INF values in the value to be encoded.';
                break;
            case JSON_ERROR_UNSUPPORTED_TYPE:
                $error = 'A value of a type that cannot be encoded was given.';
                break;
            default:
                $error = 'Unknown JSON error occured.';
                break;
        }

        if ($error !== '') {
            throw new SzamlaAgentException($error);
        }

        return $result;
    }

    public static function dotCheck($value)
    {
        if (strpos($value, ',') !== false) {
            $value = str_replace(',', '.', $value);
        }

        return $value;
    }
}
