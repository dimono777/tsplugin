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
                'uri' => '/trade',
                'title' => 'Trading Room',
            ],
            Platform::URL_CFD_ID => [
                'uri' => '/cfd-tab',
                'title' => 'CFD',
            ],
            Platform::URL_BINARY_ID => [
                'uri' => '/binary-tab',
                'title' => 'Binary',
            ],
            Platform::URL_SWITCH => [
                'uri' => '/switch',
                'title' => 'Switch to Demo Account',
            ],
        ],
        'payments' => [
            Platform::URL_DEPOSIT_ID => [
                'uri' => '/deposit-funds',
                'title' => 'Deposit',
            ],
            Platform::URL_WITHDRAW_ID => [
                'uri' => '/withdraw',
                'title' => 'Withdrawal',
            ],
        ],
        'reports' => [
            Platform::URL_STAT_CFD => [
                'uri' => '/stat/index_cfd?for_site=1',
                'title' => 'Reports',
            ],
        ],
        'other' => [
            Platform::URL_MINI_CHART_FRAME => [
                'uri' => '/cfd/cfd_chart_frame',
                'title' => 'CFD chart frame',
            ],
            Platform::URL_VERIFICATION => [
                'uri' => '/verification',
                'title' => 'Account Verification',
            ],
            Platform::URL_CHANGE_PASSWORD => [
                'uri' => '/change_password',
                'title' => 'Change Password',
            ],
            Platform::URL_EDIT_DETAILS => [
                'uri' => '/edit-details',
                'title' => 'Account Details',
            ],
            Platform::URL_SETTING => [
                'uri' => '/setting?for_site=1',
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
    'subDomain' => 'trade',
    'scriptSubDomain' => 'trade-media',
];