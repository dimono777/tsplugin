<?php if (!TSInit::$app->trader->isGuest): ?>
    <div class="balance_block" style="border: 1px solid black; padding: 10px;">
        <div class="balance_main">
            <label><?php echo \TS_Functions::__('Balance');?>: </label>
            <span><?php echo \tradersoft\helpers\Currency::getInstance()->getSymbol() ?></span><span id="balance_main">0.00</span>
        </div>
        <div class="balance_open_pnl">
            <label><?php echo \TS_Functions::__('Open P&L');?>: </label>
            <span><?php echo \tradersoft\helpers\Currency::getInstance()->getSymbol() ?></span><span id="balance_open_pnl">0.00</span>
        </div>
    </div>
<?php endif;?>