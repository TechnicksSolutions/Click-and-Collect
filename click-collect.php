<?php
/**
 * @package click_collect
 * @version 1.0.0
 */
/*
Plugin Name: Click and Collect for WooCommerce
Plugin URI: https://www.technicks.com
Description: Implements Multiple Branch Click and Collect for WooCommerce
Author: Edward Nickerson
Version: 1.0.0
Author URI: https://www.technicks.com
*/

define('cac_FUNCTIONSPATH', plugin_dir_path( __FILE__ ) . '/includes/');
define('cac_PLUGINPATH', plugin_dir_path( __FILE__ ) );
foreach (glob(cac_FUNCTIONSPATH.'autoinc-*.php') as $filename)
{
    require_once ($filename);
}