<?php
/**
 * @since 3.4
 */
class NetPay_Backend {
	public function __construct() {
		$this->initiate();
	}

	/**
	 * Class initiation.
	 *
	 * @return void
	 */
	public function initiate() {
		return;
	}

	/**
	 * @return NetPay_Capabilities  Instant.
	 */
	public function capabilities() {
		return NetPay_Capabilities::retrieve();
	}
}
