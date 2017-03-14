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
			<select class="<?php echo esc_attr( $field_name ) ?>" id="<?php echo esc_attr( $field_name ); ?>" name="<?php echo esc_attr( $field_name ); ?>" >
				<?php if ( is_array( $field_settings['options'] ) ) : ?>
					<?php foreach ( $field_settings['options'] as $value => $label ) : ?>
						<option value="<?php echo esc_attr( $value ) ?>" <?php if ( $value == $field_value ) :  $has_selected = true; ?>selected="selected"<?php endif; ?>>
							<?php echo esc_attr( $label ) ?>
						</option>
					<?php endforeach; ?>
				<?php endif; ?>
				<?php if ( ! isset( $has_selected ) ) : ?>
					<option value="" selected="selected">-select-</option>
				<?php endif; ?>
			</select>
		</td>
	</tr>
