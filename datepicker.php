<?php

class CF7_DatePicker {
	private $input_id;

	private $options = array(
		'dateFormat' => '',
		'minDate' => '',
		'maxDate' => '',
		'firstDay' => '',
		'defaultDate' => '',
		'showAnim' => 'show',
		'changeMonth' => '',
		'changeYear' => '',
		'yearRange' => '',
	);

	private static $locales = array(
		'af' =>'Afrikaans',
		'sq' =>'Albanian',
		'ar-DZ' =>'Algerian Arabic',
		'ar' =>'Arabic',
		'hy' =>'Armenian',
		'az' =>'Azerbaijani',
		'eu' =>'Basque',
		'bs' =>'Bosnian',
		'bg' =>'Bulgarian',
		'ca' =>'Catalan',
		'zh-HK' =>'Chinese Hong Kong',
		'zh-CN' =>'Chinese Simplified',
		'zh-TW' =>'Chinese Traditional',
		'hr' =>'Croatian',
		'cs' =>'Czech',
		'da' =>'Danish',
		'nl-BE' =>'Dutch',
		'nl' =>'Dutch',
		'en-AU' =>'English/Australia',
		'en-NZ' =>'English/New Zealand',
		'en-GB' =>'English/UK',
		'eo' =>'Esperanto',
		'et' =>'Estonian',
		'fo' =>'Faroese',
		'fa' =>'Farsi/Persian',
		'fi' =>'Finnish',
		'fr' =>'French',
		'fr-CH' =>'French/Swiss',
		'gl' =>'Galician',
		'de' =>'German',
		'el' =>'Greek',
		'he' =>'Hebrew',
		'hu' =>'Hungarian',
		'is' =>'Icelandic',
		'id' =>'Indonesian',
		'it' =>'Italian',
		'ja' =>'Japanese',
		'kk' =>'Kazakhstan',
		'ko' =>'Korean',
		'lv' =>'Latvian',
		'lt' =>'Lithuanian',
		'lb' =>'Luxembourgish',
		'mk' =>'Macedonian',
		'ml' =>'Malayalam',
		'ms' =>'Malaysian',
		'no' =>'Norwegian',
		'pl' =>'Polish',
		'pt' =>'Portuguese',
		'pt-BR' =>'Portuguese/Brazilian',
		'rm' =>'Rhaeto-Romanic',
		'ro' =>'Romanian',
		'ru' =>'Russian',
		'sr' =>'Serbian',
		'sr-SR' =>'Serbian',
		'sk' =>'Slovak',
		'sl' =>'Slovenian',
		'es' =>'Spanish',
		'sv' =>'Swedish',
		'ta' =>'Tamil',
		'th' =>'Thai',
		'tj' =>'Tajikistan',
		'tr' =>'Turkish',
		'uk' =>'Ukranian',
		'vi' =>'Vietnamese',
		'cy-GB' =>'Welsh/UK',
	);

	function __construct($name, $options = null) {
		$this->input_name = $name;

		$this->options['firstDay'] = get_option('start_of_week');

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

	public function generate_code($inline = false) {
		if ($inline)
			$selector = "$('#{$this->input_name}')";
		else
			$selector = "$('input[name=\"{$this->input_name}\"]')";

		$out  = "{$selector}.datepicker({$this->_options_encode()});\n";
		$out .= self::_regionalize($selector);

		$out = self::_js_wrap($out);

		return $out;
	}

	private static function _regionalize($selector) {
		$locale = get_locale();

		$key_match = array(
			substr($locale, 0, 2),
			str_replace('_', '-', $locale),
		);

		if ($key_match[1] == 'en')
			return '';
		else
			foreach ($key_match as $key)
				if (array_key_exists($key, self::$locales))
					return "{$selector}.datepicker('option', $.datepicker.regional.{$key});";

		return '';
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
		$out .= "\tjQuery(function($){\n";
		$out .= "\t\t{$code}\n";
		$out .= "\t});\n";
		$out .= "</script>\n";

		return $out;
	}
}

?>
