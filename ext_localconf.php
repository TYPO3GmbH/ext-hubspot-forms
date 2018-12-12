<?php

/*
 * This file is part of the package t3g/hubspot_forms.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(
    function () {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
            trim(
                '
plugin.tx_form {
    settings {
        yamlConfigurations {
            1483353751 = EXT:hubspot_forms/Configuration/Yaml/HubspotFrontend.yaml
        }
    }
}

module.tx_form {
    settings {
        yamlConfigurations {
            1483353751 = EXT:hubspot_forms/Configuration/Yaml/HubspotBackend.yaml
        }
    }
}
'
            )
        );
    }
);
