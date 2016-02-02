<?php    

	$euCookieSet = 0;

add_action( 'send_headers', 'eucookie_headers' );
function eucookie_headers() {
	if ( isset($_GET['nocookie']) ) {
		setcookie('euCookie', '', 1, '/');
		global $euCookieSet;
		$euCookieSet = 0;
	}
}

function eucookie_scripts() {
	
	global $euCookieSet;
	global $deleteCookieUrlCheck;
	
	if ( !isset($_GET['nocookie']) && wp_get_referer() && EU_Cookie_Law_Cacheable::get_option('navigationconsent') && (!cookie_accepted()) && (EU_Cookie_Law_Cacheable::get_option('boxlinkid') != get_the_ID()) ) {
		$euCookieSet = 1;
	}
	
	
	if ( ecl_isSearchEngine() ) {
		$euCookieSet = 1;
	}
	
	wp_register_style	('basecss', plugins_url('css/style.css', __FILE__), false);
	wp_enqueue_style	('basecss');
	
	$eclData = array(
		'euCookieSet' => $euCookieSet,
		'autoBlock' =>  EU_Cookie_Law_Cacheable::get_option('autoblock'),
		'expireTimer' => get_expire_timer(),
		'scrollConsent' => EU_Cookie_Law_Cacheable::get_option('scrollconsent'),
		'networkShareURL' => ecl_get_cookie_domain(),
		'isCookiePage' => EU_Cookie_Law_Cacheable::get_option('boxlinkid') == get_the_ID(),
		'isRefererWebsite' => EU_Cookie_Law_Cacheable::get_option('navigationconsent') && wp_get_referer(),
		'deleteCookieUrl' => esc_url( add_query_arg( 'nocookie', '1', get_permalink() ) )
	);
	
	wp_enqueue_script(
		'eucookielaw-scripts',
		plugins_url('js/scripts.js', __FILE__),
		array( 'jquery' ),
		'',
		true
	);
	wp_localize_script('eucookielaw-scripts','eucookielaw_data',$eclData);
	
}
add_action('wp_head', 'eucookie_scripts');

function ecl_isSearchEngine(){
	$engines  = array(
		'google',
		'googlebot',
		'yahoo',
		'facebook',
		'twitter',
		'slurp',
		'search.msn.com',
		'nutch',
		'simpy',
		'bot',
		'aspseek',
		'crawler',
		'msnbot',
		'libwww-perl',
		'fast',
		'baidu',
	);
				
	if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
		return false;
	}
	$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
	foreach ( $engines as $engine ) {
		if (stripos($ua, $engine) !== false) {
			return true;
		}
		return false;
	}
}

function ecl_get_cookie_domain() {
	
	if ( EU_Cookie_Law_Cacheable::get_option('networkshare') ) {
		return 'domain='.EU_Cookie_Law_Cacheable::get_option('networkshareurl').'; ';
	}
	return '';
}

function cookie_accepted() {
	global $euCookieSet;
	
	if ( ! EU_Cookie_Law_Cacheable::get_option('enabled') ) { return true; }
	
	if ( ( isset( $_COOKIE['euCookie'] ) && !isset( $_GET['nocookie'] ) ) || $euCookieSet ) {
		return true;
	} else {
		return false;
	}
}

function get_expire_timer() {
	switch( EU_Cookie_Law_Cacheable::get_option('length') ){
		case "weeks":
			$multi = 7;
			break;
		case "months":
			$multi = 30;
			break;
		default: // Days
			$multi = 1;
			break;
	}
	return $multi * EU_Cookie_Law_Cacheable::get_option('lengthnum');
}
	
function peadig_eucookie_bar() {
	if ( cookie_accepted()  ) {
		return;
	}
	
	$target = '';
	if ( EU_Cookie_Law_Cacheable::get_option('boxlinkid') == 'C') {
		$link =  EU_Cookie_Law_Cacheable::get_option('customurl');
		if ( EU_Cookie_Law_Cacheable::get_option('boxlinkblank') ) { $target = 'target="_blank" '; }
	} else if ( EU_Cookie_Law_Cacheable::get_option('boxlinkid') ) {
		$link = get_permalink( apply_filters( 'wpml_object_id', EU_Cookie_Law_Cacheable::get_option('boxlinkid'), 'page' ) );
		if ( EU_Cookie_Law_Cacheable::get_option('boxlinkblank') ) { $target = 'target="_blank" '; }
	} else {
		$link = '#';
	}
?>
		<!-- Eu Cookie Law <?php echo get_option( 'ecl_version_number' ); ?> -->
		<div
			class="pea_cook_wrapper pea_cook_<?php echo EU_Cookie_Law_Cacheable::get_option('position'); ?>"
			style="
				color:<?php echo ecl_frontstyle('fontcolor'); ?>;
				background-color: rgba(<?php echo ecl_frontstyle('backgroundcolor'); ?>,0.85);
			">
			<p><?php echo EU_Cookie_Law_Cacheable::get_option('barmessage'); ?> <a style="color:<?php echo EU_Cookie_Law_Cacheable::get_option('fontcolor'); ?>;" href="<?php echo $link; ?>" <?php echo $target; ?>id="fom"><?php echo EU_Cookie_Law_Cacheable::get_option('barlink'); ?></a> <button id="pea_cook_btn" class="pea_cook_btn" href="#"><?php echo EU_Cookie_Law_Cacheable::get_option('barbutton'); ?></button></p>
		</div>
		<div class="pea_cook_more_info_popover">
			<div
				 class="pea_cook_more_info_popover_inner"
				 style="
					color:<?php echo ecl_frontstyle('fontcolor'); ?>;
					background-color: rgba(<?php echo ecl_frontstyle('backgroundcolor'); ?>,0.9);
					">
			 <p><?php echo EU_Cookie_Law_Cacheable::get_option('boxcontent'); ?></p>
				<p><a style="color:<?php echo EU_Cookie_Law_Cacheable::get_option('fontcolor'); ?>;" href="#" id="pea_close"><?php echo EU_Cookie_Law_Cacheable::get_option('closelink'); ?></a></p>
			</div>
		</div>
<?php
}
add_action('wp_footer', 'peadig_eucookie_bar', 1000);

function generate_cookie_notice_text($height, $width, $text) {
	return '<div class="eucookie" style="color:'.ecl_frontstyle('fontcolor').'; background: rgba('.ecl_frontstyle('backgroundcolor').',0.85) url(\''.plugins_url('img/block.png', __FILE__).'\') no-repeat; background-position: -30px -20px; width:'.$width.';height:'.$height.';"><span>'.$text.'</span></div><div class="clear"></div>';    
}

function generate_cookie_notice($height, $width) {
	return generate_cookie_notice_text($height, $width, EU_Cookie_Law_Cacheable::get_option('bhtmlcontent') );
}
function eu_cookie_shortcode( $atts, $content = null ) {
	extract(shortcode_atts(
		array(
			'height' => '',
			'width' => '',
			'text' => EU_Cookie_Law_Cacheable::get_option('bhtmlcontent')
		),
		$atts)
	);
	if ( cookie_accepted() ) {
		return do_shortcode( $content );
	} else {
		if (!$width) { $width = pulisci($content,'width='); }
		if (!$height) { $height = pulisci($content,'height='); }
		return generate_cookie_notice($height, $width);
	}
}
add_shortcode( 'cookie', 'eu_cookie_shortcode' );


//add_filter( 'the_content', 'ecl_erase', 11); 
//add_filter( 'widget_text','ecl_erase', 11 ); 

function ecl_buffer_start() { ob_start("ecl_callback"); } 
function ecl_buffer_end() { ob_end_flush();	}
function ecl_callback($buffer) { return ecl_erase($buffer); }

add_action('wp_head', 'ecl_buffer_start'); 
add_action('wp_footer', 'ecl_buffer_end'); 

function ecl_erase($content) {
	if ( !cookie_accepted() && EU_Cookie_Law_Cacheable::get_option('autoblock') &&
		!(get_post_field( 'eucookielaw_exclude', get_the_id() ) )
	   ) {
		
		$content = preg_replace('#<iframe.*?\/iframe>|<object.*?\/object>|<embed.*?>#is', generate_cookie_notice('auto', '100%'), $content);
		$content = preg_replace('#<script.(?:(?!eucookielaw_exclude).)*?\/script>#is', '', $content);
		$content = preg_replace('#<!cookie_start.*?\!cookie_end>#is', generate_cookie_notice('auto', '100%'), $content);
		$content = preg_replace('#<div id=\"disqus_thread\".*?\/div>#is', generate_cookie_notice('auto', '100%'), $content);
	}
	return $content;
}

//Compatibility for Jetpack InfiniteScroll
add_filter( 'infinite_scroll_js_settings', 'ecl_infinite_scroll_js_settings' );
function ecl_infinite_scroll_js_settings($js_settings) {
	return array_merge ( $js_settings, array( 'eucookielaw_exclude' => 1) );
}

add_filter( 'widget_text', 'do_shortcode');

function pulisci($content,$ricerca){
	$caratteri = strlen($ricerca)+6;
	$stringa = substr($content, strpos($content, $ricerca), $caratteri);
	$stringa = str_replace($ricerca, '', $stringa);
	$stringa = trim(str_replace('"', '', $stringa));
	return $stringa;
}

function ecl_hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
	  $r = hexdec(substr($hex,0,1).substr($hex,0,1));
	  $g = hexdec(substr($hex,1,1).substr($hex,1,1));
	  $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
	  $r = hexdec(substr($hex,0,2));
	  $g = hexdec(substr($hex,2,2));
	  $b = hexdec(substr($hex,4,2));
   }
   return array($r, $g, $b);
}

function ecl_frontstyle($name) {
	switch ($name) {
	case 'fontcolor':
		return EU_Cookie_Law_Cacheable::get_option('fontcolor');
		break;
	case 'backgroundcolor':
		$backgroundcolors = ecl_hex2rgb( EU_Cookie_Law_Cacheable::get_option('backgroundcolor') );
		return $backgroundcolors[0].','.$backgroundcolors[1].','.$backgroundcolors[2];
		break;
	}
}