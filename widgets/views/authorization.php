<?php if (TSInit::$app->trader->isGuest): ?>
    <div class="login_form">
        <?php if (isset($_POST['error']['authorization']) AND $_POST['error']['authorization']): ?>
            <div style="color: red;"><?php echo $_POST['error']['authorization'];?></div>
        <?php endif;?>
        <form action="" method="post">
            <div><label><?php echo \TS_Functions::__('E-mail')?></label><input type="text" name="email" /></div>
            <div><label><?php echo \TS_Functions::__('Password')?></label><input type="password" name="password" /></div>
            <input type="hidden" name="tradersoft_submit" value="authorization">
            <div><label><input type="submit" value="Login" /></div>
            <div><a href="<?php echo \tradersoft\helpers\Link::getTraderForgotLink()?>" style="font-size: 10px;"><?php echo \TS_Functions::__('Forgot password?')?></a></div>
        </form>
    </div>
<?php endif;?>