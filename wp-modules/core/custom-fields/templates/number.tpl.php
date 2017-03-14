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

$min = (isset( $field_settings['min'] ) and null !== $field_settings['min']) ? $field_settings['min'] : '';
$max = (isset( $field_settings['max'] ) and null !== $field_settings['max']) ? $field_settings['max'] : '';
?>

	<tr class="form-field">
		<th scope="row" valign="top">
			<label for="">
				<?php echo esc_html( $field_settings['label'] ); ?>
			</label>
		</th>
		<td>
			<input type="number" name="<?php echo esc_attr( $field_name ); ?>"
                   step="<?php echo esc_attr( $field_settings['step'] ? $field_settings['step'] : 1 ); ?>"
				   min="<?php echo esc_attr( $min ); ?>"
				   max="<?php echo esc_attr( $max ); ?>"
				   value="<?php echo esc_attr( $field_value ); ?>"/>
		</td>
	</tr>
