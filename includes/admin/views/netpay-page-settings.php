<div class="wrap netpay">
	<style>
		.netpay-notice-testmode {
			background: #ffce00;
			color: #575D66;
			border: 1px solid #efc200;
			border-left-width: 4px;
		}
	</style>
	<h1><?php echo $title; ?></h1>

	<?php $page->display_messages(); ?>

	<?php if ( 'yes' === $settings['sandbox'] ) : ?>
		<div class="notice netpay-notice-testmode">
			<p><?php echo _e( 'Estás en MODO DE PRUEBA. No se realiza ningún pago real en este modo', 'netpay' ); ?></p>
		</div>
	<?php endif; ?>

	<h2><?php echo _e( 'Payment Settings', 'netpay' ); ?></h2>

	<p>
		<?php
		echo _e( 'All your keys can be found in NetPay Manager (login required)', 'netpay' );
		?>
	</p>

	<form method="POST">
		<!-- Section: account information TODO -->
		<?php if ( $settings['account_email']=="TODO" ) : ?>
			<hr />
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label><?php _e( 'Account status', 'netpay' ); ?></label></th>
						<td>
							<fieldset>
								Cuenta: <em><?php echo $settings['account_email']; ?> (<?php echo $settings['account_country']; ?>)</em>
							</fieldset>
						</td>
					</tr>
				</tbody>
			</table>
			<hr />
		<?php endif; ?>

		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="sandbox"><?php _e( 'Test mode', 'netpay' ); ?></label></th>
					<td>
						<fieldset>
							<label for="sandbox">
								<input name="sandbox" type="checkbox" id="sandbox" value="1" <?php echo 'yes' === $settings['sandbox'] ? 'checked="checked"' : ''; ?>>
								<?php _e( 'Enabling test mode means that all your transactions will be performed under the NetPay test account.', 'netpay' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="test_public_key"><?php _e( 'Public key for test', 'netpay' ); ?></label></th>
					<td>
						<fieldset>
							<input placeholder="pk_netpay..." name="test_public_key" type="text" id="test_public_key" value="<?php echo $settings['test_public_key']; ?>" class="regular-text" />
							<p style="font-size:12px;">Por favor coloca la llave pública de pruebas ('pk_netpay....').</p>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="test_private_key"><?php _e( 'Secret key for test', 'netpay' ); ?></label></th>
					<td>
						<fieldset>
							<input placeholder="sk_netpay..."  name="test_private_key" type="text" id="test_private_key" value="<?php echo $settings['test_private_key']; ?>" class="regular-text" />
							<p style="font-size:12px;">Por favor coloca la llave privada de pruebas ("sk_netpay....").</p>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="live_public_key"><?php _e( 'Public key for live', 'netpay' ); ?></label></th>
					<td>
						<fieldset>
							<input placeholder="pk_netpay..."  name="live_public_key" type="text" id="live_public_key" value="<?php echo $settings['live_public_key']; ?>" class="regular-text" />
							<p style="font-size:12px;">Por favor coloca la llave pública de producción ('pk_netpay....').</p>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="live_private_key"><?php _e( 'Secret key for live', 'netpay' ); ?></label></th>
					<td>
						<fieldset>
							<input placeholder="sk_netpay..."  name="live_private_key" type="text" id="live_private_key" value="<?php echo $settings['live_private_key']; ?>" class="regular-text" />
							<p style="font-size:12px;">Por favor coloca la llave privada de producción ("sk_netpay....").</p>
						</fieldset>
					</td>
				</tr>
			</tbody>
		</table>

		<hr />

		<h3><?php _e( 'Métodos de pago', 'netpay' ); ?></h3>
		<?php if ($settings['account_country']) : ?>
			<p><?php _e( 'La siguiente tabla es una lista de métodos de pago disponibles que puede habilitar en su tienda WooCommerce.', 'netpay' ); ?></p>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="sandbox"><?php _e( 'Métodos de pagos disponibles', 'netpay' ); ?></label></th>
						<td>
							<table class="widefat fixed striped" cellspacing="0">
								<thead>
									<tr>
										<?php
											$columns = array(
												'name'    => __( 'Método de pago', 'netpay' ),
												'status'  => __( 'Activo', 'netpay' ),
												'setting' => ''
											);

											foreach ( $columns as $key => $column ) {
												switch ( $key ) {
													case 'status' :
													case 'setting' :
														echo '<th style="text-align: center; padding: 10px;" class="' . esc_attr( $key ) . '">' . esc_html( $column ) . '</th>';
														break;

													default:
														echo '<th style="padding: 10px;" class="' . esc_attr( $key ) . '">' . esc_html( $column ) . '</th>';
														break;
												}

											}
										?>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach ( NetPay()->payment_methods() as $gateway ) :
										$gateway = new $gateway;
										if ( $gateway->is_country_support( $settings['account_country'] ) ) :

											echo '<tr>';

											foreach ( $columns as $key => $column ) :
												switch ( $key ) {
													case 'name' :
														$method_title = $gateway->get_title() ? $gateway->get_title() : __( '(no title)', 'netpay' );
														echo '<td class="name">
															<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=' . strtolower( $gateway->id ) ) . '">' . esc_html( $method_title ) . '</a>
														</td>';
														break;

													case 'status' :
														echo '<td class="status" style="text-align: center;">';
														echo ( 'yes' === $gateway->enabled ) ? '<span>' . __( 'Si', 'netpay' ) . '</span>' : '-';
														echo '</td>';
														break;

													case 'setting' :
														echo '<td class="setting" style="text-align: center;">
															<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=' . strtolower( $gateway->id ) ) . '">' . __( 'configurar', 'netpay' ) . '</a>
														</td>';
														break;
												}
											endforeach;

											echo '</tr>';

										endif;
									endforeach;
									?>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		<?php else: ?>
			<p><?php _e( 'Please set up your NetPay account to see all the available payment methods.', 'netpay' ); ?></p>
		<?php endif; ?>

		<input type="hidden" name="netpay_setting_page_nonce" value="<?= wp_create_nonce( 'netpay-setting' ); ?>" />
		<?php submit_button( __( 'Guardar', 'netpay' ) ); ?>

	</form>
</div>
