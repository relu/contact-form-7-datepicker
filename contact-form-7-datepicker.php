<?php
/**
Plugin Name: Contact Form 7 Datepicker
Plugin URI: https://github.com/relu/contact-form-7-datepicker/
Description: Easily add a date field using jQuery UI's datepicker to your CF7 forms. This plugin depends on Contact Form 7.
Author: Aurel Canciu
Version: 2.6.0
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

	const JQUERYUI_VERSION = '1.11.4';

	function __construct() {
		add_action('plugins_loaded', array($this, 'load_modules'), 50);

		add_action('wpcf7_enqueue_scripts', array(__CLASS__, 'enqueue_js'));
		add_action('wpcf7_enqueue_styles', array(__CLASS__, 'enqueue_css'));

		register_activation_hook(__FILE__, array($this, 'activate'));

		if (is_admin()) {
			require_once dirname(__FILE__) . '/admin.php';
		}
	}

	function load_modules() {
		require_once dirname(__FILE__) . '/datetimepicker.php';
		require_once dirname(__FILE__) . '/modules/datetime.php';
		require_once dirname(__FILE__) . '/modules/date.php';
		require_once dirname(__FILE__) . '/modules/time.php';
	}

	function activate() {
		if (! get_option('cf7dp_ui_theme'))
			add_option('cf7dp_ui_theme', 'smoothness');
	}

	public static function enqueue_js() {
		$regional = CF7_DateTimePicker::get_regional_match();
		$proto = is_ssl() ? 'https' : 'http';

		if (! empty($regional)) {
			wp_enqueue_script(
				'jquery-ui-' . $regional,
				$proto . '://ajax.googleapis.com/ajax/libs/jqueryui/' . self::JQUERYUI_VERSION . '/i18n/datepicker-' . $regional . '.min.js',
				array('jquery-ui-datepicker'),
				self::JQUERYUI_VERSION,
				true
			);

			wp_enqueue_script(
				'jquery-ui-timepicker-' . $regional,
				plugins_url('js/jquery-ui-timepicker/i18n/jquery-ui-timepicker-' . $regional . '.js', __FILE__),
				array('jquery-ui-timepicker'),
				'',
				true
			);
		}

		wp_enqueue_script('jquery-ui-datepicker');

		wp_enqueue_script(
			'jquery-ui-timepicker',
			plugins_url('js/jquery-ui-timepicker/jquery-ui-timepicker-addon.min.js', __FILE__),
			array('jquery-ui-datepicker'),
			'',
			true
		);

		wp_enqueue_script('jquery-ui-slider');

		wp_enqueue_script(
			'jquery-ui-slider-access',
			plugins_url('js/jquery-ui-sliderAccess.js', __FILE__),
			array('jquery-ui-slider', 'jquery-ui-button'),
			'',
			true
		);

		wp_register_script(
			'jquery-ui-effect-core',
			plugins_url('js/jquery.ui.effect.min.js', __FILE__),
			array('jquery-ui-datepicker'),
			self::JQUERYUI_VERSION,
			true
		);

		foreach (CF7_DateTimePicker::$effects as $effect) {
			wp_register_script(
				'jquery-ui-effect-' . $effect,
				plugins_url('js/jquery.ui.effect-' . $effect . '.min.js', __FILE__),
				array('jquery-ui-effect-core'),
				self::JQUERYUI_VERSION,
				true
			);
		}
	}

	public static function enqueue_css() {
        wp_enqueue_style(
            'jquery-ui-theme',
            self::get_theme_uri(),
            '',
            self::JQUERYUI_VERSION,
            'all'
        );

		wp_enqueue_style(
			'jquery-ui-timepicker',
			plugins_url('js/jquery-ui-timepicker/jquery-ui-timepicker-addon.min.css', __FILE__)
		);
	}

    public static function get_theme_uri() {
		$theme = apply_filters('cf7dp_ui_theme', get_option('cf7dp_ui_theme'));

		if (! is_admin() && $theme == 'disabled')
			return;

		$proto = is_ssl() ? 'https' : 'http';

        $custom_themes = (array)apply_filters('cf7dp_custom_ui_themes', array());

        if (! is_admin() && ! empty($custom_themes) && array_key_exists($theme, $custom_themes)) {
            $theme_css_uri = $custom_themes[$theme];

            $uri = get_stylesheet_directory_uri() . '/' . ltrim($theme_css_uri, '/');
        } else {
            $uri = $proto . '://ajax.googleapis.com/ajax/libs/jqueryui/' . self::JQUERYUI_VERSION . '/themes/' . $theme . '/jquery-ui.min.css';
        }

        return $uri;
    }
}

new ContactForm7Datepicker;
