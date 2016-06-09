<?php
/**
 * Available Template Variables :
 *
 * @var mixed   $data Data comes unserialized
 * @var array   $field
 * @var string  $field_name
 * @package Setting\Templates
 * */

$data = get_option( $field_name );

$class = isset( $field['class'] ) ?  $field['class'] : '';
$selected = isset( $data ) ?  $data : '';


$depth = isset( $field['depth'] ) ?  $field['depth'] : 0;
$child_of = isset( $field['child_of'] ) ?  $field['child_of'] : 0;
$echo = isset( $field['echo'] ) ?  $field['echo'] : 1;
$id = isset( $field['id'] ) ?  $field['id'] : '';
$show_option_none = isset( $field['show_option_none'] ) ?  $field['show_option_none'] : 'No value';
$show_option_no_change = isset( $field['show_option_no_change'] ) ?  $field['show_option_no_change'] : '- Select -';
$option_none_value = isset( $field['option_none_value'] ) ?  $field['option_none_value'] : '0';

$args = array(
	'depth'                 => $depth,
	'child_of'              => $child_of,
	'selected'              => $selected,
	'echo'                  => $echo,
	'name'                  => $field_name,
	'id'                    => $id,                    // String.
	'show_option_none'      => $show_option_none,      // String.
	'show_option_no_change' => $show_option_no_change, // String.
	'option_none_value'     => $option_none_value,     // String.
);
?>

<div class="<?php echo esc_attr( $class ) ?>">
	<?php wp_dropdown_pages( filter_var( $args, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) ); ?>
</div>
