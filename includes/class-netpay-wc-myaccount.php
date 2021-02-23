<?php
defined( 'ABSPATH' ) or die( 'No direct script access allowed.' );

if ( ! class_exists( 'NetPay_MyAccount' ) ) {
	class NetPay_MyAccount {
		private static $instance;
		private $netpay_customer_id;

		public static function get_instance() {
			if ( ! self::$instance) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		private function __construct() {
			// prevent running directly without wooCommerce
			if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
				return;
			}

			if ( is_user_logged_in() ) {
				$current_user = wp_get_current_user();
				$this->netpay_customer_id = NetPay()->settings()->is_test() ? $current_user->test_netpay_customer_id : $current_user->live_netpay_customer_id;
			}

			add_action( 'woocommerce_after_my_account', array( $this, 'init_panel' ) );
			add_action( 'wp_ajax_netpay_delete_card', array( $this, 'netpay_delete_card' ) );
			add_action( 'wp_ajax_netpay_create_card', array( $this, 'netpay_create_card' ) );
			add_action( 'wp_ajax_nopriv_netpay_delete_card', array( $this, 'no_op' ) );
			add_action( 'wp_ajax_nopriv_netpay_create_card', array( $this, 'no_op' ) );
		}

		/**
		 * Append NetPay Settings panel to My Account page
		 */
		public function init_panel() {
			if ( ! empty( $this->netpay_customer_id ) ) {
				try {
					$customer                  = NetPayCustomer::retrieve( $this->netpay_customer_id );
					$viewData['existingCards'] = $customer->cards();

					NetPay_Util::render_view( 'templates/myaccount/my-card.php', $viewData );
					$this->register_netpay_my_account_scripts();
				} catch (Exception $e) {
					// nothing.
				}
			}
		}

		/**
		 * Register all javascripts
		 */
		public function register_netpay_my_account_scripts() {
			wp_enqueue_script(
				'netpay-myaccount-card-handler',
				plugins_url( '/assets/javascripts/netpay-myaccount-card-handler.js', dirname( __FILE__ ) ),
				array( 'netpay-js' ),
				WC_VERSION,
				true
			);

			$netpay_params = array(
				'key'                            => NetPay()->settings()->public_key(),
				'ajax_url'                       => admin_url( 'admin-ajax.php' ),
				'ajax_loader_url'                => plugins_url( '/assets/images/ajax-loader@2x.gif', dirname( __FILE__ ) ),
				'required_card_name'             => __( 'Cardholder\'s name is a required field', 'netpay' ),
				'required_card_number'           => __( 'Card number is a required field', 'netpay' ),
				'required_card_expiration_month' => __( 'Card expiry month is a required field', 'netpay' ),
				'required_card_expiration_year'  => __( 'Card expiry year is a required field', 'netpay' ),
				'required_card_security_code'    => __( 'Card security code is a required field', 'netpay' ),
				'cannot_create_card'             => __( 'Unable to add a new card.', 'netpay' ),
				'cannot_connect_api'             => __( 'Currently, the payment provider server is undergoing maintenance.', 'netpay' ),
				'cannot_load_netpayjs'            => __( 'Cannot connect to the payment provider.', 'netpay' ),
				'check_internet_connection'      => __( 'Please make sure that your internet connection is stable.', 'netpay' ),
				'retry_or_contact_support'       => wp_kses(
					__( 'This incident could occur either from the use of an invalid card, or the payment provider server is undergoing maintenance.<br/>
					    You may retry again in a couple of seconds, or contact our support team if you have any questions.', 'netpay' ),
					array( 'br' => array() )
				)
			);

			wp_localize_script( 'netpay-myaccount-card-handler', 'netpay_params', $netpay_params );
		}

		/**
		 * Public netpay_delete_card ajax hook
		 */
		public function netpay_delete_card() {
			$card_id = isset( $_POST['card_id'] ) ? wc_clean( $_POST['card_id'] ) : '';
			if ( empty( $card_id ) ) {
				NetPay_Util::render_json_error( 'card_id is required' );
				die();
			}

			$nonce = 'netpay_delete_card_' . $_POST['card_id'];
			if ( ! wp_verify_nonce( $_POST['netpay_nonce'], $nonce ) ) {
				NetPay_Util::render_json_error( 'Nonce verification failure' );
				die();
			}

			$customer = NetPayCustomer::retrieve( $this->netpay_customer_id );
			$card     = $customer->cards()->retrieve( $card_id );
			$card->destroy();

			echo json_encode( array(
				'deleted' => $card->isDestroyed()
			) );
			die();
		}

		/**
		 * Public netpay_create_card ajax hook
		 */
		public function netpay_create_card() {
			$token = isset ( $_POST['netpay_token'] ) ? wc_clean ( $_POST['netpay_token'] ) : '';
			if ( empty( $token ) ) {
				NetPay_Util::render_json_error( 'netpay_token is required' );
				die();
			}

			if ( ! wp_verify_nonce($_POST['netpay_nonce'], 'netpay_add_card' ) ) {
				NetPay_Util::render_json_error( 'Nonce verification failure' );
				die();
			}

			try {
				$customer = NetPayCustomer::retrieve( $this->netpay_customer_id );
				$customer->update( array(
					'card' => $token
				) );

				$cards = $customer->cards( array(
					'limit' => 1,
					'order' => 'reverse_chronological'
				) );

				echo json_encode( $cards['data'][0] );
			} catch( Exception $e ) {
				echo json_encode( array(
					'object'  => 'error',
					'message' => $e->getMessage()
				) );
			}

			die();
		}

		/**
		 * No operation on no-priv ajax requests
		 */
		public function no_op() {
			exit( 'Not permitted' );
		}
	}
}

function prepare_netpay_myaccount_panel() {
	$netpay_myaccount = NetPay_MyAccount::get_instance();
}
?>
