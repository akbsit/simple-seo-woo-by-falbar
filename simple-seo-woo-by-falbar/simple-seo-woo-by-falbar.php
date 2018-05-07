<?php
/*
Plugin Name: Simple SEO Woo by falbar
Plugin URI: http://falbar.ru/
Description: This plugin extends the standard SEO WordPress features for WooCommerce.
Version: 1.1
Author: Anton Kuleshov
Author URI: http://falbar.ru/
*/

if(!defined('ABSPATH')){

	die();
}

define('SSWBF', true);

define('SSWBF_BASE', dirname(__FILE__));
define('SSWBF_DS', DIRECTORY_SEPARATOR);

define('SSWBF_DIR_INC', SSWBF_BASE.SSWBF_DS.'includes'.SSWBF_DS);

require_once(SSWBF_DIR_INC.'class-falbar-sswbf-core.php');
require_once(SSWBF_DIR_INC.'class-falbar-sswbf.php');

$sswbf = new Falbar_SSWBF();
$sswbf->run();