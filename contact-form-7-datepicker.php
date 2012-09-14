<?php
/**
Plugin Name: Contact Form 7 Datepicker
Plugin URI: https://github.com/relu/contact-form-7-datepicker/
Description: Implements a new [date] tag in Contact Form 7 that adds a date field to a form. When clicking the field a calendar pops up enabling your site visitors to easily select any date.
Author: Aurel Canciu
Version: 2.0
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

add_action('wpcf7_enqueue_scripts', 'cf7dp_enqueue_js');
add_action('wpcf7_enqueue_styles', 'cf7dp_enqueue_css');

add_action('admin_enqueue_scripts', 'cf7dp_enqueue_js');
add_action('admin_print_styles', 'cf7dp_enqueue_css');


add_action('wp_ajax_cf7dp_save_settings', 'cf7dp_ajax_save_settings');
add_action('wpcf7_admin_after_general_settings', 'cf7dp_add_theme_metabox');
add_action('admin_footer', 'cf7dp_ui_theme_js');

/* Load date-module after loading all plugins */
function cf7dp_load_date_module() {
	require_once dirname(__FILE__) . '/date-module.php';
}
add_action('plugins_loaded', 'cf7dp_load_date_module', 1);

require_once dirname(__FILE__) . '/admin.php';

function cf7dp_enqueue_js() {
	if (is_admin() && ! cf7dp_is_wpcf7_page())
		return;

	wp_enqueue_script('jquery-ui-datepicker');

	$regional = CF7_DatePicker::get_regional_match();

	if (! $regional)
		return;

	wp_enqueue_script(
		'jquery-ui-' . $regional,
		'http://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-' . $regional . '.min.js',
		array('jquery-ui'),
		'',
		false
	);
}

function cf7dp_enqueue_css() {
	if (is_admin() && ! cf7dp_is_wpcf7_page())
		return;

	$theme = get_option('cf7dp_ui_theme');

	if (! is_admin() && $theme == 'disabled')
		return;

	wp_enqueue_style('jquery-ui-theme', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/' . $theme . '/jquery-ui.css', array(), '');
}

function cf7dp_is_wpcf7_page() {
	global $current_screen;

	if ($current_screen->id != 'toplevel_page_wpcf7')
		return false;

	return true;
}

?>
