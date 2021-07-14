<?php
namespace tradersoft\controllers;

use tradersoft\helpers\Arr;
use tradersoft\helpers\Link;

class Redirect_Controller extends Base_Controller
{
    public function actionBykey()
    {
        $key = Arr::get($_GET, 'key');
        $this->redirect(Link::getForPageWithKey("[$key]"));
    }
}