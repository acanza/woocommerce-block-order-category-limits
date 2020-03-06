<?php
/**
 * Plugin Name: WooCommerce Block Order by Category Limits
 * Plugin URI: https://woodemia.com/
 * Description: Permite bloquear la compra cuando el carrito supera un límite de unidades de producto por categoría.
 * Version: 1.0.0
 * Author: Woodemia
 * Author URI: https://woodemia.com
 * Text Domain: wcbocl
 */

if ( in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins' ) ) ){
	add_action( 'woocommerce_available_payment_gateways', 'block_payment_methods_by_category_limits' );
	function block_payment_methods_by_category_limits( $_available_gateways ){

		/** Aquí se define el listado de categorías de producto con sus límites de compra
		 * usando un par clave=>valor, donde la clave se corresponde con el límite de 
		 * uds. de compra y el valor con el ID de la categoría.
		 * */
		$categories_limits = array(
			14 => 11,
			18 => 15,
			10 => 13
		);
        $cart_contents = WC()->cart->get_cart_contents();
        $categories_quantities = get_items_quantity_by_category( $cart_contents );

		foreach ( $categories_quantities as $category => $quantity ) {
			if ( $amount_limit = array_search( $category, $categories_limits ) ) {
				if ( $quantity > $amount_limit ) {
					return array();
				}
			}
		}

		return $_available_gateways;
    }
    
    function get_items_quantity_by_category( $cart_contents ){
        $categories_quantities = array();

        if ( !empty( $cart_contents ) ) {
			foreach ( $cart_contents as $line_item ) {
				$product = $line_item['data'];

				foreach ( $product->get_category_ids() as $category ) {
                    if ( !isset( $categories_quantities[$category] ) ) {
                        $categories_quantities[$category] = $line_item['quantity'];
                    } else {
                        $categories_quantities[$category] += $line_item['quantity']; 
                    } 
				}
			}
        }
        
        return $categories_quantities;
    }
}