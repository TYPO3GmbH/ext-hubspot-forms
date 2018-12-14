<?php
declare(strict_types=1);

/*
 * This file is part of the package t3g/hubspot_forms.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace T3G\HubspotForms\Tests\Unit;

use T3G\HubspotForms\Exceptions\InvalidConfigurationException;
use T3G\HubspotForms\Model\Configuration;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ConfigurationTest extends UnitTestCase
{

    /**
     * @test
     */
    public function configurationConstructorThrowsExceptionOnMissingHumweeekey(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['hubspot_forms']['baseUrl'] = 'https://base.url';
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(1544715216);
        new Configuration();
    }

    /**
     * @test
     */
    public function configurationConstructorThrowsExceptionOnMissingBaseUrl(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['hubspot_forms']['humweeekey'] = 'somekey';
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(1544715217);
        new Configuration();
    }

    /**
     * @test
     */
    public function configurationConstructorSetsConfigFromGlobals(): void
    {
        $key = 'somekey';
        $base = 'https://base.url';
        $httpOptions = [
            'verify' => true,
        ];
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['hubspot_forms']['humweeekey'] = $key;
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['hubspot_forms']['baseUrl'] = $base;
        $GLOBALS['TYPO3_CONF_VARS']['HTTP'] = $httpOptions;

        $config = new Configuration();

        self::assertSame($key, $config->getHumweeeKey());
        self::assertSame($base, $config->getBaseUrl());
        self::assertSame($httpOptions['verify'], $config->getHttpOptions()['verify']);
    }
    /**
     * @test
     */
    public function configurationConstructorSetsConfigFromEnvVars(): void
    {
        $key = 'somekey';
        $base = 'https://base.url';
        $httpOptions = [
            'verify' => true,
        ];
        $GLOBALS['TYPO3_CONF_VARS']['HTTP'] = $httpOptions;
        putenv('APP_HUBSPOT_MIDDLEWARE_BASEURL=' . $base);
        putenv('APP_HUBSPOT_FORM_FRAMEWORK_HUMWEEE_KEY=' . $key);

        $config = new Configuration();

        self::assertSame($key, $config->getHumweeeKey());
        self::assertSame($base, $config->getBaseUrl());
        self::assertSame($httpOptions['verify'], $config->getHttpOptions()['verify']);
    }

    /**
     * @test
     */
    public function configurationSetsBaseHttpOptions(): void
    {
        // base setup to avoid errors
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['hubspot_forms']['humweeekey'] = 'somekey';
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['hubspot_forms']['baseUrl'] = 'https://base.url';

        $GLOBALS['TYPO3_CONF_VARS']['HTTP'] = [
            'verify' => true,
        ];

        $config = new Configuration();

        self::assertSame(
            [
                'verify' => true,
                'base_uri' => 'https://base.url',
                'http_errors' => true,
            ],
            $config->getHttpOptions()
        );
    }

    public function httpVerifyDataProvider(): array
    {
        return [
            'bool true' => [
                true,
                true
            ],
            'bool false' => [
                false,
                false
            ],
            'string empty' => [
                '',
                false
            ],
            'string not empty' => [
                'notempty',
                true
            ],
            'int 0' => [
                0,
                false
            ],
            'int > 0' => [
                7,
                true
            ],
            'array empty' => [
                [],
                true
            ],
            'array not empty' => [
                ['notempty'],
                true
            ],
            'object' => [
                new \stdClass(),
                true
            ],
        ];
    }

    /**
     * @test
     * @dataProvider httpVerifyDataProvider
     */
    public function configurationSetsHttpOptionVerifyToBool($input, $expected): void
    {
        // base setup to avoid errors
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['hubspot_forms']['humweeekey'] = 'somekey';
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['hubspot_forms']['baseUrl'] = 'https://base.url';
        $GLOBALS['TYPO3_CONF_VARS']['HTTP'] = [
            'verify' => $input,
        ];
        $configuration = new Configuration();

        self::assertSame($expected, $configuration->getHttpOptions()['verify']);
    }
}
