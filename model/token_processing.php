<?php

use tradersoft\model\Token;

$token = new Token($_GET);
$token->processToken();
if ($redirectUrl = $token->getRedirectUrl()) {
    TS_Functions::redirectJS($redirectUrl);
}