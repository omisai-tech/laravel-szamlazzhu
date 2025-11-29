<?php

use Omisai\Szamlazzhu\SzamlaAgentUtil;

describe('SzamlaAgentUtil', function () {
    it('has correct DEFAULT_ADDED_DAYS constant', function () {
        expect(SzamlaAgentUtil::DEFAULT_ADDED_DAYS)->toBe(8);
    });

    it('returns date time with milliseconds', function () {
        $dateTime = SzamlaAgentUtil::getDateTimeWithMilliseconds();

        expect($dateTime)->toBeString();
        expect(strlen($dateTime))->toBeGreaterThan(10);
    });

    it('formats xml from SimpleXMLElement', function () {
        $simpleXml = new \SimpleXMLElement('<root><child>value</child></root>');
        $formatted = SzamlaAgentUtil::formatXml($simpleXml);

        expect($formatted)->toBeInstanceOf(\DOMDocument::class);
    });

    it('formats response xml from string', function () {
        $xmlString = '<?xml version="1.0"?><root><child>value</child></root>';
        $formatted = SzamlaAgentUtil::formatResponseXml($xmlString);

        expect($formatted)->toBeInstanceOf(\DOMDocument::class);
    });

    it('checks valid xml and returns empty array for valid xml', function () {
        $validXml = '<?xml version="1.0"?><root><child>value</child></root>';
        $errors = SzamlaAgentUtil::checkValidXml($validXml);

        expect($errors)->toBeArray();
        expect($errors)->toBeEmpty();
    });

    it('checks valid xml and returns errors for invalid xml', function () {
        $invalidXml = '<root><child>value</root>';
        $errors = SzamlaAgentUtil::checkValidXml($invalidXml);

        expect($errors)->toBeArray();
        expect($errors)->not->toBeEmpty();
    });

    it('converts data to json', function () {
        $data = ['key' => 'value', 'number' => 123];
        $json = SzamlaAgentUtil::toJson($data);

        expect($json)->toBeString();
        expect($json)->toBe('{"key":"value","number":123}');
    });

    it('converts data to array', function () {
        $data = (object) ['key' => 'value', 'number' => 123];
        $array = SzamlaAgentUtil::toArray($data);

        expect($array)->toBeArray();
        expect($array)->toHaveKey('key', 'value');
        expect($array)->toHaveKey('number', 123);
    });

    it('adds child array to xml node', function () {
        $xml = new \SimpleXMLElement('<root></root>');
        $data = ['child1' => 'value1', 'child2' => 'value2'];

        SzamlaAgentUtil::addChildArray($xml, 'parent', $data);

        expect($xml->parent)->not->toBeNull();
        expect((string) $xml->parent->child1)->toBe('value1');
        expect((string) $xml->parent->child2)->toBe('value2');
    });

    it('adds nested child array to xml node', function () {
        $xml = new \SimpleXMLElement('<root></root>');
        $data = [
            'level1' => [
                'level2' => 'nestedValue'
            ]
        ];

        SzamlaAgentUtil::addChildArray($xml, 'parent', $data);

        expect((string) $xml->parent->level1->level2)->toBe('nestedValue');
    });

    it('removes namespaces from xml', function () {
        $xmlWithNamespace = new \SimpleXMLElement('<ns:root xmlns:ns="http://example.com"><ns:child>value</ns:child></ns:root>');
        $cleanedXml = SzamlaAgentUtil::removeNamespaces($xmlWithNamespace);

        expect($cleanedXml)->toBeInstanceOf(\SimpleXMLElement::class);
        expect((string) $cleanedXml->child)->toBe('value');
    });
});
