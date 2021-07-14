<?php

use tradersoft\model\Token;

return [
    'Registration' => [
        'roles' => '?',
    ],
    'AMLVerification' => [
        'roles' => '@',
    ],
    'EmailForPasswordRecovery' => [
        'roles' => '?',
    ],
    'PasswordRecovery' => [
        'matchCallback' => function () {
            return TSInit::$app->trader->isGuest && TSInit::$app->session->has(Token::PASSWORD_RECOVERY_FLASH_KEY);
        },
    ],
];