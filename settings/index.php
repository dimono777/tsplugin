<?php

use tradersoft\helpers\HeaderSetting;
use tradersoft\helpers\Html;
use tradersoft\helpers\RecentWithdrawalsSettings;
use tradersoft\helpers\RegulationSetting;
use tradersoft\helpers\system\PlatformTypeSettings;

require_once dirname(__FILE__) . '/model.php';

?>

<style type="text/css" media="all">
    <?php include 'tradersoft-style.css'; ?>
</style>

<h1><?php echo TS_Functions::__('TraderSoft');?></h1>

<?php if ($updated):?>
    <div id="message" class="alert-box <?php echo ($updated) ? 'has-success' : 'has-error'; ?>">
        <?php echo ($updated) ? '<b>Well done!</b> Changes saved' : '<b>Validation error!</b> Please check indicated fields below'; ?>
    </div>
<?php endif;?>

<form class="wp-tradersoft tabs" action="" method="post">
    <input type="radio" name="tab-btn" id="tab-btn-1" value="" checked>
    <label for="tab-btn-1">General Settings</label>
    <input type="radio" name="tab-btn" id="tab-btn-2" value="">
    <label for="tab-btn-2">Redirect</label>
    <input type="radio" name="tab-btn" id="tab-btn-3" value="">
    <label for="tab-btn-3">Platform Settings</label>
    <input type="radio" name="tab-btn" id="tab-btn-4" value="">
    <label for="tab-btn-4">Shortcodes</label>

    <div class="info-box">
        <i class="dashicons-before dashicons-admin-generic"></i>
        <?php echo TS_Functions::__('Please add below settings you got from affiliate manager');?>
    </div>

    <div id="content-1" class="wrapper">
        <div class="form-field">
            <label><?php echo TS_Functions::__('Interlayer Domain')?></label>
            <input type="text" name="interlayer_domain" value="<?php echo $current_interlayer_domain?>" />
        </div>
        <div class="form-field"> <!-- has-error -->
            <label><?php echo TS_Functions::__('Interlayer Secret key')?></label>
            <input type="text" name="interlayer_secret_key" value="<?php echo $current_interlayer_secret_key?>" />
            <!-- <div class="error-mess">Can't be empty</div> -->
        </div>
        <div class="form-field">
            <label><?php echo TS_Functions::__('Partner Domain')?></label>
            <input type="text" name="partner_domain" value="<?php echo $current_partner_domain?>" />
        </div>
        <div class="form-field">
            <label><?php echo TS_Functions::__('Cookie salt')?></label>
            <input type="text" name="cookie_salt" value="<?php echo $current_cookie_salt?>" />
        </div>
        <div class="form-field m-b-30">
            <label><?php echo TS_Functions::__('Cookie Domain Level')?></label>
            <input type="text" name="cookies_domain_level" value="<?php echo $current_cookies_domain_level?>" />
        </div>

        <!-- recaptcha config options -->
        <div class="wp-tradersoft-title">
            <?php echo TS_Functions::__('ReCAPTCHA');?>
        </div>
        <div class="form-field">
            <label><?php echo TS_Functions::__('Site key')?></label>
            <input type="text" name="recaptcha_site_key" value="<?php echo $current_recaptcha_site_key; ?>" />
        </div>
        <div class="form-field m-b-30">
            <label><?php echo TS_Functions::__('Secret key')?></label>
            <input type="text" name="recaptcha_secret_key" value="<?php echo $current_recaptcha_secret_key; ?>" />
        </div>
        <!-- recaptcha config options -->

        <!-- Invisible reCAPTCHA config options -->
        <div class="wp-tradersoft-title">
            <?php echo TS_Functions::__('Invisible reCAPTCHA');?>
        </div>
        <div class="form-field">
            <label><?php echo TS_Functions::__('Site key')?></label>
            <input type="text" name="invisible_recaptcha_site_key" value="<?php echo $current_invisible_recaptcha_site_key; ?>" />
        </div>
        <div class="form-field m-b-30">
            <label><?php echo TS_Functions::__('Secret key')?></label>
            <input type="text" name="invisible_recaptcha_secret_key" value="<?php echo $current_invisible_recaptcha_secret_key; ?>" />
        </div>
        <!-- Invisible reCAPTCHA config options -->

        <!-- Regulation Settings -->
        <div class="wp-tradersoft-title">
            <?php echo TS_Functions::__('Regulation Settings');?>
        </div>
        <div class="form-field">
            <label class="small-label"><?php echo TS_Functions::__('Edit Profile Page')?></label>
            <div class="select-wrapper">
                <?php echo Html::dropDownList(
                    'regulation_profile_type',
                    $current_regulation_profile_type,
                    RegulationSetting::getTypes()
                );?>
            </div>
        </div>

    <!-- Header Setting -->
    <div class="wp-tradersoft-title">
        <?php echo TS_Functions::__('Header Setting');?>
    </div>
    <div class="form-field">
        <label class="small-label"><?php echo TS_Functions::__('Finance info')?></label>
        <div class="select-wrapper">
            <?php echo Html::dropDownList(
                'finance_info_type',
                $current_finance_info_type,
                HeaderSetting::getFinanceInfoTypesList()
            );?>
        </div>
    </div>

        <!-- Recent Withdrawals -->
        <div class="wp-tradersoft-title">
            <?php echo TS_Functions::__('Recent Withdrawals');?>
        </div>
        <div class="form-field">
            <label class="small-label"><?php echo TS_Functions::__('Recent Withdrawals View')?></label>
            <div class="select-wrapper">
                <?php echo Html::dropDownList(
                    RecentWithdrawalsSettings::SETTING_NAME,
                    $current_recent_withdrawals_view_type,
                    RecentWithdrawalsSettings::getAmountTypeList()
                );?>
            </div>
        </div>
    </div>

    <div id="content-2" class="wrapper">
        <?php include_once('redirect_after_action/form.php'); ?>
        <?php include_once('redirect_ip_country/form.php'); ?>

        <div class="info-text">
            <?php echo __('Partially exclude Countries from Registration Form and аdd a link to a site with another T&C for them:');?>
        </div>
        <div class="form-field">
            <label><?php echo __('Country Type ID of displayed Countries')?></label>
            <input type="text" name="registration_form_country_type" value="<?php echo $current_registration_form_country_type?>" />
        </div>
        <div class="form-field">
            <label><?php echo __('Redirection link Domain')?></label>
            <input type="text" name="registration_form_redirect_link_domain" value="<?php echo $current_registration_form_redirect_link_domain?>" />
        </div>
        <div class="form-field m-b-30">
            <label><?php echo __('Redirection link Page ID')?></label>
            <input type="text" name="registration_form_redirect_link_page_id" value="<?php echo $current_registration_form_redirect_link_page_id?>" />
        </div>
    </div>

    <div id="content-3" class="wrapper">

        <div class="row">
            <div class="col-6">
                <div class="form-field">
                    <label class="small-label"><?php echo TS_Functions::__('Platform Type')?></label>
                    <div class="select-wrapper">
                        <?php echo Html::dropDownList(
                            'platform_type',
                            $current_platform_type ?: PlatformTypeSettings::getDefaultType(),
                            PlatformTypeSettings::getTypesList(),
                            array('onchange' => 'changePlatformType(this.value)', 'id' => 'platform_type')
                        );?>
                    </div>
                    <button type="button" onclick="changePlatformType(jQuery('#platform_type').val())">Reset to Default</button>
                </div>
            </div>
        </div>

        <!-- Platform Subdomains -->
        <div class="wp-tradersoft-title m-t-0">
            <?php echo TS_Functions::__('Platform Subdomains');?>
        </div>

        <div class="row">
            <div class="col-6">
                <div class="form-field">
                    <label><?php echo TS_Functions::__('Common subdomain')?></label>
                    <input type="text" name="common_subdomain" value="<?php echo $current_common_subdomain?>" />
                </div>
            </div>

            <div class="col-6">
                <div class="form-field">
                    <label><?php echo TS_Functions::__('Scripts subdomain')?></label>
                    <input type="text" name="scripts_subdomain" value="<?php echo $current_scripts_subdomain?>" />
                </div>
            </div>
        </div>

        <!-- Platform Settings -->
        <div class="wp-tradersoft-title">
            <?php echo TS_Functions::__('Platform Settings');?>
        </div>

        <div class="row">
            <div class="col-6">
                <div class="form-field">
                    <label><?php echo TS_Functions::__('Base page link')?></label>
                    <input type="text" name="url_base_page" value="<?php echo $current_url_base_page?>" />
                </div>
            </div>

            <div class="col-6">
                <div class="form-field">
                    <label><?php echo TS_Functions::__('Trading Room link')?></label>
                    <input type="text" name="url_trading_room" value="<?php echo $current_url_trading_room?>" />
                </div>
            </div>

            <div class="col-6">
                <div class="form-field">
                    <label><?php echo TS_Functions::__('CFD link')?></label>
                    <input type="text" name="url_cfd" value="<?php echo $current_url_cfd?>" />
                </div>
            </div>

            <div class="col-6">
                <div class="form-field">
                    <label><?php echo TS_Functions::__('Binary link')?></label>
                    <input type="text" name="url_binary" value="<?php echo $current_url_binary?>" />
                </div>
            </div>

            <div class="col-6">
                <div class="form-field">
                    <label><?php echo TS_Functions::__('Switch to Demo Account link')?></label>
                    <input type="text" name="url_switch_demo_account" value="<?php echo $current_url_switch_demo_account?>" />
                </div>
            </div>

            <div class="col-6">
                <div class="form-field">
                    <label><?php echo TS_Functions::__('Deposit link')?></label>
                    <input type="text" name="url_deposit" value="<?php echo $current_url_deposit?>" />
                </div>
            </div>

            <div class="col-6">
                <div class="form-field">
                    <label><?php echo TS_Functions::__('Withdrawal link')?></label>
                    <input type="text" name="url_withdrawal" value="<?php echo $current_url_withdrawal?>" />
                </div>
            </div>

            <div class="col-6">
                <div class="form-field">
                    <label><?php echo TS_Functions::__('Reports link')?></label>
                    <input type="text" name="url_reports" value="<?php echo $current_url_reports?>" />
                </div>
            </div>

            <div class="col-6">
                <div class="form-field">
                    <label><?php echo TS_Functions::__('CFD chart frame link')?></label>
                    <input type="text" name="url_cfd_chart_frame" value="<?php echo $current_url_cfd_chart_frame?>" />
                </div>
            </div>

            <div class="col-6">
                <div class="form-field">
                    <label><?php echo TS_Functions::__('Account Verification link')?></label>
                    <input type="text" name="url_account_verification" value="<?php echo $current_url_account_verification?>" />
                </div>
            </div>

            <div class="col-6">
                <div class="form-field">
                    <label><?php echo TS_Functions::__('Change Password link')?></label>
                    <input type="text" name="url_change_password" value="<?php echo $current_url_change_password?>" />
                </div>
            </div>

            <div class="col-6">
                <div class="form-field">
                    <label><?php echo TS_Functions::__('Account Details link')?></label>
                    <input type="text" name="url_account_details" value="<?php echo $current_url_account_details?>" />
                </div>
            </div>

            <div class="col-6">
                <div class="form-field">
                    <label><?php echo TS_Functions::__('Settings link')?></label>
                    <input type="text" name="url_settings" value="<?php echo $current_url_settings?>" />
                </div>
            </div>
        </div>

        <!-- Scripts -->
        <div class="wp-tradersoft-title">
            <?php echo TS_Functions::__('Scripts');?>
        </div>

        <div class="row">
            <div class="col-6">
                <div class="form-field">
                    <label><?php echo TS_Functions::__('Users Info Script link')?></label>
                    <input type="text" name="url_script_users_info" value="<?php echo $current_url_script_users_info?>" />
                </div>
            </div>

            <div class="col-6">
                <div class="form-field">
                    <label><?php echo TS_Functions::__('Assets rss Script link')?></label>
                    <input type="text" name="url_script_assets_rss" value="<?php echo $current_url_script_assets_rss?>" />
                </div>
            </div>

            <div class="col-6">
                <div class="form-field">
                    <label><?php echo TS_Functions::__('Withdrawal Script')?></label>
                    <input type="text" name="url_script_withdrawal" value="<?php echo $current_url_script_withdrawal?>" />
                </div>
            </div>

            <div class="col-6">
                <div class="form-field">
                    <label><?php echo TS_Functions::__('CRM lib Script')?></label>
                    <input type="text" name="url_script_crm_lib" value="<?php echo $current_url_script_crm_lib?>" />
                </div>
            </div>
        </div>
    </div>

    <div id="content-4" class="wrapper">
        <div class="wp-tradersoft-title m-t-0">
            <?php echo TS_Functions::__('Plugins allows to use variables below');?>
        </div>

        <?php tradersoft\helpers\system\AutoDocumentationMaker::printPageShortCodeDoc(); ?>

        <div class="wp-tradersoft-title m-t-30">
            <?php echo TS_Functions::__('Shortcodes');?>
        </div>

        <?php tradersoft\helpers\system\AutoDocumentationMaker::printNativeShortCodeDoc(); ?>
    </div>

    <input type="hidden" name="tradersoft_submit" value="settings">
    <input type="submit" value="<?php echo TS_Functions::__('Save settings')?>">
</form>





<script>
    function changePlatformType(selectValue) {

        var platformTypesConfigs = <?php echo json_encode(PlatformTypeSettings::getPreloadedSettings()); ?>,
            platformTypesConfigsValues = platformTypesConfigs[selectValue],
            platformSettingsFields = jQuery('#platform_type').parents('.wrapper').find('input[type="text"]').get();

        for (var item in platformTypesConfigsValues) {
            var currentEl = platformSettingsFields.find(field => field.name == item);
            (currentEl) ? currentEl.value = platformTypesConfigsValues[item] : false;
        }
    }
</script>
