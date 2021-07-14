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
                'uri' => '',
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
                'uri' => '',
                'title' => 'Switch to Demo Account',
            ],
        ],
        'payments' => [
            Platform::URL_DEPOSIT_ID => [
                'uri' => '#deposit',
                'title' => 'Deposit',
            ],
            Platform::URL_WITHDRAW_ID => [
                'uri' => '',
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
                'uri' => '',
                'title' => 'CFD chart frame',
            ],
            Platform::URL_VERIFICATION => [
                'uri' => '',
                'title' => 'Account Verification',
            ],
            Platform::URL_CHANGE_PASSWORD => [
                'uri' => '',
                'title' => 'Change Password',
            ],
            Platform::URL_EDIT_DETAILS => [
                'uri' => '',
                'title' => 'Account Details',
            ],
            Platform::URL_SETTING => [
                'uri' => '',
                'title' => 'Settings',
            ],
        ],
        'scripts' => [
            Platform::URL_SCRIPT_USERS_INFO => [
                'uri' => '/js/lib/wl/usersinfo.js',
                'title' => 'Users Info Script',
            ],
            Platform::URL_SCRIPT_ASSETS_RSS => [
                'uri' => '/js/lib/wl/assetsrss.js',
                'title' => 'Assets rss Script',
            ],
            Platform::URL_SCRIPT_WITHDRAWAL => [
                'uri' => '/js/lib/wl/withdrawal.js',
                'title' => 'Withdrawal Script',
            ],
            Platform::URL_SCRIPT_CRM_LIB => [
                'uri' => '/js/lib/wl/crmlib.js',
                'title' => 'CRM lib Script',
            ],
        ],
    ],
    'subDomain' => 'trading',
    'scriptSubDomain' => 'trade-media',
];