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

class EU_Cookie_Law_Cacheable {
	const version = '3.0.0';

	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'init' ) );
	}


	public function get_options() {
		return get_option('peadig_eucookie');
	}

	public static function get_option( $name, $default = false ) {
		$options = get_option('peadig_eucookie');

		if ( isset( $options[$name] ) ) {
			return $options[$name];
		}

		return false;
	}


	public function load_textdomain() {
		load_plugin_textdomain( 'eu-cookie-law-cacheable' );
	}

	public function init() {
		if ( is_admin() ) {
			require_once 'class-admin.php';
			new EU_Cookie_Law_Cacheable_Admin();
		}
		else {
			require_once 'class-frontend.php';
			new EU_Cookie_Law_Cacheable_Frontend();
			new EU_Cookie_Law_Cacheable_Frontend_Block();
		}
	} 


	public function check_set_new_options() {
		if ( version_compare( self::version, get_option('ecl_version_number') ) == 1 ) {
			$this->set_new_defaults();
			update_option( 'ecl_version_number', self::version );   
		}

		if ( $this->option('tinymcebutton') ) {
			require 'inc/tinymce.php';
		}
	}

	public function set_new_options() {
		$my_options = get_option( 'peadig_eucookie' );
		$my_options = $this->sanitize_options( $my_options );

		update_option( 'peadig_eucookie', $my_options );
	}

	public static function sanitize_options( $options ) {
		$defaults = array(
			array( 'enabled', '0' ),
			array( 'lengthnum', '' ),
			array( 'length', 'months '),
			array( 'position', 'bottomright' ),
			array( 'barmessage', __('By continuing to use the site, you agree to the use of cookies.', 'eu-cookie-law-cacheable') ),
			array( 'barlink', __('more information', 'eu-cookie-law-cacheable') ),
			array( 'barbutton', __('Accept', 'eu-cookie-law-cacheable') ),
			array( 'closelink', __('Close', 'eu-cookie-law-cacheable') ),
			array( 'boxcontent', __('The cookie settings on this website are set to "allow cookies" to give you the best browsing experience possible. If you continue to use this website without changing your cookie settings or you click "Accept" below then you are consenting to this.', 'eu-cookie-law-cacheable') ),
			array( 'bhtmlcontent', __('<b>Content not available.</b><br><small>Please allow cookies by clicking Accept on the banner</small>', 'eu-cookie-law-cacheable') ),
			array( 'backgroundcolor', '#000000' ),
			array( 'fontcolor', '#FFFFFF' ),
			array( 'autoblock', '0' ),
			array( 'boxlinkblank', '0' ),
			array( 'tinymcebutton', '0' ),
			array( 'scrollconsent', '0' ),
			array( 'navigationconsent', '0' ),
			array( 'networkshare', '0' ),
			array( 'onlyeuropean', '0' ),
			array( 'customurl', get_site_url() ),
			array( 'cc-disablecookie', __('Revoke cookie consent', 'eu-cookie-law-cacheable') ),
			array( 'cc-cookieenabled', __('Cookies are enabled', 'eu-cookie-law-cacheable') ),
			array( 'cc-cookiedisabled', __('Cookies are disabled<br>Accept Cookies by clicking "%s" in the banner.', 'eu-cookie-law-cacheable') ),
			array( 'networkshareurl', self::get_shareurl() )
		);

		$count = count( $defaults );

		for ( $i = 0; $i < $count; $i++ ) {
			if ( ! isset( $options[ $defaults[$i][0] ] ) ) {
				$options[ $defaults[$i][0] ] = $defaults[$i][1];
			}
		}

		return $options;
	}

	private static function get_shareurl() {
		if ( is_multisite() ) {
			$sURL = network_site_url();
		} else {
			$sURL = site_url();
		}

		$asParts = parse_url( $sURL ); // PHP function

		if( ! $asParts ) {
			wp_die( 'ERROR: Path corrupt for parsing.' ); // replace this with a better error result
		}

		$sScheme = $asParts['scheme'];
		$nPort   = isset( $asParts['port'] ) ? $asParts['port'] : '';
		$sHost   = $asParts['host'];
		$nPort   = 80 == $nPort ? '' : $nPort;
		$nPort   = 'https' == $sScheme AND 443 == $nPort ? '' : $nPort;
		$sPort   = ! empty( $sPort ) ? ":$nPort" : '';
		$sReturn = $sHost . $sPort;

		return $sReturn;
	}

}

new EU_Cookie_Law_Cacheable;