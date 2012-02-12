<?php

class CF7_DatePicker {
	private $input_id;

	private $options = array(
		'currentText' => '',
		'dateFormat' => '',
		'dayNames' => '',
		'dayNamesMin' => '',
		'dayNamesShort' => '',
		'monthNames' => '',
		'monthNamesShort' => '',
		'minDate' => '',
		'maxDate' => '',
		'firstDay' => '',
		'nextText' => '',
		'prevText' =>  '',
		'defaultDate' => '',
		'showAnim' => 'show',
		'changeMonth' => '',
		'changeYear' => '',
		'yearRange' => '',
	);

	function __construct($name, $options = null) {

		$this->input_name = $name;

		$this->options['currentText'] = __('Today');

		$this->options['dayNames'] = array(
			__('Sunday'),
			__('Monday'),
			__('Tuesday'),
			__('Wednesday'),
			__('Thursday'),
			__('Friday'),
			__('Saturday')
		);

		$this->options['dayNamesMin'] = array(
			__('Su'),
			__('Mo'),
			__('Tu'),
			__('We'),
			__('Th'),
			__('Fr'),
			__('Sa')
		);

		$this->options['dayNamesShort'] = array(
			__('Sun'),
			__('Mon'),
			__('Tue'),
			__('Wed'),
			__('Thu'),
			__('Fri'),
			__('Sat')
		);

		$this->options['monthNames'] = array(
			__('January'),
			__('February'),
			__('March'),
			__('April'),
			__('May'),
			__('June'),
			__('July'),
			__('August'),
			__('September'),
			__('October'),
			__('November'),
			__('December'),
		);

		$this->options['monthNamesShort'] = array(
			__('Jan'),
			__('Feb'),
			__('Mar'),
			__('Apr'),
			__('May'),
			__('Jun'),
			__('Jul'),
			__('Aug'),
			__('Sep'),
			__('Oct'),
			__('Nov'),
			__('Dec')
		);

		$this->options['firstDay'] = get_option('start_of_week');
		$this->options['nextText'] = __('Next &gt;');
		$this->options['prevText'] = __('&lt; Prev');

		if (! empty($options) && is_array($options))
			foreach ($options as $key => $val) {
				if (array_key_exists($key, $this->options))
					$this->options[$key] = $val;
			}

		$this->options = apply_filters('cf7_datepicker_options', $this->options);
	}

	public function set($option, $value) {
		$this->options[$option] = $value;
	}

	public function get($option) {
		return $this->options[$option];
	}

	public function get_all() {
		return $this->options;
	}

	public function generate_code($echo = false) {
		$out  = "jQuery('input[name=\"{$this->input_name}\"]').datepicker({$this->_options_encode()});";

		$out = self::_js_wrap($out);

		if ($echo)
			echo $out;

		return $out;
	}

	private function _options_encode() {
		$options = json_encode(array_filter(
			$this->options,
			array(__CLASS__, '_array_filter_not_empty')
		));

		return stripslashes($options);
	}

	private static function _array_filter_not_empty($var) {
		return (! empty($var));
	}

	private static function _js_wrap($code) {
		$out  = "<script type=\"text/javascript\">\n";
		$out .= "\t{$code}\n";
		$out .= "</script>\n";

		return $out;
	}
}

?>
