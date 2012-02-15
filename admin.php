<?php

function cf7dp_activate() {
	if (! get_option('cf7dp_ui_theme'))
		add_option('cf7dp_ui_theme', 'base');
}

function cf7dp_theme_metabox() {
?>
			<div id="dpdemo" style="float: left; margin: 0 10px 0 0"></div>
<?php
	$dp = new CF7_DatePicker('dpdemo');
	echo $dp->generate_code(true);
?>
			<form action="">
				<label for="jquery-ui-theme"><?php _e('Theme'); ?></label><br />
				<?php echo cf7dp_ui_themes_dropdown(); ?>
				<input type="submit" id="save-ui-theme" value="<?php _e('Save'); ?>" class="button" />
			</form>
			<div class="clear"></div>
<?php
}

function cf7dp_add_theme_metabox() {
	if (current_user_can('publish_pages'))
		add_meta_box(
			'datepickerthemediv',
			__('Datepicker Theme'),
			'cf7dp_theme_metabox',
			'cfseven',
			'datepicker-theme',
			'core'
		);

	do_meta_boxes('cfseven', 'datepicker-theme', array());
}
function cf7dp_ui_themes_dropdown() {
	$themes = array(
		'disabled' => __('Disabled'),
		'base' => 'Base',
		'black-tie' => 'Black Tie',
		'blitzer' => 'Blitzer',
		'cupertino' => 'Cupertino',
		'dark-hive' => 'Dark Hive',
		'dot-luv' => 'Dot Luv',
		'eggplant' => 'Eggplant',
		'excite-bike' => 'Excite Bike',
		'flick' => 'Flick',
		'hot-sneaks' => 'Hot Sneaks',
		'humanity' => 'Humanity',
		'le-frog' => 'Le frog',
		'mint-choc' => 'Mint Choc',
		'overcast' => 'Overcast',
		'pepper-grinder' => 'Pepper Grinder',
		'redmond' => 'Redmond',
		'smoothness' => 'Smoothness',
		'south-street' => 'South Street',
		'start' => 'Start',
		'sunny' => 'Sunny',
		'swanky-purse' => 'Swanky Purse',
		'trontastic' => 'Trontastic',
		'ui-darkness' => 'UI Darkness',
		'ui-lightness' => 'UI Lightness',
		'vader' => 'Vader'
	);

	$themes = apply_filters('cf7dp_ui_themes', $themes);

	$html = "<select id=\"jquery-ui-theme\">\n";
	foreach ($themes as $key => $val) {
		$is_selected = ($key == get_option('cf7dp_ui_theme')) ? ' selected="selected"' : '';
		$html .= "\t<option value=\"{$key}\"{$is_selected}>{$val}</option>\n";
	}

	$html .= "</select>\n";

	return $html;
}

function cf7dp_ui_theme_js() {
	if (! cf7dp_is_wpcf7_page())
		return;
?>
<script type="text/javascript">
jQuery(function($){
	var $spinner = $( new Image() ).attr( 'src', '<?php echo admin_url( "images/wpspin_light.gif" ); ?>' );

	$('#jquery-ui-theme').change(function(){
		var style = $(this).val();

		var $link = $('#jquery-ui-theme-css');
		var href = $link.attr('href');

		href = href.replace(/\/themes\/[-a-z]+\//g, '/themes/' + style + '/');
		$link.attr('href', href);
	});

	$('#save-ui-theme').click(function(){
		var data = {
			action: 'cf7dp_save_settings',
			ui_theme: $('#jquery-ui-theme').val()
		};

		var $this_spinner = $spinner.clone();

		$(this).after($this_spinner.show());

		$.post(ajaxurl, data, function(response) {
			var $prev = $( '.wrap > .updated, .wrap > .error' );
			var $msg = $(response).hide().insertAfter($('.wrap h2'));
			if ($prev.length > 0)
				$prev.fadeOut('slow', function(){
					$msg.fadeIn('slow');
				});
			else
				$msg.fadeIn('slow');

			$this_spinner.hide();
		});

		return false;
	});
});
</script>
<?php
}

function cf7dp_ajax_save_settings() {
	$successmsg = '<div id="message" class="updated fade"><p><strong>' . __('Options saved.') . '</strong></p></div>';
	$errormsg = '<div id="message" class="error fade"><p><strong>' . __('Options could not be saved.') . '</strong></p></div>';

	if (! isset($_POST['ui_theme']))
		die($errormsg);

	if (! is_admin())
		die($errormsg);

	$theme = trim($_POST['ui_theme']);

	if (! preg_match('%[-a-z]+%i', $theme))
		die($errormsg);

	if (! update_option('cf7dp_ui_theme', $theme))
		die($errormsg);

	die($successmsg);
}

?>
