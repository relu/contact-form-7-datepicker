<?php

class CF7_DatePicker {
	private $input_name;

	private $options = array(
		'dateFormat' => '',
		'minDate' => '',
		'maxDate' => '',
		'firstDay' => '',
		'defaultDate' => '',
		'showAnim' => '',
		'changeMonth' => '',
		'changeYear' => '',
		'yearRange' => '',
		'numberOfMonths' => '',
		'showButtonPanel' => '',
	);

	protected static $regionals = array(
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

	function __construct($name, $options = array()) {
		$this->input_name = $name;

		$this->options['firstDay'] = get_option('start_of_week');

		$this->options = wp_parse_args((array)$options, $this->options);
		$this->options = apply_filters('cf7_datepicker_options', $this->options);
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
		$selector = ($inline) ? "$('$this->input_name')" : "$('input[name=\"{$this->input_name}\"]')";

		$out  = "{$selector}.datepicker({$this->options_encode()})";
		$out .= self::_regionalize();

		// Remove watermark class onSelect
		$out .= ".datepicker('option', 'onSelect', function(){ $(this).removeClass('watermark'); });\n";

		$out = "jQuery(function($){ $out });";

		return "\n<script type=\"text/javascript\">{$out}</script>\n";
	}

	private function options_encode() {
		$options = json_encode(array_filter(
			$this->options,
			create_function('$var', 'return ! empty($var);')
		));

		return stripslashes($options);
	}

	private static function _regionalize() {
		$regional = self::get_regional_match();

		$regional = apply_filters('cf7dp_datepicker_regional', $regional);

		if ($regional)
			return ".datepicker('option', $.datepicker.regional['{$regional}'])";

		return '';
	}

	public static function get_regional_match() {
		$locale = get_locale();

		$key_match = array(
			substr($locale, 0, 2),
			str_replace('_', '-', $locale),
		);

		if ($key_match[1] != 'en') {
			foreach ($key_match as $key) {
				if (array_key_exists($key, self::$regionals)) {
					return $key;
				}
			}
		}

		return null;
	}
}
