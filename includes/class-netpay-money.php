<?php
defined( 'ABSPATH' ) || exit;

/**
 * @since 3.6
 */
class NetPay_Money {
	/**
	 * @var array
	 */
	private static $subunit_multiplier = array(
		'MXN' => 100,
	);

	/**
	 * @param  int|float|string $amount
	 * @param  string           $currency
	 *
	 * @return int|float  Note that the expected output value's type of this method is to be `int` as NetPay Charge API requires.
	 *                    However, there is a case that this method will return a `float` regarding to
	 *                    the improper WooCommerce currency setting, which considered as an invalid type of amount.
	 *
	 *                    And we would like to let the API raises an error out loud instead of silently remove
	 *                    or casting a `float` value to `int` subunit.
	 *                    This is to prevent any miscalculation for those fractional subunits
	 *                    between the amount that is charged, and the actual amount from the store.
	 */
	public static function to_subunit( $amount, $currency ) {
		$amount   = self::purify_amount( $amount );
		$currency = strtoupper( $currency );

		if ( ! isset( self::$subunit_multiplier[ $currency ] ) ) {
			throw new Exception( __( 'We do not support the currency you are using.', 'netpay' ) );
		}

		return $amount * self::$subunit_multiplier[ $currency ];
	}

	/**
	 * @param  int|float $amount
	 *
	 * @return float
	 */
	private static function purify_amount( $amount ) {
		if ( ! is_numeric( $amount ) ) {
			throw new Exception( __( 'Invalid amount type given. Should be int, float, or numeric string.', 'netpay' ) );
		}

		return (float) $amount;
	}
}
