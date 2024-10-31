<?php

/**
 * Plugin Name: Rahrayan WP SMS PLUGIN
  * Plugin URI:  https://rahco.ir
 * Description: Rahrayan WordPress Plugin and woocommerce Plugins
 * Version:     0.5.1
 * Author:      Rahrayan
 * Author URI:  https:/rahco.ir/
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wporg
 * Domain Path: /languages
 */
//check access
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}
//include pluggable.php
require_once(ABSPATH . "wp-includes/pluggable.php");
//jalali date
if (!class_exists('jDateTime'))
    require_once dirname(__FILE__) . '/includes/jdatetime.class.php';
//define product version
$rahrayan_version = '0.5.1';
define('RAHRAYAN_VERSION', $rahrayan_version);
//include functions
require_once(dirname(__FILE__) . '/includes/functions.php');
//include rahrayan class
require_once(dirname(__FILE__) . '/includes/class.php');
$rahrayan = new rahrayan;
//register install hook
register_activation_hook(__FILE__, 'rahrayan_install');
//paginator
require_once dirname(__FILE__) . '/includes/paginator.php';
$rahrayan_page = (intval(get_option('rahrayan_page')) > 0 && intval(get_option('rahrayan_page')) < 31) ? intval(get_option('rahrayan_page')) : 10;
//include admin panel
require_once dirname(__FILE__) . '/includes/admin.php';
//include shortcode
require_once dirname(__FILE__) . '/includes/shortcode.php';
//plugin widget (abzarak!)
require_once dirname(__FILE__) . '/includes/widget.php';
//admin bar
require_once dirname(__FILE__) . '/includes/adminbar.php';
//plugin actions
require_once dirname(__FILE__) . '/includes/actions.php';
//Gravity Forms Mobile Verification
require_once dirname(__FILE__) . '/includes/GFVerification.php';