# Hubspot Forms

Create forms with TYPO3 Form Framework and store data in Hubspot.

Needs internal TYPO3 GmbH middleware as endpoint - currently used by
typo3.org and typo3.com.

## Settings

```
    $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['hubspot_forms']['baseUrl'] or getenv('APP_HUBSPOT_MIDDLEWARE_BASEURL')

    $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['hubspot_forms']['humweeeUser'] or getenv('APP_HUBSPOT_FORM_FRAMEWORK_HUMWEEE_USER')
    $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['hubspot_forms']['humweeeToken'] or getenv('APP_HUBSPOT_FORM_FRAMEWORK_HUMWEEE_TOKEN')
    $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['hubspot_forms']['humweeeReferer'] or getenv('APP_HUBSPOT_FORM_FRAMEWORK_HUMWEEE_REFERER')
```

