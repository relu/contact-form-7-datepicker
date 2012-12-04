<?php

class ContactForm7Datepicker_Date {

	public static function register() {
		require_once dirname(__FILE__) . '/datepicker.php';

		// Register shortcodes
		self::add_shortcodes();

		// Validations
		add_filter('wpcf7_validate_date', array(__CLASS__, 'validation_filter'), 10, 2);
		add_filter('wpcf7_validate_date*', array(__CLASS__, 'validation_filter'), 10, 2);


		// Tag generator
		add_action('load-toplevel_page_wpcf7', array(__CLASS__, 'tag_generator'));
	}

	public static function shortcode_handler($tag) {
		if (! is_array($tag))
			return;

		$type = $tag['type'];
		$name = $tag['name'];
		$options = (array) $tag['options'];
		$values = (array) $tag['values'];

		if (empty($name))
			return;

		$validation_error = wpcf7_get_validation_error($name);

		$atts = $id_att = $size_att = $maxlen_att = '';
		$tabindex_att = $title_att = '';

		$class_att = wpcf7_form_controls_class( $type, 'wpcf7-date');

		if ('date*' == $type)
			$class_att .= ' wpcf7-validates-as-required';

		if ($validation_error)
			$class_att .= ' wpcf7-not-valid';

		$dpOptions = array();
		foreach ($options as $option) {
			if (preg_match('%^id:([-_0-9a-z]+)$%i', $option, $matches)) {
				$id_att = $matches[1];
			} elseif (preg_match('%^class:([-_0-9a-z]+)$%i', $option, $matches)) {
				$class_att .= " $matches[1]";
			} elseif (preg_match('%^([0-9]*)[/x]([0-9]*)$%i', $option, $matches)) {
				$size_att = (int) $matches[1];
				$maxlen_att = (int) $matches[2];
			} elseif (preg_match('%^tabindex:(\d+)$%i', $option, $matches)) {
				$tabindex_att = (int) $matches[1];
			} elseif (preg_match('%^date-format:([-_/\.a-z0-9]+)$%i', $option, $matches)) {
				$dpOptions['dateFormat'] = str_replace('_', ' ', $matches[1]);
			} elseif (preg_match('%^(min|max)-date:([-_/\., 0-9a-z]+)$%i', $option, $matches)) {
				$dpOptions[$matches[1] . 'Date'] = $matches[2];
			} elseif (preg_match('%^first-day:(\d)$%', $option, $matches)) {
				$dpOptions['firstDay'] = (int) $matches[1];
			} elseif (preg_match('%^animate:([a-z]+)$%i', $option, $matches)) {
				$dpOptions['showAnim'] = $matches[1];
			} elseif (preg_match('%^change-month:(true|false)$%i', $option, $matches)) {
				$dpOptions['changeMonth'] = ('true' == $matches[1]);
			} elseif (preg_match('%^change-year:(true|false)$%i', $option, $matches)) {
				$dpOptions['changeYear'] = ('true' == $matches[1]);
			} elseif (preg_match('%^year-range:([\d]+)-?([\d]+)?$%', $option, $matches)) {
				$dpOptions['yearRange'] = "{$matches[1]}:{$matches[2]}";
			} elseif (preg_match('%^months:([\d]+)$%', $option, $matches)) {
				$dpOptions['numberOfMonths'] = (int) $matches[1];
			} elseif (preg_match('%^buttons:(true|false)$%', $option, $matches)) {
				$dpOptions['showButtonPanel'] = ('true' == $matches[1]);
			}

			do_action('cf7_datepicker_attr_match', $dpOptions, $option);
		}

		$value = reset($values);

		if (wpcf7_script_is() && preg_grep('%^waremark$%', $options)) {
			$class_att .= ' wpcf7-use-title-as-watermark';
			$title_att .= " $value";
			$value = '';
		}

		if (wpcf7_is_posted() && isset($_POST[$name]))
			$value = stripslashes($_POST[$name]);

		if ($id_att)
			$atts .= ' id="' . trim($id_att) . '"';

		if ($class_att)
			$atts .= ' class="' . trim($class_att) . '"';

		if ($size_att)
			$atts .= ' size="' . $size_att . '"';
		else
			$atts .= ' size="40"';

		if ($maxlen_att)
			$atts .= ' maxlength="' . $maxlen_att . '"';

		if ('' !== $tabindex_att)
			$atts .= ' tabindex="' . $tabindex_att .'"';

		if ($title_att)
			$atts .= ' title="' . trim(esc_attr($title_att)) . '"';

		$input = sprintf('<input type="text" name="%s" value="%s" %s/>',
			esc_attr($name),
			esc_attr($value),
			$atts
		);

		$dp = new CF7_DatePicker($name, $dpOptions);

		return sprintf('<span class="wpcf7-form-control-wrap %s">%s %s</span>%s',
			esc_attr($name),
			$input,
			$validation_error,
			$dp->generate_code()
		);
	}

	public static function validation_filter($result, $tag) {
		$type = $tag['type'];
		$name = $tag['name'];

		$value = trim($_POST[$name]);

		if ('date*' == $type && '' == $value) {
			$result['valid'] = false;
			$result['reason'][$name] = wpcf7_get_message('invalid_required');
		}

		return $result;
	}

	public static function tag_generator() {
		wpcf7_add_tag_generator('date',
			__('Date field', 'wpcf7'),
			'wpcf7-tg-pane-date',
			array(__CLASS__, 'tg_pane_date')
		);
	}

	public static function tg_pane_date() {
		require_once 'date-tag-generator.php';
	}

	private static function add_shortcodes() {
		if (function_exists('wpcf7_add_shortcode')) {
			wpcf7_add_shortcode('date', array(__CLASS__, 'shortcode_handler'), true);
			wpcf7_add_shortcode('date*', array(__CLASS__, 'shortcode_handler'), true);
		}
	}


	private static function animate_dropdown() {
		$effects = array(
			'show' => __('Show'),
			'blind' => __('Blind'),
			'clip' => __('Clip'),
			'drop' => __('Drop'),
			'explode' => __('Explode'),
			'fade' => __('Fade'),
			'fold' => __('Fold'),
			'puff' => __('Puff'),
			'slide' => __('Slide'),
			'scale' => __('Scale')
		);

		$effects = apply_filters('cf7dp_effects', $effects);

		$html = "<select id=\"animate\">\n";
		foreach ($effects as $key => $val) {
			$html .= "\t<option value=\"{$key}\">{$val}</option>\n";
		}

		$html .= "</select>";

		echo $html;
	}
}
