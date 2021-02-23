<?php

defined( 'ABSPATH' ) || exit;

/**
 * @since 4.0
 */
class NetPay_Callback {
	/**
	 * @var \WC_Abstract_Order
	 */
	protected $order;

	/**
	 * @var \NetPayCharge
	 */
	protected $charge;

	/**
	 * @param \WC_Abstract_Order $order
	 */
	public function __construct( $order ) {
		$this->order = $order;
		if ( ! $this->order || ! $this->order instanceof WC_Abstract_Order ) $this->invalid_result();
	}

	public static function execute() {
		$order_id = isset( $_GET['order_id'] ) ? sanitize_text_field( $_GET['order_id'] ) : null;

		$callback = new self( wc_get_order( $order_id ) );
		$callback->validate();
	}

	public function validate() {
		$this->order->add_order_note( __( 'NETPAY: Validating the payment result...', 'netpay' ) );

		try {
			$this->charge = NetPayCharge::retrieve( $this->order->get_transaction_id() );

			switch ( strtolower( $this->charge['status'] ) ) {
				case 'successful':
				case 'failed':
				case 'pending':
					$resolving_method = strtolower( 'payment_' . $this->charge['status'] );
					$this->$resolving_method();
					break;

				default:
					throw new Exception( __( 'Unrecognized NetPay Charge status.', 'netpay' ) );
					break;
			}
		} catch ( Exception $e ) {
			$this->order->add_order_note(
				sprintf(
					wp_kses( __( 'NETPAY: Unable to validate the result.<br/>%s', 'netpay' ), array( 'br' => array() ) ),
					$e->getMessage()
				)
			);

			$this->invalid_result();
		}
	}

	/**
	 * Resolving a case of undefined charge status
	 */
	protected function invalid_result() {
		$message = __(
			'<strong>We cannot validate your payment result:</strong><br/>
			 Note that your payment may have already been processed.<br/>
			 Please contact our support team if you have any questions.',
			'netpay'
		);

		wc_add_notice( wp_kses( $message, array( 'br' => array(), 'strong' => array() ) ), 'error' );
		wp_redirect( wc_get_checkout_url() );
		exit;
	}

	/**
	 * Resolving a case of charge status: successful.
	 */
	protected function payment_successful() {
		$message = __( 'NETPAY: Pago realizado.<br/>por la cantidad de %1$s %2$s ', 'netpay' );

		$this->order->payment_complete();
		$this->order->add_order_note(
			sprintf(
				wp_kses( $message, array( 'br' => array() ) ),
				$this->order->get_total(),
				$this->order->get_currency()
			)
		);

		WC()->cart->empty_cart();
		$this->order->update_meta_data( 'is_netpay_payment_resolved', 'yes' );
		$this->order->save();

		wp_redirect( $this->order->get_checkout_order_received_url() );
		exit;
	}

	/**
	 * Resolving a case of charge status: pending.
	 */
	protected function payment_pending() {
		if ( ! $this->charge['capture'] && $this->charge['authorized'] ) {
			// Card authorized case.
			$message = __(
				'NetPay: El pago está siendo procesado<br/>
				 por la cantidad %1$s %2$s .',
				'netpay'
			);

			$this->order->add_order_note(
				sprintf(
					wp_kses( $message, array( 'br' => array() ) ),
					$this->order->get_total(),
					$this->order->get_currency()
				)
			);
			$this->order->payment_complete();

			// Remove cart
			//WC()->cart->empty_cart(); //TODO
			$this->order->update_meta_data( 'is_netpay_payment_resolved', 'yes' );
			$this->order->save();

			wp_redirect( $this->order->get_checkout_order_received_url() );
			exit;
		}

		// Offsite case.
		$message = __(
			'NetPay: The payment is being processed.<br/>
			 Depending on the payment provider, this may take some time to process.<br/>
			 Please do a manual \'Sync Payment Status\' action from the <strong>Order Actions</strong> panel, or check the payment status directly at the NetPay Dashboard later.',
			'netpay'
		);

		$this->order->add_order_note( wp_kses( $message, array( 'br' => array(), 'strong' => array() ) ) );
		$this->order->update_status( 'on-hold' );
		$this->order->update_meta_data( 'is_netpay_payment_resolved', 'yes' );
		$this->order->save();

		wp_redirect( $this->order->get_checkout_order_received_url() );
		exit;
	}

	/**
	 * Resolving a case of charge status: failed.
	 */
	protected function payment_failed() {
		$message         = __( 'Parece que no hemos podido procesar su pago correctamente:<br/>%s', 'netpay' );
		$failure_message = NetPay()->translate( $this->charge['failure_message'] ) . ' (code: ' . $this->charge['failure_code'] . ')';

		$this->order->add_order_note( sprintf( wp_kses( __( 'NETPAY: Payment failed.<br/>%s', 'netpay' ), array( 'br' => array() ) ), $failure_message ) );
		$this->order->update_status( 'failed' );
		$this->order->update_meta_data( 'is_netpay_payment_resolved', 'yes' );
		$this->order->save();

		wc_add_notice( sprintf( wp_kses( $message, array( 'br' => array() ) ), $failure_message ), 'error' );
		wp_redirect( wc_get_checkout_url() );
		exit;
	}
}
