<?php

/*
 * This file is part of the package t3g/hubspot_forms.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace T3G\HubspotForms\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use T3G\HubspotForms\Model\Configuration;
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

    /**
     * @var string
     */
    protected $humweeeKey = '';

    public function __construct(LoggerInterface $logger = null)
    {
        $config = GeneralUtility::makeInstance(Configuration::class);
        $this->humweeeKey = $config->getHumweeeKey();
        $this->client = new Client($config->getHttpOptions());
        $this->logger = $logger ?? GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
    }

    /**
     * @param array $batchData
     * @return null|ResponseInterface
     */
    public function genericFormData(array $batchData): ?ResponseInterface
    {
        $requestParams = [
            'humweeekey' => $this->humweeeKey,
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
