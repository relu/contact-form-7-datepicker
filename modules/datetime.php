<?php

class ContactForm7Datepicker_DateTime {

	static $inline_js = array();

	public static function register() {
		// Register shortcodes
        add_action('wpcf7_init', array(__CLASS__, 'add_shortcodes'));

		// Validations
		add_filter('wpcf7_validate_datetime', array(__CLASS__, 'validation_filter'), 10, 2);
		add_filter('wpcf7_validate_datetime*', array(__CLASS__, 'validation_filter'), 10, 2);

		// Tag generator
		add_action('wpcf7_admin_init', array(__CLASS__, 'tag_generator'), 70);

		// Messages
		add_filter('wpcf7_messages', array(__CLASS__, 'messages'));

		// Print inline javascript
		add_action('wp_print_footer_scripts', array(__CLASS__, 'print_inline_js'), 99999);
	}

	public static function shortcode_handler($tag) {
		$tag = new WPCF7_Shortcode($tag);

		if (empty($tag->name))
			return '';

		$validation_error = wpcf7_get_validation_error($tag->name);

		$class = wpcf7_form_controls_class($tag->type, 'wpcf7-date');

		if ($validation_error)
			$class .= ' wpcf7-not-valid';

		$atts = array();

		$atts['size'] = $tag->get_size_option('40');
		$atts['maxlength'] = $tag->get_maxlength_option();
		$atts['class'] = $tag->get_class_option($class);
		$atts['id'] = $tag->get_option('id', 'id', true);
		$atts['tabindex'] = $tag->get_option('tabindex', 'int', true);
		$atts['type'] = 'text';

		if ($tag->has_option('readonly'))
			$atts['readonly'] = 'readonly';

		if ($tag->is_required())
			$atts['aria-required'] = 'true';

		$value = (string)reset($tag->values);

		if ($tag->has_option('placeholder') || $tag->has_option('watermark')) {
			$atts['placeholder'] = $value;
			$value = '';
		}

		if (wpcf7_is_posted() && isset($_POST[$tag->name]))
			$value = stripslashes_deep($_POST[$tag->name]);

		$atts['value'] = $value;

		$dpOptions = array();
		$dpOptions['dateFormat'] = str_replace('_', ' ', $tag->get_option('date-format', '', true));
		$dpOptions['timeFormat'] = str_replace('_', ' ', $tag->get_option('time-format', '', true));
		$dpOptions['minDate'] = $tag->get_option('min-date', '', true);
		$dpOptions['maxDate'] = $tag->get_option('max-date', '', true);
		$dpOptions['firstDay'] = (int)$tag->get_option('first-day', 'int', true);
		$dpOptions['showAnim'] = $tag->get_option('animate', '', true);
		$dpOptions['yearRange'] = str_replace('-', ':', $tag->get_option('year-range', '', true));
		$dpOptions['numberOfMonths'] = $tag->get_option('months', 'int', true);
		$dpOptions['controlType'] = $tag->get_option('control-type', '', true);

		$dpOptions['showButtonPanel'] = $tag->has_option('buttons');
		$dpOptions['changeMonth'] = $tag->has_option('change-month');
		$dpOptions['changeYear'] = $tag->has_option('change-year');
		$dpOptions['noWeekends'] = $tag->has_option('no-weekends');

		foreach (array('minute', 'hour', 'second') as $s) {
			foreach (array('min', 'max') as $m) {
				$dpOptions[$s . ucfirst($m)] = (int)$tag->get_option("$m-$s", 'int', true);
			}

			$dpOptions['step' . ucfirst($s)] = (int)$tag->get_option("step-$s", 'int', true);
		}

		$inline = $tag->has_option('inline');

		if ($inline) {
			$dpOptions['altField'] = "#{$tag->name}_alt";
			$atts['id'] = "{$tag->name}_alt";
		}

		$atts['type'] = $inline ? 'hidden' : 'text';
		$atts['name'] = $tag->name;

		$atts = wpcf7_format_atts($atts);

		$html = sprintf(
			'<span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s %4$s</span>',
			$tag->name, $atts, $validation_error,
			$inline ? "<div id=\"{$tag->name}_datetimepicker\"></div>" : '');

		$html = apply_filters('cf7dp_datetime_input', $html);

		$dp_selector = $inline ? '#' . $tag->name . '_datetimepicker' : $tag->name;

		$dp = new CF7_DateTimePicker('datetime', $dp_selector, $dpOptions);

		self::$inline_js[] = $dp->generate_code($inline);

		return $html;
	}

	public static function validation_filter($result, $tag) {
		$type = $tag['type'];
		$name = $tag['name'];

		$value = trim($_POST[$name]);

		if ('datetime*' == $type && empty($value)) {
            		$result->invalidate($tag, wpcf7_get_message('invalid_required'));
		}

		if (! empty($value) && ! self::is_valid_date($value)) {
            		$result->invalidate($tag, wpcf7_get_message('invalid_datetime'));
		}

		return $result;
	}

	public static function tag_generator() {
        if (! class_exists( 'WPCF7_TagGenerator' ))
            return;

        $tag_generator = WPCF7_TagGenerator::get_instance();
        $tag_generator->add( 'datetime', __( 'datetime', 'contact-form-7' ),
            array(__CLASS__, 'tg_pane') );
	}

	public static function tg_pane($contact_form, $args = '') {
        $args = wp_parse_args( $args, array() );
        $type = 'datetime';

		require_once dirname(__FILE__) . '/generators/datetime.php';
	}

	public static function add_shortcodes() {
		if (function_exists('wpcf7_add_form_tag')) {
			wpcf7_add_form_tag(array('datetime', 'datetime*'), array(__CLASS__, 'shortcode_handler'), true);
		}
	}

	public static function messages($messages) {
		$messages['invalid_datetime'] = array(
			'description' => __('The date and time that the sender entered is invalid'),
			'default' => __('Invalid date and time supplied.'),
		);

		return $messages;
	}

	public static function print_inline_js() {
		if (! wp_script_is('jquery-ui-timepicker', 'done') || empty(self::$inline_js))
			return;

		$out = implode("\n\t", self::$inline_js);
		$out = "jQuery(function($){\n\t$out\n});";

		echo "\n<script type=\"text/javascript\">\n{$out}\n</script>\n";
	}

	private static function animate_dropdown() {
		$html = "<select id=\"animate\">\n";

		foreach (CF7_DateTimePicker::$effects as $val) {
			$html .= '<option value="' . esc_attr($val) . '">' . ucfirst($val) . '</option>';
		}

		$html .= "</select>";

		echo $html;
	}

	private static function is_valid_date($value) {
		$valid = strtotime($value) ? true : false;

		if (! $valid) {
			// Validate dd/mm/yy
			$new_value = str_replace('/', '-', $value);
			$valid = strtotime($new_value) ? true : false;
		}

		return apply_filters( 'cf7dp_is_valid_datetime', $valid, $value );
	}

}

ContactForm7Datepicker_DateTime::register();
