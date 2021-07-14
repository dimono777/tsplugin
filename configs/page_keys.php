<?php

return [
    '[TS-AUTHORIZATION]' => [
        'action' => 'trader/login',
        'description' => 'Authorization form',
        'example' => '',
    ],
    '[TS-REGISTRATION]' => [
        'action' => 'registration/registration',
        'description' => 'Registration form',
        'example' => '',
    ],
    '[TS-REGISTRATION-MINI]' => [
        'action' => 'registration/registration-mini',
        'description' => 'Registration form mini',
        'example' => '',
    ],
    '[TS-REGISTRATION-DEMO]' => [
        'action' => 'registration/registration-demo',
        'description' => 'Registration form demo account',
        'example' => '',
    ],
    '[TS-REGISTRATION-ISLAMIC]' => [
        'action' => 'registration/registration-islamic',
        'description' => 'Registration form islamic account',
        'example' => '',
    ],
    '[TS-AFTER-REGISTRATION]' => [
        'action' => 'registration/after-registration',
        'description' => 'Thank you page after registration',
        'example' => '',
    ],

    '[TS-WITHDRAWAL]' => [
        'action' => 'withdrawal/withdrawal-request',
        'description' => 'Withdrawal form',
        'example' => '',
    ],

    '[TS-FORGOT-PASSWORD]' => [
        'action' => 'trader/forgot-password',
        'description' => 'Forgot password form. Incoming params: enableCaptcha.',
        'example' => '[TS-FORGOT-PASSWORD enableCaptcha =`1`]',
    ],
    '[TS-AFTER-FORGOT-PASSWORD]' => [
        'action' => 'trader/after-forgot-password',
        'description' => 'Success page after forgot password',
        'example' => '',
    ],
    '[TS-AFTER-CHANGE-PASSWORD]' => [
        'action' => 'trader/after-change-password',
        'description' => 'Success page after change password',
        'example' => '',
    ],
    '[TS-AFTER-RECOVERY-PASSWORD]' => [
        'action' => '',
        'description' => 'Success page after recovery password',
        'example' => '',
    ],
    '[TS-CHANGE-PASSWORD]' => [
        'action' => 'trader/change-password',
        'description' => 'Change password form',
        'example' => '',
    ],
    '[TS-ACCOUNT-DETAILS]' => [
        'action' => 'trader/account-details',
        'description' => 'Account details form. Incoming params: receiveEmailNewslettersAgreement',
        'example' => '[TS-ACCOUNT-DETAILS receiveEmailNewslettersAgreement=`1`]',
    ],
    '[TS-LOGOUT]' => [
        'action' => 'trader/logout',
        'description' => 'Logout page',
        'example' => '',
    ],
    '[TS-ACCOUNT-VERIFICATION-UPLOAD]' => [
        'action' => 'trader/verification-upload',
        'description' => 'Account verification upload form',
        'example' => '[TS-ACCOUNT-VERIFICATION-UPLOAD types=`1,2,...,7` uploadbuttonlabel=`Upload` uploadanotherbuttonlabel=`Upload another document` mandatorycomments=`1,2,...,7` commentlabel=`Enter reason why you don`t have a password`]',
    ],

    '[TS-SURVEY]' => [
        'action' => 'survey/index',
        'description' => 'Survey form',
        'example' => '',
    ],
    '[TS-PROFESSIONAL-REQUEST-FORM]' => [
        'action' => 'professional/index',
        'description' => 'Professional Level Form',
        'example' => '',
    ],
    // Connect the quiz controller. Knowledge assessment - universal name
    '[TS-KNOWLEDGE-ASSESSMENT]' => [
        'action' => 'quiz/index',
        'description' => 'Knowledge assessment form',
        'example' => '',
    ],

    '[TS-ECONOMIC-CALENDAR]' => [
        'action' => 'economic_calendar/index',
        'description' => 'Economic calendar',
        'example' => '',
    ],
    '[TS-WEBINAR-CALENDAR]' => [
        'action' => 'webinar_calendar/index',
        'description' => 'Webinar calendar',
        'example' => '',
    ],

    '[TS-ASSETS-INDEX]' => [
        'action' => 'assets/index',
        'description' => "
            Assets index page.
            Supports additional parameter <b>enableMarkets</b> with the markets ids in provided order, separated by comma, you want to show on the page.",
        'example' => 'Example of usage - <b>enableMarkets=`1,2,3`</b>.
            Markets identifiers: 1 - Stocks; 2 - Forex; 201 - Crypto; 3 - Commodities; 4 - Indices.',
    ],
    '[TS-ASSETS-MINI]' => [
        'action' => 'assets/mini',
        'description' => '
            Assets mini.
            Supports additional parameter <b>enableMarkets</b> as <b>[TS-ASSETS-INDEX]</b>.
        ',
        'example' => '',
    ],
    '[TS-ASSETS-SINGLE]' => [
        'action' => 'assets/single',
        'description' => '
            Shows single asset by given name
            Required parameter <b>assetName</b> with name of asset
        ',
        'example' => '<b>[TS-ASSETS-SINGLE assetName=`USDBTC`]</b>',
    ],

    '[TS-PARTNERS-AUTHORIZATION]' => [
        'action' => 'partners/authorization',
        'description' => 'Partners authorization form',
        'example' => '',
    ],
    '[TS-PARTNERS-REGISTRATION]' => [
        'action' => 'partners/registration',
        'description' => 'Partners registration form',
        'example' => '',
    ],
    '[TS-PARTNERS-FORGOT-PASSWORD]' => [
        'action' => 'partners/forgot-password',
        'description' => 'Partners forgot password form. Incoming params: enableCaptcha.',
        'example' => '[TS-PARTNERS-FORGOT-PASSWORD enableCaptcha =`1`]',
    ],
    '[TS-IFRAME-PAYMENT-TERMINAL]' => [
        'action' => 'payment-terminal',
        'description' => 'Iframe payment terminal',
        'example' => '',
    ],

    '[TS-TRADING-ROOM]' => [
        'action' => 'platform/trading-room',
        'description' => 'Redirect to trading platform',
        'example' => '',
    ],
    '[TS-IFRAME-PLATFORM-REPORTS]' => [
        'action' => 'platform/reports',
        'description' => 'Iframe platform reports',
        'example' => '',
    ],
    '[TS-IFRAME-PLATFORM-SETTINGS]' => [
        'action' => 'platform/settings',
        'description' => 'Iframe platform settings',
        'example' => '',
    ],

    '[TS-CALL-BACK]' => [
        'action' => 'home/call-back',
        'description' => 'Call back form',
        'example' => '',
    ],
    '[TS-CONTACT-US]' => [
        'action' => 'home/contact-us',
        'description' => 'Contact us form',
        'example' => '',
    ],
    '[TS-SKIP-POPUPER-ASSETS]' => [
        'action' => 'home/skip-popuper-assets',
        'description' => 'Popups are enabled on every pages by default. Put this key on any page where you need turn off Popups functionality.',
        'example' => '',
    ],
    '[TS-GUEST-ONLY]' => [
        'action' => '',
        'description' => 'Show page in menu for not logged users only. Put this key in any place of page content. System will cut the key',
        'example' => '',
    ],
    '[TS-AUTH-ONLY]' => [
        'action' => '',
        'description' => 'Show page in menu for logged users only. Put this key in any place of page content. System will cut the key',
        'example' => '',
    ],
    '[TS-DONT-SHOW]' => [
        'action' => '',
        'description' => 'Do not show page in menu. Put this key in any place of page content. System will cut the key',
        'example' => '',
    ],
    '[TS-TERMS-AND-CONDITIONS]' => [
        'action' => 'terms/page',
        'description' => 'Put this key in any place of the Terms & Conditions page to let the plugin link to that page. The key will always be removed from the page contents upon rendering',
        'example' => '',
    ],
    '[TS-PRIVACY_POLICY]' => [
        'action' => '',
        'description' => 'Put this key in any place of the Privacy Policy page to let the plugin link to that page. The key will always be removed from the page contents upon rendering',
        'example' => '',
    ],
    '[TS-TERMS-AND-CONDITIONS-IFRAME]' => [
        'action' => 'terms/iframe',
        'description' => 'Put this key in any place of the Terms & Conditions page that is meant to be used within an iframe, for example, in the T&C pop-ups. It will allow the plugin to link to that page. The key will always be removed from the page contents upon rendering.',
        'example' => '',
    ],
    '[TS-PARTNERS-TERMS-AND-CONDITIONS]' => [
        'action' => '',
        'description' => 'Put this key in any place of Partner Terms & Condition page to let plugin to link with the page. System will cut the key',
        'example' => '',
    ],
    '[TS-FORMS]' => [
        'action' => 'form/index',
        'description' => 'Displays the form configured via "CRM". Type - required parameter. Incoming params: type, template, ajaxEnable, ajaxTrigger and any parameter to search for a form.',
        'example' => '[TS-FORMS type=`Registration`  template=`templateFileName` ajaxEnable=`1` myPram=`myParamValue`]',
    ],
    '[TS-AML-VERIFICATION-FORM]' => [
        'action' => 'verification/aml',
        'description' => 'AML Verification Form',
        'example' => '',
    ],
];