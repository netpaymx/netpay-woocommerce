<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../class-netpay-unit-test.php';

class NetPay_Localization_Test extends TestCase {
	public static function setUpBeforeClass(): void {
		global $l10n;
		$l10n[] = 'netpay';

		require_once __DIR__ . '/../../../includes/class-netpay-localization.php';
	}

	/**
	 * @test
	 * Note that because the actualy translation is happening.
	 */
	public function translate_message() {
		$message = 'currency is currently not supported';
		$this->assertEquals( $message, NetPay_Localization::translate( $message ) );
	}

	/**
	 * @test
	 */
	public function translate_multi_sentences_message() {
		$message = 'name cannot be blank, email is in invalid format, and phone_number must contain 10-11 digit characters';
		$this->assertEquals( $message, NetPay_Localization::translate( $message ) );
	}

	/**
	 * @test
	 */
	public function translate_unknown_message() {
		$message = 'unrecognized message';
		$this->assertEquals( $message, NetPay_Localization::translate( $message ) );
	}
}
