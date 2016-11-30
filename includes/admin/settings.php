<?php
/**
 * Settings
 *
 * @package     EDD\VariableDefaults\Settings
 * @since       1.0.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Register new settings in Extensions
 *
 * @since       1.0.0
 * @param       array $settings The existing settings
 * @return      array The new settings
 */
function edd_variable_defaults_settings( $settings ) {
	$new_settings = array(
		array(
			'id'   => 'edd_variable_defaults_header',
			'name' => '<strong>' . __( 'Variable Defaults', 'edd-variable-defaults' ) . '</strong>',
			'desc' => '',
			'type' => 'header'
		),
		array(
			'id'   => 'variable_defaults_table',
			'name' => __( 'Default Variable Prices', 'edd-variable-defaults' ),
			'desc' => __( 'Configure defaults', 'edd-variable-defaults' ),
			'type' => 'hook'
		)
	);

	return array_merge( $settings, $new_settings );
}
add_filter( 'edd_settings_extensions', 'edd_variable_defaults_settings' );


/**
 * Display the email table
 *
 * @since       1.0.0
 * @return      void
 */
function edd_variable_defaults_table() {
	ob_start(); ?>
	<table id="edd-variable-defaults-table" class="wp-list-table widefat fixed posts">
		<thead>
			<tr>
				<th style="width: 325%; padding-left: 10px;" scope="col"><?php _e( 'Name', 'edd-variable-defaults' ); ?></th>
				<th style="width: 325%; padding-left: 10px;" scope="col"><?php _e( 'Price', 'edd-variable-defaults' ); ?></th>
				<th style="width: 50%; padding-left: 10px;" scope="col"><?php _e( 'Order', 'edd-variable-defaults' ); ?></th>
				<th style="padding-left: 10px;" scope="col"><?php _e( 'Actions', 'edd-variable-defaults' ); ?></th>
			</tr>
		</thead>
		<?php
		$prices = get_posts(
			array(
				'posts_per_page' => 99999,
				'post_type'      => 'variable-default',
				'post_status'    => 'publish',
				'order'          => 'ASC',
				'orderby'        => 'meta_value_num',
				'meta_key'       => '_edd_variable_default_order'
			)
		);

		if ( ! empty( $prices ) ) {
			$i = 1;
			foreach ( $prices as $key => $price ) {
				$value             = get_post_meta( $price->ID, '_edd_variable_default_price', true );
				$order             = get_post_meta( $price->ID, '_edd_variable_default_order', true );
				$currency_position = edd_get_option( 'currency_position', false );

				echo '<tr' . ( $i % 2 == 0 ? ' class="alternate"' : '' ) . '>';
				echo '<td>' . esc_html( $price->post_title ) . '</td>';
				echo '<td>';
				if ( ! $currency_position || $currency_position == 'before' ) {
					echo edd_currency_filter( '' );
					echo ( isset( $value ) ? esc_attr( edd_format_amount( $value ) ) : '' );
				} else {
					echo ( isset( $value ) ? esc_attr( edd_format_amount( $value ) ) : '' );
					echo edd_currency_filter( '' );
				}
				echo '</td>';
				echo '<td style="text-align: center;">' . ( $order ? $order : '0' ) . '</td>';
				echo '<td>';
				echo '<a href="' . esc_url( admin_url( 'edit.php?post_type=download&page=edd-variable-defaults&edd-ca-action=edit-variable-price&price-id=' . $price->ID ) ) . '" class="edd-edit-variable-price" data-key="' . esc_attr( $price->ID ) . '">' . __( 'Edit', 'edd-variable-defaults' ) . '</a>&nbsp;|';
				echo '<a href="' . esc_url( wp_nonce_url( admin_url( 'edit.php?post_type=download&page=edd-variable-defaults&edd_action=delete_default_variable_price&price-id=' . $price->ID ) ) ) . '" class="edd-delete">' . __( 'Delete', 'edd-variable-defaults' ) . '</a>';
				echo '</td>';
				echo '</tr>';

				$i++;
			}
		} else {
			echo '<tr>';
			echo '<td colspan="4">' . __( 'No options defined! Click the \'Add Variable Price\' button below to add one!', 'edd-variable-defaults' ) . '</td>';
			echo '</tr>';
		}
		?>
	</table>
	<p>
		<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=download&page=edd-variable-defaults&edd-ca-action=add-variable-price' ) ); ?>" class="button-secondary" id="edd-add-conditional-email"><?php _e( 'Add Variable Price', 'edd-variable-defaults' ); ?></a>
	</p>
	<?php
	echo ob_get_clean();
}
add_action( 'edd_variable_defaults_table', 'edd_variable_defaults_table' );


/**
 * Render the add/edit screen
 *
 * @since       1.0.0
 * @return      void
 */
function edd_variable_defaults_render_edit() {
	$action   = isset( $_GET['edd-ca-action'] ) ? sanitize_text_field( $_GET['edd-ca-action'] ) : 'add-variable-price';
	$price_id = ( isset( $_GET['price-id'] ) ? absint( $_GET['price-id'] ) : false );

	// Maybe get value
	if ( $price_id ) {
		$price = get_post( $price_id );
		$title = $price->post_title;
		$value = get_post_meta( $price_id, '_edd_variable_default_price', true );
		$order = get_post_meta( $price_id, '_edd_variable_default_order', true );
	} else {
		$title = '';
		$value = false;
		$order = 0;
	}

	?>
	<div class="wrap">
		<h2><?php ( $action == 'edit-variable-price' ? _e( 'Edit Variable Price', 'edd-variable-defaults' ) : _e( 'Add Variable Price', 'edd-variable-defaults' ) ); ?> <a href="<?php echo admin_url( 'edit.php?post_type=download&page=edd-settings&tab=extensions' ); ?>" class="add-new-h2"><?php _e( 'Go Back', 'edd' ); ?></a></h2>

		<form id="edd-edit-conditional-email" action="" method="post">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row" valign="top">
							<label for="edd-variable-default-name"><?php _e( 'Name', 'edd-variable-defaults' ); ?></label>
						</th>
						<td>
							<input name="name" id="edd-variable-default-name" type="text" value="<?php echo esc_attr( $title ); ?>" style="width: 300px;" />
							<p class="description"><?php _e( 'The name of this variable item.', 'edd-variable-defaults' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="edd-variable-default-price"><?php _e( 'Price', 'edd-variable-defaults' ); ?></label>
						</th>
						<td>
							<?php
							$currency_position = edd_get_option( 'currency_position', false );
							if ( ! $currency_position || $currency_position == 'before' ) {
								echo edd_currency_filter( '' );
								echo '<input name="price" id="edd-variable-default-price" type="text" value="' . ( isset( $value ) ? esc_attr( edd_format_amount( $value ) ) : '' ) . '" style="width: 100px;" />';
							} else {
								echo '<input name="price" id="edd-variable-default-price" type="text" value="' . ( isset( $value ) ? esc_attr( edd_format_amount( $value ) ) : '' ) . '" style="width: 100px;" />';
								echo edd_currency_filter( '' );
							}
							?>
							<p class="description"><?php _e( 'The price of this variable item.', 'edd-variable-defaults' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="edd-variable-default-order"><?php _e( 'Order', 'edd-variable-defaults' ); ?></label>
						</th>
						<td>
							<input name="order" id="edd-variable-default-order" type="number" value="<?php echo absint( $order ); ?>" min="0" step="1" class="small-text" />
							<p class="description"><?php _e( 'The weight of this variable item.', 'edd-variable-defaults' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
				<input type="hidden" name="price-id" value="<?php echo ( $price_id ? $price_id : 0 ); ?>" />
				<input type="hidden" name="edd-action" value="edit_default_variable_price" />
				<input type="hidden" name="edd-variable-defaults-nonce" value="<?php echo wp_create_nonce( 'edd_variable_defaults_nonce' ); ?>" />
				<input type="submit" value="<?php echo ( $action == 'edit-variable-price' ? __( 'Edit Variable Price', 'edd-variable-defaults' ) : __( 'Add Variable Price', 'edd-variable-defaults' ) ); ?>" class="button-primary" />
			</p>
		</form>
	</div>
	<?php
}
