<?php
/*
Plugin Name:  EU Cookie Law Cachable
Plugin URI:   https://github.com/markoheijnen/eu-cookie-law-cacheable
Description:  EU Cookie Law informs users that your site uses cookies, with option to lock scripts before consent. Light + Customizable style.
Version:      3.0.0
Author:       Marko Heijnen, Alex Moss, Marco Milesi, Peadig, Shane Jones
Author URI:   https://github.com/markoheijnen/eu-cookie-law-cacheable
Contributors: markoheijnen, alexmoss, Milmor, peer, ShaneJones
Text Domain:  eu-cookie-law-cacheable
*/

function eucookie_start() {
	
	load_plugin_textdomain( 'eu-cookie-law-cacheable' );
	
	if ( is_admin() ) {
		require 'class-admin.php';
	} else {
		require 'class-frontend.php';
	}
	
} add_action('init', 'eucookie_start');

function ecl_action_admin_init() {
	$arraya_ecl_v = get_plugin_data ( __FILE__ );
	$new_version = $arraya_ecl_v['Version'];
		
	if ( version_compare($new_version,  get_option('ecl_version_number') ) == 1 ) {
		ecl_check_defaults();
		update_option( 'ecl_version_number', $new_version );   
	}

	if ( eucookie_option('tinymcebutton') ) {
		require 'inc/tinymce.php';
	}
	$eda = __('EU Cookie Law informs users that your site uses cookies, with option to lock scripts before consent. Light + Customizable style.', 'eu-cookie-law-cacheable');
} add_action('admin_init', 'ecl_action_admin_init');

function ecl_check_defaults() { require 'defaults.php'; }

function eucookie_option($name) {
	$options = get_option('peadig_eucookie');
	if ( isset( $options[$name] ) ) { return $options[$name]; }
	return false;
}