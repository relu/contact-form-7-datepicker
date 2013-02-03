<?php
/**
Plugin Name: Contact Form 7 Datepicker
Plugin URI: https://github.com/relu/contact-form-7-datepicker/
Description: Easily add a date field using jQuery UI's datepicker to your CF7 forms. This plugin depends on Contact Form 7.
Author: Aurel Canciu
Version: 2.2.1
Author URI: https://github.com/relu/
*/

/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/
?>
<?php

class ContactForm7Datepicker {

	public static function init() {
		add_action('plugins_loaded', array(__CLASS__, 'load_date_module'), 10);

		add_action('wpcf7_enqueue_scripts', array(__CLASS__, 'enqueue_js'));
		add_action('wpcf7_enqueue_styles', array(__CLASS__, 'enqueue_css'));

		register_activation_hook(__FILE__, array(__CLASS__, 'activate'));

		if (is_admin()) {
			require_once dirname(__FILE__) . '/admin.php';
			ContactForm7Datepicker_Admin::init();
		}
	}

	public static function load_date_module() {
		require_once dirname(__FILE__) . '/date-module.php';
		ContactForm7Datepicker_Date::register();
	}

	public static function activate() {
		if (! get_option('cf7dp_ui_theme'))
			add_option('cf7dp_ui_theme', 'base');
	}

	public static function enqueue_js() {
		wp_enqueue_script('jquery-ui-datepicker', null, null, null, true);

		$regional = CF7_DatePicker::get_regional_match();

		if (! $regional)
			return;

		wp_enqueue_script(
			'jquery-ui-' . $regional,
			'http://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-' . $regional . '.min.js',
			array('jquery-ui-datepicker'),
			null,
			true
		);
	}

	public static function enqueue_css() {
		$theme = get_option('cf7dp_ui_theme');

		if (! is_admin() && $theme == 'disabled')
			return;

		wp_enqueue_style('jquery-ui-theme', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/' . $theme . '/jquery-ui.css', array(), '');
	}
}

ContactForm7Datepicker::init();
