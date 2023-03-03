<?php
/*
Plugin Name: NetWise PDF Generator
Plugin URI: https://netwiseuk.com/
Description: A plugin that generates or convert the plain text documents into PDFs using mPDF.
Version: 1.0.0
Author: fysalyaqoob
Author URI: https://fysalyaqoob.com
License: GPL2
*/

// Security check
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Load the Plugin Update Checker library.
if ( ! class_exists( 'Puc_v4_Factory' ) ) {
    require_once plugin_dir_path( __FILE__ ) . 'plugin-update-checker/plugin-update-checker.php';
}
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

// Check for updates
$updateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/fysalyaqoob/netwise-pdf-generator/',
    __FILE__,
    'netwise-pdf-generator'
);

// Optional: If you're using a private repository, specify the access token like this:
$updateChecker->setAuthentication( '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' );

// Optional: Set the branch that contains the stable release.
$updateChecker->setBranch( 'main' );

// Load the plugin class file
require_once( plugin_dir_path( __FILE__ ) . 'class-netwise-pdf-generator.php' );

