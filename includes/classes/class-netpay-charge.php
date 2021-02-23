<?php
defined( 'ABSPATH' ) or die( "No direct script access allowed." );

if ( ! class_exists( 'NetPay_Charge' ) ) {
	class NetPay_Charge {
		/**
		 * @param NetPayCharge $charge  NetPay's charge object
		 * @return boolean
		 */
		public static function is_authorized( $charge ) {
			return NetPayPluginHelperCharge::isAuthorized( $charge );
		}

		/**
		 * @param NetPayCharge $charge  NetPay's charge object
		 * @return boolean
		 */
		public static function is_paid( $charge ) {
			return NetPayPluginHelperCharge::isPaid( $charge );
		}

		/**
		 * @param NetPayCharge $charge  NetPay's charge object
		 * @return boolean
		 */
		public static function is_failed( $charge ) {
			return NetPayPluginHelperCharge::isFailed( $charge );
		}

		/**
		 * @param NetPayCharge $charge  NetPay's charge object
		 * @return string | boolean
		 */
		public static function get_error_message( $charge ) {
			if ( '' !== $charge['failure_code'] ) {
				return '(' . $charge['failure_code'] . ') ' . NetPay()->translate( $charge['failure_message'] );
			}

			return '';
		}
	}
}