<?php

use tradersoft\helpers\Platform;

return [
    Platform::URL_BASE_ID => [
        'ts_setting_name' => 'url_base_page',
        'title' => 'Base page',
        'showInList' => false,
        'isAsset' => false,
    ],
    Platform::URL_BINARY_ID => [
        'ts_setting_name' => 'ts_url_binary_page',
        'title' => 'Binary',
        'showInList' => false,
        'isAsset' => false,
    ],
    Platform::URL_CFD_ID => [
        'ts_setting_name' => 'url_cfd',
        'title' => 'CFD',
        'showInList' => false,
        'isAsset' => false,
    ],
    Platform::URL_TRADE_ID => [
        'ts_setting_name' => 'url_trading_room',
        'title' => 'Trading Room',
        'showInList' => true,
        'isAsset' => false,
    ],
    Platform::URL_DEPOSIT_ID => [
        'ts_setting_name' => 'url_deposit',
        'title' => 'Deposit',
        'showInList' => true,
        'isAsset' => false,
    ],
    Platform::URL_WITHDRAW_ID => [
        'ts_setting_name' => 'url_withdrawal',
        'title' => 'Withdrawal',
        'showInList' => true,
        'isAsset' => false,
    ],
    Platform::URL_MINI_CHART_FRAME => [
        'ts_setting_name' => 'url_cfd_chart_frame',
        'title' => 'CFD chart frame',
        'showInList' => false,
        'isAsset' => false,
    ],
    Platform::URL_VERIFICATION => [
        'ts_setting_name' => 'url_account_verification',
        'title' => 'Account Verification',
        'showInList' => true,
        'isAsset' => false,
    ],
    Platform::URL_CHANGE_PASSWORD => [
        'ts_setting_name' => 'url_change_password',
        'title' => 'Change Password',
        'showInList' => true,
        'isAsset' => false,
    ],
    Platform::URL_EDIT_DETAILS => [
        'ts_setting_name' => 'url_account_details',
        'title' => 'Account Details',
        'showInList' => true,
        'isAsset' => false,
    ],
    Platform::URL_SWITCH => [
        'ts_setting_name' => 'url_switch_demo_account',
        'title' => 'Switch to Demo Account',
        'showInList' => true,
        'isAsset' => false,
    ],
    Platform::URL_SETTING => [
        'ts_setting_name' => 'url_settings',
        'title' => 'Settings',
        'showInList' => true,
        'isAsset' => false,
    ],
    Platform::URL_STAT_CFD => [
        'ts_setting_name' => 'url_reports',
        'title' => 'Reports',
        'showInList' => true,
        'isAsset' => false,
    ],
    Platform::URL_SCRIPT_USERS_INFO => [
        'ts_setting_name' => 'url_script_users_info',
        'title' => 'Users Info Script',
        'showInList' => false,
        'isAsset' => true,
    ],
    Platform::URL_SCRIPT_ASSETS_RSS => [
        'ts_setting_name' => 'url_script_assets_rss',
        'title' => 'Assets rss Script',
        'showInList' => false,
        'isAsset' => true,
    ],
    Platform::URL_SCRIPT_WITHDRAWAL => [
        'ts_setting_name' => 'url_script_withdrawal',
        'title' => 'Withdrawal Script',
        'showInList' => false,
        'isAsset' => true,
    ],
    Platform::URL_SCRIPT_CRM_LIB => [
        'ts_setting_name' => 'url_script_crm_lib',
        'title' => 'CRM lib Script',
        'showInList' => false,
        'isAsset' => true,
    ],
];