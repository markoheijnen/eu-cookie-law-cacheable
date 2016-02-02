<?php
class EU_Cookie_Law_Cacheable_Frontend {

	public function __construct() {
		if ( EU_Cookie_Law_Cacheable::get_option('enabled') ) {
			add_action( 'wp_head', array( $this, 'cookie_scripts' ) );
			add_action( 'wp_footer', array( $this, 'cookie_bar' ), 1000 );
		}
	}

	public function cookie_scripts() {
		global $deleteCookieUrlCheck;

		wp_register_style( 'basecss', plugins_url( 'css/style.css', __FILE__ ), false );
		wp_enqueue_style('basecss');

		$eclData = array(
			'autoBlock'        => EU_Cookie_Law_Cacheable::get_option('autoblock'),
			'expireTimer'      => $this->get_expire_timer(),
			'scrollConsent'    => EU_Cookie_Law_Cacheable::get_option('scrollconsent'),
			'networkShareURL'  => $this->get_cookie_domain(),
			'isCookiePage'     => EU_Cookie_Law_Cacheable::get_option('boxlinkid') == get_the_ID(),
			'isRefererWebsite' => EU_Cookie_Law_Cacheable::get_option('navigationconsent') && wp_get_referer()
		);

		wp_enqueue_script(
			'eucookielaw-scripts',
			plugins_url('js/scripts.js', __FILE__),
			array( 'jquery' ),
			'',
			true
		);

		wp_localize_script( 'eucookielaw-scripts', 'eucookielaw_data', $eclData );
		
	}

	public function cookie_bar() {
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




	private function get_cookie_domain() {
		if ( EU_Cookie_Law_Cacheable::get_option('networkshare') ) {
			return 'domain=' . EU_Cookie_Law_Cacheable::get_option('networkshareurl') . '; ';
		}

		return '';
	}

	private function get_expire_timer() {
		switch( EU_Cookie_Law_Cacheable::get_option('length') ) {
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

}

class EU_Cookie_Law_Cacheable_Frontend_Block {

	public function __construct() {
		add_shortcode( 'cookie', array( $this, 'eu_cookie_shortcode' ) );
		add_filter( 'widget_text', 'do_shortcode' );


		//Compatibility for Jetpack InfiniteScroll
		add_filter( 'infinite_scroll_js_settings', array( $this, 'infinite_scroll_js_settings' ) );

		add_action( 'wp_head', array( $this, 'buffer_start' ) ); 
		add_action( 'wp_footer', array( $this, 'buffer_end' ) ); 
	}

	public function eu_cookie_shortcode( $atts, $content = null ) {
		extract(shortcode_atts(
			array(
				'height' => '',
				'width' => '',
				'text' => EU_Cookie_Law_Cacheable::get_option('bhtmlcontent')
			),
			$atts)
		);

		if ( ! $width ) {
			$width = $this->pulisci( $content, 'width=' );
		}

		if ( ! $height ) {
			$height = $this->pulisci( $content, 'height=' );
		}

		return $this->generate_cookie_notice( $height, $width );
	}

	public function generate_cookie_notice( $height, $width ) {
		return $this->generate_cookie_notice_text( $height, $width, EU_Cookie_Law_Cacheable::get_option('bhtmlcontent') );
	}

	public function generate_cookie_notice_text( $height, $width, $text ) {
		return '<span class="eucookie" style="color:' . ecl_frontstyle('fontcolor').'; background: rgba(' . ecl_frontstyle('backgroundcolor').',0.85) url(\'' . plugins_url('img/block.png', __FILE__).'\') no-repeat; background-position: -30px -20px; width:' . $width . ';height:' . $height . ';"><span>' . $text . '</span></span><span class="clear"></span>';    
	}


	private function pulisci($content,$ricerca){
		$caratteri = strlen( $ricerca ) + 6;
		$stringa   = substr( $content, strpos( $content, $ricerca ), $caratteri );
		$stringa   = str_replace( $ricerca, '', $stringa );
		$stringa   = trim( str_replace( '"', '', $stringa ) );

		return $stringa;
	}


	public function infinite_scroll_js_settings( $js_settings ) {
		return array_merge ( $js_settings, array( 'eucookielaw_exclude' => 1 ) );
	}


	public function buffer_start() {
		ob_start( array( $this, 'ecl_callback' ) );
	}

	public function buffer_end() {
		ob_end_flush();
	}

	public function ecl_callback( $buffer ) {
		return $this->erase( $buffer );
	}

	public function erase($content) {
		if ( EU_Cookie_Law_Cacheable::get_option('autoblock') && ! ( get_post_field( 'eucookielaw_exclude', get_the_id() ) ) ) {
			$content = preg_replace_callback( '#<iframe.*?\/iframe>|<object.*?\/object>|<embed.*?>#is', function( $matches ) {
				$matches[0] = str_replace( 'src="', 'data-src="', $matches[0] );

				$new_html  = '<span class="eu-cookie-law-embed">';
				$new_html .= '<span class="eu-embed" style="display:none;">' . $matches[0] . '</span>';
				$new_html .= $this->generate_cookie_notice('auto', '100%');
				$new_html .= '</span>';

				return $new_html;
			}, $content );

			// Old integration
			$content = preg_replace( '#<script.(?:(?!eucookielaw_exclude).)*?\/script>#is', '', $content );
			$content = preg_replace( '#<!cookie_start.*?\!cookie_end>#is', $this->generate_cookie_notice('auto', '100%'), $content );
			$content = preg_replace( '#<div id=\"disqus_thread\".*?\/div>#is', $this->generate_cookie_notice('auto', '100%'), $content );
		}

		return $content;
	}
}



function ecl_hex2rgb($hex) {
   $hex = str_replace( '#', '', $hex );

   if ( strlen($hex) == 3 ) {
	  $r = hexdec(substr($hex,0,1).substr($hex,0,1));
	  $g = hexdec(substr($hex,1,1).substr($hex,1,1));
	  $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   }
   else {
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
		case 'backgroundcolor':
			$backgroundcolors = ecl_hex2rgb( EU_Cookie_Law_Cacheable::get_option('backgroundcolor') );
			return $backgroundcolors[0].','.$backgroundcolors[1].','.$backgroundcolors[2];
	}
}