jQuery(document).ready(function($){

	var euCookieSet = eucookielaw_data.euCookieSet;
	var expireTimer = eucookielaw_data.expireTimer;
	var scrollConsent = eucookielaw_data.scrollConsent;
	var networkShareURL = eucookielaw_data.networkShareURL;
	var isCookiePage = eucookielaw_data.isCookiePage;
	var isRefererWebsite = eucookielaw_data.isRefererWebsite;
	var autoBlock = eucookielaw_data.autoBlock;
	
	if (document.cookie.indexOf("euCookie") >= 0) {
		euCookieSet = 1;
	}

	if ( euCookieSet > 0) {
		euCookieConsent(0);
		return;
	}

	$("#fom").click(function() {
		if( $('#fom').attr('href') === '#') { 
			$(".pea_cook_more_info_popover").fadeIn("slow");
			$(".pea_cook_wrapper").fadeOut("fast");
		}
	});

	$("#pea_close").click(function() {
		$(".pea_cook_wrapper").fadeIn("fast");
		$(".pea_cook_more_info_popover").fadeOut("slow");
	});

	$('#pea_cook_btn, .eucookie').on('click', function () {
		euCookieConsent('fast');
	});

	$(window).scroll(function(){
		if ( scrollConsent > 0 && !isCookiePage && !euCookieSet && document.cookie.indexOf("euCookie") < 0  ) {
			var current_scroll = parseInt( $(window).scrollTop() );
			var window_height = parseInt( $(window).height() );

			// Full page scroll
			if( current_scroll > window_height ) {
				euCookieSet = 1;
				euCookieConsent();
			}
			// Bottom
			if( current_scroll + window_height == $(document).height() ) {
				euCookieSet = 1;
				euCookieConsent();
			}
		}
	});

	function euCookieConsent(speed) {
		createCookie();
		$(".pea_cook_wrapper").fadeOut(speed);
		showembeds();
	}

	function createCookie() {
		var today = new Date(), expire = new Date();

		if (expireTimer > 0) {
			expire.setTime(today.getTime() + (expireTimer * 24 * 60 * 60 * 1000) );
			cookiestring = "euCookie=set; "+networkShareURL+"expires=" + expire.toUTCString() + "; path=/";
		} else {
			cookiestring = "euCookie=set; "+networkShareURL+"path=/";
		}

		document.cookie = cookiestring;
	}

	function showembeds() {
		$('.eu-cookie-law-embed .eucookie').hide();

		$('.eu-cookie-law-embed').each(function () {
			var embed_frame = $('.eu-embed', this);
			var embed = embed_frame.find('>:first-child');

			embed_frame.show()
			embed.attr('src', embed.data('src') );
			embed.removeAttr('data-src');
		});
	}

});