<?php
declare(strict_types = 1);

/*
 * This file is part of the package t3g/hubspot_forms.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace T3G\HubspotForms\ViewHelpers;

use TYPO3\CMS\Fluid\ViewHelpers\Form\SelectViewHelper;

/**
 * Class HubspotPropertiesViewHelper
 */
class HubspotPropertiesViewHelper extends SelectViewHelper
{

    /**
     * @return array
     */
    protected function getOptions(): array
    {
        $options = parent::getOptions();
        $options['contact'] = 'Contact';
        $options['deal'] = 'Deal';
        $options['company'] = 'Company';

        return $options;
    }
}
