<?php
/**
 * Template file
 *
 * @package App
 * @subpackage Custom_Fields
 *
 * @var $field_settings
 * @var $field_name
 * @var $field_value
 * */
?>
<tr class="form-field">
	<th scope="row" valign="top">
		<label for="">
			<?php echo esc_html( $field_settings['label'] ); ?>
		</label>
	</th>
	<td>
		<input type="text" name="<?php echo esc_attr( $field_name ); ?>"
			   value="<?php echo esc_attr( $field_value ); ?>"/>
	</td>
</tr>
