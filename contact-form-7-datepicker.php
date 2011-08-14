<?php
/*
Plugin Name: Contact Form 7 Datepicker
Plugin URI: https://github.com/relu/contact-form-7-datepicker/
Description: Implements a new [date] tag in Contact Form 7 that adds a date field to a form. When clicking the field a calendar pops up enabling your site visitors to easily select any date. Now you can use the [datepicker] shortcode outside of CF7.
Author: Aurel Canciu
Version: 0.7.2
Author URI: https://github.com/relu/
*/
?>
<?php
/* Copyright 2011 Aurel Canciu <aurelcanciu at gmail.com>

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

define('CF7_DATE_PICKER_VERSION', '0.7.2');
define('PLUGIN_PATH', '/wp-content/plugins/'.plugin_basename(dirname(__FILE__)));

if (!defined('CF7_DATE_PICKER_ENQUEUES')) {
	define('CF7_DATE_PICKER_ENQUEUES', true);
}

class CF7DatePicker {
	
	/**
	 * static Array $option_defaults
	 * 
	 * Holds the default option values for the plugin
	 */
	static $option_defaults = array(
		"useMode" => 2, 
		"isStripped" => "false", 
		"limitToToday" => 0, 
		"cellColorScheme" => "beige", 
		"dateFormat" => "%d-%m-%Y", 
		"weekStartDay" => 1,
		"directionality" => "ltr",
		"yearsRange" => "1970,2100",
		"yearButtons" => "true",
		"monthButtons" => "true",
		"animate" => "true",
		"selectedDate" => ""
	);

	/**
	 * __construct()
	 * 
	 * This is the class constructor method, it registers actions and initializes the plugin
	 */
	function __construct() {
		register_activation_hook(__FILE__, array(__CLASS__, 'activate'));
		register_deactivation_hook(__FILE__, array(__CLASS__, 'deactivate'));
		
		add_action('plugins_loaded', array(__CLASS__, 'register_shortcodes'));
		add_action('admin_init', array(__CLASS__, 'tag_generator'));
		add_action('admin_menu', array(__CLASS__, 'register_admin_settings'));
		add_action('init', array(__CLASS__, 'register_files'));
		if (CF7_DATE_PICKER_ENQUEUES) {
			add_action('wp_enqueue_scripts', array(__CLASS__, 'plugin_enqueues'));
		}
		add_action('admin_enqueue_scripts', array(__CLASS__, 'plugin_enqueues'));
		
		add_action('init', array(__CLASS__, 'calendar_l10n'));
		

		add_filter('wpcf7_validate_date', array(__CLASS__, 'wpcf7_validation_filter'), 10, 2);
		add_filter('wpcf7_validate_date*', array(__CLASS__, 'wpcf7_validation_filter'), 10, 2);
		
		add_action('init', array(__CLASS__, 'admin_l10n'));
	}

	/**
	* activate()
	* 
	* Action triggered when plugin is activated
	* It inserts some default values as options
	*/	
	public static function activate() {
		foreach (self::$option_defaults as $option => $value) {
			add_option($option, $value);
		}
	}

	/**
	* deactivate()
	* 
	* Action triggered when plugin is deactivated
	* It deletes the settings stored in the database
	*/	
	public static function deactivate() {
		foreach (self::$option_defaults as $option => $value) {
			delete_option($option);
		}
	}

	/**
	* update_settings($dataupdate)
	* 
	* Updates plugin's settings into the database
	* @param Array $dateupdate, contains the updated settings
	*/
	public static function update_settings($dataupdate) {
		foreach ($dataupdate as $option => $value) {
			if ($value != get_option($option))
				update_option($option, $value);
		}
	}

	/**
	* register_admin_settings()
	*
	* Registers the Admin panel so that it will show up as a submenu page in Contact Form 7's menu
	*/
	public static function register_admin_settings() {
		if (function_exists('add_submenu_page')) {
			add_submenu_page('wpcf7',__('Datepicker Settings', 'contact-form-7-datepicker'),__('Datepicker Settings', 'contact-form-7-datepicker'),
							 'edit_themes',
							 basename(__FILE__),
							 array(__CLASS__,'admin_settings_html'));
		}
	}

	/**
	* read_schemes()
	*
	* Gets the names of the schemes available from the img/ directory
	* @return Array $themes, the names of the schemes found
	*/
	private function read_schemes() {
	$path = ABSPATH.PLUGIN_PATH.'/img/';
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
	* get_scheme_images($scheme)
	*
	* Gets the images of a scheme and natural sorts them
	* @param String $scheme, the name of the scheme to get images for
	* @return Array $schemeimg, the paths to the scheme images
	*/
	private function get_scheme_images($scheme) {
		$path = ABSPATH.PLUGIN_PATH.'/img/'.$scheme.'/';
		if ($handle = opendir($path)) {
			$schemeimg = array();
			while (false !== ($file = readdir($handle))) {
				if (is_file($path.$file) && preg_match('/\.gif$/i', $file))
					$schemeimg[] = get_option('siteurl').PLUGIN_PATH.'/img/'.$scheme.'/'.$file;
			}
			natsort($schemeimg);
		}
		closedir($handle);
		return $schemeimg;
	}
	
	/**
	* get_scheme_style($scheme)
	*
	* Checks if a CSS file exists in the scheme's directory and returns the path if so
	* @param String $scheme, the name of the scheme to get the CSS for
	* @return String the path to the CSS file
	* @return Boolean false if no file found
	*/
	private function get_scheme_style($scheme) {
		$file = PLUGIN_PATH.'/css/schemes/'.$scheme.'.css';
		if (is_file(ABSPATH.$file)) {
			return get_option('siteurl').$file;
		}
		return false;
	}

	/**
	* admin_settings_html()
	*
	* Generates the admin panel HTML
	*/
	public static function admin_settings_html() {
		if(isset($_POST['datepickersave'])) {
				foreach(self::$option_defaults as $option => $value)
					$dataupdate[$option] = $_POST[$option];
				$dataupdate['yearsRange'] = trim($_POST['yearmin']).",".trim($_POST['yearmax']);
				
				$dataupdate['yearButtons'] = (isset($_POST['yearButtons'])) ? "true" : "false";
				$dataupdate['monthButtons'] = (isset($_POST['monthButtons'])) ? "true" : "false";
				
				if ($_POST['selectedDate'] !== '') {
					if (get_option('dateFormat') !== $dataupdate['dateFormat']) {
						$df = $dataupdate['dateFormat'];
					} else {
						$df = get_option('dateFormat');
					}
					$df = str_replace('%', '', trim($df));
					
					$dataupdate['selectedDate'] = date($df, strtotime($_POST['selectedDate']));
					$dataupdate['selectedDate'] = date("Y-m-d", strtotime($dataupdate['selectedDate']));
				}
				
				self::update_settings($dataupdate);
			}
			$useMode = array(1,2);
			$limitToToday = array(
				__('Today and future', 'contact-form-7-datepicker'),
				__('Today and past', 'contact-form-7-datepicker'),
				__('No limit', 'contact-form-7-datepicker')
			);
			$isStripped = $animate = array(
				__('true', 'contact-form-7-datepicker'),
				__('false', 'contact-form-7-datepicker')
			);
			$cellColorScheme = self::read_schemes();
			$weekStartDay = array(
				__('Sunday', 'contact-form-7-datepicker'),
				__('Monday', 'contact-form-7-datepicker')
			);
			$directionality = array(
				__('Left to right', 'contact-form-7-datepicker'),
				__('Right to left', 'contact-form-7-datepicker')
			);
			$yearsRange = explode(",", trim(get_option('yearsRange')));
	
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
								$checked = "checked=\"checked\"";
							else
								$checked = ""; ?>
								
								<div style="float: left; width: 100px; margin: 30px 30px 0 0; text-align: center;">
									<div style="display: block; padding: 5px; background: #fff; border: 1px solid #ccc; border-radius: 4px 4px 4px 4px;">
										<label><?php echo $scheme; ?></label><br /><?php
									foreach(self::get_scheme_images($scheme) as $img) { ?>
										<img src="<?php echo $img; ?>" style="margin: 5px;" /><?php 
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
							<label><?php echo __('Limit Dates To', 'contact-form-7-datepicker'); ?></label>
						</th>
						<td>
							<select name="limitToToday"><?php
							foreach($limitToToday as $row) {
								if ($row == __('Today and future', 'contact-form-7-datepicker'))
									$val = 1;
								elseif ($row == __('Today and past', 'contact-form-7-datepicker'))
									$val = -1;
								else
									$val = 0;
								
								if ($val == get_option('limitToToday'))
									$selected = "selected";
								else
									$selected = "";
								
								echo "<option value='".$val."' ".$selected." >".__($row, 'contact-form-7-datepicker')."</option>";
							} ?>
							</select>
						</td>
						<td>
							<?php echo __('<p>Enables you to limit the possible picking dates according to the current date.</p>','contact-form-7-datepicker'); ?>
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
							<label><?php echo __('Years Range', 'contact-form-7-datepicker'); ?></h2></label>
						</th>
						<td colspan="2">
							<input name="yearmin" id="yearmin" type="text" value="<?php echo $yearsRange[0]; ?>" />&nbsp;-&nbsp;
							<input name="yearmax" id="yearmax" type="text" value="<?php echo $yearsRange[1]; ?>" />
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
							<label><?php echo __('Controls', 'contact-form-7-datepicker'); ?></h2></label>
						</th>
						<td><?php
								
							if (get_option('yearButtons') == "true")
								$checked = "checked=\"checked\"";
							else
								$checked = "";
							echo "<input type=\"checkbox\" name=\"yearButtons\" ".$checked.">"; ?>
							<label><?php echo __('Year Controls','contact-form-7-datepicker'); ?>&nbsp;</label>
							<br /><?php
							
							if (get_option('monthButtons') == "true")
								$checked = "checked=\"checked\"";
							else
								$checked = "";
							echo "<input type=\"checkbox\" name=\"monthButtons\" ".$checked." >"; ?>
							<label><?php echo __('Month Controls','contact-form-7-datepicker'); ?>&nbsp;</label>
						</td>
						<td>
							<?php echo __('<p>You can select here what controls would you like to display on the calendar.</p>', 'contact-form-7-datepicker'); ?>
						</td>
					</tr>
					
					<tr>
						<th>
							<label><?php echo __('Selected Date', 'contact-form-7-datepicker'); ?></label>
						</th>
						<td style="overflow: visible">
							<?php 
								echo self::page_text_filter_callback("selectedDate");
							?>
						</td>
						<td>
							<?php echo __('<p>You can set here a default selected date and have a look of how the calendar shows up.</p>', 'contact-form-7-datepicker'); ?>
						</td>
					</tr>
					
					<tr>
						<th>
							<label><?php echo __('Animate', 'contact-form-7-datepicker'); ?></label>
						</th>
						<td>
							<select name="animate"><?php
							foreach($animate as $row) {
								if($row == __('true', 'contact-form-7-datepicker'))
									$val = "true";
								else
									$val = "false";
								
								if ($val == get_option('animate'))
									$selected = "selected";
								else
									$selected = "";
								
								echo "<option value='".$val."' ".$selected." >".__($row, 'contact-form-7-datepicker')."</option>";
							} ?>
							</select>
						</td>
						<td>
							<?php echo __('<p>Animation on display.</p>','contact-form-7-datepicker'); ?>
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
	* register_files()
	*
	* Registers needed files
	*/
	public static function register_files() {
		wp_register_style('jsdp_ltr', plugins_url( '/css/jsDatePick_ltr.min.css', __FILE__ ), array(), CF7_DATE_PICKER_VERSION);
		wp_register_style('jsdp_rtl', plugins_url( '/css/jsDatePick_rtl.min.css', __FILE__ ), array(), CF7_DATE_PICKER_VERSION);
		
		wp_register_script('jsDatePickJS', plugins_url( '/js/jsDatePick.jquery.min.js', __FILE__ ), array('jquery'), CF7_DATE_PICKER_VERSION, true);
	}
	
	/**
	* plugin_enqueues()
	*
	* Enqueues JS/CSS
	*/
	public static function plugin_enqueues() {
		wp_enqueue_style('jsdp_'.get_option('directionality'));
		wp_enqueue_script('jsDatePickJS');
		
		do_action('plugin_enqueues');
	}

	/**
	* page_text_filter_callback($data)
	*
	* If a match is found in the content of a form, this returns the HTML for the matched date input field
	* @param Array $data, an array of attributes and options for the input date field that we generate code for
	* @return String $string, the HTML for our match
	*/
	private function page_text_filter_callback($data) {
		if (!is_array($data)) {
			$name = $data;
		} else {
			$name = $data['name'];
		}
		
		if (is_array($data) && isset($data['atts']['id'])) {
			$id = $data['atts']['id'];
		} else {
			$id = $name;
		}
		
		$jssafeid = preg_replace('/[^A-Za-z0-9]/', '', $id);
		
		if (is_array($data) && !empty($data['value']) && is_numeric(strtotime($data['value']))) {
			$seldate = date('Y-m-d', strtotime($data['value']));
			
		} else {
			$seldate = get_option('selectedDate');
		}
			
		if ($seldate) {
			$ts = strtotime($seldate);
			$seldate = array(
				'd' => date('d', $ts),
				'm' => date('m', $ts),
				'y' => date('Y', $ts)
			);
			
			$dateval = $seldate['y'].'-'.$seldate['m'].'-'.$seldate['d'];
		} else {
			$dateval = '';
		}
		
		$attributes = '';
		
		if (is_array($data['atts'])) {
			foreach ($data['atts'] as $key => $val) {
				if (!empty($val))
					$attributes .= $key.'="'.$val.'" ';
			}
		}
		
		if (!is_array($data) || (is_array($data['atts']) && empty($data['atts']['id']))) {
			$attributes .= 'id="'.$id.'" ';
		}
		
		if (!empty($dateval)) {	
			$df = str_replace('%', '', get_option('dateFormat'));
			$dateval = date($df, strtotime($dateval));
			$attributes .= 'value="'.$dateval.'"';
		}
		
		$attributes = trim($attributes);
		
		$string = '';
		
		if ( (is_array($data) && $data['opts']['newfield'] === 'true') || !is_array($data) || (is_array($data) && empty($data['opts']['newfield'])))
			$string = '<input type="text" name="'.$name.'" '.$attributes.' />';
		
		$string .= '
		<script type="text/javascript">
			jQuery(document).ready(function() {
				DatePicker_'.$jssafeid.' = new JsDatePick({
					useMode:'.get_option('useMode').',
					isStripped:'.get_option('isStripped').',
					target:"'.$id.'",
					limitToToday:"'.get_option('limitToToday').'",
					cellColorScheme:"'.get_option('cellColorScheme').'",
					dateFormat:"'.get_option('dateFormat').'",
					imgPath:"'.plugins_url('/img/'.get_option('cellColorScheme').'/', __FILE__).'",
					weekStartDay:'.get_option('weekStartDay').',
					yearsRange:['.get_option('yearsRange').'],
					directionality:"'.get_option('directionality').'",
					yearButtons:'.get_option('yearButtons').',
					monthButtons:'.get_option('monthButtons').',
					animate:'.get_option('animate');
		if ($seldate) {
			$string .= ',
				selectedDate: {
					year: '.$seldate['y'].', 
					month: '.$seldate['m'].',
					day: '.$seldate['d'].'
				}';
		}
		$string .= '
				});
			});
		</script>';
		$schemecss = self::get_scheme_style(get_option('cellColorScheme'));
		if ($schemecss)
		$string .= '
		<style type="text/css">
			@import url(\''.$schemecss.'\');
		</style>';
		
		return $string;
	}

	/**
	* wpcf7_shotcode_handler($tag)
	*
	* Handler for wpcf7 shortcodes [date ] and [date* ]
	* @param Array $tag, this is the tag that will be handled (can be 'date' or 'date*')
	* @return String $html, the HTML that will be appended to the form
	*/
	public static function wpcf7_shotcode_handler($tag) {
		global $wpcf7_contact_form;

		if ( ! is_array( $tag ) )
			return '';

		$type = $tag['type'];
		$name = $tag['name'];
		
		$options = (array) $tag['options'];
		$values = (array) $tag['values'];
	
		if ( empty( $name ) )
			return '';

		$atts = array();

		if ( 'date*' == $type )
			$atts['class'] = ' wpcf7-validates-as-required';

		foreach ( $options as $option ) {
			if ( preg_match( '%^id:([-0-9a-zA-Z_]+)$%', $option, $matches ) ) {
				$atts['id'] = $matches[1];

			} elseif ( preg_match( '%^class:([-0-9a-zA-Z_]+)$%', $option, $matches ) ) {
				$atts['class'] .= ' ' . $matches[1];

			} elseif ( preg_match( '%^([0-9]*)[/x]([0-9]*)$%', $option, $matches ) ) {
				$atts['size'] = (int) $matches[1];
				$atts['maxlength'] = (int) $matches[2];
			}
		}

		if ( is_a( $wpcf7_contact_form, 'WPCF7_ContactForm' ) && $wpcf7_contact_form->is_posted() ) {
			if ( isset( $_POST['_wpcf7_mail_sent'] ) && $_POST['_wpcf7_mail_sent']['ok'] )
				$value = '';
			else
				$value = $_POST[$name];
		} else {
			$value = $values[0];
		}
		
		$data = array(
			"name" => $name,
			"atts" => (array) $atts,
			"opts" => NULL,
			"value" => $value
		);

		$html = self::page_text_filter_callback($data);
		$validation_error = '';
		if ( is_a( $wpcf7_contact_form, 'WPCF7_ContactForm' ) )
			$validation_error = $wpcf7_contact_form->validation_error( $name );

		$html = '<span class="wpcf7-form-control-wrap ' . $name . '">' . str_replace('<p>','',$html) . $validation_error . '</span>';

		return $html;
	}

	/**
	* wpcf7_validation_filter($result, $tag)
	*
	* This is used to validate the Contact Form 7 'date' field
	* @param Array $result, 'valid' key has a boolean value (true if valid)
	* and 'reason' key with a message if not valid
	* @param Array $tag, contains the type and name of the field that is validated
	* @return Array $result
	*/
	public static function wpcf7_validation_filter( $result, $tag ) {
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
	* admin_l10n()
	*
	* Function for loading the l10n files from /languages/ dir for the administatrion panel
	*/
	public static function admin_l10n() {
		load_plugin_textdomain( 'contact-form-7-datepicker', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
	
	/**
	* datepicker_shortcode_handler()
	*
	* Function that handles the [datepicker name="?" id="?" class="?" newfield="?" value="?"] shortcode 
	*/
	public static function datepicker_shortcode_handler($atts) {
		extract(shortcode_atts(array(
			'name' => '',
			'id' => '',
			'class' => '',
			'newfield' => 'true',
			'value' => ''
		), $atts));
		
		$data = array(
			"name" => ($name) ? "{$name}" : "{$id}",
			"atts" => array(
				"id" => ($id) ? "{$id}" : "{$name}",
				"class" => "{$class}"),
			"opts" => array(
				"newfield" => "{$newfield}"),
			"value" => "{$value}"
		);

		return self::page_text_filter_callback($data);
	}
	
	/**
	* calendar_l10n()
	*
	* Localization of JS file strings
	*/
	public static function calendar_l10n() {
		$l10n_strings = array(
						'MONTHS' => array(
											__('Janaury', 'contact-form-7-datepicker'), 
											__('February', 'contact-form-7-datepicker'),
											__('March', 'contact-form-7-datepicker'),
											__('April', 'contact-form-7-datepicker'),
											__('May', 'contact-form-7-datepicker'),
											__('June', 'contact-form-7-datepicker'),
											__('July', 'contact-form-7-datepicker'),
											__('August', 'contact-form-7-datepicker'),
											__('September', 'contact-form-7-datepicker'),
											__('October', 'contact-form-7-datepicker'),
											__('November', 'contact-form-7-datepicker'),
											__('December', 'contact-form-7-datepicker')
										),
						'DAYS_3' => array(
											__('Sun', 'contact-form-7-datepicker'),
											__('Mon', 'contact-form-7-datepicker'),
											__('Tue', 'contact-form-7-datepicker'),
											__('Wed', 'contact-form-7-datepicker'),
											__('Thu', 'contact-form-7-datepicker'),
											__('Fri', 'contact-form-7-datepicker'),
											__('Sat', 'contact-form-7-datepicker')
										),
						'MONTH_FWD' => __('Move a month forward', 'contact-form-7-datepicker'),
						'MONTH_BCK' => __('Move a month backward', 'contact-form-7-datepicker'),
						'YEAR_FWD' => __('Move a year forward', 'contact-form-7-datepicker'),
						'YEAR_BCK' => __('Move a year backward', 'contact-form-7-datepicker'),
						'CLOSE' => __('Close the calendar', 'contact-form-7-datepicker'),
						'ERROR_2' => __('Date object invalid!', 'contact-form-7-datepicker'),
						'ERROR_1' => __('Date object invalid!', 'contact-form-7-datepicker'),
						'ERROR_4' => __('Target invalid!', 'contact-form-7-datepicker'),
						'ERROR_3' => __('Target invalid!', 'contact-form-7-datepicker')
						);
		$l10n = array('l10n_print_after' => 'g_l10n = ' . json_encode($l10n_strings) . ';');		
		
		wp_localize_script('jsDatePickJS', 'g_l10n', $l10n);
	}
	
	/**
	* register_shortcodes()
	* 
	* Function for registering our shortcodes with CF7
	*/
	public static function register_shortcodes() {
		if (function_exists('wpcf7_add_shortcode')) {
			wpcf7_add_shortcode('date', array(__CLASS__, 'wpcf7_shotcode_handler'), true);
			wpcf7_add_shortcode('date*', array(__CLASS__, 'wpcf7_shotcode_handler'), true);
			add_shortcode( 'datepicker', array(__CLASS__, 'datepicker_shortcode_handler') );
		}
	}
	
	/**
	* tag_generator()
	* 
	* Registers the tag generator for CF7
	*/
	public static function tag_generator() {
		if (function_exists('wpcf7_add_tag_generator')) {
			wpcf7_add_tag_generator('date', __('Date field', 'contact-form-7-datepicker'),
			'wpcf7-tg-pane-date', array(__CLASS__, 'wpcf7_tg_pane_datepicker_'));
		}
	}
	
	/**
	* wpcf7_tg_pane_datepicker_(&$contact_form)
	* 
	* Caller function for the tag generator
	* @param reference &$contact_form
	*/
	public static function wpcf7_tg_pane_datepicker_(&$contact_form) {
		self::wpcf7_tg_pane_datepicker( 'date' );
	}
	
	/**
	* wpcf7_tg_pane_datepicker($type = 'date')
	* 
	* Callback function for the tag generator (called by wpcf7_tg_pane_datepicker_)
	* @param $type = 'date'
	*/
	private function wpcf7_tg_pane_datepicker($type = 'date') { ?>
		<div id="wpcf7-tg-pane-<?php echo $type; ?>" class="hidden">
			<form action="">
			<table>
				<tr>
					<td>
						<input type="checkbox" name="required" />&nbsp;<?php echo esc_html( __( 'Required field?', 'wpcf7' ) ); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo esc_html( __( 'Name', 'wpcf7' ) ); ?><br /><input type="text" name="name" class="tg-name oneline" />
					</td>
					<td></td>
				</tr>
			</table>

			<table>
				<tr>
					<td>
						<code>id</code> (<?php echo esc_html( __( 'optional', 'wpcf7' ) ); ?>)<br />
						<input type="text" name="id" class="idvalue oneline option" />
					</td>

					<td>
						<code>class</code> (<?php echo esc_html( __( 'optional', 'wpcf7' ) ); ?>)<br />
						<input type="text" name="class" class="classvalue oneline option" />
					</td>
				</tr>

				<tr>
					<td>
						<code>size</code> (<?php echo esc_html( __( 'optional', 'wpcf7' ) ); ?>)<br />
						<input type="text" name="size" class="numeric oneline option" />
					</td>

					<td>
						<code>maxlength</code> (<?php echo esc_html( __( 'optional', 'wpcf7' ) ); ?>)<br />
						<input type="text" name="maxlength" class="numeric oneline option" />
					</td>
				</tr>
			</table>

			<div class="tg-tag">
				<?php echo esc_html( __( "Copy this code and paste it into the form left.", 'wpcf7' ) ); ?><br /><input type="text" name="<?php echo $type; ?>" class="tag" readonly="readonly" onfocus="this.select()" />
			</div>

			<div class="tg-mail-tag">
				<?php echo esc_html( __( "And, put this code into the Mail fields below.", 'wpcf7' ) ); ?><br /><span class="arrow">&#11015;</span>&nbsp;<input type="text" class="mail-tag" readonly="readonly" onfocus="this.select()" />
			</div>
			</form>
		</div><?php
	}

}

new CF7DatePicker;

?>
