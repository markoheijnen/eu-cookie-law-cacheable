<?php

class EU_Cookie_Law_Cacheable_Admin {

	public function __construct() {
		add_action( 'admin_init', array( $this, 'register_setting' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_color_picker' ) );
	}

	
	public function register_setting(){
		register_setting( 'eucookie_options', 'eucookie', array( $this, 'sanitize_callback' ) );
	}
		
	
	public function add_options_page() {
		add_options_page( 'EU Cookie Law', 'EU Cookie Law', 'manage_options', 'eucookie', array( $this, 'get_options_page' ) );
	}

	
	public function enqueue_color_picker( $hook_suffix ) {
		$screen = get_current_screen();

		if ( $screen->id == 'settings_page_eucookie') {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'elc-color-picker', plugins_url( 'js/eucookiesettings.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
		}
	}

	public function sanitize_callback( $input ) {
		return EU_Cookie_Law_Cacheable::sanitize_options( $input );
	}

	public function get_options_page() {
		?>
			<div class="wrap">
				<h1>EU Cookie Law</h1>

				<form method="post" action="options.php">
					<?php settings_fields('eucookie_options'); ?>
					<?php
						$options = EU_Cookie_Law_Cacheable::get_options();
					?>
					<table class="form-table">
						<tr valign="top"><th scope="row"><label for="enabled"><?php _e('Activate'); ?></label></th>
							<td><input id="enabled" name="eucookie[enabled]" type="checkbox" value="1" <?php checked('1', $options['enabled']); ?> /></td>
						</tr>
						<tr valign="top"><th scope="row"><label for="autoblock"><?php _e('Auto Block', 'eu-cookie-law-cacheable'); ?></label></th>
							<td><input id="autoblock" name="eucookie[autoblock]" type="checkbox" value="1" <?php checked('1', $options['autoblock']); ?> /><br>
		<small><?php _e('This function will automatically block iframes, embeds and scripts in your post, pages and widgets.', 'eu-cookie-law-cacheable'); ?></small></td>
						</tr>
						<tr valign="top"><th scope="row"><label for="tinymcebutton"><?php _e('Enable TinyMCE Button', 'eu-cookie-law-cacheable'); ?></label></th>
							<td><input id="tinymcebutton" name="eucookie[tinymcebutton]" type="checkbox" value="1" <?php checked('1', $options['tinymcebutton']); ?> /><br>
		<small><?php _e('Click here if you want to turn on the TinyMCE button for manual insertion of EU Cookie Law shortcodes while editing contents.', 'eu-cookie-law-cacheable'); ?></small></td>
						</tr>
						<tr valign="top"><th scope="row"><label for="lengthnum">
							<?php _e('Cookie acceptance length', 'eu-cookie-law-cacheable'); ?></label></th>
							<td><input id="lengthnum" type="text" name="eucookie[lengthnum]" value="<?php echo $options['lengthnum']; ?>" size="5" /> 
								<select name="eucookie[length]">
									  <option value="days"<?php if ($options['length'] == 'days') { echo ' selected="selected"'; } ?>>
										  <?php _e('days', 'eu-cookie-law-cacheable'); ?></option>
									  <option value="weeks"<?php if ($options['length'] == 'weeks') { echo ' selected="selected"'; } ?>>
										  <?php _e('weeks', 'eu-cookie-law-cacheable'); ?></option>
									  <option value="months"<?php if ($options['length'] == 'months') { echo ' selected="selected"'; } ?>>
										  <?php _e('months', 'eu-cookie-law-cacheable'); ?></option>
								</select><br>
		<small><?php _e('Once the user clicks accept the bar will disappear. You can set how long this will apply for before the bar reappears to the user.', 'eu-cookie-law-cacheable'); ?> <?php _e('Set "0" for SESSION cookie.', 'eu-cookie-law-cacheable'); ?></small>
							</td>
						</tr>
						<tr valign="top"><th scope="row"><label for="scrollconsent"><?php _e('Scroll Consent', 'eu-cookie-law-cacheable'); ?></label></th>
							<td><input id="scrollconsent" name="eucookie[scrollconsent]" type="checkbox" value="1" <?php checked('1', $options['scrollconsent']); ?> /><br>
		<small><?php _e('Click here if you want to consider scrolling as cookie acceptation. Users should be informed about this...', 'eu-cookie-law-cacheable'); ?></small></td>
						</tr>
						<tr valign="top"><th scope="row"><label for="navigationconsent"><?php _e('Navigation Consent', 'eu-cookie-law-cacheable'); ?></label></th>
							<td><input id="navigationconsent" name="eucookie[navigationconsent]" type="checkbox" value="1" <?php checked('1', $options['navigationconsent']); ?> /><br>
		<small><?php _e('Click here if you want to consider continuing navigation as cookie acceptation. Users should be informed about this...', 'eu-cookie-law-cacheable'); ?></small></td>
						</tr>
						<tr valign="top"><th scope="row"><label for="networkshare"><?php _e('Share Cookie across Network', 'eu-cookie-law-cacheable'); ?></label></th>
							<td><input id="networkshare" name="eucookie[networkshare]" type="checkbox" value="1" <?php checked('1', $options['networkshare']); ?> /><br>
		<small><?php _e('Click here if you want to share euCookie across your network (subdomains or multisite)', 'eu-cookie-law-cacheable'); ?></small></td>
						</tr>
						<tr valign="top"><th scope="row"><label for="networkshareurl">
							<?php _e('Network Domain', 'eu-cookie-law-cacheable'); ?></label></th>
							<td><input id="networkshareurl" type="text" name="eucookie[networkshareurl]" value="<?php echo $options['networkshareurl']; ?>" size="40" /></td>
						</tr>
					</table>
				<hr>
					<h3 class="title"><?php _e('Appearance'); ?></h3>
					<table class="form-table">
						<tr valign="top"><th scope="row"><label for="position"><?php _e('Position', 'eu-cookie-law-cacheable'); ?></label></th>
							<td>
								<select name="eucookie[position]">
									  <option value="bottomright"<?php if ($options['position'] == 'bottomright') { echo ' selected="selected"'; } ?>>
										  <?php _e('Bottom Right', 'eu-cookie-law-cacheable'); ?></option>
									  <option value="topright"<?php if ($options['position'] == 'topright') { echo ' selected="selected"'; } ?>>
										  <?php _e('Top Right', 'eu-cookie-law-cacheable'); ?></option>
									  <option value="topcenter"<?php if ($options['position'] == 'topcenter') { echo ' selected="selected"'; } ?>>
										  <?php _e('Top Center', 'eu-cookie-law-cacheable'); ?></option>
									  <option value="bottomleft"<?php if ($options['position'] == 'bottomleft') { echo ' selected="selected"'; } ?>>
										  <?php _e('Bottom Left', 'eu-cookie-law-cacheable'); ?></option>
									  <option value="topleft"<?php if ($options['position'] == 'topleft') { echo ' selected="selected"'; } ?>>
										  <?php _e('Top Left', 'eu-cookie-law-cacheable'); ?></option>
									  <option value="bottomcenter"<?php if ($options['position'] == 'bottomcenter') { echo ' selected="selected"'; } ?>>
										  <?php _e('Bottom Center', 'eu-cookie-law-cacheable'); ?></option>
								</select>
							</td>
						</tr>
						<tr valign="top"><th scope="row"><label for="backgroundcolor">
							<?php _e('Background Color', 'eu-cookie-law-cacheable'); ?></label></th>
							<td><input id="backgroundcolor" type="text" name="eucookie[backgroundcolor]" value="<?php echo $options['backgroundcolor']; ?>" class="color-field" data-default-color="#000000"/></td>
						</tr>
						<tr valign="top"><th scope="row"><label for="fontcolor">
							<?php _e('Font Color', 'eu-cookie-law-cacheable'); ?></label></th>
							<td><input id="fontcolor" type="text" name="eucookie[fontcolor]" value="<?php echo $options['fontcolor']; ?>"  class="color-field" data-default-color="#ffffff"/></td>
						</tr>
					</table>
				<hr>
					<h3 class="title"><?php _e('Content'); ?></h3>
					<table class="form-table">
						<tr valign="top"><th scope="row"><label for="barmessage">
							<?php _e('Bar Message', 'eu-cookie-law-cacheable'); ?></label></th>
							<td><input class="i18n-multilingual-display" id="barmessage" type="text" name="eucookie[barmessage]" value="<?php echo $options['barmessage']; ?>" size="100" /></td>
						</tr>
						<tr valign="top"><th scope="row"><label for="barlink">
							<?php _e('More Info Text', 'eu-cookie-law-cacheable'); ?></label></th>
							<td><input id="barlink" type="text" name="eucookie[barlink]" value="<?php echo $options['barlink']; ?>" /></td>
						</tr>
						<tr valign="top"><th scope="row"><label for="barbutton">
							<?php _e('Accept Text', 'eu-cookie-law-cacheable'); ?></label></th>
							<td><input id="barbutton" type="text" name="eucookie[barbutton]" value="<?php echo $options['barbutton']; ?>" /></td>
						</tr>
						<tr valign="top"><th scope="row"><label for="boxlinkid">
							<?php _e('Bar Link', 'eu-cookie-law-cacheable'); ?><br/><small>
							<?php _e('Use this field if you want to link a page instead of showing the popup', 'eu-cookie-law-cacheable'); ?></small></label></th>
							<td>
							<?php
							$args = array(
								'depth'                 => 0,
								'child_of'              => 0,
								'selected'              => $options['boxlinkid'],
								'echo'                  => 0,
								'name'                  => 'eucookie[boxlinkid]',
								'id'                    => 'boxlinkid', 
								'show_option_none'      => '* '.__('Custome Message'), 
								'show_option_no_change' => null, 
								'option_none_value'     => null, 
							); ?>

							<?php
							$lol = wp_dropdown_pages($args);
							$add = null;
							if ( $options['boxlinkid'] == 'C' ) { $add = ' selected="selected" '; }
							$end = '<option class="level-0" value="C"'.$add.'>* '.__('Custom URL').'</option></select>';
							$lol = preg_replace('#</select>$#', $end, trim($lol)); 
							echo $lol; ?>
								<br><br><input id="boxlinkblank" name="eucookie[boxlinkblank]" type="checkbox" value="1" <?php checked('1', $options['boxlinkblank']); ?> /><label for="boxlinkblank"><small>Add target="_blank"</small></label>
							</td>
							
						</tr>
						<tr valign="top"><th scope="row"><label for="customurl">
							<?php _e('Custom URL'); ?></label></th>
							<td><input id="customurl" type="text" name="eucookie[customurl]" value="<?php echo $options['customurl']; ?>" />
								<small> <?php _e('Enter the destination URL'); ?></small></td>
						</tr>
						<tr valign="top"><th scope="row"><label for="closelink">
							<?php _e('"Close Popup" Text', 'eu-cookie-law-cacheable'); ?></label></th>
							<td><input id="closelink" type="text" name="eucookie[closelink]" value="<?php echo $options['closelink']; ?>" /></td>
						</tr>
						<tr valign="top"><th scope="row"><label for="boxcontent">
							<?php _e('Popup Box Content', 'eu-cookie-law-cacheable'); ?><br>
							<small><?php _e('Use this to add a popup that informs your users about your cookie policy', 'eu-cookie-law-cacheable'); ?></small></label></th>
							<td>
		<textarea style='font-size: 90%; width:95%;' name='eucookie[boxcontent]' id='boxcontent' rows='9' ><?php echo $options['boxcontent']; ?></textarea>
							</td>
						</tr>
						<tr valign="top"><th scope="row"><label for="bhtmlcontent">
							<?php _e('Blocked code message', 'eu-cookie-law-cacheable'); ?><br>
							<small><?php _e('This is the message that will be displayed for locked-code areas', 'eu-cookie-law-cacheable'); ?></small></label></th>
							<td>
		<textarea style='font-size: 90%; width:95%;' name='eucookie[bhtmlcontent]' id='bhtmlcontent' rows='9' ><?php echo $options['bhtmlcontent']; ?></textarea>
							</td>
						</tr>
						<tr>
					</table>
						<hr>
						<h3 class="title">Shortcode [cookie-control]</h3>
					<table class="form-table">
						</tr>
							<tr valign="top"><th scope="row"><label for="cc-cookieenabled">
							<?php _e('Cookie enabled message', 'eu-cookie-law-cacheable'); ?><br>
							<small><?php _e('This is the message that will be displayed when cookie are enabled', 'eu-cookie-law-cacheable'); ?></small></label></th>
							<td>
		<textarea style='font-size: 90%; width:95%;' name='eucookie[cc-cookieenabled]' id='cc-cookieenabled' rows='9' ><?php echo $options['cc-cookieenabled']; ?></textarea><br>
							
							<label style="font-size:0.9em;font-weight:bold;" for="cc-disablecookie"><?php _e('"Disable Cookie" Text', 'eu-cookie-law-cacheable'); ?></label>
							<input id="cc-disablecookie" type="text" name="eucookie[cc-disablecookie]" value="<?php echo $options['cc-disablecookie']; ?>" />
							</td>
						</tr>
						<tr valign="top"><th scope="row"><label for="cc-cookiedisabled">
							<?php _e('Cookie disabled message', 'eu-cookie-law-cacheable'); ?><br>
							<small><?php _e('This is the message that will be displayed when cookie are not accepted', 'eu-cookie-law-cacheable'); ?></small></label></th>
							<td>
		<textarea style='font-size: 90%; width:95%;' name='eucookie[cc-cookiedisabled]' id='cc-cookiedisabled' rows='9' ><?php echo $options['cc-cookiedisabled']; ?></textarea>
							</td>
						</tr>
					</table>
					<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
					</p>
				</form>
			</div>
		<?php
	}

}