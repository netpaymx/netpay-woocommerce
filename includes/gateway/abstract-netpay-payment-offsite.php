<?php
defined( 'ABSPATH' ) or die( 'No direct script access allowed.' );

require_once dirname( __FILE__ ) . '/class-netpay-payment.php';

/**
 * @since 3.10
 */
abstract class NetPay_Payment_Offsite extends NetPay_Payment {
	/**
	 * @inheritdoc
	 */
	public function result( $order_id, $order, $charge ) {
		if ( self::STATUS_FAILED === $charge['status'] ) {
			return $this->payment_failed( NetPay()->translate( $charge['failure_message'] ) . ' (code: ' . $charge['failure_code'] . ')' );
		}

		if ( self::STATUS_PENDING === $charge['status'] ) {
			$order->add_order_note( sprintf( __( 'NetPay: Redirecting buyer to %s', 'netpay' ), esc_url( $charge['authorize_uri'] ) ) );

			return array (
				'result'   => 'success',
				'redirect' => $charge['authorize_uri'],
			);
		}

		return $this->payment_failed(
			sprintf(
				__( 'Please feel free to try submitting your order again, or contact our support team if you have any questions (Your temporary order id is \'%s\')', 'netpay' ),
				$order_id
			)
		);
	}
}
