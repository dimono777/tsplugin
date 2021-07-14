<?php

use tradersoft\components\redirect_ip_country\model\RedirectModel;

return [
    'settings' => [
        'title' => 'Redirect by IP/Country to specific site (after login):'
    ],
    'rule' => [
        'title' => 'Redirect',
        'options' => [
            RedirectModel::REDIRECT_NO => 'No',
            RedirectModel::REDIRECT_YES => 'Yes',
        ],
    ],
];