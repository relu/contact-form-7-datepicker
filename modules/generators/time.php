<div id="wpcf7-tg-pane-time" class="hidden">
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
					<code>time-format</code><br />
					<input type="text" value="HH:mm" name="time-format" class="oneline option" />
				</td>
				<td>
					<br />
					<a href="http://trentrichardson.com/examples/timepicker/#tp-formatting" title="tp-formatting" target="_blank"><?php _e('Help'); ?></a>
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
				<td> </td>
			</tr>

			<tr>
				<td colspan="2">
					<code>animate</code><br />
					<input type="text" name="animate" class="option" style="display: none" />
					<?php self::animate_dropdown(); ?>
				</td>
			</tr>

			<tr>
				<td colspan="2">
					<label><code>inline</code> <input type="checkbox" name="inline" class="option" /></label>
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

		<div class="tg-tag"><?php echo esc_html( __( "Copy this code and paste it into the form left.", 'wpcf7' ) ); ?><br /><input type="text" name="time" class="tag" readonly="readonly" onfocus="this.select()" /></div>

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
});
</script>
