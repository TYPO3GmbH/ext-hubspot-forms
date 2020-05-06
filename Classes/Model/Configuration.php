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
    private $humweeeUser = '';
    private $humweeeToken = '';
    private $humweeeReferer = '';

    public function __construct()
    {
        if ($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['hubspot_forms']['baseUrl'] ?? false) {
            $this->baseUrl = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['hubspot_forms']['baseUrl'];
        } else {
            $this->baseUrl = (string)getenv('APP_HUBSPOT_MIDDLEWARE_BASEURL');
        }
        if ($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['hubspot_forms']['humweeeUser'] ?? false) {
            $this->humweeeUser = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['hubspot_forms']['humweeeUser'];
        } elseif (getenv('APP_HUBSPOT_FORM_FRAMEWORK_HUMWEEE_USER')) {
            $this->humweeeUser = (string)getenv('APP_HUBSPOT_FORM_FRAMEWORK_HUMWEEE_USER');
        }
        if ($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['hubspot_forms']['humweeeToken'] ?? false) {
            $this->humweeeToken = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['hubspot_forms']['humweeeToken'];
        } elseif (getenv('APP_HUBSPOT_FORM_FRAMEWORK_HUMWEEE_TOKEN')) {
            $this->humweeeToken = (string)getenv('APP_HUBSPOT_FORM_FRAMEWORK_HUMWEEE_TOKEN');
        }
        if ($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['hubspot_forms']['humweeeReferer'] ?? false) {
            $this->humweeeReferer = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['hubspot_forms']['humweeeReferer'];
        } elseif (getenv('APP_HUBSPOT_FORM_FRAMEWORK_HUMWEEE_REFERER')) {
            $this->humweeeReferer = (string)getenv('APP_HUBSPOT_FORM_FRAMEWORK_HUMWEEE_REFERER');
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
    public function getHumweeeUser(): string
    {
        return $this->humweeeUser;
    }

    /**
     * @return string
     */
    public function getHumweeeToken(): string
    {
        return $this->humweeeToken;
    }

    /**
     * @return string
     */
    public function getHumweeeReferer(): string
    {
        return $this->humweeeReferer;
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
        if (empty($this->humweeeUser)) {
            throw new InvalidConfigurationException(
                'Missing humweee user. Configure via $GLOBALS[\'TYPO3_CONF_VARS\'][\'EXTENSIONS\'][\'hubspot_forms\'][\'humweeeUser\'] or as env var \'APP_HUBSPOT_FORM_FRAMEWORK_HUMWEEE_USER\'',
                1588754250
            );
        }
        if (empty($this->humweeeToken)) {
            throw new InvalidConfigurationException(
                'Missing humweee token. Configure via $GLOBALS[\'TYPO3_CONF_VARS\'][\'EXTENSIONS\'][\'hubspot_forms\'][\'humweeeToken\'] or as env var \'APP_HUBSPOT_FORM_FRAMEWORK_HUMWEEE_TOKEN\'',
                1588754255
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
