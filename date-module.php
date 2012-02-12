<?php

wpcf7_add_shortcode('date', 'cf7dp_date_shortcode_handler', true);
wpcf7_add_shortcode('date*', 'wpcf7_date_shortcode_handler', true);

require_once dirname(__FILE__) . '/datepicker.php';

function cf7dp_date_shortcode_handler($tag) {
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
			$dpOptions['changeMonth'] = $matches[1];
		} elseif (preg_match('%^change-year:(true|false)$%i', $option, $matches)) {
			$dpOptions['changeYear'] = $matches[1];
		} elseif (preg_match('%^year-range:([\d]+)-?([\d]+)?$%', $option, $matches)) {
			$dpOptions['yearRange'] = "{$matches[1]}:{$matches[2]}";
		}
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

	$input = '<input type="text" name="' . $name . '" value="' . esc_attr($value) . '"' . $atts . ' />';

	$html = '<span class="wpcf7-form-control-wrap ' . $name . '">' . $input . $validation_error . '</span>';

	$dp = new CF7_DatePicker($name, $dpOptions);
	$html .= $dp->generate_code();

	return $html;
}

add_filter('wpcf7_validate_date', 'cf7dp_date_validation_filter', 10, 2);
add_filter('wpcf7_validate_date*', 'cf7dp_date_validation_filter', 10, 2);

function cf7dp_date_validation_filter($result, $tag) {
	$type = $tag['type'];
	$name = $tag['name'];

	$value = trim($_POST[$name]);

	if ('date*' == $type && '' == $value) {
		$result['valid'] = false;
		$result['reason'] = wpcf7_get_message('invalid_required');
	} elseif (! strtotime($value)) {
		$result['valid'] = false;
		$result['reason'] = __('Invalid date specified');
	}

	return $result;
}

add_action( 'admin_init', 'cf7dp_add_tag_generator_date', 15 );

function cf7dp_add_tag_generator_date() {
	wpcf7_add_tag_generator( 'date', __( 'Date field', 'wpcf7' ),
		'wpcf7-tg-pane-date', 'cf7dp_tg_pane_date' );
}

function cf7dp_tg_pane_date() {
?>
<div id="wpcf7-tg-pane-date" class="hidden">
	<form action="">
		<table>
			<tr>
				<td><input type="checkbox" name="required" />&nbsp;<?php echo esc_html( __( 'Required field?', 'wpcf7' ) ); ?></td>
			</tr>
			<tr>
				<td><?php echo esc_html( __( 'Name', 'wpcf7' ) ); ?><br /><input type="text" name="name" class="tg-name oneline" /></td><td></td>
			</tr>
		</table>

		<table>
			<tr>
				<td>
					<code>id</code> (<?php echo esc_html( __( 'optional', 'wpcf7' ) ); ?>)<br />
					<input type="text" name="id" class="idvalue oneline option" />
				</td>

				<td>
					<code>class</code> (<?php echo esc_html( __( 'optional', 'wpcf7' ) ); ?>)<br />
					<input type="text" name="class" class="classvalue oneline option" />
				</td>
			</tr>

			<tr>
				<td>
					<code>size</code> (<?php echo esc_html( __( 'optional', 'wpcf7' ) ); ?>)<br />
					<input type="text" name="size" class="numeric oneline option" />
				</td>

				<td>
					<code>maxlength</code> (<?php echo esc_html( __( 'optional', 'wpcf7' ) ); ?>)<br />
					<input type="text" name="maxlength" class="numeric oneline option" />
				</td>
			</tr>

			<tr>
				<td>
					<code>date-format</code><br />
					<input type="text" value="mm/dd/yy" name="date-format" class="oneline option" />
				</td>
				<td>
					<br />
					<a href="http://docs.jquery.com/UI/Datepicker/formatDate" title="formatDate" target="_blank"><?php _e('See here for posible values'); ?></a>
				</td>
			</tr>

			<tr>
				<td>
					<code>min-date</code><br />
					<input type="text" name="min-date" class="oneline option" />
				</td>
				<td>
					<code>max-date</code><br />
					<input type="text" name="max-date" class="oneline option" />
				</td>
			</tr>

			<tr>
				<td>
					<code>first-day</code><br />
					<input type="text" name="first-day" class="option" style="display: none" />
					<select id="first-day">
						<option value="0" selected="selected"><?php _e('Sunday'); ?></option>
						<option value="1"><?php _e('Monday'); ?></option>
					</select>
				</td>
				<td>
					<code>animate</code><br />
					<input type="text" name="animate" class="option" style="display: none" />
					<?php echo cf7dp_animate_dropdown(); ?>
				</td>
			</tr>

			<tr>
				<td>
					<code>change-month</code><br />
					<input type="text" name="change-month" class="option" style="display: none" />
					<select id="change-month">
						<option value="true"><?php _e('True'); ?></option>
						<option value="false"><?php _e('False'); ?></option>
					</select>
				</td>
			</tr>

			<tr>
				<td>
					<code>change-year</code><br />
					<input type="text" name="change-year" class="option" style="display: none" />
					<select id="change-year">
						<option value="true"><?php _e('True'); ?></option>
						<option value="false" selected="selected"><?php _e('False'); ?></option>
					</select>
				</td>
				<td>
					<code>year-range</code><br />
					<input type="text" name="year-range" class="option" style="display: none"/>
					<input size="4" type="text" name="year-range-start" class="numeric" /> -
					<input size="4"type="text" name="year-range-end" class="numeric" />
				</td>
			</tr>

			<tr>
				<td>
					<?php echo esc_html( __( 'Default value', 'wpcf7' ) ); ?> (<?php echo esc_html( __( 'optional', 'wpcf7' ) ); ?>)<br /><input type="text" name="values" class="oneline" />
				</td>

				<td>
					<br /><input type="checkbox" name="watermark" class="option" />&nbsp;<?php echo esc_html( __( 'Use this text as watermark?', 'wpcf7' ) ); ?>
				</td>
			</tr>
		</table>


		<div class="tg-tag"><?php echo esc_html( __( "Copy this code and paste it into the form left.", 'wpcf7' ) ); ?><br /><input type="text" name="date" class="tag" readonly="readonly" onfocus="this.select()" /></div>

		<div class="tg-mail-tag"><?php echo esc_html( __( "And, put this code into the Mail fields below.", 'wpcf7' ) ); ?><br /><span class="arrow">&#11015;</span>&nbsp;<input type="text" class="mail-tag" readonly="readonly" onfocus="this.select()" /></div>
	</form>
	<script type="text/javascript">
	jQuery(document).ready(function($){
		$('select').change(function(){
			var val = $(this).val();

			if (! val)
				return;

			$('input[name="'+$(this).attr('id')+'"]').val(val);
		});

		$('input[name="year-range-start"], input[name="year-range-end"]').change(function(){
			var val = $('input[name="year-range-start"]').val() + '-' + $('input[name="year-range-end"]').val();

			if (! val)
				return;

			$('input[name="year-range"]').val(val);
		});
	});
	</script>
<?php
}

function cf7dp_animate_dropdown() {
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

	return $html;
}

?>
