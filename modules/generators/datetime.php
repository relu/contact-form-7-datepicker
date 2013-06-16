<div id="wpcf7-tg-pane-datetime" class="hidden">
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
					<label><code>id</code> (<?php echo esc_html( __( 'optional', 'wpcf7' ) ); ?>)<br />
					<input type="text" name="id" class="idvalue oneline option" /></label>
				</td>

				<td>
					<label><code>class</code> (<?php echo esc_html( __( 'optional', 'wpcf7' ) ); ?>)
					<input type="text" name="class" class="classvalue oneline option" /></label>
				</td>
			</tr>

			<tr>
				<td>
					<label><code>size</code> (<?php echo esc_html( __( 'optional', 'wpcf7' ) ); ?>)
					<input type="text" name="size" class="numeric oneline option" /></label>
				</td>

				<td>
					<label><code>maxlength</code> (<?php echo esc_html( __( 'optional', 'wpcf7' ) ); ?>)
					<input type="text" name="maxlength" class="numeric oneline option" /><label>
				</td>
			</tr>

			<tr>
				<td>
					<label><code>date-format</code>
					<input type="text" value="mm/dd/yy" name="date-format" class="oneline option" /><label>
				</td>
				<td>
					<br />
					<a href="http://docs.jquery.com/UI/Datepicker/formatDate" title="formatDate" target="_blank"><?php _e('Help'); ?></a>
				</td>
			</tr>

			<tr>
				<td>
					<label><code>time-format</code>
					<input type="text" value="HH:mm" name="time-format" class="oneline option" /><label>
				</td>
				<td>
					<br />
					<a href="http://trentrichardson.com/examples/timepicker/#tp-formatting" title="tp-formatting" target="_blank"><?php _e('Help'); ?></a>
				</td>
			</tr>

			<tr>
				<td>
					<label><code>min-date</code>
					<input type="text" name="min-date" class="oneline option" /><label>
				</td>
				<td>
					<label><code>max-date</code>
					<input type="text" name="max-date" class="oneline option" /><label>
				</td>
			</tr>

			<tr>
				<td>
					<label><code>min-hour</code>
					<input type="text" name="min-hour" class="oneline option" /></label>
				</td>
				<td>
					<label><code>max-hour</code>
					<input type="text" name="max-hour" class="oneline option" /></label>
				</td>
			</tr>

			<tr>
				<td>
					<label><code>step-hour</code>
					<input type="text" name="step-hour" class="oneline option" /></label>
				</td>
				<td> </td>
			</tr>

			<tr>
				<td>
					<label><code>min-minute</code>
					<input type="text" name="min-minute" class="oneline option" /></label>
				</td>
				<td>
					<label><code>max-minute</code>
					<input type="text" name="max-minute" class="oneline option" /></label>
				</td>
			</tr>

			<tr>
				<td>
					<label><code>step-minute</code>
					<input type="text" name="step-minute" class="oneline option" /></label>
				</td>
				<td> </td>
			</tr>

			<tr>
				<td>
					<label><code>min-second</code>
					<input type="text" name="min-second" class="oneline option" /></label>
				</td>
				<td>
					<label><code>max-second</code>
					<input type="text" name="max-second" class="oneline option" /></label>
				</td>
			</tr>

			<tr>
				<td>
					<label><code>step-second</code>
					<input type="text" name="step-second" class="oneline option" /></label>
				</td>
			</tr>

			<tr>
				<td>
					<code>first-day</code><br />
					<input type="checkbox" id="first-day-sunday" name="first-day:0" class="option exclusive" />
					<label for="first-day-sunday"><?php _e('Sunday'); ?></label>
					<input type="checkbox" value="1" id="first-day-monday" name="first-day:1" class="option exclusive" />
					<label for="first-day-monday"><?php _e('Monday'); ?></label>
				</td>
				<td>
					<code>animate</code>
					<input type="text" name="animate" class="option" style="display: none" />
					<?php self::animate_dropdown(); ?>
				</td>
			</tr>

			<tr>
				<td>
					<label><code>change-month</code> <input type="checkbox" name="change-month" id="change-month" class="option" /></label>
				</td>
				<td>
					<label><code>change-year</code> <input type="checkbox" name="change-year" id="change-year" class="option" /></label>
				</td>
			</tr>

			<tr>
				<td colspan="2">
					<code>year-range</code>
					<input type="text" id="year-range" name="year-range" class="option" style="display: none;" />
					<input size="4" type="text" name="year-range-start" class="year-range numeric" /> -
					<input size="4"type="text" name="year-range-end" class="year-range numeric" />
				</td>
			</tr>

			<tr>
				<td>
					<label><code>months</code>
					<input type="text" size="2" name="months" class="option numeric"/></label>
				</td>
				<td>
					<label><code>buttons</code> <input type="checkbox" name="buttons" class="option" /></label>
				</td>
			</tr>

			<tr>
				<td>
					<label><code>inline</code> <input type="checkbox" name="inline" class="option" /></label>
				</td>
				<td>
					<label><code>no-weekends</code> <input type="checkbox" name="no-weekends" class="option" /></label>
				</td>
			</tr>

			<tr>
				<td>
					<?php echo esc_html( __( 'Default value', 'wpcf7' ) ); ?> (<?php echo esc_html( __( 'optional', 'wpcf7' ) ); ?>)<br /><input type="text" name="values" class="oneline" />
				</td>

				<td>
					<br /><input type="checkbox" name="placeholder" class="option" />&nbsp;<?php echo esc_html( __( 'Use this text as placeholder?', 'wpcf7' ) ); ?>
				</td>
			</tr>
		</table>

		<div class="tg-tag"><?php echo esc_html( __( "Copy this code and paste it into the form left.", 'wpcf7' ) ); ?><br /><input type="text" name="datetime" class="tag" readonly="readonly" onfocus="this.select()" /></div>

		<div class="tg-mail-tag"><?php echo esc_html( __( "And, put this code into the Mail fields below.", 'wpcf7' ) ); ?><br /><span class="arrow">&#11015;</span>&nbsp;<input type="text" class="mail-tag" readonly="readonly" onfocus="this.select()" /></div>
	</form>
</div>

<script type="text/javascript">
jQuery(function($){
	$(document).on('change', 'select', function(){
		var $this = $(this),
			value = $this.val();

		if (! value)
			return;

		$('input[name="'+$this.attr('id')+'"]').val(value).trigger('change');
	});

	$(document).on('keyup', '.year-range', function(){
		var value = $('input[name="year-range-start"]').val() + '-' + $('input[name="year-range-end"]').val();

		if (! value)
			return;

		$('#year-range').val(value);
	});
});
</script>
