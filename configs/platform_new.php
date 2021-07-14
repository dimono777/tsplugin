<?php

use tradersoft\helpers\Platform;

return [
    'urls' => [
        'trade' => [
            Platform::URL_BASE_ID => [
                'uri' => '/',
                'title' => 'Base page',
            ],
            Platform::URL_TRADE_ID => [
                'uri' => '/',
                'title' => 'Trading Room',
            ],
            Platform::URL_CFD_ID => [
                'uri' => '',
                'title' => 'CFD',
            ],
            Platform::URL_BINARY_ID => [
                'uri' => '',
                'title' => 'Binary',
            ],
            Platform::URL_SWITCH => [
                'uri' => '/trader/switch_account?is_demo=1',
                'title' => 'Switch to Demo Account',
            ],
        ],
        'payments' => [
            Platform::URL_DEPOSIT_ID => [
                'uri' => '/account/deposit',
                'title' => 'Deposit',
            ],
            Platform::URL_WITHDRAW_ID => [
                'uri' => '/account/withdrawal',
                'title' => 'Withdrawal',
            ],
        ],
        'reports' => [
            Platform::URL_STAT_CFD => [
                'uri' => '',
                'title' => 'Reports',
            ],
        ],
        'other' => [
            Platform::URL_MINI_CHART_FRAME => [
                'uri' => '/cfd/cfd_chart_frame',
                'title' => 'CFD chart frame',
            ],
            Platform::URL_VERIFICATION => [
                'uri' => '/account/verification',
                'title' => 'Account Verification',
            ],
            Platform::URL_CHANGE_PASSWORD => [
                'uri' => '/account/change_password',
                'title' => 'Change Password',
            ],
            Platform::URL_EDIT_DETAILS => [
                'uri' => '/account/details',
                'title' => 'Account Details',
            ],
            Platform::URL_SETTING => [
                'uri' => '',
                'title' => 'Settings',
            ],
        ],
        'scripts' => [
            Platform::URL_SCRIPT_USERS_INFO => [
                'uri' => '/js/site/usersinfo.js',
                'title' => 'Users Info Script',
            ],
            Platform::URL_SCRIPT_ASSETS_RSS => [
                'uri' => '/js/site/assetsrss.js',
                'title' => 'Assets rss Script',
            ],
            Platform::URL_SCRIPT_WITHDRAWAL => [
                'uri' => '/js/site/withdrawal.js',
                'title' => 'Withdrawal Script',
            ],
            Platform::URL_SCRIPT_CRM_LIB => [
                'uri' => '/js/site/crmlib.js',
                'title' => 'CRM lib Script',
            ],
        ],
    ],
    'subDomain' => 'trading',
    'scriptSubDomain' => 'media-trading-common',
];