<?php
/**
 * Product quantity inputs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/quantity-input.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;
 
$defaults = array(
    'max_value'   => apply_filters( 'woocommerce_quantity_input_max', '', $product ),
    'min_value'   => apply_filters( 'woocommerce_quantity_input_min', '', $product ),
    'step'        => apply_filters( 'woocommerce_quantity_input_step', '1', $product ),
);
 
if ( ! empty( $defaults['min_value'] ) )
    $min = $defaults['min_value'];
else $min = 1;
 
if ( ! empty( $defaults['max_value'] ) )
    $max = $defaults['max_value'];
else $max = 10;
 
if ( ! empty( $defaults['step'] ) )
    $step = $defaults['step'];
else $step = 1;
 
?>
<div class="quantity">
    <select name="<?php echo esc_attr( $input_name ); ?>" title="<?php _ex( 'Qty', 'Product quantity input tooltip', 'woocommerce' ) ?>" class="drop">
    <?php
    for ( $count = $min; $count <= $max; $count = $count+$step ) {
        if ( $count == $input_value )
            $selected = ' selected';
        else $selected = '';
        echo '<option value="' . $count . '"' . $selected . '>' . $count . '</option>';
    }
    ?>
    </select>
</div>

