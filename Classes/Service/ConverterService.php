<?php
declare(strict_types = 1);

/*
 * This file is part of the package t3g/hubspot_forms.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace T3G\HubspotForms\Service;

/**
 * Class ConverterService
 */
class ConverterService
{
    private const STRINGIFY = 'stringify-';

    /**
     * Converts EXT:form structure to hubspot endpoint format
     *
     * @param array $formData
     *
     * @return array
     */
    public function convertToHubspotFormat(array $formData): array
    {
        $hubspotData = [[]];
        foreach ($formData as $datum) {
            $parts = [];
            if (isset($datum['hubspotTable'], $datum['hubspotProperty'], $datum['value'])) {
                $hubspotProperty = $datum['hubspotProperty'];
                $hubspotTable = $datum['hubspotTable'];

                if (strpos($hubspotProperty, '.') > 0) {
                    $parts = $this->convertToNestedStructure($hubspotProperty, $datum);
                } else {
                    $value = $datum['value'];
                    if (is_array($datum['value'])) {
                        $value = implode(';', $value);
                    }
                    $parts[$hubspotTable][] = [
                        'value' => $value,
                        'property' => $hubspotProperty,
                    ];
                }
                $hubspotData[] = $parts;
            }
        }
        $hubspotData = array_merge_recursive(...$hubspotData);
        $hubspotData = self::normalizeKeys($hubspotData);
        return $hubspotData;
    }

    /**
     * Convert string foo.1.bar.2 to a nested array structure
     *
     * @param string $hubspotProperty
     * @param array $datum
     * @return array
     */
    protected function convertToNestedStructure(string $hubspotProperty, array $datum): array
    {
        $parts = explode('.', $hubspotProperty);
        $reversed = array_reverse($parts);
        array_walk(
            $reversed,
            function ($v, $k) use (&$parts, $datum) {
                if (is_numeric($v)) {
                    $v = self::STRINGIFY . $v;
                }
                if (!empty($k)) {
                    $parts = [$v => $parts];
                } else {
                    $value = $datum['value'];
                    if (is_array($datum['value'])) {
                        $value = implode(';', $value);
                    }
                    $parts = [
                    [
                        'property' => $v,
                        'value' => $value,
                    ],
                ];
                }
            }
        );
        return $parts;
    }

    /**
     * Normalize Keys (PHP is stupid - numerical keys needed to be prefixed for the nesting
     * - now we need to remove the prefix again)
     *
     * @param array $input
     * @return array
     */
    protected static function normalizeKeys(array $input): array
    {
        $return = [];
        $len = strlen(self::STRINGIFY);
        foreach ($input as $key => $value) {
            if (is_string($key) && strpos($key, self::STRINGIFY) === 0) {
                $key = (int)substr($key, $len);
            }

            if (is_array($value)) {
                $value = self::normalizeKeys($value);
            }

            $return[$key] = $value;
        }
        return $return;
    }
}
