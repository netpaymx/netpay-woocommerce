<?php
defined( 'ABSPATH' ) or die( 'No direct script access allowed.' );

if ( class_exists( 'NetPay_Events' ) ) {
	return;
}

class NetPay_Events {
	/**
	 * @var array  of event classes that we can handle.
	 */
	protected $events = array();

	/**
	 * All the available event handler classes
	 * that NetPay WooCommerce supported.
	 *
	 * @var array
	 */
	public static $event_classes = array(
		'NetPay_Event_Charge_Capture',
		'NetPay_Event_Charge_Complete',
		'NetPay_Event_Charge_Create'
	);

	public function __construct() {
		foreach ( self::$event_classes as $event ) {
			$this->events[ $event::EVENT_NAME ] = $event;
		}
	}

	/**
	 * Note. It doesn't return anything back because nobody using the result
	 * unless we have a 'log' system.
	 *
	 * @param  string $event_key
	 * @param  mixed  $data
	 *
	 * @return void
	 */
	public function handle( $event_key, $data ) {
		if ( ! isset( $this->events[ $event_key ] ) ) {
			return;
		}

		$event_hook_name = str_replace( '.', '_', $event_key );

		/**
		 * Hook before NetPay handle an event from webhook.
		 *
		 * @param mixed $data  a data of an event object
		 */
		do_action( 'netpay_before_handle_event_' . $event_hook_name, $data );

		$event = new $this->events[ $event_key ]( $data );
		if ( $event->validate() ) {
			$result = $event->resolve();

			/**
			 * Hook before NetPay handle an event from webhook.
			 *
			 * @param WC_Order $order  an order object.
			 * @param mixed    $data   a data of an event object
			 */
			do_action( 'netpay_handled_event_' . $event_hook_name, $event->get_order(), $event->get_data() );
		}

		/**
		 * Hook after NetPay handle an event from webhook.
		 *
		 * @param WC_Order $order  an order object.
		 * @param mixed    $data   a data of an event object
		 * @param mixed    $result  a result of an event handler
		 */
		do_action( 'netpay_after_handle_event_' . $event_hook_name, $event->get_order(), $event->get_data(), $result );

		return $result;
	}
}
