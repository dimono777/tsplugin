<?php if (TSInit::$app->trader->isGuest): ?>
<div class="register_form">
    <?php if (isset($_POST['error']['registration']) AND $_POST['error']['registration']): ?>
        <div style="color: red;"><?php echo $_POST['error']['registration'];?></div>
    <?php endif;?>
    <form action="" method="post">
        <div><label><?php echo \TS_Functions::__('First name')?></label><input type="text" name="fname" /></div>
        <div><label><?php echo \TS_Functions::__('Last name')?></label><input type="text" name="lname" /></div>
        <div><label><?php echo \TS_Functions::__('Country')?></label>
            <?php require_once dirname(__FILE__) . '/countries.php';?>
        </div>
        <div><label><?php echo \TS_Functions::__('Phone')?></label><input type="text" name="phone" /></div>
        <div><label><?php echo \TS_Functions::__('E-mail')?></label><input type="text" name="email" /></div>
        <div><label><?php echo \TS_Functions::__('Password')?></label><input type="password" name="password" /></div>
        <div><label><?php echo \TS_Functions::__('Confirm password')?></label><input type="password" name="confirm_password" /></div>
        <input type="hidden" name="tradersoft_submit" value="registration">
        <div><label><input type="submit" value="Register" /></div>
    </form>
</div>
<?php endif;?>