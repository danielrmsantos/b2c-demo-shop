<?php
$stores = [];

$stores['US'] = array(
    'frontends' => array('sao'), // first entry is default TODO move to Yves ???
    'contexts' => array( // different contexts
        // shared settings for all contexts
        '*' => array(
            'timezone' => 'America/Los_Angeles',
            'dateFormat' => array(
                'short' => 'd/m/Y', // short date (01.02.12)
                'medium' => 'd. M Y', // medium Date (01. Feb 2012)
                'rfc' => 'r', // date formatted as described in RFC 2822
                'datetime' => 'Y-m-d H:i:s',
                'sao_long' => 'Y-m-d H:i:s T', // date formatted like datetime but with timezone
            ),
        ),
        // settings for contexts (overwrite shared)
        'yves' => array(),
        'zed' => array(
            'dateFormat' => array(
                'short' => 'Y-m-d', // short date (2012-12-28)
            ),
        ),
    ),
    'locales' => array('en_US', 'de_DE', 'fr_FR'),   // first entry is default
    'countries' => array('US'),   // first entry is default
    'currencyIsoCode' => 'USD', // internal and shop
);

$stores['DE'] = array(
    'frontends' => array('sao'), // first entry is default TODO move to Yves ???
    'contexts' => array( // different contexts
        // shared settings for all contexts
        '*' => array(
            'timezone' => 'America/Los_Angeles',
            'dateFormat' => array(
                'short' => 'd/m/Y', // short date (01.02.12)
                'medium' => 'd. M Y', // medium Date (01. Feb 2012)
                'rfc' => 'r', // date formatted as described in RFC 2822
                'datetime' => 'Y-m-d H:i:s',
                'sao_long' => 'Y-m-d H:i:s T', // date formatted like datetime but with timezone
            ),
        ),
        // settings for contexts (overwrite shared)
        'yves' => array(),
        'zed' => array(
            'dateFormat' => array(
                'short' => 'Y-m-d', // short date (2012-12-28)
            ),
        ),
    ),
    'locales' => array('de_DE'),   // first entry is default
    'countries' => array('DE'),   // first entry is default
    'currencyIsoCode' => 'EUR', // internal and shop
);

return $stores;
