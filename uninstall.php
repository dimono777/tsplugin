<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

global $wpdb;

// Delete setting table after uninstall plugin
$wpdb->query(sprintf("DROP TABLE IF EXISTS %s", $wpdb->prefix . 'ts_settings'));