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

		// Messages
		add_filter('wpcf7_messages', array(__CLASS__, 'messages'));
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

		$inline = false;

		$dpOptions = array();
		foreach ($options as $option) {
			if (preg_match('%^id:([-_\w\d]+)$%i', $option, $matches)) {
				$id_att = $matches[1];
			} elseif (preg_match('%^class:([-_\w\d]+)$%i', $option, $matches)) {
				$class_att .= " $matches[1]";
			} elseif (preg_match('%^(\d*)[/x](\d*)$%i', $option, $matches)) {
				$size_att = (int) $matches[1];
				$maxlen_att = (int) $matches[2];
			} elseif (preg_match('%^tabindex:(\d+)$%i', $option, $matches)) {
				$tabindex_att = (int) $matches[1];
			} elseif (preg_match('%^date-format:([-_/\.\w\d]+)$%i', $option, $matches)) {
				$dpOptions['dateFormat'] = str_replace('_', ' ', $matches[1]);
			} elseif (preg_match('%^(min|max)-date:([-_/\.\w\d]+)$%i', $option, $matches)) {
				$dpOptions[$matches[1] . 'Date'] = $matches[2];
			} elseif (preg_match('%^first-day:(\d)$%', $option, $matches)) {
				$dpOptions['firstDay'] = (int) $matches[1];
			} elseif (preg_match('%^animate:(\w+)$%i', $option, $matches)) {
				$dpOptions['showAnim'] = $matches[1];
			} elseif (preg_match('%^change-month$%i', $option, $matches)) {
				$dpOptions['changeMonth'] = true;
			} elseif (preg_match('%^change-year$%i', $option, $matches)) {
				$dpOptions['changeYear'] = true;
			} elseif (preg_match('%^year-range:(\d+)-?(\d+)?$%', $option, $matches)) {
				$dpOptions['yearRange'] = $matches[1] . ':' . @$matches[2];
			} elseif (preg_match('%^months:(\d+)$%', $option, $matches)) {
				$dpOptions['numberOfMonths'] = (int) $matches[1];
			} elseif (preg_match('%^buttons$%', $option, $matches)) {
				$dpOptions['showButtonPanel'] = true;
			} elseif (preg_match('%inline$%', $option, $matches)) {
				$inline = true;
				$dpOptions['altField'] = "#{$name}_alt";
			}

			do_action_ref_array('cf7_datepicker_attr_match', array($dpOptions), $option);
		}

		$value = reset($values);

		if (wpcf7_script_is() && preg_grep('%^watermark$%', $options)) {
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

		$input_type = $inline ? 'hidden' : 'text';
		$input_atts = $inline ? "id=\"{$name}_alt\"" : $atts;

		$input = sprintf('<input type="%s" name="%s" value="%s" %s/>',
			$input_type,
			esc_attr($name),
			esc_attr($value),
			$input_atts
		);

		if ($inline)
			$input .= sprintf('<div id="%s_datepicker" %s></div>', $name, $atts);

		$dp_selector = $inline ? '#' . $name . '_datepicker' : $name;

		$dp = new CF7_DatePicker($dp_selector, $dpOptions);

		return sprintf('<span class="wpcf7-form-control-wrap %s">%s %s</span>%s',
			esc_attr($name),
			$input,
			$validation_error,
			$dp->generate_code($inline)
		);
	}

	public static function validation_filter($result, $tag) {
		$type = $tag['type'];
		$name = $tag['name'];

		$value = trim($_POST[$name]);

		if ('date*' == $type && empty($value)) {
			$result['valid'] = false;
			$result['reason'][$name] = wpcf7_get_message('invalid_required');
		}

		// TODO: Implement date format verification
		if (! empty($value) && ! self::is_valid_date($value)) {
			$result['valid'] = false;
			$result['reason'][$name] = wpcf7_get_message('invalid_date');
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

	public static function messages($messages) {
		$messages['invalid_date'] = array(
			'description' => __('The date that the sender entered is invalid'),
			'default' => __('Invalid date supplied.'),
		);

		return $messages;
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

	private static function is_valid_date($value) {
		return strtotime($value) ? true : false;
	}
}
