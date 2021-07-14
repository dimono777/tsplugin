<?php

use tradersoft\controllers\Assets_Controller;
use tradersoft\controllers\Economic_Calendar_Controller;
use tradersoft\controllers\Form_Controller;
use tradersoft\controllers\Partners_Controller;
use tradersoft\controllers\Professional_Controller;
use tradersoft\controllers\Quiz_Controller;
use tradersoft\controllers\Survey_Controller;
use tradersoft\controllers\Trader_Controller;
use tradersoft\controllers\Verification_Controller;
use tradersoft\controllers\Webinar_Calendar_Controller;
use tradersoft\controllers\Withdrawal_Controller;
use tradersoft\helpers\Form;
use tradersoft\helpers\Partner;
use tradersoft\helpers\Platform;
use tradersoft\model\Media_Queue;
use tradersoft\widgets\Asset_Mini;
use tradersoft\widgets\Asset_Row;
use tradersoft\widgets\Call_Back;
use tradersoft\widgets\Rss;
use tradersoft\widgets\Update_Balance_Js_List;
use tradersoft\widgets\Welcome_Form;
use tradersoft\widgets\Welcome_Trader;

return [
    /*
    [
        'handle' => 'someUniqueName_for_dependencies_usage_and_its_not_required',
        'src'    => '/someAssetsAddress/filename.js',
        'container' => null,
        'deps'   => [],
        'ver'    => '12323',
        'inFooter' => false,
    ],
    [
        'handle' => 'someUniqueName2',
        'src'    => '/someAssetsAddress/filename.js',
        'container' => 'system',
        'deps'   => [
            'someUniqueName_for_dependencies_usage_and_its_not_required',
        ],
        'ver'    => '12323',
        'inFooter' => true,//default value
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
        'handle' => 'jquery',
        'src' => 'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js',
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => TSInit::class,
            ],
        ],
    ],
    [
        'src' => '/js/main.js',
        'ver' => 8,
        'container' => 'system',
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
        'src' => '/js/callBackForm.js',
        'ver' => 10,
        'container' => 'system',
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
        'handle' => 'ts-popuper',

        'src' => '//assets-common-popuper.' . TSInit::$app->request->getMainDomain() . '/js/popups.js',
        'ver' => 202106151200,
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => TSInit::class,
            ],
        ],
    ],
    [
        'src' => '/js/webinarsCalendar.js',
        'ver' => '202008271124',
        'container' => 'system',
        'deps' => [
            'jquery',
        ],
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Webinar_Calendar_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionIndex',
            ],
        ],
    ],
    [
        'handle' => 'vue',
        'src' => '/js/plugins/vue/vue.min.js',
        'ver' => 1,
        'container' => 'system',
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Withdrawal_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionWithdrawalRequest',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Quiz_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionIndex',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Assets_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionIndex',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Assets_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionMini',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Assets_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionSingle',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Asset_Mini::class,
            ],
        ],
    ],
    [
        'src' => '/js/withdrawal.js',
        'ver' => 201909161446,
        'container' => 'system',
        'deps' => [
            'vue',
            'ts-platform-lib-withdrawal',
        ],
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Withdrawal_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionWithdrawalRequest',
            ],
        ],
    ],
    [
        'handle' => 'ts-platform-lib-withdrawal',
        'src' => Platform::getURL(Platform::URL_SCRIPT_WITHDRAWAL),
        'ver' => 2,
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Withdrawal_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionWithdrawalRequest',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Balance_Widget::class,
            ],
        ],
    ],
    [
        'handle' => 'ts-partners-auth',
        'src' => Partner::getPartnerIsAuthJSLink(),
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Partners_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionAuthorization',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Partners_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionRegistration',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Partners_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionForgotPassword',
            ],
        ],
    ],
    [
        'src' => '/js/surveys.js',
        'container' => 'system',
        'ver' => 10,
        'deps' => [
            'jquery',
        ],
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Survey_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionIndex',
            ],
        ],
    ],
    [
        'src' => '/js/plugins/vue/vue-validator.js',
        'container' => 'system',
        'deps' => [
            'vue',
        ],
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Quiz_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionIndex',
            ],
        ],
    ],
    [
        'handle' => 'vue2',
        'src' => '/js/plugins/vue2/vue.min.js',
        'ver' => 2,
        'container' => 'system',
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Economic_Calendar_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionIndex',
            ],
        ],
    ],
    [
        'handle' => 'moment-js',
        'src' => '/js/plugins/moment/moment.min.js',
        'ver' => 2,
        'container' => 'system',
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Economic_Calendar_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionIndex',
            ],
        ],
    ],
    [
        'src' => '/js/plugins/moment/moment-timezone-with-data-2012-2022.min.js',
        'container' => 'system',
        'deps' => [
            'moment-js',
        ],
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Economic_Calendar_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionIndex',
            ],
        ],
    ],
    [
        'src' => '/js/economicCalendar.js',
        'container' => 'system',
        'ver' => 5,
        'deps' => [
            'vue2',
            'moment-js',
        ],
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Economic_Calendar_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionIndex',
            ],
        ],
    ],
    [
        'handle' => 'ts-platform-lib-assetsrss',
        'src' => Platform::getURL(Platform::URL_SCRIPT_ASSETS_RSS),
        'ver' => 5,
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Assets_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionIndex',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Rss::class,
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Assets_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionSingle',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Assets_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionMini',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Asset_Row::class,
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Asset_Mini::class,
            ],
        ],
    ],
    [
        'handle' => 'ts-financialAssets',
        'src' => '/js/financialAssets.js',
        'container' => 'system',
        'ver' => 5,
        'deps' => [
            'vue',
        ],
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Assets_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionIndex',
            ],
        ],
	],
	[
        'handle' => 'ts-singleFinancialAsset',
        'src' => '/js/singleFinancialAsset.js',
        'container' => 'system',
        'ver' => 5,
        'deps' => [
            'vue',
        ],
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Assets_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionSingle',
            ],
        ],
    ],
    [
        'src' => 'assets/js/TSAsset.js',
        'handle' => 'ts-TSAsset',
        'deps' => [
            'vue',
            'ts-platform-lib-assetsrss',
        ],
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Assets_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionMini',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Asset_Mini::class,
            ],
        ],
    ],

    [
        'src' => 'assets/js/TSMarket.js',
        'handle' => 'ts-TSMarket',
        'ver' => 3,
        'deps' => [
            'ts-TSAsset',
        ],
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Assets_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionMini',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Asset_Mini::class,
            ],
        ],
    ],
    [
        'src' => 'assets/js/TSCollection.js',
        'handle' => 'ts-TSCollection',
        'deps' => [
            'ts-TSMarket',
        ],
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Assets_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionMini',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Asset_Mini::class,
            ],
        ],
    ],
    [
        'src' => 'assets/js/TSAssetCollection.js',
        'handle' => 'ts-TSAssetCollection',
        'deps' => [
            'ts-TSCollection',
        ],
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Assets_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionMini',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Asset_Mini::class,
            ],
        ],
    ],
    [
        'src' => 'assets/js/TSMarketCollection.js',
        'handle' => 'ts-TSMarketCollection',
        'deps' => [
            'ts-TSAssetCollection',
        ],
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Assets_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionMini',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Asset_Mini::class,
            ],
        ],
    ],
    [
        'src' => 'assets/js/TSAssetSubscription.js',
        'handle' => 'ts-TSAssetSubscription',
        'deps' => [
            'ts-TSMarketCollection',
        ],
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Assets_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionMini',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Asset_Mini::class,
            ],
        ],
    ],
    [
        'src' => 'assets/js/TSPublisher.js',
        'handle' => 'ts-TSPublisher',
        'deps' => [
            'ts-TSAssetSubscription',
        ],
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Assets_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionMini',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Asset_Mini::class,
            ],
        ],
    ],
    [
        'src' => 'assets/js/TSSortedAssetCollection.js',
        'handle' => 'ts-TSSortedAssetCollection',
        'deps' => [
            'ts-TSPublisher',
        ],
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Assets_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionMini',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Asset_Mini::class,
            ],
        ],
    ],
    [
        'src' => 'assets/js/TSIOWSConnection.js',
        'ver' => '201901231900',
        'handle' => 'ts-TSIOWSConnection',
        'deps' => [
            'ts-TSSortedAssetCollection',
        ],
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Assets_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionMini',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Asset_Mini::class,
            ],
        ],
    ],
    [
        'src' => '/js/verification-upload.js',
        'container' => 'system',
        'ver' => '201912171030',
        'deps' => [
            'jquery',
        ],
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Trader_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionVerificationUpload',
            ],
        ],
    ],
    [
        'handle' => 'ts-validation',
        'src' => '/js/validation.js',
        'container' => 'system',
        'ver' => '202104161600',
        'deps' => [
            'jquery',
        ],
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Form::class,
            ],
            [
                \tradersoft\model\Media_Queue::FILTER_KEY_INITIATOR => \tradersoft\helpers\form\MultiForm::class,
            ],
        ],
    ],
    [
        'handle' => 'ts-activeForm',
        'src' => '/js/activeForm.js',
        'container' => 'system',
        'ver' => '202101111225',
        'deps' => [
            'jquery',
            'ts-validation',
        ],
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Form::class,
            ],
            [
                \tradersoft\model\Media_Queue::FILTER_KEY_INITIATOR => \tradersoft\helpers\form\MultiForm::class,
            ],
        ],
    ],
    [
        'src' => '/wp-content/plugins/tradersoft/widgets/views/call_back/js/call_back.js',
        'ver' => '201709131500',
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Call_Back::class,
            ],
        ],
    ],
    [
        'src' => '/wp-content/plugins/tradersoft/widgets/views/welcome_form/js/welcome.js',
        'ver' => '201711061500',
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Welcome_Form::class,
            ],
        ],
    ],
    [
        'src' => '/assets/js/jquery.marquee.min.js',
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Asset_Row::class,
            ],
        ],
    ],
    [
        'src' => '/assets/js/assetsRow.js',
        'ver' => '201812141300',
        'deps' => [
            'ts-platform-lib-assetsrss',
        ],
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Asset_Row::class,
            ],
        ],
    ],
    [
        'handle' => 'ts-platform-lib-crmlib',
        'src' => Platform::getURL(Platform::URL_SCRIPT_CRM_LIB),
        'ver' => 201905170924,
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Update_Balance_Js_List::class,
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Welcome_Trader::class,
            ],
        ],
    ],
    [
        'src' => '/js/updateBalance.js',
        'container' => 'system',
        'ver' => '202101191430',
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Update_Balance_Js_List::class,
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Welcome_Trader::class,
            ],
        ],
    ],
    [
        'handle' => 'ts-tooltip',
        'src' => '/js/tooltip.js',
        'ver' => 4,
        'deps' => [
            'jquery',
            'vue',
        ],
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Assets_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionIndex',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Assets_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionSingle',
            ],
        ],
    ],
    [
        'src' => '/js/verification/aml.js',
        'container' => 'system',
        'ver' => 5,
        'deps' => [
            'jquery',
        ],
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Verification_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionAml',
            ],
        ]
    ],
    [
        'src' => '/js/professionalRequest.js',
        'container' => 'system',
        'ver' => 2,
        'deps' => [
            'jquery',
        ],
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Professional_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionIndex',
            ],
        ],
    ],
    [
        'src' => '/formbuilder/js/form.js',
        'ver' => '202005121200',
        'deps' => [
            'jquery',
        ],
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Form_Controller::class,
            ],
        ],
    ],
];