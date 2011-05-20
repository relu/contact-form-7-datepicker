<?php
/*
Plugin Name: Contact Form 7 Datepicker
Plugin URI: https://github.com/relu/contact-form-7-datepicker/
Description: Implements a new [date] tag in Contact Form 7 that adds a date field to a form. When clicking the field a calendar pops up enabling your site visitors to easily select any date.
Author: Aurel Canciu
Version: 0.2.1
Author URI: https://github.com/relu/
*/
?>
<?php
/* Copyright 2011 Aurel Canciu <aurelcanciu@gmail.com>

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/
?>
<?php

define('CF7_DATE_PICKER_VERSION', '0.2.1');

class CF7DatePicker {
	
	static $option_defaults = array(
		"useMode" => 2, 
		"isStripped" => false, 
		"limitToToday" => false, 
		"cellColorScheme" => "beige", 
		"dateFormat" => "%d-%m-%Y", 
		"weekStartDay" => 1,
		"directionality" => "ltr"
	);

	function init() {
		register_activation_hook( __FILE__, array( __CLASS__, 'activate') );
		register_deactivation_hook( __FILE__, array( __CLASS__, 'deactivate') );

		add_action( 'admin_menu', array( __CLASS__, 'register_admin_settings') );
		add_action( 'wp_head', array( __CLASS__, 'plugin_enqueues' ), 1002 );
		add_action( 'init', array( __CLASS__, 'load_plugin_text_domain') );

		add_filter( 'wpcf7_validate_date', 'wpcf7_validation_filter_cf7datepicker', 10, 2 );
		add_filter( 'wpcf7_validate_date*', 'wpcf7_validation_filter_cf7datepicker', 10, 2 );
		
		/* Add wpcf7 shortcodes [date ] and [date* ] */
		if ( function_exists( 'wpcf7_add_shortcode' ) ) {
			if( is_file( WP_PLUGIN_DIR."/contact-form-7/includes/shortcodes.php" ) ) {
				include WP_PLUGIN_DIR."/contact-form-7/includes/shortcodes.php";
				wpcf7_add_shortcode( 'date', array(__CLASS__, 'wpcf7_shotcode_handler'), true );
				wpcf7_add_shortcode( 'date*', array(__CLASS__, 'wpcf7_shotcode_handler'), true );
			}
		}
	}

	/**
	* activate()
	* 
	* Action triggered when plugin is activated
	* It inserts some default values as options
	*/	
	public function activate() {
		foreach (self::$option_defaults as $optname => $optvalue) {
			add_option($optname, $optvalue);
		}
	}

	/**
	* deactivate()
	* 
	* Action triggered when plugin is deactivated
	* It deletes the settings stored in the database
	*/	
	public function deactivate() {
		foreach (self::$option_defaults as $optname) {
			delete_option($optname);
		}
	}

	/**
	* update_settings($dataupdate)
	* 
	* Updates plugin's settings into the database
	* @param Array $dateupdate, contains the updated settings
	*/
	public function update_settings($dataupdate) {
		foreach ($dateupdate as $optname => $optvalue){
			if (self::$option_defaults[$optname] != $optvalue)
				update_option($optname, $optvalue);
		}
	}

	/**
	* register_admin_settings()
	*
	* Registers the Admin panel so that it will show up as a submenu page in Contact Form 7's menu
	*/
	private function register_admin_settings() {
		if (function_exists('add_submenu_page')) {
			add_submenu_page('wpcf7',__('Datepicker Settings', 'contact-form-7-datepicker'),__('Datepicker Settings', 'contact-form-7-datepicker'),
							 'edit_themes',
							 basename(__FILE__),
							 'admin_settings_html');
		}	
	}

	/**
	* read_schemes()
	*
	* Gets the names of the schemes available from the img/ directory
	* @return Array $themes, the names of the schemes found
	*/
	private function read_schemes() {
	$path = ABSPATH.'/wp-content/plugins/'.plugin_basename(dirname(__FILE__)).'/img/';
	if ($handle = opendir($path)) {
		$themes = array() ;
		while (false !== ($file = readdir($handle))) {
			if (is_dir($path.$file) && $file != "." && $file != "..") {
				$themes[] = $file;
			}
		}
	}
	closedir($handle);
	return $themes;
}

	/**
	* get_scheme_images_cf7datepicker()
	*
	* Gets the images of a scheme and natural sorts them
	* @param String $scheme, the name of the scheme to get images for
	* @return Array $schemeimg, the paths to the scheme images
	*/
	function get_scheme_images_cf7datepicker($scheme) {
		$path = ABSPATH.'/wp-content/plugins/'.plugin_basename(dirname(__FILE__)).'/img/'.$scheme.'/';
		if ($handle = opendir($path)) {
			$schemeimg = array();
			while (false !== ($file = readdir($handle))) {
				if (is_file($path.$file) && preg_match('/\.gif$/i', $file))
					$schemeimg[] = '/wp-content/plugins/'.plugin_basename(dirname(__FILE__)).'/img/'.$scheme.'/'.$file;
			}
			natsort($schemeimg);
		}
		closedir($handle);
		return $schemeimg;
}

	/**
	* admin_settings_html()
	*
	* Generates the admin panel HTML
	*/
	private function admin_settings_html() {
		if(isset($_POST['datepickersave'])) {
				$dataupdate = array($_POST['useMode'], $_POST['isStripped'], $_POST['limitToToday'], $_POST['cellColorScheme'], $_POST['dateFormat'], $_POST['weekStartDay'], $_POST['directionality']);
			self::update_settings($dataupdate);
			}
			$useMode = array(1,2);
			$limitToToday = $isStripped = array(__('true', 'contact-form-7-datepicker'),__('false', 'contact-form-7-datepicker'));
			$cellColorScheme = read_schemes_cf7datepicker();
			$weekStartDay = array(__('Sunday', 'contact-form-7-datepicker'),__('Monday', 'contact-form-7-datepicker'));
			$directionality = array(__('Left to right', 'contact-form-7-datepicker'),__('Right to left', 'contact-form-7-datepicker'));
	
		?>
		<div class="wrap">
		<h2>Contact Form 7 Datepicker</h2><?php
		echo __('<p>This plugin implements a new <strong>[date]</strong> tag in <a href="http://wordpress.org/extend/plugins/contact-form-7/">Contact Form 7</a> 
		that adds a date field to a form. When clicking the field a calendar pops up enabling your site visitors to easily select any date.<br />
		To use it simply insert the <strong>[date your-field-name]</strong> or <strong>[date* your-requierd-field-name]</strong> if you want it to be mandatory,
		in your Contact Form 7 edit section.</p>', 'contact-form-7-datepicker'); ?>
		<form method="post">
			<table class="widefat">
				<tbody>
					<tr>
						<th style="width:20%">
							<label><?php echo __('Color scheme', 'contact-form-7-datepicker'); ?></label>
						</th>
						<td colspan="2"><?php
						foreach($cellColorScheme as $scheme) {
							if($scheme == get_option('cellColorScheme'))
								$checked = "checked='checked'";
							else
								$checked = ""; ?>
								
								<div style="float: left; width: 100px; margin: 30px 30px 0 0; text-align: center;">
									<div style="display: block; padding: 5px; background: #fff; border: 1px solid #ccc; border-radius: 4px 4px 4px 4px;">
										<label><?php echo $scheme; ?></label><br /><?php
									foreach(self::get_scheme_images($scheme) as $img) { ?>
										<img src="<?php echo get_option('siteurl') . $img; ?>" style="margin: 5px;" /><?php 
									} ?><br /><br />
										<input name="cellColorScheme" type="radio" width="24" height="25" value="<?php echo $scheme; ?>" <?php echo $checked; ?> />
									</div>
								</div><?php 
							} ?>
						</td>
					</tr>
					
					<tr>
						<th>
							<label><?php echo __('Use Mode', 'contact-form-7-datepicker'); ?></label>
						</th>
						<td>
							<select name="useMode"><?php
							foreach($useMode as $row) {
								if($row == get_option('useMode'))
									$selected = "selected";
								else
									$selected = "";
								
								echo "<option value='".$row."' ".$selected." >".$row."</option>";
							} ?>
							</select>
						</td>
						<td>
							<?php echo __('<p>1 – The calendar\'s HTML will be directly appended to the field supplied by target<br />
							2 – The calendar will appear as a popup when the field with the id supplied in target is clicked.</p>', 'contact-form-7-datepicker'); ?>
						</td>
					</tr>
					
					<tr>
						<th>
							<label><?php echo __('Sripped', 'contact-form-7-datepicker'); ?></label>
						</th>
						<td>
							<select name="isStripped"><?php
							foreach($isStripped as $row) {
								if($row == __('true', 'contact-form-7-datepicker'))
									$val = "true";
								else
									$val = "false";
								
								if ($val == get_option('isStripped'))
									$selected = "selected";
								else
									$selected = "";
								
								echo "<option value='".$val."' ".$selected." >".__($row, 'contact-form-7-datepicker')."</option>";
							} ?>
							</select>
						</td>
						<td>
							<?php echo __('<p>When set to true the calendar appears without the visual design - usually used with \'Use Mod\' 1.</p>','contact-form-7-datepicker'); ?>
						</td>
					</tr>
					
					<tr>
						<th>
							<label><?php echo __('Limit To Today', 'contact-form-7-datepicker'); ?></label>
						</th>
						<td>
							<select name="limitToToday"><?php
							foreach($limitToToday as $row) {
								if($row == __('true', 'contact-form-7-datepicker'))
									$val = "true";
								else
									$val = "false";
								
								if ($val == get_option('limitToToday'))
									$selected = "selected";
								else
									$selected = "";
								
								echo "<option value='".$val."' ".$selected." >".__($row, 'contact-form-7-datepicker')."</option>";
							} ?>
							</select>
						</td>
						<td>
							<?php echo __('<p>Enables you to limit the possible picking days to today\'s date.</p>','contact-form-7-datepicker'); ?>
						</td>
					</tr>
					
					<tr>
						<th>
							<label><?php echo __('Week Start Day', 'contact-form-7-datepicker'); ?></h2></label>
						</th>
						<td>
							<select name="weekStartDay"><?php
							foreach($weekStartDay as $row) {
								if ($row == __('Sunday','contact-form-7-datepicker'))
									$val = 0;
								else
									$val = 1;
								
								if($val == get_option('weekStartDay'))
									$selected = "selected";
								else
									$selected = "";
								
								echo "<option value='".$val."' ".$selected." >".__($row,'contact-form-7-datepicker')."</option>";
							} ?>
							</select>
						</td>
						<td>
						</td>
					</tr>
					
					<tr>
						<th>
							<label><?php echo __('Text Direction', 'contact-form-7-datepicker'); ?></h2></label>
						</th>
						<td>
							<select name="directionality"><?php
							foreach($directionality as $row) {
								if ($row == __('Left to right','contact-form-7-datepicker'))
									$val = "ltr";
								else
									$val = "rtl";
								
								if($val == get_option('directionality'))
									$selected = "selected";
								else
									$selected = "";
								
								echo "<option value='".$val."' ".$selected." >".__($row,'contact-form-7-datepicker')."</option>";
							} ?>
							</select>
						</td>
						<td>
						</td>
					</tr>
					
					<tr>
						<th>
							<label><?php echo __('Date Format', 'contact-form-7-datepicker'); ?></label>
						</th>
						<td>
							<input name="dateFormat" id="dateFormat" type="text" value="<?php echo get_option('dateFormat'); ?>" />
						</td>
						<td>
<?php echo __('<p>Possible values to use in the date format:<br />
<br />
%d - Day of the month, 2 digits with leading zeros<br />
%j - Day of the month without leading zeros<br />
%m - Numeric representation of a month, with leading zeros<br />
%M - A short textual representation of a month, three letters<br />
%n - Numeric representation of a month, without leading zeros<br />
%F - A full textual representation of a month, such as January or March<br />
%Y - A full numeric representation of a year, 4 digits<br />
%y - A two digit representation of a year<br />
<br />
You can of course put whatever divider you want between them.<br /></p>', 
'contact-form-7-datepicker'); ?>
						</td>
					</tr>
					
					<tr>
						<td colspan="2">
						</td>
						<td>
							<input name="datepickersave" id="datepickersave" type="submit" value="<?php echo __('Save Setting', 'contact-form-7-datepicker'); ?>" class="button" />
						</td>
					</tr>
				</tbody>
			</table>
		</form><?php
	}

	/**
	* plugin_enqueues()
	*
	* Loads needed scripts and CSS
	*/
	public function plugin_enqueues() {
		if( is_admin() )
			return; ?>
		<link rel="stylesheet" type="text/css" href="<?php echo plugins_url( '/css/jsDatePick_'.((get_option('directionality') != "") ? get_option('directionality') : "ltr").'.min.css', __FILE__ ); ?>" />
		<script type="text/javascript" src="<?php echo plugins_url( '/js/jsDatePick.jquery.min.1.3.js', __FILE__ ); ?>"></script>
		<script type="text/javascript"><?php echo "
			g_l = [];
			g_l[\"MONTHS\"] = [\"".__('Janaury', 'contact-form-7-datepicker').
				"\",\"".__('February', 'contact-form-7-datepicker').
				"\",\"".__('March', 'contact-form-7-datepicker').
				"\",\"".__('April', 'contact-form-7-datepicker').
				"\",\"".__('May', 'contact-form-7-datepicker').
				"\",\"".__('June', 'contact-form-7-datepicker').
				"\",\"".__('July', 'contact-form-7-datepicker').
				"\",\"".__('August', 'contact-form-7-datepicker').
				"\",\"".__('September', 'contact-form-7-datepicker').
				"\",\"".__('October', 'contact-form-7-datepicker').
				"\",\"".__('November', 'contact-form-7-datepicker').
				"\",\"".__('December', 'contact-form-7-datepicker')."\"];
			g_l[\"DAYS_3\"] = [\"".__('Sun', 'contact-form-7-datepicker').
				"\",\"".__('Mon', 'contact-form-7-datepicker').
				"\",\"".__('Tue', 'contact-form-7-datepicker').
				"\",\"".__('Wed', 'contact-form-7-datepicker').
				"\",\"".__('Thu', 'contact-form-7-datepicker').
				"\",\"".__('Fri', 'contact-form-7-datepicker').
				"\",\"".__('Sat', 'contact-form-7-datepicker')."\"];
			g_l[\"MONTH_FWD\"] = \"".__('Move a month forward', 'contact-form-7-datepicker')."\";
			g_l[\"MONTH_BCK\"] = \"".__('Move a month backward', 'contact-form-7-datepicker')."\";
			g_l[\"YEAR_FWD\"] = \"".__('Move a year forward', 'contact-form-7-datepicker')."\";
			g_l[\"YEAR_BCK\"] = \"".__('Move a year backward', 'contact-form-7-datepicker')."\";
			g_l[\"CLOSE\"] = \"".__('Close the calendar', 'contact-form-7-datepicker')."\";
			g_l[\"ERROR_2\"] = g_l[\"ERROR_1\"] = \"".__('Date object invalid!', 'contact-form-7-datepicker')."\";
			g_l[\"ERROR_4\"] = g_l[\"ERROR_3\"] = \"".__('Target invalid!', 'contact-form-7-datepicker')."\";"; ?>
	</script><?php
	}

	/**
	* page_text_filter($content)
	*
	* Searches for [datepicker ] tag inside page content
	* @param String $content, the page content which gets filtered
	* @return function preg_replace_callback, searches for the tag and calls page_text_filter_callback_cf7datepicker if found
	*/
	public function page_text_filter_cf7datepicker($content) {
		$regex = '/\[datepicker\s(.*?)\]/';
		return preg_replace_callback($regex, array(__CLASS__, 'page_text_filter_callback'), $content);
	}

	/**
	* page_text_filter_callback_cf7datepicker($matches)
	*
	* If a match is found in the content of a form, this returns the HTML for the matched date input field
	* @param Array $matches, the name of the input date field that we generate code for
	* @return String $string, the HTML for our match
	*/
	public function page_text_filter_callback($matches) { ?>
	<input type="text" name="<?php echo $matches[1]; ?>" id="<?php echo $matches[1];?>" />
	<script type="text/javascript">
		jQuery(document).ready(function() {
			DatePicker_<?php echo $matches[1]; ?> = new JsDatePick({<?php
			foreach (self::$option_defaults as $optname => $optvalue)
				echo "\"".$optname.": ".$optvalue.(next(self::$option_defaults)) ? "," : "" ; ?>
			});
		});
	</script><?php
	}

	/**
	* wpcf7_shotcode_handler($tag)
	*
	* Handler for wpcf7 shortcodes [date ] and [date* ]
	* @param Array $tag, this is the tag that will be handled (can be 'date' or 'date*')
	* @return String $html, the HTML that will be appended to the form
	*/
	public function wpcf7_shotcode_handler($tag) {
		global $wpcf7_contact_form;

		if ( ! is_array( $tag ) )
			return '';

		$type = $tag['type'];
		$name = $tag['name'];
		$options = (array) $tag['options'];
		$values = (array) $tag['values'];
	
		if ( empty( $name ) )
			return '';

		static $atts = '';
		static $id_att = '';
		static $class_att = '';
		static $size_att = '';
		static $maxlength_att = '';

		if ( 'date*' == $type )
			$class_att .= ' wpcf7-validates-as-required';

		foreach ( $options as $option ) {
			if ( preg_match( '%^id:([-0-9a-zA-Z_]+)$%', $option, $matches ) ) {
				$id_att = $matches[1];

			} elseif ( preg_match( '%^class:([-0-9a-zA-Z_]+)$%', $option, $matches ) ) {
				$class_att .= ' ' . $matches[1];

			} elseif ( preg_match( '%^([0-9]*)[/x]([0-9]*)$%', $option, $matches ) ) {
				$size_att = (int) $matches[1];
				$maxlength_att = (int) $matches[2];
			}
		}

		if ( $id_att )
			$atts .= ' id="' . trim( $id_att ) . '"';

		if ( $class_att )
			$atts .= ' class="' . trim( $class_att ) . '"';

		if ( $size_att )
			$atts .= ' size="' . $size_att . '"';
		else
			$atts .= ' size="40"';

		if ( $maxlength_att )
			$atts .= ' maxlength="' . $maxlength_att . '"';

		if ( is_a( $wpcf7_contact_form, 'WPCF7_ContactForm' ) && $wpcf7_contact_form->is_posted() ) {
			if ( isset( $_POST['_wpcf7_mail_sent'] ) && $_POST['_wpcf7_mail_sent']['ok'] )
				$value = '';
			else
				$value = $_POST[$name];
		} else {
			$value = $values[0];
		}

		$html = self::page_text_filter_callback(array('',$name));
		$validation_error = '';
		if ( is_a( $wpcf7_contact_form, 'WPCF7_ContactForm' ) )
			$validation_error = $wpcf7_contact_form->validation_error( $name );

		$html = '<span class="wpcf7-form-control-wrap ' . $name . '">' . str_replace('<p>','',$html) . $validation_error . '</span>';

		return $html;
	}

	/**
	* wpcf7_validation_filter_cf7datepicker($result, $tag)
	*
	* This is used to validate the Contact Form 7 'date' field
	* @param Array $result, 'valid' key has a boolean value (true if valid)
	* and 'reason' key with a message if not valid
	* @param Array $tag, contains the type and name of the field that is validated
	* @return Array $result
	*/
	public function wpcf7_validation_filter( $result, $tag ) {
		global $wpcf7_contact_form;

		$type = $tag['type'];
		$name = $tag['name'];

		$_POST[$name] = trim( strtr( (string) $_POST[$name], "\n", " " ) );

		if ( 'date*' == $type ) {
			if ( '' == $_POST[$name] ) {
				$result['valid'] = false;
				$result['reason'][$name] = $wpcf7_contact_form->message( 'invalid_required' );
			}
		}

		return $result;
	}

	/**
	* load_plugin_text_domain_cf7datepicker()
	*
	* Function for loading the l10n files from /languages/ dir
	*/
	function load_plugin_text_domain() {
		load_plugin_textdomain( 'contact-form-7-datepicker', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

}

CF7DatePicker::init();

?>
