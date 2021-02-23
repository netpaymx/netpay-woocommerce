<?php
defined( 'ABSPATH' ) or die( "No direct script access allowed." );

if ( ! class_exists( 'NetPay_Admin' ) ) {
	class NetPay_Admin {
		/**
		 * The NetPay Instance.
		 *
		 * @var \NetPay_Admin
		 */
		protected static $the_instance;

		/**
		 * @return \NetPay_Admin  The instance.
		 */
		public static function get_instance() {
			if ( ! self::$the_instance ) {
				self::$the_instance = new self();
			}

			return self::$the_instance;
		}

		/**
		 * @since 3.3
		 */
		public function init() {
			require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/admin/class-netpay-admin-page.php';
			require_once NETPAY_WOOCOMMERCE_PLUGIN_PATH . '/includes/admin/class-netpay-page-settings.php';

			$this->register_admin_menu();
			$this->register_woocommerce_filters();
		}

		/**
		 * Register NetPay's custom menu to WordPress admin menus.
		 */
		public function register_admin_menu() {
			add_action( 'admin_menu', array( $this, 'wordpress_hook_admin_menu' ) );
		}

		/**
		 * Callback to $this::register_admin_menu() method.
		 * Register NetPay's custom menu to WordPress admin menus.
		 */
		public function wordpress_hook_admin_menu() {
			add_menu_page( 'NetPay', 'NetPay', 'manage_options', 'netpay', array( $this, 'page_settings') );

			add_submenu_page( 'netpay', __( 'NetPay Configuraciones', 'netpay' ), __( 'Configurar', 'netpay' ), 'manage_options', 'netpay-settings', array( $this, 'page_settings') );
		}

		/**
		 * Render NetPay Setting page.
		 */
		public function page_settings() {
			NetPay_Page_Settings::render();
		}

		/**
		 * @since 3.3
		 */
		public function register_woocommerce_filters() {
			add_filter( 'woocommerce_order_actions', array( $this, 'woocommerce_filter_order_actions' ) );
		}

		/**
		 * Callback to $this::register_woocommerce_filters() method.
		 *
		 * @since  3.3
		 *
		 * @param  array $order_actions
		 *
		 * @return array
		 */
		public function woocommerce_filter_order_actions( $order_actions ) {
			global $theorder;

			/** backward compatible with WooCommerce v2.x series **/
			$payment_method = version_compare( WC()->version, '3.0.0', '>=' ) ? $theorder->get_payment_method() : $theorder->payment_method;

			if ( 'netpay' === $payment_method ) {
				$order_actions[ $payment_method . '_charge_capture'] = __( 'NetPay: Capture this order', 'netpay' );
			}

			$order_actions[ $payment_method . '_sync_payment'] = __( 'NetPay: Manual sync payment status', 'netpay' );

			return $order_actions;
		}
	}
}
?>
