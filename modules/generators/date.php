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
					<a href="http://docs.jquery.com/UI/Datepicker/formatDate" title="formatDate" target="_blank"><?php _e('Help'); ?></a>
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
					<input type="checkbox" id="first-day-sunday" name="first-day:0" class="option exclusive" />
					<label for="first-day-sunday"><?php _e('Sunday'); ?></label>
					&nbsp;&nbsp;

					<input type="checkbox" value="1" id="first-day-monday" name="first-day:1" class="option exclusive" />
					<label for="first-day-monday"><?php _e('Monday'); ?></label>
				</td>
				<td>
					<code>animate</code><br />
					<input type="text" name="animate" class="option" style="display: none" />
					<?php self::animate_dropdown(); ?>
				</td>
			</tr>

			<tr>
				<td>
					<label><code>change-month</code> <input type="checkbox" name="change-month" id="change-month" class="option" /></label>
				</td>
			</tr>

			<tr>
				<td>
					<label><code>change-year</code> <input type="checkbox" name="change-year" id="change-year" class="option" /></label>
				</td>
				<td>
					<code>year-range</code><br />
					<input type="text" id="year-range" name="year-range" class="option" style="display: none;" />
					<input size="4" type="text" name="year-range-start" class="year-range numeric" /> -
					<input size="4"type="text" name="year-range-end" class="year-range numeric" />
				</td>
			</tr>

			<tr>
				<td>
					<code>months</code><br />
					<input type="text" size="2" name="months" class="option numeric"/>
				</td>
				<td>
					<label><code>buttons</code> <input type="checkbox" name="buttons" class="option" /></label>
				</td>
			</tr>

			<tr>
				<td>
					<label><code>inline</code> <input type="checkbox" name="inline" class="option" /></label>
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
