<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Do intval on counter here so we don't have to run it each time we use it below. Saves some function calls.
$counter = absint( $counter );
?>

<!-- Label input -->
<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo "simpay-wc-product-label-{$counter}"; ?>">
			<?php esc_html_e( 'Form Field Label', 'simple-pay' ); ?>
		</label>
	</th>
	<td>
		<?php simpay_print_field( [
			'type'        => 'standard',
			'subtype'     => 'text',
			'name'        => "_simpay_custom_field[wc-product][{$counter}][label]",
			'id'          => "simpay-wc-product-label-{$counter}",
			'value'       => isset( $field['label'] ) ? $field['label'] : '',
			'class'       => [ 'simpay-field-text', 'simpay-label-input' ],
			'attributes'  => [ 'data-field-key' => $counter	],
			'description' => simpay_form_field_label_description(),
		] ); ?>
	</td>
</tr>

<!-- Products select (multiple) -->
<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo "simpay-wc-product-products-{$counter}"; ?>">
			<?php esc_html_e( 'Products', 'swcp' ); ?>
		</label>
	</th>
	<td>
		<?php simpay_print_field( [
			'type'       => 'select',
			'name'       => "_simpay_custom_field[wc-product][{$counter}][products][]",
			'id'         => "simpay-wc-product-products-{$counter}",
			'value'      => isset( $field['products'] ) ? $field['products'] : '',
			'class'      => [ 'simpay-field-dropdown', 'simpay-field-text' ],
			'attributes' => [
				'data-field-key' => $counter,
				'multiple'       => true,
			],
			'options'     => \SWCP\get_wc_products(),
			'description' => esc_html__( 'Select the products that are available for sale in this form.', 'swcp' ),
		] ); ?>
	</td>
</tr>

<!-- Stripe metadata input -->
<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo "simpay-wc-product-metadata-{$counter}"; ?>">
			<?php esc_html_e( 'Stripe Metadata Label', 'swcp' ); ?>
		</label>
	</th>
	<td>
		<?php simpay_print_field( [
			'type'       => 'standard',
			'subtype'    => 'text',
			'name'       => "_simpay_custom_field[wc-product][{$counter}][metadata]",
			'id'         => "simpay-wc-product-metadata-{$counter}",
			'value'      => isset( $field['metadata'] ) ? $field['metadata'] : '',
			'class'      => [ 'simpay-field-text', 'simpay-label-input' ],
			'attributes' => [
				'data-field-key' => $counter,
				'maxlength'      => simpay_metadata_title_length(),
			],
			'description' => simpay_metadata_label_description(),
		] ); ?>
	</td>
</tr>

<!-- Hidden ID Field -->
<tr class="simpay-panel-field">
	<th>
		<?php esc_html_e( 'Field ID:', 'swcp' ); ?>
	</th>
	<td>
		<?php echo absint( $uid );

		simpay_print_field( [
			'type'       => 'standard',
			'subtype'    => 'hidden',
			'name'       => "_simpay_custom_field[wc-product][{$counter}][id]",
			'id'         => "simpay-wc-product-id-{$counter}",
			'value'      => isset( $field['id'] ) ? $field['id'] : '',
			'attributes' => [
				'data-field-key' => $counter,
			],
		] ); ?>
	</td>
</tr>
