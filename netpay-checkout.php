<?php
/**
 * Plugin Name: NetPay Checkout
 * Plugin URI:  https://docs.netpay.com.mx/docs/woocommerce
 * Description: NetPay WooCommerce Gateway Plugin is a WordPress plugin designed specifically for WooCommerce. The plugin adds support for NetPay Checkout payment method to WooCommerce.
 * Version:     1.0.0.0
 * Author:      NetPay
 * Author URI:  https://netpay.mx
 * Text Domain: netpay
 * License:     MIT
 * License URI: https://opensource.org/licenses/MIT
 */
defined( 'ABSPATH' ) or die( 'No direct script access allowed.' );

class NetPay {
	/**
	 * NetPay plugin version number.
	 *
	 * @var string
	 */
	public $version = '4.3';

	/**
	 * The NetPay Instance.
	 *
	 * @since 3.0
	 *
	 * @var   \NetPay
	 */
	protected static $the_instance = null;

	/**
	 * @since 3.3
	 *
	 * @var   boolean
	 */
	protected static $can_initiate = false;

	/**
	 * @since  3.0
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'check_dependencies' ) );
		add_action( 'init', array( $this, 'init' ) );

		add_action( 'init', array( $this, 'netpay_cash_ipn_listener' ));
		add_action( 'netpay_process_ipn_cash', array( $this, 'process_ipn' ) );

		do_action( 'netpay_initiated' );
	}

	public function netpay_cash_ipn_listener() {
		if ( isset( $_GET['netpay-listener'] ) ) {
	
			$gateway = filter_var ( $_GET['netpay-listener'], FILTER_SANITIZE_STRING);
	
			/**
			 * Handle a gateway's IPN.
			 *
			 * @since 1.0.0
			 */
			do_action( 'netpay_process_ipn_' . $gateway );
	
			return true;
		}
	
		return false;
	}

	public static function process_ipn() {
		$json = file_get_contents('php://input');
		$data = json_decode($json, true);
		if(isset($data["data"]["transactionId"])) {
			\NetPay\NetPayConfig::init(NETPAY_TEST_MODE);
			$transaction = \NetPay\Api\NetPayTransaction::get(NETPAY_SECRET_KEY, $data["data"]["transactionId"]);
			if($transaction["result"]["status"] == "IN_PROCESS" 
				&& $transaction["result"]["transactionTokenId"] == $data["data"]["transactionId"]
				&& $transaction["result"]["amount"] == $data["data"]["amount"]) {
					$order_id = \NetPay\NetPayFunctions::get_post_id_by_transaction_id($transaction["result"]["transactionTokenId"]);
					$order = new WC_Order($order_id);
					$order->payment_complete();
			}
		}
	}

	/** 
	 * Check if all dependencies are loaded
	 * properly before NetPay-WooCommerce.
	 * 
	 * @since  3.2
	 */
	public function check_dependencies() {
		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		static::$can_initiate = true;
	}

	/**
	 * @since  3.0
	 */
	public function init() {
		if ( ! static::$can_initiate ) {
			add_action( 'admin_notices', array($this, 'init_error_messages') );
			return;
		}

		$this->include_classes();
		$this->define_constants();
		$this->load_plugin_textdomain();
		$this->register_post_types();
		$this->init_admin();
		$this->init_route();
		$this->register_payment_methods();
		$this->register_hooks();
		$this->register_ajax_actions();

		prepare_netpay_myaccount_panel();

		//$this->set_default_payment_method();
	}

	public function set_default_payment_method()
	{
		$gateways = WC()->payment_gateways->get_available_payment_gateways();
		$enabled_gateways = [];
		if ($gateways) {
			foreach ($gateways as $gateway) {
				if ($gateway->enabled == 'yes') {
					$enabled_gateways[] = $gateway->id;
				}
			}
		}
		if (in_array('netpay_installment', $enabled_gateways)) {
			//WC()->session->set('chosen_payment_method', 'netpay_installment');
		}
		else if (in_array('netpay', $enabled_gateways)) {
			//WC()->session->set('chosen_payment_method', 'netpay');
		}
	}

	/**
	 * Callback to display message about activation error
	 *
	 * @since  3.2
	 */
	public function init_error_messages(){
		?>
		<div class="error">
			<p><?php echo __( 'NetPay WooCommerce plugin requires <strong>WooCommerce</strong> to be activated.', 'netpay' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Define NetPay necessary constants.
	 *
	 * @since 3.3
	 */
	private function define_constants() {
		global $wp_version;

		defined( 'NETPAY_WOOCOMMERCE_PLUGIN_VERSION' ) || define( 'NETPAY_WOOCOMMERCE_PLUGIN_VERSION', $this->version );
		defined( 'NETPAY_PUBLIC_KEY' ) || define( 'NETPAY_PUBLIC_KEY', $this->settings()->public_key() );
		defined( 'NETPAY_SECRET_KEY' ) || define( 'NETPAY_SECRET_KEY', $this->settings()->secret_key() );
		defined( 'NETPAY_TEST_MODE' ) || define( 'NETPAY_TEST_MODE', $this->settings()->is_test_mode() );
		defined( 'NETPAY_PLUGIN_DIR' ) || define( 'NETPAY_PLUGIN_DIR', dirname(__FILE__ ) . "/" );
		defined( 'NETPAY_PLUGIN_URL' ) || define( 'NETPAY_PLUGIN_URL', plugins_url( '', dirname( __FILE__ ) ) . "/netpay-checkout/" );
		defined( 'NETPAY_API_VERSION' ) || define( 'NETPAY_API_VERSION', '2020-11-24' );
		defined( 'NETPAY_USER_AGENT_SUFFIX' ) || define( 'NETPAY_USER_AGENT_SUFFIX', sprintf( 'NetPayWooCommerce/%s WordPress/%s WooCommerce/%s', NETPAY_WOOCOMMERCE_PLUGIN_VERSION, $wp_version, WC()->version ) );
	}

	/**
	 * @since 3.3
	 */
	private function include_classes() {
		defined( 'NETPAY_WOOCOMMERCE_PLUGIN_PATH' ) || define( 'NETPAY_WOOCOMMERCE_PLUGIN_PATH', __DIR__ );

		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/class-netpay-queue-runner.php';
		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/class-netpay-queueable.php';
		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/backends/class-netpay-backend.php';
		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/backends/class-netpay-backend-installment.php';
		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/classes/class-netpay-charge.php';
		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/classes/class-netpay-card-image.php';
		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/events/class-netpay-event.php';
		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/events/class-netpay-event-charge-capture.php';
		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/events/class-netpay-event-charge-complete.php';
		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/events/class-netpay-event-charge-create.php';
		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/gateway/abstract-netpay-payment-offline.php';
		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/gateway/abstract-netpay-payment-offsite.php';
		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/gateway/class-netpay-payment-creditcard.php';
		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/gateway/class-netpay-payment-installment.php';
		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/gateway/class-netpay-payment-cash.php';
		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/gateway/class-netpay-payment.php';
		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/libraries/netpay-php/init.php';
		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/class-netpay-ajax-actions.php';
		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/class-netpay-callback.php';
		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/class-netpay-capabilities.php';
		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/class-netpay-events.php';
		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/class-netpay-localization.php';
		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/class-netpay-money.php';
		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/class-netpay-payment-factory.php';
		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/class-netpay-rest-webhooks-controller.php';
		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/class-netpay-setting.php';
		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/class-netpay-wc-myaccount.php';
		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/netpay-util.php';
		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/libraries/netpay-plugin/NetPay.php';
		require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/libraries/netpay-php/lib/NetPay.php';
	}

	/**
	 * @since  3.0
	 */
	protected function init_admin() {
		if ( is_admin() ) {
			require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/class-netpay-admin.php';
			NetPay_Admin::get_instance()->init();
		}
	}

	/**
	 * @since  3.1
	 */
	protected function init_route() {
		add_action( 'rest_api_init', function () {
			$controllers = new NetPay_Rest_Webhooks_Controller;
			$controllers->register_routes();
		} );
	}

	/**
	 * @since  3.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'netpay', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * @since  3.11
	 */
	public function register_payment_methods() {
		add_filter( 'woocommerce_payment_gateways', function( $methods ) {
			return array_merge( $methods, $this->payment_methods() );
		} );
	}

	/**
	 * @since  4.0
	 */
	public function register_hooks() {
		add_action( 'netpay_async_webhook_event_handler', 'NetPay_Queue_Runner::execute_webhook_event_handler', 10, 3 );
	}

	/**
	 * @since  4.1
	 */
	public function register_ajax_actions() {
		add_action('wp_ajax_nopriv_fetch_order_status', 'NetPay_Ajax_Actions::fetch_order_status' );
		add_action('wp_ajax_fetch_order_status', 'NetPay_Ajax_Actions::fetch_order_status' );
	}

	/**
	 * Register necessary post-types
	 *
	 * @deprecated 3.0  NetPay-WooCommerce was once storing NetPay's charge id
	 *                  with WooCommerce's order id together in a
	 *                  customed-post-type, 'netpay_charge_items'.
	 *
	 *                  Since NetPay-WooCoomerce v3.0, now the plugin stores
	 *                  NetPay's charge id as a 'customed-post-meta' in the
	 *                  WooCommerce's 'order' post-type instead.
	 */
	public function register_post_types() {
		register_post_type(
			'netpay_charge_items',
			array(
				'supports' => array('title','custom-fields'),
				'label'    => 'NetPay Charge Items',
				'labels'   => array(
					'name'          => 'NetPay Charge Items',
					'singular_name' => 'NetPay Charge Item'
				)
			)
		);
	}

	/**
	 * The NetPay Instance.
	 *
	 * @see    NetPay()
	 *
	 * @since  3.0
	 *
	 * @static
	 *
	 * @return \NetPay - The instance.
	 */
	public static function instance() {
		if ( is_null( self::$the_instance ) ) {
			self::$the_instance = new self();
		}

		return self::$the_instance;
	}

	/**
	 * Get setting class.
	 *
	 * @since  3.4
	 *
	 * @return NetPay_Setting
	 */
	public function settings() {
		return NetPay_Setting::instance();
	}

	/**
	 * @since  4.0
	 *
	 * @return array of all the available payment methods
	 *               that NetPay WooCommerce supported.
	 */
	public function payment_methods() {
		\NetPay\NetPayConfig::init($this->settings()->is_test_mode() );
		$is_cash_enable = \NetPay\Api\NetPayCashEnable::get($this->settings()->secret_key());
		if($is_cash_enable['result']['cashPaymentEnable'] === true)
		{
			return NetPay_Payment_Factory::$payment_methods;
		}
		else {
			$payments =  NetPay_Payment_Factory::$payment_methods;
			return array_splice($payments, 0,2);
		}
	}

	/**
	 * L10n the given string.
	 *
	 * @since  4.1
	 *
	 * @return string
	 */
	public function translate( $message ) {
		return NetPay_Localization::translate( $message );
	}
}

function NetPay() {
	return NetPay::instance();
}

NetPay();
