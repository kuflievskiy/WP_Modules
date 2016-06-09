<?php
/**
 * Available Template Variables :
 *
 * @var mixed  $data Data comes unserialized
 * @var array  $field
 * @var array  $field_name
 * @var object $form Form generator
 * @package Setting\Templates
 * */

$data = get_option( $field_name );

if ( ( ! $data ) && isset( $field['def'] ) ) {
	$data = $field['def'];
}

$required    = ( isset( $field['rules']['required'] ) && $field['rules']['required'] ) ? 'required="required"' : '';
$max_length  = ( isset( $field['rules']['maxlength'] ) ) ? $field['rules']['maxlength'] : 250;
$min_length  = ( isset( $field['rules']['minlength'] ) ) ? $field['rules']['minlength'] : 0;
$placeholder = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
$class       = isset( $field['class'] ) ? $field['class'] : '';
$id          = isset( $field['id'] ) ? $field['id'] : '';
?>

<input id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $class ); ?>" type="<?php echo esc_attr( $field['type'] ) ?>"
	name="<?php echo esc_attr( $field_name ) ?>" value="<?php echo esc_attr( $data ); ?>" <?php echo filter_var( $required ); ?>
	maxlength="<?php echo esc_attr( $max_length ); ?>" minlength="<?php echo esc_attr( $min_length ); ?>"
	placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"/>
