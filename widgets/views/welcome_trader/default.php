<?php
/** @var $trader tradersoft\model\Trader */

use tradersoft\helpers\HeaderSetting;
use \tradersoft\helpers\Link;
use tradersoft\helpers\Platform;

$trader = TSInit::$app->trader
?>
<?php if (!$trader->isGuest): ?>
    <!--User authorization log in-->
    <div class="user-authorization">
        <!--user name-->
        <div class="user-name dropdown">
            <button type="button" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <?php if ($trader->get('fname', false)) { ?>
                    <?php echo $trader->get('fname', false) ; ?>
                <?php } elseif ($trader->get('username', false)) { ?>
                    <?php echo $trader->get('username') ; ?>
                <?php } ?>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                <?php if ($trader->accountNumber) { ?>
                    <li><a><?php echo \TS_Functions::__('ID #') . $trader->accountNumber; ?></a></li>
                <?php } ?>
                <li><a href="<?php echo TS_Functions::link('edit-details')?>"><?php echo \TS_Functions::__('Update Details') ?></a></li>
                <li><a href="<?php echo TS_Functions::link('change-password')?>"><?php echo \TS_Functions::__('Change password') ?></a></li>
                <li><a href="<?php echo TS_Functions::link('withdrawal')?>"><?php echo \TS_Functions::__('Withdrawal') ?></a></li>
                <?php if ($professionFormUrl = Link::getTraderProfessionalForm()) { ?>
                    <li><a href="<?php echo $professionFormUrl; ?>"><?php echo TS_Functions::__('Professional Level') ?></a></li>
                <?php } ?>
                <li><a href="<?php echo TS_Functions::link('logout')?>"><?php echo \TS_Functions::__('Log out') ?></a></li>
            </ul>
        </div>
        <!--user balance-->
        <div class="user-balance">
            <div class="title"><?php echo HeaderSetting::getCurrentFinanceInfoTypeLabel() . ':' ?></div>
            <div class="balance">
                <?php echo \tradersoft\helpers\Currency::getInstance()->getSymbol() ?><span id = "header-user-balance"><?php echo $trader->getFinanceInfoTypeValue() ?></span>
            </div>
        </div>
        <!--navigation links log in-->
        <div class="navigation-links">
            <a href="<?php echo Platform::getURL(Platform::URL_DEPOSIT_ID)?>" class="light-button"><?php echo \TS_Functions::__('Deposit') ?></a>
            <a href="<?php echo Platform::getURL()?>"><?php echo \TS_Functions::__('TRADE NOW') ?></a>
        </div>
    </div>

    <?php the_widget('tradersoft\widgets\Update_Balance_Js_List') ?>

<?php endif; ?>