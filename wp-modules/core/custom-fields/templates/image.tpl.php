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
 * @var $file_path
 * */

?>

	<tr class="form-field">
		<th scope="row" valign="top">
			<label for="">
				<?php echo esc_html( $field_settings['label'] ); ?>
			</label>
		</th>
		<td class="settings-image">
			<input type="file" name="<?php echo esc_attr( $field_name ); ?>" />
			<input type="hidden" class="delete-settings-image" name="<?php echo esc_attr( $field_name ); ?>_delete" />
			<?php if ( $field_value ) : ?>
				<img src="<?php echo esc_attr( $file_path ) ?>" style="max-width: 400px; max-height: 400px;" /><span class="delete-settings-image-button">X</span>
			<?php endif; ?>
		</td>
	</tr>
