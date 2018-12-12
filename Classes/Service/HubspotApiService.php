<?php

/*
 * This file is part of the package t3g/querybuilder.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace T3G\HubspotForms\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Handles requests to humweee for communication with hubspot
 * Class HubspotApiService
 */
class HubspotApiService
{

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $url = '';

    /**
     * @var \TYPO3\CMS\Core\Log\Logger
     */
    protected $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        $httpOptions = $GLOBALS['TYPO3_CONF_VARS']['HTTP'];
        $httpOptions['verify'] = filter_var($httpOptions['verify'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $httpOptions['verify'];
        $httpOptions['base_uri'] = getenv('APP_HUBSPOT_MIDDLEWARE_BASEURL');
        $httpOptions['http_errors'] = true;
        $this->client = new Client($httpOptions);
        $this->logger = $logger ?? GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
    }

    /**
     * @param array $batchData
     * @return null|ResponseInterface
     */
    public function genericFormData(array $batchData): ?ResponseInterface
    {
        $requestParams = [
            'humweeekey' => getenv('APP_HUBSPOT_FORM_FRAMEWORK_HUMWEEE_KEY'),
        ];

        try {
            return $this->client->post('/api/genericFormData?' . http_build_query($requestParams), ['json' => $batchData]);
        } catch (ClientException $exception) {
            $this->logger->error($exception->getMessage());
            if (strpos($exception->getMessage(), '401 Unauthorized') === false) {
                throw $exception;
            }
        }
        return null;
    }
}
