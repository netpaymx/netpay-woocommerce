<?php
defined( 'ABSPATH' ) or die( 'No direct script access allowed.' );

class NetPay_Payment_Cash extends NetPay_Payment {
	public function __construct() {
		parent::__construct();

		$this->id                 = 'netpay_cash';
		$this->has_fields         = true;
		$this->method_title       = __( 'Pago en efectivo', 'netpay' );
		$this->method_description = wp_kses(
			__( 'Acepta pagos en efectivo en mas de 700 corresponsalias.', 'netpay' ),
			array( 'strong' => array() )
		);
		$this->supports           = array( 'cash');

		$this->init_form_fields();
		$this->init_settings();

		$this->title                = $this->get_option( 'title' ) . " - (by Netpay)";
		$this->description          = $this->get_option( 'description' );
		$this->payment_action       = $this->get_option( 'payment_action' );
		$this->restricted_countries = array( 'MX');

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_order_action_' . $this->id . '_charge_capture', array( $this, 'process_capture' ) );
		add_action( 'woocommerce_order_action_' . $this->id . '_sync_payment', array( $this, 'sync_payment' ) );

		add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));

		$this->cash();
	}

	/**
     * Hooks a function on to a specific woocommerce action.
     */
    private function cash() {
		add_filter( 'woocommerce_default_address_fields' , array($this, 'custom_override_default_address_fields') );

		add_filter('woocommerce_billing_fields', array($this, 'custom_billing_fields'));
	
	}

	// Our hooked in function - $address_fields is passed via the filter!
    function custom_override_default_address_fields( $address_fields ) {
        $address_fields['first_name']['custom_attributes'] = array(
            'maxlength' => 35
        );
        $address_fields['last_name']['custom_attributes'] = array(
            'maxlength' => 35
        );
        $address_fields['address_1']['custom_attributes'] = array(
            'maxlength' => 50
        );
        $address_fields['address_2']['custom_attributes'] = array(
            'maxlength' => 50
        );
        $address_fields['city']['custom_attributes'] = array(
            'maxlength' => 90
        );
        $address_fields['state']['custom_attributes'] = array(
            'maxlength' => 30
        );
        $address_fields['postcode']['custom_attributes'] = array(
            'maxlength' => 20
        );

        return $address_fields;
    }

    function custom_billing_fields( $fields ) {
        $fields['billing_email']['custom_attributes'] = array(
            'maxlength' => 50
        );

        $fields['billing_address_2']['required'] = true;
        $fields['billing_phone']['custom_attributes'] = array(
            'maxlength' => 15
        );
    
        return $fields;
	}

	/**
	 * @see WC_Settings_API::init_form_fields()
	 * @see woocommerce/includes/abstracts/abstract-wc-settings-api.php
	 */
	function init_form_fields() {
		$this->form_fields = array_merge(
			array(
				'enabled' => array(
					'title'   => __( 'Enable/Disable', 'netpay' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable NetPay Cash Payments', 'netpay' ),
					'default' => 'no'
				),
	
				'title' => array(
					'title'       => __( 'Title', 'netpay' ),
					'type'        => 'text',
					'description' => __( 'This controls the title the user sees during checkout.', 'netpay' ),
					'default'     => __( 'Pago en efectivo', 'netpay' ),
				),
	
				'description' => array(
					'title'       => __( 'Description', 'netpay' ),
					'type'        => 'textarea',
					'description' => __( 'This controls the description the user sees during checkout.', 'netpay' )
				)
			)
		);
	}

	function netpay_cash_form() {
		$viewData['is_test'] = $this->is_test();
		NetPay_Util::render_view( 'templates/payment/form-cash.php', $viewData );
	}

	/**
	 * @see WC_Payment_Gateway::payment_fields()
	 * @see woocommerce/includes/abstracts/abstract-wc-payment-gateway.php
	 */
	public function payment_fields() {
		wc_clear_notices();
		$this->netpay_cash_form();
	}

	/**
	 * @inheritdoc
	 */
	public function charge( $order_id, $order ) {
		//TODO metodo que llama cuando se hace el cargo
		\NetPay\NetPayConfig::init($this->is_test() );
		$is_cash_enable = \NetPay\Api\NetPayCashEnable::get(NETPAY_SECRET_KEY);
		if($is_cash_enable['result']['cashPaymentEnable'] === true)
		{
			$order_data = $order->get_data();
			$billing = array(
				'amount' => $order->get_total(),
				'description' => 'Cobro de la orden ' . version_compare( WC()->version, '3.0.0', '>=' ) ? $order->get_id() : $order->id,
				'billing_firstName' => \NetPay\NetPayFunctions::replace_caracters(version_compare( WC()->version, '3.0.0', '>=' ) ? $order_data['billing']['first_name'] : $order->billing_first_name),
				'billing_lastName' => \NetPay\NetPayFunctions::replace_caracters(version_compare( WC()->version, '3.0.0', '>=' ) ? $order_data['billing']['last_name'] : $order->billing_last_name),
				'billing_email' => \NetPay\NetPayFunctions::replace_caracters(version_compare( WC()->version, '3.0.0', '>=' ) ? $order_data['billing']['email'] : $order->billing_email),
				'billing_phone' => \NetPay\NetPayFunctions::replace_caracters(str_replace("+52", "", version_compare( WC()->version, '3.0.0', '>=' ) ? $order_data['billing']['phone'] : $order->billing_phone))
			);
			return \NetPay\Api\NetPayCash::post(NETPAY_SECRET_KEY, $billing);
		}
		else {
			throw new Exception( __( 'De momento el servicio no está disponible.', 'netpay' ) );
		}
	}

	/**
	 * @inheritdoc
	 */
	public function result( $order_id, $order, $charge ) {
		if($charge['result']['status'] == "success") {
			\NetPay\NetPayFunctions::custom_field_update_order_meta($order->id, '_transaction_token_id', $charge['result']['transactionTokenId']);
			\NetPay\NetPayFunctions::custom_field_update_order_meta($order->id, '_reference', $charge['result']['paymentSource']['cashDto']['reference']);
			\NetPay\NetPayFunctions::custom_field_update_order_meta($order->id, '_expireInDays', $charge['result']['paymentSource']['cashDto']['expireInDays']);
			\NetPay\NetPayFunctions::custom_field_update_order_meta($order->id, '_amount', $charge['result']['amount']);

			$order->add_order_note(
				sprintf(
					wp_kses(
						__( 'NetPay: Referencia generada.<br/>Por la cantidad de %1$s %2$s', 'netpay' ),
						array( 'br' => array() )
					),
					$order->get_total(),
					$order->get_currency()
				)
			);

			$order->add_order_note(
				sprintf(
					__( 'NetPay: TransactionTokenId: %s', 'netpay' ),
					$charge['result']['transactionTokenId']
				)
			);

			// Remove cart
			WC()->cart->empty_cart();

			return array(
				'result' 	 => 'success',
				'redirect'	 => $order->get_checkout_payment_url( true ),
			);
			
		}
		else {
			return $this->payment_failed( __( 'No se pudo generar la referencia de pago, por favor intenta nuevamente.', 'netpay' ) );
		}
	}

	function receipt_page($order_id)
    {
		$amount = \NetPay\NetPayFunctions::custom_field_get_order_meta($order_id, '_amount');
		$reference = \NetPay\NetPayFunctions::custom_field_get_order_meta($order_id, '_reference');
		$expireInDays = \NetPay\NetPayFunctions::custom_field_get_order_meta($order_id, '_expireInDays');

        wc_get_template(
            'templates/thank-you.php',
            array(
				"amount" => number_format($amount, 2, '.', ''),
				"reference" => $reference,
				"expire_in_days" => $expireInDays,
				"plugin_dir" => NETPAY_PLUGIN_URL . 'assets/images/cash'
			),
            '',
			NETPAY_PLUGIN_DIR
		);
    }

	/**
	 * Capture an authorized charge.
	 *
	 * @param  WC_Order $order WooCommerce's order object
	 *
	 * @return void
	 *
	 * @see    WC_Meta_Box_Order_Actions::save( $post_id, $post )
	 * @see    woocommerce/includes/admin/meta-boxes/class-wc-meta-box-order-actions.php
	 */
	public function process_capture( $order ) {
		$this->load_order( $order );

		try {
			$charge = NetPayCharge::retrieve( $this->get_charge_id_from_order() );
			$charge->capture();

			if ( ! NetPayPluginHelperCharge::isPaid( $charge ) ) {
				throw new Exception( NetPay()->translate( $charge['failure_message'] ) );
			}

			$this->order()->add_order_note(
				sprintf(
					wp_kses(
						__( 'NetPay: Payment successful (manual capture).<br/>An amount of %1$s %2$s has been paid', 'netpay' ),
						array( 'br' => array() )
					),
					$this->order()->get_total(),
					$this->order()->get_currency()
				)
			);
			$this->order()->payment_complete();
		} catch ( Exception $e ) {
			$this->order()->add_order_note(
				sprintf(
					wp_kses( __( 'NetPay: Payment failed (manual capture).<br/>%s', 'netpay' ), array( 'br' => array() ) ),
					$e->getMessage()
				)
			);
			$this->order()->update_status( 'failed' );
		}
	}
}
