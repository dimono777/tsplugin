<?php

use tradersoft\controllers\Assets_Controller;
use tradersoft\controllers\Economic_Calendar_Controller;
use tradersoft\controllers\Form_Controller;
use tradersoft\controllers\Partners_Controller;
use tradersoft\controllers\Quiz_Controller;
use tradersoft\controllers\Registration_Controller;
use tradersoft\controllers\Survey_Controller;
use tradersoft\controllers\Trader_Controller;
use tradersoft\controllers\Webinar_Calendar_Controller;
use tradersoft\controllers\Withdrawal_Controller;
use tradersoft\helpers\Form;
use tradersoft\model\Media_Queue;
use tradersoft\widgets\Asset_Mini;
use tradersoft\widgets\Asset_Row;
use tradersoft\widgets\Welcome_Form;
use tradersoft\widgets\Welcome_Trader;

return [
    /*
    [
        'handle' => 'someUniqueName_for_dependencies_usage_and_its_not_required',
        'src'    => '/someAssetsAddress/filename.css',
        'container' => null,
        'deps'   => [],
        'ver'    => '12323',
        'inFooter' => false,
    ],
    [
        'handle' => 'someUniqueName2',
        'src'    => '/someAssetsAddress/filename.css',
        'container' => 'system',
        'inFooter' => true,//default value
        'deps'   => [
            'someUniqueName_for_dependencies_usage_and_its_not_required',
        ],
        'ver'    => '12323',
        // Params to allow usage. Styles without any allowedForParams will be add to all calls.
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
        'src' => '/css/style.css',
        'ver' => '1',
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Trader_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionAccountDetails',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Trader_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionLogin',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Trader_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionForgotPassword',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Trader_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionChangePassword',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Trader_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionDefault',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Registration_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionRegistration',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Registration_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionRegistrationDemo',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Registration_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionDefault',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Withdrawal_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionWithdrawalRequest',
            ],
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
            [
                Media_Queue::FILTER_KEY_INITIATOR => Hello_Widget::class,
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Welcome_Trader::class,
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Welcome_Form::class,
            ],
        ],
    ],
    [
        'src' => '/css/open-account.css',
        'ver' => 2,
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Registration_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionRegistration',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Registration_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionRegistrationIslamic',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Registration_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionRegistrationDemo',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Partners_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionRegistration',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Form_Controller::class,
                Media_Queue::FILTER_KEY_FORM_TYPE => 'Registration',
            ],
        ],
    ],
    [
        'src' => '/css/asset.css',
        'container' => 'system',
        'ver' => 4,
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
        'src' => '/assets/css/mini-asset-index.css',
        'ver' => '201906061112',
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
        'src' => 'https://unpkg.com/animate.css@3.5.1/animate.min.css',
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Economic_Calendar_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionIndex',
            ],
        ],
    ],
    [
        'src' => '/css/calendar.css',
        'container' => 'system',
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Economic_Calendar_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionIndex',
            ],
        ],
    ],
    [
        'src' => '/css/verification.css',
        'ver' => '17',
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Trader_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionVerificationUpload',
            ],
        ],
    ],
    [
        'src' => '/assets/css/popup-ver.css',
        'ver' => '6',
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Trader_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionVerificationUpload',
            ],
        ],
    ],
    [
        'src' => '/css/quiz.css',
        'ver' => '20181126',
        'container' => 'quiz',
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Quiz_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionIndex',
            ],
            [
                Media_Queue::FILTER_KEY_INITIATOR => Quiz_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionSubmit',
            ],
        ],
    ],
    [
        'src' => '/css/webinar-calendar.css',
        'container' => 'system',
        'ver' => '202001101159',
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Webinar_Calendar_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionIndex',
            ],
        ],
    ],
    [
        'src' => '/css/withdrawal.css',
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Withdrawal_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionWithdrawalRequest',
            ],
        ],
    ],
    [
        'src' => '/css/form.css',
        'container' => 'system',
        'ver' => '202104141530',
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Form::class,
            ],
        ],
    ],
    [
        'src' => '/assets/css/assetsRow.css',
        'ver' => '201807111200',
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Asset_Row::class,
            ],
        ],
    ],
    [
        'src' => '/survey/css/questionnaire.css',
        'ver' => '20190606',
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Survey_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionIndex',
            ],
        ],
    ],
    [
        'src' => '/formbuilder/css/form.css',
        'ver' => '202005251624',
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Form_Controller::class,
                Media_Queue::FILTER_KEY_FB_SHOW_DEFAULT_STYLES => true
            ],
        ],
    ],
    [
        'src' => '/css/loader.css',
        'container' => 'system',
        'ver' => '202001101219',
        'allowedForParams' => [
            [
                Media_Queue::FILTER_KEY_INITIATOR => Webinar_Calendar_Controller::class,
                Media_Queue::FILTER_KEY_ACTION => 'actionIndex',
            ],
        ],
    ],
];