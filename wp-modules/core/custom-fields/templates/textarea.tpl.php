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
        <textarea name="<?php echo esc_attr( $field_name ); ?>" ><?php echo esc_attr( $field_value ); ?></textarea>
	</td>
</tr>
