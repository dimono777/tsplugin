<?php

// Multi language plugins (path to plugin => plugin's helper class name)
// the first active plugin from the array will be selected
return [
    'sitepress-multilingual-cms/sitepress.php' => \tradersoft\helpers\multi_language\Multi_Language_WPML::class,
    'wp-multilang/wp-multilang.php' =>  \tradersoft\helpers\multi_language\Multi_Language_WPM::class,
];