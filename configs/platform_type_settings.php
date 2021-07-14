<?php

use tradersoft\helpers\system\PlatformTypeSettings;

/**
 * preset default settings for platform types
 */
return [
    PlatformTypeSettings::CLASSIC => [
        'common_subdomain' => 'trade',
        'scripts_subdomain' => 'trade-media',
        'url_base_page' => '/',
        'url_trading_room' => '/trade',
        'url_cfd' => '/cfd-tab',
        'url_binary' => '/binary-tab',
        'url_switch_demo_account' => '/switch',
        'url_deposit' => '/deposit-funds',
        'url_withdrawal' => '/withdraw',
        'url_reports' => '/stat/index_cfd?for_site=1',
        'url_cfd_chart_frame' => '/cfd/cfd_chart_frame',
        'url_account_verification' => '/verification',
        'url_change_password' => '/change_password',
        'url_account_details' => '/edit-details',
        'url_settings' => '/setting?for_site=1',
        'url_script_users_info' => '/js/lib/wl/go.socket.js',
        'url_script_assets_rss' => '/js/lib/wl/assetsrss.js',
        'url_script_withdrawal' => '/js/lib/wl/withdrawal.js',
        'url_script_crm_lib' => '/js/lib/wl/crmlib.js',
    ],
    PlatformTypeSettings::MODERN => [
        'common_subdomain' => 'trading',
        'scripts_subdomain' => 'media-trading-common',
        'url_base_page' => '/',
        'url_trading_room' => '/',
        'url_cfd' => '/cfd-tab',
        'url_binary' => '',
        'url_switch_demo_account' => '/account/switch?is_demo=1',
        'url_deposit' => '/account/deposit',
        'url_withdrawal' => '/account/withdrawal',
        'url_reports' => '',
        'url_cfd_chart_frame' => '/cfd/cfd_chart_frame',
        'url_account_verification' => '/account/verification',
        'url_change_password' => '/account/change_password',
        'url_account_details' => '/account/details',
        'url_settings' => '',
        'url_script_users_info' => '/js/site/go.socket.js',
        'url_script_assets_rss' => '/js/site/assetsrss.js',
        'url_script_withdrawal' => '/js/site/withdrawal.js',
        'url_script_crm_lib' => '/js/site/crmlib.js',
    ],
];