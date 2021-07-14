<?php

use tradersoft\controllers\Trader_Controller;
use tradersoft\controllers\Verification_Controller;
use tradersoft\model\Media_Queue;
use tradersoft\widgets\Asset_Mini as AssetMiniWidget;
use tradersoft\controllers\Assets_Controller;
use tradersoft\widgets\Registration_Mini;
use tradersoft\controllers\Form_Controller;
use tradersoft\helpers\FormBuilder;

return [
    /*
    [
        'body'    => 'var someTestVar = "it is string var in JS ";',
        'afterScripts' => false,
    ],
    [
        'view'    => '/someViewAddress/js/someinlinejs-2',
        // Params to allow usage. Scripts without any allowedForParams will be add to all calls.
        // Each inner array will be checked via OR.
        // Params values inside inner array will be checked via AND
        'allowedForParams' => [
            [
                \tradersoft\model\Media_Queue::FILTER_KEY_INITIATOR => classNameFUllNameWithNameSpace,
                'param1' => '1',
                'param2' => 'blabla',
            ],
            [
                \tradersoft\model\Media_Queue::FILTER_KEY_INITIATOR => classNameFUllNameWithNameSpace,
                'param1' => '2',
            ],
        ],
    ],
    */
    [
        'view' => 'system/inlinescripts/baseJS',
    ],
    [
        'view'    => "system/inlinescripts/aml",
        'afterScripts' => false,
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Verification_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionAml',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Form_Controller::class,
                Media_Queue::FILTER_KEY_FORM_TYPE => FormBuilder::TYPE_AML_VERIFICATION,
            ],
        ],
    ],
    [
        'view'    => "system/inlinescripts/verification",
        'afterScripts' => false,
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Trader_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionVerificationUpload',
            ],
        ],
    ],
    [
        'view'    => "system/inlinescripts/on-jquery-inited",
        'afterScripts' => false,
        'deps' => [
            'jquery',
        ],
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => TSInit::class,
            ],
        ],
    ],
    [
        'view' => 'system/inlinescripts/registration-mini',
        'ver' => '1',
        'deps' => [
            'jquery',
        ],
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Registration_Mini::class,
            ],
        ],
    ],
    [
        'view' => 'system/inlinescripts/assets-mini',
        'ver' => '1',
        'afterScripts' => true,
        'deps' => [
            'jquery',
            'vue',
            'ts-platform-lib-assetsrss',
            'ts-TSMarketCollection',
        ],
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => AssetMiniWidget::class,
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Assets_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionMini',
            ],
        ],
    ],
];