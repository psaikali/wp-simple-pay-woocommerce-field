<?php
/**
 * Plugin Name: WP Simple Pay WooCommerce Product Field
 * Description: Display a dropdown in a WP Simple Pay form, with a list of WooCommerce products.
 * Author: Pierre Saïkali
 * Author URI: https://saika.li
 * Text Domain: simpay-wc-product
 * Domain Path: /languages/
 * Version: 1.0.0
 */

namespace SWCP;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//=================================================================================
//                                                                                 
//   ####   #####   ##     ##   ####  ######    ###    ##     ##  ######   ####  
//  ##     ##   ##  ####   ##  ##       ##     ## ##   ####   ##    ##    ##     
//  ##     ##   ##  ##  ## ##   ###     ##    ##   ##  ##  ## ##    ##     ###   
//  ##     ##   ##  ##    ###     ##    ##    #######  ##    ###    ##       ##  
//   ####   #####   ##     ##  ####     ##    ##   ##  ##     ##    ##    ####   
//                                                                                 
//=================================================================================

define( 'SWCP_VERSION', '1.0.0' );
define( 'SWCP_URL', plugin_dir_url( __FILE__ ) );
define( 'SWCP_DIR', plugin_dir_path( __FILE__ ) );
define( 'SWCP_PLUGIN_DIRNAME', basename( rtrim( dirname( __FILE__ ), '/' ) ) );
define( 'SWCP_BASENAME', plugin_basename( __FILE__ ) );

//==============================================================
//                                                              
//  #####     ###     ####  ##  ##  #####  ##     ##  ####    
//  ##  ##   ## ##   ##     ## ##   ##     ####   ##  ##  ##  
//  #####   ##   ##  ##     ####    #####  ##  ## ##  ##  ##  
//  ##  ##  #######  ##     ## ##   ##     ##    ###  ##  ##  
//  #####   ##   ##   ####  ##  ##  #####  ##     ##  ####    
//                                                              
//==============================================================

/**
 * Register a new Custom Form Fields group
 *
 * @param array $groups
 * @return array
 */
function register_woocommerce_fields_group( $groups ) {
	$groups['woocommerce'] = _x( 'WooCommerce', 'custom field group', 'simpay-wc-product' );
	return $groups;
}
add_filter( 'simpay_custom_field_group_labels', __NAMESPACE__ . '\\register_woocommerce_fields_group' );


/**
 * Register our new Product Dropdown form field type
 *
 * @param array $fields
 * @return array $fields
 */
function register_wc_product_dropdown_field( $fields ) {
	$fields['wc-product'] = [
		'label'      => esc_html__( 'Product Dropdown', 'simpay-wc-product' ),
		'type'       => 'wc-product',
		'category'   => 'woocommerce',
		'active'     => true,
		'repeatable' => true,
	];

	return $fields;
}
add_filter( 'simpay_custom_field_options', __NAMESPACE__ . '\register_wc_product_dropdown_field' );

/**
 * Display the new field type admin settings
 *
 * @param string $template
 * @return string $template
 */
function display_wc_product_dropdown_settings( $template ) {
	$template = SWCP_DIR . '/views/field-settings.php';

	return $template;
}
add_filter( 'simpay_admin_wc-product_field_template', __NAMESPACE__ . '\display_wc_product_dropdown_settings' );

/**
 * Populate select dropdown with WC producs (id => name).
 *
 * @return array
 */
function get_wc_products() {
	$wc_products      = wc_get_products( [ 'posts_per_page' => 99 ] );
	$products_options = [];

	foreach ( $wc_products as $product ) {
		$products_options[ $product->get_id() ] = esc_html( $product->get_name() );
	}

	return $products_options;
}

//==========================================================================
//                                                                          
//  #####  #####     #####   ##     ##  ######  #####  ##     ##  ####    
//  ##     ##  ##   ##   ##  ####   ##    ##    ##     ####   ##  ##  ##  
//  #####  #####    ##   ##  ##  ## ##    ##    #####  ##  ## ##  ##  ##  
//  ##     ##  ##   ##   ##  ##    ###    ##    ##     ##    ###  ##  ##  
//  ##     ##   ##   #####   ##     ##    ##    #####  ##     ##  ####    
//                                                                          
//==========================================================================

/**
 * Output our custom field HTML in the form
 *
 * @param string $html
 * @param object $form
 * @return string $html
 */
function display_addon_checkbox_frontend_html( $html, $field, $form ) {
	if ( 'wc-product' === $field['type'] ) {
		$html .= output_wc_product_field_html( $field );
	}

	return $html;
}
add_filter( 'simpay_custom_field_html_for_non_native_fields', __NAMESPACE__ . '\display_addon_checkbox_frontend_html', 20, 3 );

/**
 * Helper function to output our custom field HTML
 *
 * @param array $item The fields settings.
 * @return string $html
 */
function output_wc_product_field_html( $item ) {
	$html      = '';
	$id        = isset( $item['id'] ) ? $item['id'] : '';
	$meta_name = isset( $item['metadata'] ) && ! empty( $item['metadata'] ) ? $item['metadata'] : $id;
	$label     = isset( $item['label'] ) ? $item['label'] : '';
	$name      = 'simpay_field[' . esc_attr( $meta_name ) . ']';

	if ( ! is_array( $item['products'] ) || empty( $item['products'] ) ) {
		return sprintf(
			'<div><p>%1$s</p></div>',
			esc_html__( 'You need to select at least one product in the form settings.', 'simpay-wc-product' )
		);
	}

	$id = simpay_dashify( $id );

	$label = '<p><label for="' . esc_attr( simpay_dashify( $id ) ) . '">' . $label . '</label></p>';
	$field = '<select name="' . $name . '" id="' . esc_attr( $id ) . '" class="simpay-amount-dropdown">';

	foreach ( $item['products'] as $product_id ) {
		$product = wc_get_product( (int) $product_id );

		if ( $product ) {
			$field .= sprintf(
				'<option value="%1$d" data-amount="%4$s">%2$s (%3$s)</option>',
				(int) $product->get_id(),
				esc_html( $product->get_name() ),
				$product->get_price_html(),
				$product->get_price()
			);
		}
	}

	$field .= '</select>';

	$html .= '<div class="simpay-form-control simpay-wc-product-container">';
	$html .= '<div class="simpay-checkbox-wrap simpay-field-wrap">';
	$html .= $label . $field;
	$html .= '</div>';
	$html .= '</div>';

	return $html;
}
