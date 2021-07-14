<?php
namespace tradersoft\inc;

use tradersoft\helpers\Arr;
use tradersoft\helpers\Html;
use tradersoft\helpers\Interlayer_Crm;
use tradersoft\helpers\Link;
use tradersoft\helpers\Assets;
use tradersoft\helpers\Platform;
use tradersoft\helpers\multi_language\Multi_Language;

/**
 * Class for short code.
 *
 * <autodoc> - deprecated, for documentation use configs/short_codes.php
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Short_Code
{
    private $_notInit = [
        '__construct',
    ];

    public function __construct()
    {
        foreach (get_class_methods($this) as $method) {
            if (!in_array($method, $this->_notInit)) {
                $this->$method();
            }
        }
    }

    /**
     * <autodoc>
     * <div><b>[trader_name]</b> - Get trader name (Use example: echo do_shortcode( '[trader_name]' );)</div>
     * </autodoc>
     */
    public function _traderName()
    {
        add_shortcode('trader_name', function() {
            if (!\TSInit::$app->trader->isGuest) {
                return \TSInit::$app->trader->fullName;
            }
            return '';
        });
    }

    /**
     * <autodoc>
     * <div><b>[get_link_by_short_code]</b> - Get link by short code (Use example: echo do_shortcode( '[get_link_by_short_code code="TS-FORGOT-PASSWORD" text="Forgot password?" class="class" id="id"]' );)</div>
     * </autodoc>
     */
    public function _getLinkByShortCode()
    {
        add_shortcode('get_link_by_short_code', function($param) {
            if (isset($param['code'])) {
                $classes = Arr::get($param, 'class', '');
                $id = Arr::get($param, 'id', '');

                switch ($param['code']) {
                    case 'TS-FORGOT-PASSWORD':
                        $link = Link::getTraderForgotLink();
                        break;
                    case 'TS-REGISTRATION':
                        $link = Link::getTraderRegistrationLink();
                        break;
                    default:
                        $link = Link::getForPageWithKey('[' . $param['code'] . ']');
                }

                $text = Arr::get($param, 'text');
                $text = ($text) ? \TS_Functions::__($text) : '';
                return '<a href="' . $link . '" id="' . $id . '" class="' . $classes . '">' . $text . '</a>';
            }
            return '';
        });
    }

    /**
     * <autodoc>
     * <div><b>[ts_link]</b> - Get any link (Use example: echo do_shortcode( '[ts_link url="http://site.com" text="Link text" class="class" id="id"]' );)</div>
     * </autodoc>
     */
    public function _getAnyLink()
    {
        add_shortcode('ts_link', function($param) {
            $linkParams = [];
            if (isset($param['class'])) {
                $linkParams['class'] = $param['class'];
            }
            if (isset($param['id'])) {
                $linkParams['id'] = $param['id'];
            }
            if (isset($param['target'])) {
                $linkParams['target'] = $param['target'];
            }

            return Html::a(Arr::get($param, 'text'), Arr::get($param, 'url'), $linkParams);
        });
    }

    /**
     * <autodoc>
     * <div><b>[trader_account_number]</b> - Return trader account number (Use example: echo do_shortcode( '[trader_account_number]' );)</div>
     * </autodoc>
     */
    public function _traderAccountNumber()
    {
        add_shortcode('trader_account_number', function()
        {
            if (!\TSInit::$app->trader->isGuest) {
                return \TSInit::$app->trader->accountNumber;
            }
            return '';
        }
        );
    }

    /**
     * <autodoc>
     * <div><b>[ts_is_guest]</b> - Return true if user isn't logged (Use example: do_shortcode( '[ts_is_guest]' );)</div>
     * </autodoc>
     */
    public function _isGuest()
    {
        add_shortcode('ts_is_guest', function ()
        {
            return \TSInit::$app->trader->isGuest;
        }
        );
    }

    /**
     * <autodoc>
     * <div><b>[ts_platform_url]</b> - Get platform url (Use example: echo do_shortcode( '[ts_platform_url]' );)</div>
     * </autodoc>
     */
    public function _getPlatformURL()
    {
        add_shortcode('ts_platform_url', function ()
        {
            return Platform::getURL();
        }
        );
    }

    /**
     * <autodoc>
     * <div><b>[ts_main_domain]</b> - Get main domain (Use example: echo do_shortcode( '[ts_main_domain]' );)</div>
     * </autodoc>
     */
    public function _getMainDomain()
    {
        add_shortcode('ts_main_domain', function ($data)
        {
            $step = !empty($data['step']) ? $data['step'] : null;

            return \TSInit::$app->request->getMainDomain($step);
        }
        );
    }

    /**
     * <autodoc>
     * <div><b>[ts_forgot_link]</b> - Get forgot link (Use example: echo do_shortcode( '[ts_forgot_link]' );)</div>
     * </autodoc>
     */
    public function _forgotLink()
    {
        add_shortcode('ts_forgot_link', function ()
        {
            return Link::getTraderForgotLink();
        }
        );
    }

    /**
     * <autodoc>
     * <div><b>[ts_get_link]</b> - Get link by url (Use example: echo do_shortcode( '[ts_get_link url="/example"]' );)</div>
     * </autodoc>
     */
    public function _getLink()
    {
        add_shortcode('ts_get_link', function ($data)
        {
            if (empty($data['url'])) {
                return '';
            }

            return \TSInit::$app->request->getLink($data['url']);
        }
        );
    }

    /**
     * <autodoc>
     * <div><b>[ts_get_page_link]</b> - Get url by key (Use example: echo do_shortcode( '[ts_get_page_link key="TS-REGISTRATION"]' );  or echo do_shortcode( '[ts_get_page_link key="TS-FORMS" type="Registration"]' );)</div>
     * </autodoc>
     */
    public function _getPageLink()
    {
        add_shortcode('ts_get_page_link', function ($data)
        {
            if (empty($data['key'])) {
                return '';
            }

            if ($data['key'] == 'TS-REGISTRATION') {
                return Link::getTraderRegistrationLink();
            }

            $pageKey = '[' . $data['key'] . ']';
            unset($data['key']);

            return Link::getForPageWithKey($pageKey, $data);
        }
        );
    }

    /**
     * <autodoc>
     * <div><b>[ts_get_form_link]</b> - Get html by key(<a href="">Link title</a>) (Use example: echo do_shortcode( '[ts_get_form_link  key="TS-FORMS" type="Registration" text="Link title" param1="val1"]' );)</div>
     * </autodoc>
     */
    public function _getHtmlLink()
    {
        add_shortcode('ts_get_form_link', [Html::class, 'getFormLink']);
    }

    /**
     * <autodoc>
     * <div><b>[ts_phone_code_by_ip]</b> - Get phone code by IP (Use example: echo do_shortcode( '[ts_phone_code_by_ip]' );)</div>
     * </autodoc>
     */
    public function _phoneCodeByIP()
    {
        add_shortcode('ts_phone_code_by_ip', function ()
        {
            return Interlayer_Crm::getPhoneCodeByIP();
        }
        );
    }

    /**
     * <autodoc>
     * <div><b>[ts_assets_base_url]</b> - Get base theme assets url (Use example: echo do_shortcode( '[ts_assets_base_url folder="/folder"]' );)</div>
     * </autodoc>
     */
    public function _assetsBaseUrl()
    {
        add_shortcode('ts_assets_base_url', function($data)
        {
            $folder = !empty($data['folder']) ? $data['folder'] : null;

            return Assets::getActualContainer($folder);

        }
        );
    }

    /**
     * <autodoc>
     * <div><b>[ts_asset_url file=""]</b> - Returns URL of an asset file if found (Use example: echo do_shortcode( '[ts_asset_url file="/folder/my.css"]' );)</div>
     * </autodoc>
     */
    public function _assetUrl()
    {
        add_shortcode(
            'ts_asset_url',
            function($data)
            {
                return Assets::findUrl(Arr::get($data, 'file', ''));
            }
        );
    }

    /**
     * <autodoc>
     * <div><b>[ts_is_trader_guest]</b> - Get content for guest trader (Use example: echo do_shortcode( '[ts_is_trader_guest]Content for guest trader[/ts_is_trader_guest]' );)</div>
     * </autodoc>
     */
    public function _isTraderGuest()
    {
        add_shortcode('ts_is_trader_guest', function($data, $content = '')
        {
            if (\TSInit::$app->trader->isGuest) {
                return do_shortcode($content);
            } else {
                return '';
            }
        }
        );
    }

    /**
     * <autodoc>
     * <div><b>[ts_is_trader_logged]</b> - Get content for logged trader (Use example: echo do_shortcode( '[ts_is_trader_logged]Content for logged trader[/ts_is_trader_logged]' );)</div>
     * </autodoc>
     */
    public function _isTraderLogged()
    {
        add_shortcode('ts_is_trader_logged', function($data, $content = '')
        {
            if (!\TSInit::$app->trader->isGuest) {
                return do_shortcode($content);
            } else {
                return '';
            }
        }
        );
    }

    /**
     * <autodoc>
     * <div><b>[ts_geo_content]</b> - Get Content for specific country (Use example: echo do_shortcode( '[ts_geo_content country="UA"]Content for trader from UA[/ts_geo_content]' );)</div>
     * </autodoc>
     */
    public function _isGeoContent()
    {
        add_shortcode('ts_geo_content', function($data, $content = '')
        {
            $countries = explode(',', Arr::get($data, 'country', ''));
            $show = (bool) Arr::get($data, 'show', '1');

            $realCountryData = Interlayer_Crm::getCountryByIP(\TSInit::$app->request->userIP);
            $realCountry = Arr::get($realCountryData, 'country_code');
            if (empty($realCountry)) {
                return '';
            }

            if (
                (in_array($realCountry, $countries) && $show)
                || (!in_array($realCountry, $countries) && !$show)
            ) {
                return do_shortcode($content);
            } else {
                return '';
            }
        }
        );
    }

    /**
     * <autodoc>
     * <div><b>[ts_get_trader_info]</b> - Get trader info by key (Use example: echo do_shortcode( '[ts_get_trader_info key="fname"]' );)</div>
     * </autodoc>
     */
    public function _getTraderInfo()
    {
        add_shortcode('ts_get_trader_info', function($data) {
            if (empty($data['key'])) {
                return '';
            }
            return \TSInit::$app->trader->get(
                $data['key'],
                ((isset($data['default'])) ? $data['default'] : null)
            );
        });
    }

    /**
     * <autodoc>
     * <div><b>[ts_get_trader_balance]</b> - Get trader balance (Use example: echo do_shortcode( '[ts_get_trader_balance]' );)</div>
     * </autodoc>
     */
    public function _getTraderBalance()
    {
        add_shortcode('ts_get_trader_balance', function() {
            return \TSInit::$app->trader->getBalance();
        });
    }

    /**
     * <autodoc>
     * <div><b>[ts_is_local]</b> - Check if http host is local (Use example: do_shortcode( '[ts_is_local]' );)</div>
     * </autodoc>
     */
    public function _isLocal()
    {
        add_shortcode('ts_is_local', function() {
            return \TSInit::$app->request->isLocal;
        });
    }

    /**
     * <autodoc>
     * <div><b>[ts_is_trader_with_deposit]</b> - Get content for trader who had a deposit(Use example: echo do_shortcode( '[ts_is_trader_with_deposit]Content for trader who had a deposit[/ts_is_trader_with_deposit]' );)</div>
     * </autodoc>
     */
    public function _isTraderWithDeposit()
    {
        add_shortcode('ts_is_trader_with_deposit', function($data, $content = '') {
            if (!\TSInit::$app->trader->isGuest && \TSInit::$app->trader->deposited) {
                return do_shortcode($content);
            } else {
                return '';
            }
        });
    }

    /**
     * <autodoc>
     * <div><b>[ts_is_trader_without_deposit]</b> - Get content for trader who had not a deposit(Use example: echo do_shortcode( '[ts_is_trader_without_deposit]Content for trader who had not a deposit[/ts_is_trader_without_deposit]' );)</div>
     * </autodoc>
     */
    public function _isTraderWithoutDeposit()
    {
        add_shortcode('ts_is_trader_without_deposit', function($data, $content = '') {
            if (!\TSInit::$app->trader->isGuest && !\TSInit::$app->trader->deposited) {
                return do_shortcode($content);
            } else {
                return '';
            }
        });
    }

    /**
     * <autodoc>
     * <div><b>[ts_home_url]</b> - Get home url with current language (Use example: echo do_shortcode( '[ts_home_url]' );)</div>
     * </autodoc>
     * @author Alex Dryn <alexandr.dryn@tstechpro.com>
     */
    public function _getHomeUrl()
    {
        add_shortcode('ts_home_url', function() {
            return Multi_Language::getInstance()->getHomeUrl();
        });
    }

    /**
     * <autodoc>
     * <div><b>[ts_get_language]</b> - Get available languages config list (Use example: echo do_shortcode( '[ts_get_language]' );)</div>
     * </autodoc>
     * @author Alex Dryn <alexandr.dryn@tstechpro.com>
     */
    public function _getCurrentLanguage()
    {
        add_shortcode('ts_get_language', function() {
            return Multi_Language::getInstance()->getCurrentLanguage();
        });
    }

    /**
     * <autodoc>
     * <div><b>[ts_list_languages]</b> - Get available languages config list (Use example: echo do_shortcode( '[ts_list_languages]' );)</div>
     * </autodoc>
     * @author Alex Dryn <alexandr.dryn@tstechpro.com>
     */
    public function _getActiveLanguages()
    {
        add_shortcode('ts_list_languages', function() {
            return Multi_Language::getInstance()->getActiveLanguages();
        });
    }

    /**
     * <autodoc>
     * <div><b>[ts_set_language]</b> - Get available languages config list (Use example: echo do_shortcode( '[ts_set_language code="<LANG_CODE>" cookieLang="TRUE|FALSE"]' );)
     * <br>cookieLang - also change cookie lang, FALSE by default
     * </div>
     * </autodoc>
     * @author Alex Dryn <alexandr.dryn@tstechpro.com>
     */
    public function _setCurrentLanguage()
    {
        add_shortcode('ts_set_language', function($data = []) {
            return Multi_Language::getInstance()->switchLang($data['code'], $data['cookieLang']);
        });
    }

    /**
     * <autodoc>
     * <div><b>[ts_platform_full_url]</b> - Get specified platform full url (Use example: echo do_shortcode('[ts_platform_full_url linkId="PLATFORM_LINK_ID"]'); )
     * </div>
     * </autodoc>
     * @author Yaroslav Zinych <yaroslav.zinych@tstechpro.com>
     */
    public function _getPlatformFullUrl()
    {
        add_shortcode('ts_platform_full_url', function($data = []) {

            return Platform::getURL($data['linkid']);
        });
    }

    /**
     * <autodoc>
     * <div><b>[ts_assetsrss_url]</b> - Get specified platform full url (Use example: echo do_shortcode( '[ts_assetsrss_url]' ); )
     * </div>
     * </autodoc>
     * @author Yaroslav Zinych <yaroslav.zinych@tstechpro.com>
     */
    public function _getAssetsrssUrl()
    {
        add_shortcode('ts_assetsrss_url', function() {

            return Platform::getURL(Platform::URL_SCRIPT_ASSETS_RSS);
        });
    }

    public function _translate()
    {
        add_shortcode('ts_translate', function($data, $content = '') {
            return do_shortcode(\TS_Functions::__($content));
        });
    }

    /**
     * <autodoc>
     * <div><b>[ts_url_base_page]</b> - Get platform base page link (Use example: echo do_shortcode( '[ts_url_base_page]' ); )
     * </div>
     * </autodoc>
     * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
     */
    public function _platformUrlBasePage()
    {
        add_shortcode('ts_url_base_page', function() {
            return Platform::getURL(Platform::URL_BASE_ID);
        });
    }

    /**
     * <autodoc>
     * <div><b>[ts_url_binary_page]</b> - Get platform binary page link (Use example: echo do_shortcode( '[ts_url_binary_page]' ); )
     * </div>
     * </autodoc>
     * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
     */
    public function _platformUrlBinaryPage()
    {
        add_shortcode('ts_url_binary_page', function() {
            return Platform::getURL(Platform::URL_BINARY_ID);
        });
    }

    /**
     * <autodoc>
     * <div><b>[ts_url_cfd_page]</b> - Get platform CFD page link (Use example: echo do_shortcode( '[ts_url_cfd_page]' ); )
     * </div>
     * </autodoc>
     * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
     */
    public function _platformUrlCFDPage()
    {
        add_shortcode('ts_url_cfd_page', function() {
            return Platform::getURL(Platform::URL_CFD_ID);
        });
    }

    /**
     * <autodoc>
     * <div><b>[ts_url_trading_room_page]</b> - Get platform trading room link (Use example: echo do_shortcode( '[ts_url_trading_room_page]' ); )
     * </div>
     * </autodoc>
     * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
     */
    public function _platformUrlTradingRoomPage()
    {
        add_shortcode('ts_url_trading_room_page', function() {
            return Platform::getURL(Platform::URL_TRADE_ID);
        });
    }

    /**
     * <autodoc>
     * <div><b>[ts_url_deposit_page]</b> - Get platform deposit link (Use example: echo do_shortcode( '[ts_url_deposit_page]' ); )
     * </div>
     * </autodoc>
     * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
     */
    public function _platformUrlDepositPage()
    {
        add_shortcode('ts_url_deposit_page', function() {
            return Platform::getURL(Platform::URL_DEPOSIT_ID);
        });
    }

    /**
     * <autodoc>
     * <div><b>[ts_url_withdrawal_page]</b> - Get platform withdrawal page link (Use example: echo do_shortcode( '[ts_url_withdrawal_page]' ); )
     * </div>
     * </autodoc>
     * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
     */
    public function _platformUrlWithdrawalPage()
    {
        add_shortcode('ts_url_withdrawal_page', function() {
            return Platform::getURL(Platform::URL_WITHDRAW_ID);
        });
    }

    /**
     * <autodoc>
     * <div><b>[ts_url_cfd_chart_frame_page]</b> - Get platform CFD chart frame link (Use example: echo do_shortcode( '[ts_url_cfd_chart_frame_page]' ); )
     * </div>
     * </autodoc>
     * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
     */
    public function _platformUrlCFDChartFramePage()
    {
        add_shortcode('ts_url_cfd_chart_frame_page', function() {
            return Platform::getURL(Platform::URL_MINI_CHART_FRAME);
        });
    }

    /**
     * <autodoc>
     * <div><b>[ts_url_account_verification_page]</b> - Get platform account verification link (Use example: echo do_shortcode( '[ts_url_account_verification_page]' ); )
     * </div>
     * </autodoc>
     * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
     */
    public function _platformUrlAccountVerificationPage()
    {
        add_shortcode('ts_url_account_verification_page', function() {
            return Platform::getURL(Platform::URL_VERIFICATION);
        });
    }

    /**
     * <autodoc>
     * <div><b>[ts_url_change_password_page]</b> - Get platform change password link (Use example: echo do_shortcode( '[ts_url_change_password_page]' ); )
     * </div>
     * </autodoc>
     * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
     */
    public function _platformUrlChangePasswordPage()
    {
        add_shortcode('ts_url_change_password_page', function() {
            return Platform::getURL(Platform::URL_CHANGE_PASSWORD);
        });
    }

    /**
     * <autodoc>
     * <div><b>[ts_url_account_details_page]</b> - Get platform account details link (Use example: echo do_shortcode( '[ts_url_account_details_page]' ); )
     * </div>
     * </autodoc>
     * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
     */
    public function _platformUrlAccountDetailsPage()
    {
        add_shortcode('ts_url_account_details_page', function() {
            return Platform::getURL(Platform::URL_EDIT_DETAILS);
        });
    }

    /**
     * <autodoc>
     * <div><b>[ts_url_switch_demo_account_page]</b> - Get platform switch to demo account link (Use example: echo do_shortcode( '[ts_url_switch_demo_account_page]' ); )
     * </div>
     * </autodoc>
     * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
     */
    public function _platformUrlSwitchDemoAccountPage()
    {
        add_shortcode('ts_url_switch_demo_account_page', function() {
            return Platform::getURL(Platform::URL_SWITCH);
        });
    }

    /**
     * <autodoc>
     * <div><b>[ts_url_settings_page]</b> - Get platform settings page link (Use example: echo do_shortcode( '[ts_url_settings_page]' ); )
     * </div>
     * </autodoc>
     * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
     */
    public function _platformUrlSettingsPage()
    {
        add_shortcode('ts_url_settings_page', function() {
            return Platform::getURL(Platform::URL_SETTING);
        });
    }

    /**
     * <autodoc>
     * <div><b>[ts_url_reports_page]</b> - Get platform reports page link (Use example: echo do_shortcode( '[ts_url_reports_page]' ); )
     * </div>
     * </autodoc>
     * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
     */
    public function _platformUrlReportsPage()
    {
        add_shortcode('ts_url_reports_page', function() {
            return Platform::getURL(Platform::URL_STAT_CFD);
        });
    }

    /**
     * <autodoc>
     * <div><b>[ts_url_script_users_info]</b> - Get platform url users info script (Use example: echo do_shortcode( '[ts_url_script_users_info]' ); )
     * </div>
     * </autodoc>
     * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
     */
    public function _platformUrlUsersInfoScript()
    {
        add_shortcode('ts_url_script_users_info', function() {
            return Platform::getURL(Platform::URL_SCRIPT_USERS_INFO);
        });
    }

    /**
     * <autodoc>
     * <div><b>[ts_url_script_assets_rss]</b> - Get platform url assets rss script (Use example: echo do_shortcode( '[ts_url_script_assets_rss]' ); )
     * </div>
     * </autodoc>
     * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
     */
    public function _platformUrlAssetsRssScript()
    {
        add_shortcode('ts_url_script_assets_rss', function() {
            return Platform::getURL(Platform::URL_SCRIPT_ASSETS_RSS);
        });
    }

    /**
     * <autodoc>
     * <div><b>[ts_url_script_withdrawal]</b> - Get platform url withdrawal script (Use example: echo do_shortcode( '[ts_url_script_withdrawal]' ); )
     * </div>
     * </autodoc>
     * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
     */
    public function _platformUrlWithdrawalScript()
    {
        add_shortcode('ts_url_script_withdrawal', function() {
            return Platform::getURL(Platform::URL_SCRIPT_WITHDRAWAL);
        });
    }

    /**
     * <autodoc>
     * <div><b>[ts_url_script_crm_lib]</b> - Get platform url CRM lib script (Use example: echo do_shortcode( '[ts_url_script_crm_lib]' ); )
     * </div>
     * </autodoc>
     * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
     */
    public function _platformUrlCRMLibScript()
    {
        add_shortcode('ts_url_script_crm_lib', function() {
            return Platform::getURL(Platform::URL_SCRIPT_CRM_LIB);
        });
    }

    /**
     * <autodoc>
     * <div><b>[trader_show_professional_form]</b> - Checking can trader see professional form
     * (Use example: echo do_shortcode( '[trader_show_professional_form]' ); )
     * </div>
     * </autodoc>
     *
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    public function _traderShowProfessionalForm()
    {
        add_shortcode('trader_show_professional_form', function () {
            return Link::getTraderProfessionalForm() !== false;
        });
    }
}