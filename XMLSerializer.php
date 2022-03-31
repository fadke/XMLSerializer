#!/usr/bin/php
<?php

declare(strict_types = 1);

namespace fadke;

use \DOMDocument;
use \DOMElement;

/**
 * Class XMLSerializer
 * @package fadke
 */
class XMLSerializer
{
    /**
     * @var string
     */
    public const VALUE = '@value';

    /**
     * @var string
     */
    public const ATTRIBUTES = '@attributes';

    /**
     * @var string
     */
    public const CUSTOM_NAME = '@custom-name';

    /**
     * @var string
     */
    private $rootName;

    /**
     * @var \DOMDocument
     */
    private $dom;

    /**
     * @param string $rootName
     * @param string $version
     * @param string $encoding
     */
    public function __construct(string $rootName, string $version = '1.0', string $encoding = 'UTF-8')
    {
        $this->rootName = $rootName;
        $this->dom = new DOMDocument($version, $encoding);
    }

    /**
     * @param array $params
     * @return void
     */
    public function serialize(array $params)
    {
        $xml = $this->generateXML($this->rootName, $params);
        return $this->dom->saveXML($xml);
    }

    /**
     * @param string $node
     * @param array $params
     * @return \DOMElement
     */
    private function generateXML(string $node, array $params): DOMElement
    {
        $root = $this->dom->createElement($node);

        foreach ($params as $name => $options) {
            [$value, $attributes, $customName] = $this->parseOptions($options);

            $nodeName = $customName ?? $name;

            if (is_array($value)) {
                $element = $this->generateXML($nodeName, $value);
            } else {
                $element = $this->dom->createElement($nodeName, $value);
            }

            if ($attributes) {
                foreach ($attributes as $attr => $val) {
                    $element->setAttribute($attr, $val);
                }
            }

            $root->appendChild($element);
        }

        return $root;
    }

    /**
     * @param $options
     * @return array
     */
    private function parseOptions($options): array
    {
        $value = $options;
        $attributes = [];
        $name = null;

        if (!is_array($value)) {
            return [$value, $attributes, $name];
        }

        if (!empty($value[self::ATTRIBUTES])) {
            $attributes = $value[self::ATTRIBUTES];

            unset($value[self::ATTRIBUTES]);
        }

        if (!empty($value[self::CUSTOM_NAME])) {
            $name = $value[self::CUSTOM_NAME];

            unset($value[self::CUSTOM_NAME]);
        }

        if (array_key_exists(self::VALUE, $value)) {
            $value = $value[self::VALUE];
        }

        return [$value, $attributes, $name];
    }
}
