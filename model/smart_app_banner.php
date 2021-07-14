<?php
namespace tradersoft\model;

/**
 * Class Smart_App_Banner
 * @package tradersoft\model
 * @author Anatolii Lishchynskyi <anatolii.lishchynsky@tstechpro.com>
 */
class Smart_App_Banner
{
    const PREFIX = 'sab_';

    public $status;
    public $appleId;
    public $gPlayId;
    public $daysHidden;
    public $daysReminder;
    public $title;
    public $author;
    public $button;
    public $applePrice;
    public $gPlayPrice;
    public $image;

    protected $updated = false;

    public static function fields()
    {
        return [
            'status' => self::PREFIX . 'status',
            'appleId' => self::PREFIX . 'apple_id',
            'gPlayId' => self::PREFIX . 'g_play_id',
            'daysHidden' => self::PREFIX . 'days_hidden',
            'daysReminder' => self::PREFIX . 'days_reminder',
            'title' => self::PREFIX . 'title',
            'author' => self::PREFIX . 'author',
            'button' => self::PREFIX . 'button',
            'applePrice' => self::PREFIX . 'apple_price',
            'gPlayPrice' => self::PREFIX . 'g_play_price',
            'image' => self::PREFIX . 'image',
        ];
    }

    /**
     * Load settings
     */
    public function load()
    {
        foreach (self::fields() as $fieldName => $fieldOption) {
            $this->$fieldName = get_option($fieldOption);
        }
    }

    /**
     * Update settings
     *
     * @param $data
     * @return bool
     */
    public function update($data)
    {
        if (!isset($data['update_settings'])) {
            return false;
        }

        $sabRandom = esc_attr($data['sab_random']);
        check_admin_referer('sab_settings' . $sabRandom);

        foreach (self::fields() as $fieldName => $fieldOption) {
            update_option($fieldOption, esc_attr($data[$fieldOption]));
        }

        $this->load();

        $this->updated = true;
        return true;
    }

    public function isUpdated()
    {
        return $this->updated;
    }

    /**
     * Get list of statuses for select
     *
     * @return array
     */
    public static function getStatusList()
    {
        return [
            0 => 'Disabled',
            1 => 'Enabled',
        ];
    }

    /**
     * Generate random string
     *
     * @param int $length
     * @return string
     */
    public static function generateRandomString($length = 10)
    {
        return substr(
            str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'),
            0,
            $length
        );
    }

    /**
     * Set defaults values if empty
     */
    public function setDefaults()
    {
        if (empty($this->gPlayPrice)) {
            $this->gPlayPrice = 'Free';
        }

        if (empty($this->applePrice)) {
            $this->applePrice = 'Free';
        }

        if (empty($this->daysHidden)) {
            $this->daysHidden = 15;
        }

        if (empty($this->daysReminder)) {
            $this->daysReminder = 90;
        }

        if (empty($this->button)) {
            $this->button = 'View';
        }
    }

    /**
     * Init Smart App Banner
     *
     * @return bool
     */
    public static function init()
    {
        $smartBanner = new self();
        $smartBanner->load();
        $smartBanner->setDefaults();

        if (!$smartBanner->status) {
            return false;
        }
        add_action(
            'get_footer',
            static function() {
                wp_enqueue_style(
                    'smart-app-banner',
                    \tradersoft\helpers\Assets::findUrl('/smart-app-banner/smart-app-banner.css?v=2', 'system'),
                    false
                );
            },
            1
        );

        add_action('wp_enqueue_scripts', function () {
            wp_enqueue_script(
                'smart-app-banner',
                \tradersoft\helpers\Assets::findUrl('/smart-app-banner/smart-app-banner.min.js', 'system'),
                false,
                false,
                true
            );
        });

        add_action('wp_head', function () use ($smartBanner) {
            if ($smartBanner->appleId) {
                echo '<meta name="apple-itunes-app" content="app-id=' . $smartBanner->appleId . '">' . PHP_EOL;
            }
            if ($smartBanner->gPlayId) {
                echo '<meta name="google-play-app" content="app-id=' . $smartBanner->gPlayId . '">' . PHP_EOL;
            }
            if ($smartBanner->image) {
                echo '<link rel="apple-touch-icon" href="' . $smartBanner->image . '">' . PHP_EOL;
                echo '<link rel="android-touch-icon" href="' . $smartBanner->image . '" />' . PHP_EOL;
            }
        });

        add_action('wp_footer', function () use ($smartBanner) {
            echo "<script>
                new SmartBanner({
                    daysHidden: " . $smartBanner->daysHidden . ",   
                    daysReminder: " . $smartBanner->daysReminder . ",
                    title: '" . $smartBanner->title . "',
                    author: '" . $smartBanner->author . "',
                    button: '" . $smartBanner->button . "',
                    store: {
                        ios: 'On the App Store',
                        android: 'In Google Play'
                    },
                    price: {
                        ios: '" . $smartBanner->applePrice . "',
                        android: '" . $smartBanner->gPlayPrice . "'
                    }
                    //, force: 'ios' // Uncomment for platform emulation
                });
            </script>";
        });

        return true;
    }
}