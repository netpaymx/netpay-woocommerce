<?php

defined( 'ABSPATH' ) || exit;

/**
 * Handles all the API response messages localization.
 *
 * @since 4.1
 */
class NetPay_Localization {
	/**
	 * @param  string $message
	 * @return string
	 */
	public static function translate( $message ) {
		$known_messages = array(
			'amount must be at least 200'
				=> __( 'amount must be at least 200', 'netpay' ),

			'amount must be less than 50000'
				=> __( 'amount must be less than 50000', 'netpay' ),

			'amount must be greater than or equal to 200'
				=> __( 'amount must be greater than or equal to 200', 'netpay' ),

			'amount must be greater than or equal to 200 and phone_number must contain 10-11 digit characters'
				=> __( 'amount must be greater than or equal to 200 and phone_number must contain 10-11 digit characters', 'netpay' ),

			'card is stolen or lost'
				=> __( 'card is stolen or lost', 'netpay' ),

			'currency is currently not supported'
				=> __( 'currency is currently not supported', 'netpay' ),

			'email is in invalid format'
				=> __( 'email is in invalid format', 'netpay' ),

			'failed fraud check'
				=> __( 'failed fraud check', 'netpay' ),

			'failed processing'
				=> __( 'failed processing', 'netpay' ),

			'insufficient funds in the account or the card has reached the credit limit'
				=> __( 'insufficient funds in the account or the card has reached the credit limit', 'netpay' ),

			'Metadata should be a JSON hash'
				=> __( 'Metadata should be a JSON hash', 'netpay' ),

			'name cannot be blank'
				=> __( 'name cannot be blank', 'netpay' ),

			'name cannot be blank, email is in invalid format, and phone_number must contain 10-11 digit characters'
				=> __( 'name cannot be blank, email is in invalid format, and phone_number must contain 10-11 digit characters', 'netpay' ),

			'payment rejected'
				=> __( 'payment rejected', 'netpay' ),

			'phone_number must contain 10-11 digit characters'
				=> __( 'phone_number must contain 10-11 digit characters', 'netpay' ),

			'return uri is invalid'
				=> __( 'return uri is invalid', 'netpay' ),

			'the account number is invalid'
				=> __( 'the account number is invalid', 'netpay' ),

			'the security code is invalid'
				=> __( 'the security code is invalid', 'netpay' ),

			'type is currently not supported'
				=> __( 'type is currently not supported', 'netpay' ),
		);

		return isset( $known_messages[ $message ] ) ? $known_messages[ $message ] : $message;
	}
}
