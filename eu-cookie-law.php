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


	public static function get_options() {
		$options = get_option('eucookie');

		if ( ! $options ) {
			$options = self::default_options();
		}

		return $options;
	}

	public static function get_option( $name, $default = false ) {
		$options = get_option('eucookie');

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
		$my_options = get_option( 'eucookie' );
		$my_options = $this->sanitize_options( $my_options );

		update_option( 'eucookie', $my_options );
	}

	public static function sanitize_options( $options ) {
		$defaults = self::default_options();

		foreach ( $defaults as $key => $default_value ) {
			if ( ! isset( $options[ $key ] ) ) {
				$options[ $key ] = $default_value;
			}
		}

		return $options;
	}

	public static function default_options() {
		return array(
			'enabled' => '0',
			'lengthnum' => '',
			'length' => 'months',
			'position' => 'bottomright',
			'barmessage' => __('By continuing to use the site, you agree to the use of cookies.', 'eu-cookie-law-cacheable'),
			'barlink' => __('more information', 'eu-cookie-law-cacheable'),
			'barbutton' => __('Accept', 'eu-cookie-law-cacheable'),
			'closelink' => __('Close', 'eu-cookie-law-cacheable'),
			'boxcontent' => __('The cookie settings on this website are set to "allow cookies" to give you the best browsing experience possible. If you continue to use this website without changing your cookie settings or you click "Accept" below then you are consenting to this.', 'eu-cookie-law-cacheable'),
			'bhtmlcontent' => __('<b>Content not available.</b><br><small>Please allow cookies by clicking Accept on the banner</small>', 'eu-cookie-law-cacheable'),
			'backgroundcolor' => '#000000',
			'fontcolor' => '#FFFFFF',
			'autoblock' => '0',
			'boxlinkblank' => '0',
			'tinymcebutton' => '0',
			'scrollconsent' => '0',
			'navigationconsent' => '0',
			'networkshare' => '0',
			'onlyeuropean' => '0',
			'customurl' => get_site_url(),
			'cc-disablecookie' => __('Revoke cookie consent', 'eu-cookie-law-cacheable'),
			'cc-cookieenabled' => __('Cookies are enabled', 'eu-cookie-law-cacheable'),
			'cc-cookiedisabled' => __('Cookies are disabled<br>Accept Cookies by clicking "%s" in the banner.', 'eu-cookie-law-cacheable'),
			'networkshareurl' => self::get_shareurl(),
			'boxlinkid' => ''
		);
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