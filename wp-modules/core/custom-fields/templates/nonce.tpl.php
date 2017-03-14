<?php
/**
 * Template file
 *
 * @package    App
 * @subpackage Custom_Fields
 * */

?>

<tr class="form-field">
	<th></th>
	<td>
		<input type="hidden" name="<?php echo esc_attr( 'custom_fields_nonce' ); ?>"
		       value="<?php echo esc_attr( wp_create_nonce( 'custom_fields_nonce' ) ); ?>"/>
	</td>
</tr>

