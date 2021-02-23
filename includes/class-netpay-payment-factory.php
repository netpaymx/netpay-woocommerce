<?php

defined( 'ABSPATH' ) || exit;

/**
 * @since 4.0
 */
class NetPay_Payment_Factory {
	/**
	 * All the available payment methods
	 * that NetPay WooCommerce supported.
	 *
	 * @var array
	 */
	public static $payment_methods = array(
		'NetPay_Payment_Installment',
		'NetPay_Payment_Creditcard',
		'NetPay_Payment_Cash',
	);

	/**
	 * @param string $id  NetPay payment method's id.
	 */
	public static function get_payment_method( $id ) {
		$methods = ( WC_Payment_Gateways::instance() )->payment_gateways();
		return isset( $methods[ $id ] ) ? $methods[ $id ] : null;
	}
}
