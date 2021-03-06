<?php

defined( 'ABSPATH' ) || exit;

/**
 * @since 4.0
 */
class NetPay_Event extends NetPay_Queueable {
	/**
	 * @var array  of NetPay event's payload.
	 */
	protected $data;

	/**
	 * @var \WC_Abstract_Order
	 */
	protected $order;

	public function __construct( $data ) {
		$this->data = $data;
	}
	
	/**
	 * @return boolean
	 */
	public function validate() {
		return true;
	}

	/**
	 * @return bool
	 */
	public function resolve() {
		return true;
	}

	/**
	 * @return array  of NetPay event's payload.
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * @return \WC_Abstract_Order
	 */
	public function get_order() {
		return $this->order;
	}
}
