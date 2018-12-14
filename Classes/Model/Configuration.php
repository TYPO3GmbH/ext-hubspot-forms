<?php
declare(strict_types=1);

/*
 * This file is part of the package t3g/hubspot_forms.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace T3G\HubspotForms\Model;

use T3G\HubspotForms\Exceptions\InvalidConfigurationException;

class Configuration
{
    private $httpOptions;
    private $baseUrl = '';
    private $humweeeKey = '';

    public function __construct()
    {
        if ($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['hubspot_forms']['baseUrl'] ?? false) {
            $this->baseUrl = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['hubspot_forms']['baseUrl'];
        } else {
            $this->baseUrl = (string)getenv('APP_HUBSPOT_MIDDLEWARE_BASEURL');
        }
        if ($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['hubspot_forms']['humweeekey'] ?? false) {
            $this->humweeeKey = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['hubspot_forms']['humweeekey'];
        } elseif (getenv('APP_HUBSPOT_FORM_FRAMEWORK_HUMWEEE_KEY')) {
            $this->humweeeKey = (string)getenv('APP_HUBSPOT_FORM_FRAMEWORK_HUMWEEE_KEY');
        }
        $this->httpOptions = $this->configureHttpOptions();
        $this->validateConfiguration();
    }

    /**
     * @return array
     */
    public function getHttpOptions(): array
    {
        return $this->httpOptions;
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @return string
     */
    public function getHumweeeKey(): string
    {
        return $this->humweeeKey;
    }

    /**
     * @return array
     */
    private function configureHttpOptions(): array
    {
        $httpOptions = $GLOBALS['TYPO3_CONF_VARS']['HTTP'] ?? [];
        $httpOptions['verify'] = filter_var($httpOptions['verify'] ?? true, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true;
        $httpOptions['base_uri'] = $this->baseUrl;
        $httpOptions['http_errors'] = true;
        return $httpOptions;
    }

    private function validateConfiguration(): void
    {
        if (empty($this->humweeeKey)) {
            throw new InvalidConfigurationException(
                'Missing humweeekey. Configure via $GLOBALS[\'TYPO3_CONF_VARS\'][\'EXTENSIONS\'][\'hubspot_forms\'][\'humweeekey\'] or as env var \'APP_HUBSPOT_FORM_FRAMEWORK_HUMWEEE_KEY\'',
                1544715216
            );
        }
        if (empty($this->baseUrl)) {
            throw new InvalidConfigurationException(
                'Missing baseURL. Configure via $GLOBALS[\'TYPO3_CONF_VARS\'][\'EXTENSIONS\'][\'hubspot_forms\'][\'baseUrl\'] or as env var \'APP_HUBSPOT_MIDDLEWARE_BASEURL\'',
                1544715217
            );
        }
    }
}
