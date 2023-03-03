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

// Load the plugin class file
require_once( plugin_dir_path( __FILE__ ) . 'class-netwise-pdf-generator.php' );

