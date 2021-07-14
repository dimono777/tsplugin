<?php
    use \tradersoft\model\Smart_App_Banner;

    $settings = new Smart_App_Banner();

    if (!empty($_POST)) {
        $settings->update($_POST);
    } else {
        $settings->load();
    }

    $random = Smart_App_Banner::generateRandomString();
?>

<style type="text/css" media="all">
<?php include 'tradersoft-style.css'; ?>

<?php
    $options = Smart_App_Banner::getStatusList();
?>
.checkbox-switcher .switch-title {
    width: 65px;
}
.checkbox-switcher input:checked+.switch-title:before {
    content: "<?php echo $options[1] ?>";
}

.checkbox-switcher input+.switch-title:before {
    content: "<?php echo $options[0] ?>";
}

.wp-tradersoft .form-field input[type="text"] {
    max-width: 300px;
}
</style>

<div class="wp-tradersoft">
    <h1>Smart App Banner</h1>

    <?php if ($settings->isUpdated()):?>
        <div id="message" class="alert-box <?php echo ($settings->isUpdated()) ? 'has-success' : 'has-error'; ?>">
            <?php echo ($settings->isUpdated()) ? '<b>Well done!</b> Changes saved' : '<b>Validation error!</b> Please check indicated fields below'; ?>
        </div>
    <?php endif;?>

    <div class="wrapper">
        <form action="" method="post">
            <?php
                wp_nonce_field( 'sab_settings'.$random );
            ?>
            <input type="hidden" name="sab_random" value="<?php echo $random; ?>" />
            <input type="hidden" name="update_settings" value="Y" />

            <div class="form-field m-b-26">
                 <label class="small-label"><?php echo \TS_Functions::__('Status')?></label>
                 <div class="checkbox-switcher">
                     <input id="sab_status"
                            type="checkbox"
                            name="sab_status"
                            onchange="this.value = +this.checked"
                            <?php echo $settings->status ? "checked" : ''; ?>
                     />
                     <div class="switch-title"></div>
                     <label for="sab_status" class="switch-selection"></label>
                 </div>
             </div>

                <?php //$options = Smart_App_Banner::getStatusList(); ?>
<!--            <div class="form-field m-b-26">-->
<!--                <label class="small-label">--><?php //echo \TS_Functions::__('Status')?><!--</label>-->
<!--                <div class="select-wrapper">-->
<!--                    <select name="sab_status" id="sab_status">-->
<!--                        --><?php
//                            $options = Smart_App_Banner::getStatusList();
//                        ?>
<!--                        --><?php //foreach ($options as $value => $label ) { ?>
<!--                            <option value="--><?php //echo $value; ?><!--" --><?php //echo $value == $settings->status ? 'selected="selected"' :''; ?><!--><?php //echo esc_html( $label ); ?><!--</option>-->
<!--                        --><?php //}?>
<!--                    </select>-->
<!--                </div>-->
<!--            </div>-->

            <div class="row">
                <div class="col-6">
                    <div class="form-field">
                        <label>iOS App ID</label>
                        <input type='text' name='sab_apple_id'  value='<?php echo $settings->appleId; ?>' />
                        <p class="example-field">example: 12345678</p>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-field">
                        <label>iOS Price</label>
                        <input type='text' name='sab_apple_price'  value='<?php echo $settings->applePrice; ?>' />
                        <p class="example-field">example: 12345678</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="form-field">
                        <label>Google Play App ID</label>
                        <input type='text' name='sab_g_play_id'  value='<?php echo $settings->gPlayId; ?>' />
                        <p class="example-field">example: app.name</p>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-field">
                        <label>Google Play Price</label>
                        <input type='text' name='sab_g_play_price'  value='<?php echo $settings->gPlayPrice; ?>' />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="form-field">
                        <label>Days Hidden</label>
                        <input type='text' name='sab_days_hidden'  value='<?php echo $settings->daysHidden; ?>' />
                        <p class="example-field">days to hide banner after close button is clicked<br/>(15 by default)</p>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-field">
                        <label>Days Reminder</label>
                        <input type='text' name='sab_days_reminder'  value='<?php echo $settings->daysReminder; ?>' />
                        <p class="example-field">days to hide banner after "VIEW" button is clicked<br/>(90 by default)</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="form-field">
                        <label>App Title</label>
                        <input type='text' name='sab_title'  value='<?php echo $settings->title; ?>' />
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-field">
                        <label>Author</label>
                        <input type='text' name='sab_author'  value='<?php echo $settings->author; ?>' />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="form-field">
                        <label>Button</label>
                        <input type='text' name='sab_button'  value='<?php echo $settings->button; ?>' />
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-field">
                        <label>Logo</label>
                        <input id="sab_image" type="text" name="sab_image" value="<?php echo $settings->image; ?>" />
                        <p class="example-field">image url for the banner</p>
                    </div>
                </div>
            </div>

            <hr />
            <div class="form-field m-b-0">
                <input type="submit" value="Save settings" />
            </div>
        </form>
    </div>

</div>