<?php

class CF7_DateTimePicker {

	private $type, $input_name;

	private $options = array(
		'dateFormat' => '',
		'timeFormat' => '',
		'minDate' => '',
		'maxDate' => '',
		'firstDay' => '',
		'noWeekends' => '',
		'defaultDate' => '',
		'showAnim' => '',
		'changeMonth' => '',
		'changeYear' => '',
		'yearRange' => '',
		'numberOfMonths' => '',
		'showButtonPanel' => '',
		'showSecond' => '',
		'showTimezone' => '',
		'controlType' => 'slider',
		'addSliderAccess' => true,
		'sliderAccessArgs' => array(
			'touchonly' => true
		),
		'hourMin' => 0,
		'hourMax' => 0,
		'stepHour' => 1,
		'minuteMin' => 0,
		'minuteMax' => 0,
		'stepMinute' => 1,
		'secondMin' => 0,
		'secondMax' => 0,
		'stepSecond' => 1
	);

	private static $regionals = array(
		'af' => 'Afrikaans',
		'sq' => 'Albanian',
		'ar-DZ' => 'Algerian Arabic',
		'ar' => 'Arabic',
		'hy' => 'Armenian',
		'az' => 'Azerbaijani',
		'eu' => 'Basque',
		'bs' => 'Bosnian',
		'bg' => 'Bulgarian',
		'ca' => 'Catalan',
		'zh-HK' => 'Chinese Hong Kong',
		'zh-CN' => 'Chinese Simplified',
		'zh-TW' => 'Chinese Traditional',
		'hr' => 'Croatian',
		'cs' => 'Czech',
		'da' => 'Danish',
		'nl-BE' => 'Dutch',
		'nl' => 'Dutch',
		'en-AU' => 'English/Australia',
		'en-NZ' => 'English/New Zealand',
		'en-GB' => 'English/UK',
		'eo' => 'Esperanto',
		'et' => 'Estonian',
		'fo' => 'Faroese',
		'fa' => 'Farsi/Persian',
		'fi' => 'Finnish',
		'fr' => 'French',
		'fr-CH' => 'French/Swiss',
		'gl' => 'Galician',
		'de' => 'German',
		'el' => 'Greek',
		'he' => 'Hebrew',
		'hu' => 'Hungarian',
		'is' => 'Icelandic',
		'id' => 'Indonesian',
		'it' => 'Italian',
		'ja' => 'Japanese',
		'kk' => 'Kazakhstan',
		'ko' => 'Korean',
		'lv' => 'Latvian',
		'lt' => 'Lithuanian',
		'lb' => 'Luxembourgish',
		'mk' => 'Macedonian',
		'ml' => 'Malayalam',
		'ms' => 'Malaysian',
		'no' => 'Norwegian',
		'pl' => 'Polish',
		'pt' => 'Portuguese',
		'pt-BR' => 'Portuguese/Brazilian',
		'rm' => 'Rhaeto-Romanic',
		'ro' => 'Romanian',
		'ru' => 'Russian',
		'sr' => 'Serbian',
		'sr-SR' => 'Serbian',
		'sk' => 'Slovak',
		'sl' => 'Slovenian',
		'es' => 'Spanish',
		'sv' => 'Swedish',
		'ta' => 'Tamil',
		'th' => 'Thai',
		'tj' => 'Tajikistan',
		'tr' => 'Turkish',
		'uk' => 'Ukranian',
		'vi' => 'Vietnamese',
		'cy-GB' => 'Welsh/UK',
	);

	public static $effects = array(
		'show',
		'blind',
		'clip',
		'drop',
		'explode',
		'fade',
		'fold',
		'highlight',
		'puff',
		'pulsate',
		'slide',
		'scale',
		'shake',
		'transfer',
	);

	function __construct($type, $name, $options = array()) {
		$this->input_name = $name;
		$this->type = in_array($type, array('date', 'time', 'datetime')) ? $type . 'picker' : 'datepicker';
		$this->options['firstDay'] = get_option('start_of_week');

		if (isset($this->options['noWeekends'])) {
			$this->noWeekends = $this->options['noWeekends'];
			unset($this->options['noWeekends']);
		}

		if (isset($this->options['minDate'])) {
			$this->minDate = $this->options['minDate'];
			unset($this->options['minDate']);
		}

		if (isset($this->options['maxDate'])) {
			$this->maxDate = $this->options['maxDate'];
			unset($this->options['maxDate']);
		}

		$this->options = wp_parse_args((array)$options, $this->options);
		$this->options = apply_filters('cf7_datepicker_options', $this->options);

		if ('' !== $this->showAnim) {
			add_action('wp_enqueue_scripts', array($this, 'enqueue_effect'));
		}
	}

	public function __set($option, $value) {
		if (isset($this->options[$option])) {
			$this->options[$option] = $value;
		}
	}

	public function __get($option) {
		return isset($this->options[$option]) ?  $this->options[$option] : null;
	}

	public function get_all() {
		return $this->options;
	}

	public function generate_code($inline = false) {
		$selector = $inline ? "$('$this->input_name')" : "$('input[name=\"{$this->input_name}\"]')";

		$out  = "{$selector}.{$this->type}({$this->options_encode()})";
		$out .= $this->regionalize();

		if ($this->noWeekends)
			$out .= ".{$this->type}('option', 'beforeShowDay', $.datepicker.noWeekends)";

		foreach ( array("min", "max") as $item ){
			if ( preg_match('/(\d{4})-(\d{2})-(\d{2})/i', $this->{$item . 'Date'}, $matches) ) {
				$matches[2] .= ' - 1';
				$this->{$item . 'Date'} = "new Date({$matches[1]}, {$matches[2]}, {$matches[3]})";
			} else {
				$this->{$item . 'Date'} = '"' . $this->{$item .'Date'} . '"';
			}

			if($this->{$item . 'Date'}){
				$out .= ".{$this->type}('option', '{$item}Date', " . $this->{$item . 'Date'} . ")";
			}
		}

		$out .= ".{$this->type}('refresh');";
		$out = apply_filters('cf7dp_datepicker_javascript', $out, $this);

		return $out;
	}

	private function options_encode() {
		$options = json_encode(array_filter(
			$this->options,
			create_function('$var', 'return ! empty($var);')
		));
		return stripslashes($options);
	}

	private function regionalize() {
		$regional = self::get_regional_match();

		$regional = apply_filters('cf7dp_datepicker_regional', $regional);

		if ($regional)
			return ".{$this->type}('option', $.datepicker.regional['{$regional}'])";

		return '';
	}

	public static function get_regional_match() {
		$locale = get_locale();
		$key_match = array(
			substr($locale, 0, 2),
			str_replace('_', '-', $locale),
		);

		$lang = '';

		if ($key_match[1] != 'en') {
			foreach ($key_match as $key) {
				if (array_key_exists($key, self::$regionals)) {
					$lang = $key;
				}
			}
		}

		return apply_filters('cf7dp_language', $lang);
	}

	public function enqueue_effect() {
		wp_enqueue_script('jquery-ui-effect-' . $this->showAnim);
	}

	public function enqueue_timepicker() {
		wp_enqueue_script('jquery-ui-timepicker');
		wp_enqueue_style('jquery-ui-timepicker');
	}

}
