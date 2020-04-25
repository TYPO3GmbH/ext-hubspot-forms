<?php
declare(strict_types=1);

/*
 * This file is part of the package t3g/hubspot_forms.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace T3G\HubspotForms\Finishers;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use T3G\HubspotForms\Service\ConverterService;
use T3G\HubspotForms\Service\HubspotApiService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Form\Domain\Finishers\AbstractFinisher;
use TYPO3\CMS\Form\Domain\Model\FormElements\FormElementInterface;

/**
 * Class HubspotFinisher
 * Scope: frontend
 */
class HubspotFinisher extends AbstractFinisher implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(string $finisherIdentifier = '')
    {
        $this->defaultOptions = [
            'hubspotTableProperty' => 'hubspotTable',
            'hubspotPropertyProperty' => 'hubspotProperty',
        ];
        parent::__construct($finisherIdentifier);
    }

    /**
     * Collect all form elements with hubspot properties
     * and submit it.
     *
     * @return void
     * @throws \InvalidArgumentException
     * @throws \GuzzleHttp\Exception\ClientException
     */
    protected function executeInternal()
    {
        $formRuntime = $this->finisherContext->getFormRuntime();

        $hubSpotTableProperty = $this->parseOption('hubspotTableProperty');
        $hubSpotPropertyProperty = $this->parseOption('hubspotPropertyProperty');
        $hubSpotData = [];

        foreach ($formRuntime->getFormDefinition()->getRenderablesRecursively() as $element) {
            if (!$element instanceof FormElementInterface || empty($element->getProperties()[$hubSpotPropertyProperty])) {
                continue;
            }

            $identifier = $element->getIdentifier();
            $value = $formRuntime[$identifier];
            $re = '/{([^{}]*)}/m';
            if (\is_string($value)) {
                preg_match_all($re, $value, $matches, PREG_SET_ORDER, 0);
                foreach ($matches as $match) {
                    if (!empty($formRuntime[$match[1]])) {
                        $value = str_replace($match[0], $formRuntime[$match[1]], $value);
                    }
                }
            }
            $hubSpotData[$identifier] = [
                'identifier' => $identifier,
                'value' => $value,
                'hubspotTable' => $element->getProperties()[$hubSpotTableProperty],
                'hubspotProperty' => $element->getProperties()[$hubSpotPropertyProperty],
            ];
        }

        if (!empty($hubSpotData)) {
            $response = $this->submitData($hubSpotData);
            if ($response === null) {
                $this->logger->warning('Submitting Data to Hubspot Failed.');
            }
        }
    }

    /**
     * Send the form values to hubspot.
     *
     * @param array $data
     * @return \Psr\Http\Message\ResponseInterface|null
     * @throws \InvalidArgumentException
     * @throws \GuzzleHttp\Exception\ClientException
     */
    protected function submitData(array $data)
    {
        return GeneralUtility::makeInstance(HubspotApiService::class)
            ->genericFormData((new ConverterService())->convertToHubspotFormat($data));
    }
}
