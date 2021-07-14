<?php


return [
    '[trader_name]' => [
        'description' => '<div>Get trader name</div>',
        'example' => 'echo do_shortcode( \'[trader_name]\' );'
    ],
    '[get_link_by_short_code]' => [
        'description' => '<div>Get link by short code</div>',
        'example' => 'echo do_shortcode( \'[get_link_by_short_code code="TS-FORGOT-PASSWORD" text="Forgot password?" class="class" id="id"]\' );'
    ],
    '[ts_link]' => [
        'description' => ' <div>Get any link</div>',
        'example' => 'echo do_shortcode( \'[ts_link url="http://site.com" text="Link text" class="class" id="id"]\' );'
    ],
    '[trader_account_number]' => [
        'description' => ' <div>Return trader account number</div>',
        'example' => 'echo do_shortcode( \'[trader_account_number]\' );'
    ],
    '[ts_is_guest]' => [
        'description' => '<div>Return true if user isn\'t logged</div>',
        'example' => 'do_shortcode( \'[ts_is_guest]\' );'
    ],
    '[ts_platform_url]' => [
        'description' => '<div>Get platform url</div>',
        'example' => 'echo do_shortcode( \'[ts_platform_url]\' );'
    ],
    '[ts_main_domain]' => [
        'description' => '<div>Get main domain</div>',
        'example' => 'echo do_shortcode( \'[ts_main_domain]\' );'
    ],
    '[ts_forgot_link]' => [
        'description' => '<div>Get forgot link</div>',
        'example' => 'echo do_shortcode( \'[ts_forgot_link]\' );'
    ],
    '[ts_get_link]' => [
        'description' => '<div>Get link by url</div>',
        'example' => 'echo do_shortcode( \'[ts_get_link url="/example"]\' );'
    ],
    '[ts_get_page_link]' => [
        'description' => '<div>Get url by key</div>',
        'example' => 'echo do_shortcode( \'[ts_get_page_link key="TS-REGISTRATION"]\' );  or echo do_shortcode( \'[ts_get_page_link key="TS-FORMS" type="Registration"]\' );'
    ],
    '[ts_get_form_link]' => [
        'description' => '<div>Get html by key(<a href="">Link title</a>)</div>',
        'example' => 'echo do_shortcode( \'[ts_get_form_link  key="TS-FORMS" type="Registration" text="Link title" param1="val1"]\' );'
    ],
    '[ts_phone_code_by_ip]' => [
        'description' => '<div>Get phone code by IP</div>',
        'example' => ' echo do_shortcode( \'[ts_phone_code_by_ip]\' );'
    ],
    '[ts_assets_base_url]' => [
        'description' => ' <div>Get base theme assets url</div>',
        'example' => 'echo do_shortcode( \'[ts_assets_base_url folder="/folder"]\' );'
    ],
    '[ts_asset_url]' => [
        'description' => '<div>Returns URL of an asset file if found</div>',
        'example' => 'echo do_shortcode( \'[ts_asset_url file="/folder/my.css"]\' );'
    ],
    '[ts_is_trader_guest]' => [
        'description' => '<div>Get content for guest trader</div>',
        'example' => 'echo do_shortcode( \'[ts_is_trader_guest]Content for guest trader[/ts_is_trader_guest]\' );'
    ],
    '[ts_is_trader_logged]' => [
        'description' => '<div>Get content for logged trader</div>',
        'example' => 'echo do_shortcode( \'[ts_is_trader_logged]Content for logged trader[/ts_is_trader_logged]\' );'
    ],
    '[ts_geo_content]' => [
        'description' => '<div>Get Content for specific country</div>',
        'example' => 'echo do_shortcode( \'[ts_geo_content country="UA"]Content for trader from UA[/ts_geo_content]\' );'
    ],
    '[ts_get_trader_info]' => [
        'description' => '<div>Get trader info by key.
Following key values are supported:<br>
crmId - Lead ID<br>
crmHashId - Lead Hash ID<br>
marketspulseId - MP (platform) ID<br>
username - Username (email) to log in a user on the plaform<br>
passwordHash - MD5 encrypted password<br>
email - Email<br>
registrationDateTime - Registration date<br>
fname - First name<br>
lname - Last name<br>
fullname - Full name<br>
currency - Сurrency<br>
phone - Phone<br>
cellphone - Сellphone<br>
phone2 - Phone2<br>
country - Country<br>
countryReal - Registration country (by IP)<br>
state - Country state<br>
town - Town<br>
address - Address<br>
address2 - Address2<br>
postalCode - Postal Code<br>
born - Birthday<br>
language - Language<br>
traderIp - IP<br>
countryType - Сountry type<br>
department - Department<br>
accountType - Account Type<br>
isVerified - Is verified<br>
clientId - Platform identifier<br>
isSuspended - Is lead suspended?<br>
showQuiz - Is quiz available for current lead?<br>
professionalSuitabilityAvailable - Is professional application flow available for the lead?<br>
termStatus - Did the current lead accept the terms & conditions?<br>
agreedReceiveNewsletters - Newsletter agreement status<br>
mt4Id - MT4 login<br>
balance - Balance<br>
currencySymbol - Сurrency symbol<br>
currencyPrecision - Сurrency precision<br>
hasDepositOrCashback - Has the lead any financial history (deposits or cashbacks)?<br>
depositsNumber - How many deposits did the lead?<br>
firstDepositDate - Lead’s first deposit date<br>
lastDepositDate - Lead’s last deposit date<br>
deposited - Does lead have any<br>
depositSum - Total sum of lead’s deposits<br>
phoneCode - Phone code from “Phone” field<br>
nationalPhone - National phone from “Phone” field<br>
cellphoneCode - Phone code from “cellphone” field<br>
nationalCellphone - National phone from “cellphone” field<br>
suspendedWithChargeback - Is lead suspended due to chargeback request?<br>
traderHash - Lead’s cookie hash<br>
force - Did lead log in using force auth token?<br>
cookieTTL - Authentication cookies expiration period</div>',
        'example' => 'echo do_shortcode( \'[ts_get_trader_info key="fname"]\' );'
    ],
    '[ts_get_trader_balance]' => [
        'description' => '<div>Get trader balance</div>',
        'example' => 'echo do_shortcode( \'[ts_get_trader_balance]\' );'
    ],
    '[ts_is_local]' => [
        'description' => '<div>Check if http host is local</div>',
        'example' => 'echo do_shortcode( \'[ts_is_local]\' );'
    ],
    '[ts_is_trader_with_deposit]' => [
        'description' => '<div>Get content for trader who had a deposit</div>',
        'example' => 'echo do_shortcode( \'[ts_is_trader_with_deposit]Content for trader who had a deposit[/ts_is_trader_with_deposit]\' );'
    ],
    '[ts_is_trader_without_deposit]' => [
        'description' => '<div>Get content for trader who had not a deposit</div>',
        'example' => 'echo do_shortcode( \'[ts_is_trader_without_deposit]Content for trader who had not a deposit[/ts_is_trader_without_deposit]\' );'
    ],
    '[ts_home_url]' => [
        'description' => '<div>Get home url with current language</div>',
        'example' => 'echo do_shortcode( \'[ts_home_url]\' );'
    ],
    '[ts_get_language]' => [
        'description' => '<div>Get current language</div>',
        'example' => 'echo do_shortcode( \'[ts_get_language]\' );'
    ],
    '[ts_list_languages]' => [
        'description' => '<div>Get available languages config list</div>',
        'example' => 'echo do_shortcode( \'[ts_list_languages]\' );'
    ],
    '[ts_set_language]' => [
        'description' => '<div>Get available languages config list. <br>CookieLang - also change cookie lang, FALSE by default</div>',
        'example' => 'do_shortcode( \'[ts_set_language code="<LANG_CODE>" cookieLang="TRUE|FALSE"]\' );'
    ],
    '[ts_platform_full_url]' => [
        'description' => '<div>Get specified platform full url</div>',
        'example' => 'echo do_shortcode(\'[ts_platform_full_url linkId="PLATFORM_LINK_ID"]\');'
    ],
    '[ts_assetsrss_url]' => [
        'description' => '<div>Get specified platform full url</div>',
        'example' => 'echo do_shortcode( \'[ts_assetsrss_url]\' );'
    ],
    '[ts_url_base_page]' => [
        'description' => '<div>Get platform base page link</div>',
        'example' => 'echo do_shortcode( \'[ts_url_base_page]\' );'
    ],
    '[ts_url_binary_page]' => [
        'description' => '<div>Get platform binary page link</div>',
        'example' => 'echo do_shortcode( \'[ts_url_binary_page]\' );'
    ],
    '[ts_url_cfd_page]' => [
        'description' => '<div>Get platform CFD page link</div>',
        'example' => 'echo do_shortcode( \'[ts_url_cfd_page]\' );'
    ],
    '[ts_url_trading_room_page]' => [
        'description' => '<div>Get platform trading room link</div>',
        'example' => 'echo do_shortcode( \'[ts_url_trading_room_page]\' );'
    ],
    '[ts_url_deposit_page]' => [
        'description' => '<div>Get platform deposit link</div>',
        'example' => 'echo do_shortcode( \'[ts_url_deposit_page]\' );'
    ],
    '[ts_url_withdrawal_page]' => [
        'description' => '<div>Get platform withdrawal page link</div>',
        'example' => 'echo do_shortcode( \'[ts_url_withdrawal_page]\' );'
    ],
    '[ts_url_cfd_chart_frame_page]' => [
        'description' => '<div>Get platform CFD chart frame link</div>',
        'example' => 'echo do_shortcode( \'[ts_url_cfd_chart_frame_page]\' );'
    ],

    '[ts_url_account_verification_page]' => [
        'description' => '<div>Get platform account verification link</div>',
        'example' => 'echo do_shortcode( \'[ts_url_account_verification_page]\' );'
    ],

    '[ts_url_change_password_page]' => [
        'description' => '<div>Get platform change password link</div>',
        'example' => 'echo do_shortcode( \'[ts_url_change_password_page]\' );'
    ],
    '[ts_url_account_details_page]' => [
        'description' => '<div>Get platform account details link</div>',
        'example' => 'echo do_shortcode( \'[ts_url_account_details_page]\' );'
    ],
    '[ts_url_switch_demo_account_page]' => [
        'description' => '<div>Get platform switch to demo account link</div>',
        'example' => 'echo do_shortcode( \'[ts_url_switch_demo_account_page]\' );'
    ],
    '[ts_url_settings_page]' => [
        'description' => '<div>Get platform settings page link</div>',
        'example' => 'echo do_shortcode( \'[ts_url_settings_page]\' );'
    ],
    '[ts_url_reports_page]' => [
        'description' => '<div>Get platform reports page link</div>',
        'example' => 'echo do_shortcode( \'[ts_url_reports_page]\' );'
    ],
    '[ts_url_script_users_info]' => [
        'description' => '<div>Get platform url users info script</div>',
        'example' => 'echo do_shortcode( \'[ts_url_script_users_info]\' );'
    ],
    '[ts_url_script_assets_rss]' => [
        'description' => '<div>Get platform url assets rss script</div>',
        'example' => 'echo do_shortcode( \'[ts_url_script_assets_rss]\' );'
    ],
    '[ts_url_script_withdrawal]' => [
        'description' => '<div>Get platform url withdrawal script</div>',
        'example' => 'echo do_shortcode( \'[ts_url_script_withdrawal]\' ); '
    ],
    '[ts_url_script_crm_lib]' => [
        'description' => '<div>Get platform url CRM lib script</div>',
        'example' => 'echo do_shortcode( \'[ts_url_script_crm_lib]\' );'
    ],
    '[trader_show_professional_form]' => [
        'description' => '<div>Checking can trader see professional form </div>',
        'example' => 'echo do_shortcode( \'[trader_show_professional_form]\' );'
    ],
];