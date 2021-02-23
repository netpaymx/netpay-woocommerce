<?php
defined( 'ABSPATH' ) or die( 'No direct script access allowed.' );

class NetPay_Payment_Creditcard extends NetPay_Payment {
	public function __construct() {
		parent::__construct();

		$this->id                 = 'netpay';
		$this->has_fields         = true;
		$this->method_title       = __( 'NetPay Credit / Debit Card', 'netpay' );
		$this->method_description = wp_kses(
			__( 'Accept payment through <strong>Credit / Debit Card</strong> via NetPay payment gateway.', 'netpay' ),
			array(
				'strong' => array()
			)
		);
		$this->supports           = array( 'products', 'refunds' );

		$this->init_form_fields();
		$this->init_settings();

		$this->title                = $this->get_option( 'title' ) . " - (by Netpay)";
		$this->description          = $this->get_option( 'description' );
		$this->payment_action       = $this->get_option( 'payment_action' );
		$this->restricted_countries = array( 'MX');

		add_action( 'woocommerce_api_' . $this->id . '_callback', 'NetPay_Callback::execute' );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'netpay_scripts_card' ) );
		add_action( 'woocommerce_order_action_' . $this->id . '_charge_capture', array( $this, 'process_capture' ) );
		add_action( 'woocommerce_order_action_' . $this->id . '_sync_payment', array( $this, 'sync_payment' ) );

		/** @deprecated 3.0 */
		add_action( 'woocommerce_api_wc_gateway_' . $this->id, 'NetPay_Callback::execute' );

		$this->card();
	}

	/**
     * Hooks a function on to a specific woocommerce action.
     */
    private function card() {
        add_action(
            'woocommerce_api_' . strtolower(get_class($this)),
            array($this, 'check_netpay_card_response')
		);

		add_filter( 'woocommerce_default_address_fields' , array($this, 'custom_override_default_address_fields') );

		add_filter('woocommerce_billing_fields', array($this, 'custom_billing_fields'));

		add_filter( 'woocommerce_thankyou_order_received_text', array($this, 'netpay_card_thank_you_title'), 20, 2 );
		
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
					'label'   => __( 'Enable NetPay Credit / Debit Card Payment', 'netpay' ),
					'default' => 'no'
				),

				'title' => array(
					'title'       => __( 'Title', 'netpay' ),
					'type'        => 'text',
					'description' => __( 'This controls the title the user sees during checkout.', 'netpay' ),
					'default'     => __( 'Pago con tarjeta de crédito o débito', 'netpay' )
				),

				'description' => array(
					'title'       => __( 'Description', 'netpay' ),
					'type'        => 'textarea',
					'description' => __( 'This controls the description the user sees during checkout.', 'netpay' )
				),
			),
			array(
				'advanced' => array(
					'title'       => __( 'Advance Settings', 'netpay' ),
					'type'        => 'title'
				),
				'accept_visa' => array(
					'title'       => __( 'Supported card icons', 'netpay' ),
					'type'        => 'checkbox',
					'label'       => NetPay_Card_Image::get_visa_image(),
					'css'         => NetPay_Card_Image::get_css(),
					'default'     => NetPay_Card_Image::get_visa_default_display()
				),
				'accept_mastercard' => array(
					'type'        => 'checkbox',
					'label'       => NetPay_Card_Image::get_mastercard_image(),
					'css'         => NetPay_Card_Image::get_css(),
					'default'     => NetPay_Card_Image::get_mastercard_default_display()
				),
				'accept_amex' => array(
					'type'        => 'checkbox',
					'label'       => NetPay_Card_Image::get_amex_image(),
					'css'         => NetPay_Card_Image::get_css(),
					'default'     => NetPay_Card_Image::get_amex_default_display(),
					'description' => wp_kses(
						__( 'This only controls the icons displayed on the checkout page.<br />It is not related to card processing on NetPay payment gateway.', 'netpay' ),
						array( 'br' => array() )
					)
				)
			)
		);
	}

	function netpay_card_form() {
		$viewData['is_test'] = $this->is_test();
		NetPay_Util::render_view( 'templates/payment/form.php', $viewData );
	}

	/**
	 * @see WC_Payment_Gateway::payment_fields()
	 * @see woocommerce/includes/abstracts/abstract-wc-payment-gateway.php
	 */
	public function payment_fields() {
		wc_clear_notices();
		$this->netpay_card_form();
	}

	/**
	 * @inheritdoc
	 */
	public function charge( $order_id, $order ) {
		//TODO metodo que llama cuando se hace el cargo

		$netpay_card_number   = isset( $_POST['netpay_card_number'] ) ? wc_clean( $_POST['netpay_card_number'] ) : '';
		$netpay_card_name   = isset( $_POST['netpay_card_name'] ) ? wc_clean( $_POST['netpay_card_name'] ) : '';
		$netpay_card_expiration_card   = isset( $_POST['netpay_card_expiration_card'] ) ? wc_clean( $_POST['netpay_card_expiration_card'] ) : '';
		$netpay_card_security_code   = isset( $_POST['netpay_card_security_code'] ) ? wc_clean( $_POST['netpay_card_security_code'] ) : '';
		$netpay_card_devicefingerprint = isset( $_POST['netpay_card_devicefingerprint'] ) ? wc_clean( $_POST['netpay_card_devicefingerprint'] ) : '';
		
		$cardTypes = $this->getCardTypes();
		$cardScheme = \NetPay\NetPayFunctions::getCardScheme(\NetPay\NetPayFunctions::replace_only_numbers($netpay_card_number));
		if($cardScheme == 'amex' && strlen(\NetPay\NetPayFunctions::replace_only_numbers($netpay_card_number) < 15)) {
			throw new Exception( __( 'No. tarjeta inválido, deben ser 15-16 dígitos.', 'netpay' ) );
		}
		if(($cardScheme == 'visa' || $cardScheme == 'mastercard') && strlen(\NetPay\NetPayFunctions::replace_only_numbers($netpay_card_number) < 16)) {
			throw new Exception( __( 'No. tarjeta inválido, deben ser 15-16 dígitos.', 'netpay' ) );
		}

		if (!in_array($cardScheme, $cardTypes)) {
			throw new Exception( __( 'No. tarjeta inválido, sólo se aceptan tarjetas ' . implode(",", $this->getCardTypesTitle()), 'netpay' ) );
		}

		$lunhCheck = \NetPay\NetPayFunctions::isValidCard(\NetPay\NetPayFunctions::replace_only_numbers($netpay_card_number));
		if(!$lunhCheck || empty($lunhCheck) || $lunhCheck == null) {
			throw new Exception( __( 'No. tarjeta inválido.', 'netpay' ) );
		}

		$name = explode(" ", $netpay_card_name);
		if(count($name) < 2 || strlen($name[1]) < 1) {
			throw new Exception( __( 'Nombre inválido.', 'netpay' ) );
		}

		$expiry_card = explode("/", $netpay_card_expiration_card);
		$netpay_card_expiration_month = $expiry_card[0];
		$netpay_card_expiration_year = $expiry_card[1];

		$month = date('m');
		$year = date("Y");

		if((int)$netpay_card_expiration_year < (int)$year-2000) {
			throw new Exception( __( 'Fecha de vencimiento inválida, debe tener el formato mm/aa y debe ser posterior a la actual.', 'netpay' ) );
		}
		else if((int)$netpay_card_expiration_year == (int)$year-2000 && (int)$netpay_card_expiration_month < $month) {
			throw new Exception( __( 'Fecha de vencimiento inválida, debe tener el formato mm/aa y debe ser posterior a la actual.', 'netpay' ) );
		}
		
		if($cardScheme == 'amex' && strlen($netpay_card_security_code) <> 4) {
			throw new Exception( __( 'Código de seguridad inválido, deben ser 4 dígitos.', 'netpay' ) );
		}
		if($cardScheme != 'amex' && strlen($netpay_card_security_code) <> 3) {
			throw new Exception( __( 'Código de seguridad inválido, deben ser 3 dígitos.', 'netpay' ) );
		}


		if ( empty( $netpay_card_number ) 
			&& empty( $netpay_card_name )
			&& empty( $netpay_card_expiration_card )
			&& empty( $netpay_card_security_code ) ) {
			throw new Exception( __( 'Por favor llena todos los valores de la tarjeta.', 'netpay' ) );
		}
		else if ( isset( $netpay_card_number ) 
		&& isset( $netpay_card_name )
		&& isset( $netpay_card_expiration_card )
		&& isset( $netpay_card_security_code ) ) {
			try {
				$request_token = array(
            		"cardNumber" => \NetPay\NetPayFunctions::replace_only_numbers($netpay_card_number),
            		"expMonth" => $netpay_card_expiration_month,
            		"expYear" => $netpay_card_expiration_year,
            		"cvv2" => $netpay_card_security_code,
            		"cardHolderName" => $netpay_card_name,
            		"deviceFingerPrint" => $netpay_card_devicefingerprint
				);

				\NetPay\NetPayConfig::init($this->is_test() );
				$get_token = \NetPay\Api\NetPayToken::post(NETPAY_PUBLIC_KEY, $request_token);
				$token = $get_token['result']['token'];
				
				$order_data = $order->get_data();

				$country = version_compare( WC()->version, '3.0.0', '>=' ) ? $order_data['billing']['country'] : $order->billing_country;
            	if(empty($country)) {
                	$country = 'MX';
            	}

        		$billing = array(
            		'billing_city' => \NetPay\NetPayFunctions::replace_caracters(version_compare( WC()->version, '3.0.0', '>=' ) ? $order_data['billing']['city'] : $order->billing_city),
            		'billing_country' => $country,
            		'billing_first_name' => \NetPay\NetPayFunctions::replace_caracters(version_compare( WC()->version, '3.0.0', '>=' ) ? $order_data['billing']['first_name'] : $order->billing_first_name),
            		'billing_last_name' => \NetPay\NetPayFunctions::replace_caracters(version_compare( WC()->version, '3.0.0', '>=' ) ? $order_data['billing']['last_name'] : $order->billing_last_name),
            		'billing_email' => \NetPay\NetPayFunctions::replace_caracters(version_compare( WC()->version, '3.0.0', '>=' ) ? $order_data['billing']['email'] : $order->billing_email),
            		'billing_phone' => \NetPay\NetPayFunctions::replace_caracters(str_replace("+52", "", version_compare( WC()->version, '3.0.0', '>=' ) ? $order_data['billing']['phone'] : $order->billing_phone)),
            		'billing_postcode' => version_compare( WC()->version, '3.0.0', '>=' ) ? $order_data['billing']['postcode']: $order->billing_postcode,
            		'billing_state' => \NetPay\NetPayFunctions::replace_caracters(version_compare( WC()->version, '3.0.0', '>=' ) ? $order_data['billing']['state']: $order->billing_state),
            		'billing_address_1' => \NetPay\NetPayFunctions::replace_caracters(version_compare( WC()->version, '3.0.0', '>=' ) ? $order_data['billing']['address_1'] : $order->billing_address_1),
            		'billing_address_2' => \NetPay\NetPayFunctions::replace_caracters(version_compare( WC()->version, '3.0.0', '>=' ) ? $order_data['billing']['address_2']: $order->billing_address_2),
            		'reference' => \NetPay\NetPayFunctions::replace_caracters(version_compare( WC()->version, '3.0.0', '>=' ) ? $order->get_id() : $order->id),
				);
				
				$shipping_country = version_compare( WC()->version, '3.0.0', '>=' ) ? $order_data['shipping']['country']: $order->shipping_country;
				$shipping_phone = str_replace("+52", "", version_compare( WC()->version, '3.0.0', '>=' ) ? $order_data['billing']['phone'] : $order->billing_phone);
				if(empty($shipping_country)) {
					$shipping_phone = "";
				}

        		$shipping = array( //optional, for virtual products it must be empty
            		'shipping_city' => \NetPay\NetPayFunctions::replace_caracters(version_compare( WC()->version, '3.0.0', '>=' ) ? $order_data['shipping']['city']: $order->shipping_city),
            		'shipping_country' => $shipping_country,
            		'shipping_first_name' => \NetPay\NetPayFunctions::replace_caracters(version_compare( WC()->version, '3.0.0', '>=' ) ? $order_data['shipping']['first_name']: $order->shipping_first_name),
            		'shipping_last_name' => \NetPay\NetPayFunctions::replace_caracters(version_compare( WC()->version, '3.0.0', '>=' ) ? $order_data['shipping']['last_name']: $order->shipping_last_name),
            		'shipping_phone' => \NetPay\NetPayFunctions::replace_caracters($shipping_phone),
            		'shipping_postcode' => version_compare( WC()->version, '3.0.0', '>=' ) ? $order_data['shipping']['postcode']: $order->shipping_postcode,
            		'shipping_state' => \NetPay\NetPayFunctions::replace_caracters(version_compare( WC()->version, '3.0.0', '>=' ) ? $order_data['shipping']['state']: $order->shipping_state),
            		'shipping_address_1' => \NetPay\NetPayFunctions::replace_caracters(version_compare( WC()->version, '3.0.0', '>=' ) ? $order_data['shipping']['address_1']: $order->shipping_address_1),
            		'shipping_address_2' => \NetPay\NetPayFunctions::replace_caracters(version_compare( WC()->version, '3.0.0', '>=' ) ? $order_data['shipping']['address_2']: $order->shipping_address_2),
            		'shipping_method' => \NetPay\NetPayFunctions::replace_caracters($order->get_shipping_method()),
				);

				$redirect3dsUri = add_query_arg(
					'wc-api',
					strtolower(get_class($this)),
					get_site_url()
				);

				$redirect3dsUri = add_query_arg(
					'order_id',
					$order_id,
					$redirect3dsUri
				);

				$encrypted_redirect = \NetPay\NetPayFunctions::encrypt($this->get_return_url( $order ), $this->password);

				$redirect3dsUri = add_query_arg(
					'redirect',
					$encrypted_redirect,
					$redirect3dsUri
				);

        		$request_checkout = array(
            		'description' => 'Cobro de la orden ' . version_compare( WC()->version, '3.0.0', '>=' ) ? $order->get_id() : $order->id,
            		'source' => $token,
            		'amount' => $order->get_total(),
            		"billing" => \NetPay\NetPayBill::format($billing),
            		"shipping" => \NetPay\NetPayShip::format($shipping),
            		'redirect3dsUri' => \NetPay\NetPayFunctions::urlencode($redirect3dsUri)
        		);
        		return \NetPay\Api\NetPayCheckout::post(NETPAY_SECRET_KEY, $request_checkout, null);
        		//$transaction_token_id = $checkout['result']['transactionTokenId'];
				//return \NetPay\Api\NetPayTransaction::get(NETPAY_SECRET_KEY, $transaction_token_id);
				
    		} catch (Exception $e) {
        		$description = $e->getMessage();
        		echo json_encode(
            		array(
                		"status"=>"ERROR", 
                		"result"=>$description
            		));
    		}
		}
	}

	/**
	 * @inheritdoc
	 */
	public function result( $order_id, $order, $charge ) {
		if($charge['result']['status'] == "success") {
			$order->add_order_note(
				sprintf(
					wp_kses(
						__( 'NetPay: Pago realizado.<br/>Por la cantidad de %1$s %2$s', 'netpay' ),
						array( 'br' => array() )
					),
					$order->get_total(),
					$order->get_currency()
				)
			);
			$order->payment_complete();

			$order->add_order_note(
				sprintf(
					__( 'NetPay: TransactionTokenId: %s', 'netpay' ),
					$charge['result']['transactionTokenId']
				)
			);

			\NetPay\NetPayFunctions::custom_field_update_order_meta($order->id, '_transaction_token_id', $charge['result']['transactionTokenId']);

			// Remove cart
			WC()->cart->empty_cart();
			return array (
				'result'   => 'success',
				'redirect' => $this->get_return_url( $order )
			);
		}
		else if($charge['result']['status'] == "review") {
			$order->add_order_note(
				sprintf(
					__( 'NetPay: Procesando, redirigiendo al cliente a %s', 'netpay' ),
					esc_url( $charge['result']['returnUrl'])
				)
			);

			return array(
				'result'   => 'success',
				'redirect' => $charge['result']['returnUrl'],
			);
		}
		else if($charge['result']['status'] == "failed") {
			return $this->payment_failed( __( \NetPay\NetPayFunctions::friendly_response($charge['result']['error']), 'netpay' ) );
		}
		else if($charge['result']['status'] == "rejected") {
			return $this->payment_failed( __( \NetPay\NetPayFunctions::friendly_response($charge['result']['error']), 'netpay' ) );
		}
		else if($charge['result']['status'] == "insecure") {
			return $this->payment_failed( __( \NetPay\NetPayFunctions::friendly_response($charge['result']['error']), 'netpay' ) );
		}
		else {
			return $this->payment_failed( __( \NetPay\NetPayFunctions::friendly_response("Error al procesar el carrito"), 'netpay' ) );
		}

	}

	/**
     * Check for valid NetPay transaction server callback.
     */
    public function check_netpay_card_response() {
		$order_id = filter_var ( $_GET['order_id'], FILTER_SANITIZE_NUMBER_INT);
		$redirect = filter_var ( $_GET['redirect'], FILTER_SANITIZE_STRING);

		$order = new WC_Order($order_id);
		$order->update_status( 'pending');
		\NetPay\NetPayConfig::init($this->is_test() );
		if(!empty($_GET['transaction_token'])) {
			$transaction_token = filter_var ( $_GET['transaction_token'], FILTER_SANITIZE_STRING);
    		if(!empty($transaction_token)) {
				$status = \NetPay\Api\NetPayTransaction::get(NETPAY_SECRET_KEY, $transaction_token);

				\NetPay\NetPayFunctions::custom_field_update_order_meta($order_id, '_transaction_token_id', $transaction_token);

        		if($status['result']['status'] == "CHARGEABLE") {
					\NetPay\Api\NetPayConfirm::post(NETPAY_SECRET_KEY, $transaction_token);
					$status_confirm = \NetPay\Api\NetPayTransaction::get(NETPAY_SECRET_KEY, $transaction_token);
					if($status_confirm['result']['status'] == "DONE") {
						$order->add_order_note(
							sprintf(
								wp_kses(
									__( 'NetPay: Pago realizado.<br/>por la cantidad de %1$s %2$s', 'netpay' ),
									array( 'br' => array() )
								),
								$order->get_total(),
								$order->get_currency()
							)
						);
						$order->payment_complete();
					}
					else if($status_confirm['result']['status'] == "REJECT") {
						$this->payment_failed( __( 'Note that your payment was rejected.', 'netpay' ) );
						//$order->update_status( 'failed');
					}
					else {
						$this->payment_failed( __( 'Por favor, póngase en contacto con nuestro equipo de soporte soporte@netpay.com.mx si tiene alguna pregunta.', 'netpay' ) );
						//$order->update_status( 'failed');
					}
        		}
        		else if($status['result']['status'] == "REJECT") {
					$this->payment_failed( __( 'Note that your payment was rejected.', 'netpay' ) );
					//$order->update_status( 'failed');
				}
				else if($status['result']['status'] == "DONE") {
					$order->update_status( 'processing' );
            		$this->payment_failed( __( 'Tenga en cuenta que es posible que su pago ya se haya procesado. Por favor, póngase en contacto con nuestro equipo de soporte soporte@netpay.com.mx si tiene alguna pregunta.', 'netpay' ) );
        		}
        		else {
					//$order->update_status( 'failed' );
            		$this->payment_failed( __( 'Por favor, póngase en contacto con nuestro equipo de soporte soporte@netpay.com.mx si tiene alguna pregunta.', 'netpay' ) );
		        }
    		}
		}

		$decrypted_redirect = \NetPay\NetPayFunctions::decrypt($redirect, $this->password);
		wp_redirect($decrypted_redirect);
		exit();
	
	}

	function netpay_card_thank_you_title( $thank_you_title, $order ) {
		$transaction_token_id = get_post_meta($order->id, '_transaction_token_id', true);
		\NetPay\NetPayConfig::init($this->is_test() );
		$status = \NetPay\Api\NetPayTransaction::get(NETPAY_SECRET_KEY, $transaction_token_id);
		return \NetPay\NetPayFunctions::friendly_response($status['result']['responseMsg']);
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

	/**
	 * Get icons of all supported card types
	 *
	 * @see WC_Payment_Gateway::get_icon()
	 */
	public function get_icon() {
		$icon = '';

		// TODO: Refactor 'NetPay_Card_Image' class that we don't need to pass
		//       these options to check outside this class.
		$card_icons['accept_amex']       = $this->get_option( 'accept_amex' );
		$card_icons['accept_mastercard'] = $this->get_option( 'accept_mastercard' );
		$card_icons['accept_visa']       = $this->get_option( 'accept_visa' );

		if ( NetPay_Card_Image::is_visa_enabled( $card_icons ) ) {
			$icon .= NetPay_Card_Image::get_visa_image()." ";
		}

		if ( NetPay_Card_Image::is_mastercard_enabled( $card_icons ) ) {
			$icon .= NetPay_Card_Image::get_mastercard_image()." ";
		}

		if ( NetPay_Card_Image::is_amex_enabled( $card_icons ) ) {
			$icon .= NetPay_Card_Image::get_amex_image()." ";
		}

		return empty( $icon ) ? '' : apply_filters( 'woocommerce_gateway_icon', $icon, $this->id );
	}

	public function getCardTypes() {
		$cardType = array();
		$card_icons['accept_amex']       = $this->get_option( 'accept_amex' );
		$card_icons['accept_mastercard'] = $this->get_option( 'accept_mastercard' );
		$card_icons['accept_visa']       = $this->get_option( 'accept_visa' );

		if ( NetPay_Card_Image::is_visa_enabled( $card_icons ) ) {
			array_push($cardType, "visa");
		}

		if ( NetPay_Card_Image::is_mastercard_enabled( $card_icons ) ) {
			array_push($cardType, "mastercard");
		}

		if ( NetPay_Card_Image::is_amex_enabled( $card_icons ) ) {
			array_push($cardType, "amex");
		}

		return $cardType;
	}

	public function getCardTypesTitle() {
		$cardType = array();
		$card_icons['accept_amex']       = $this->get_option( 'accept_amex' );
		$card_icons['accept_mastercard'] = $this->get_option( 'accept_mastercard' );
		$card_icons['accept_visa']       = $this->get_option( 'accept_visa' );

		if ( NetPay_Card_Image::is_visa_enabled( $card_icons ) ) {
			array_push($cardType, "Visa");
		}

		if ( NetPay_Card_Image::is_mastercard_enabled( $card_icons ) ) {
			array_push($cardType, " MasterCard");
		}

		if ( NetPay_Card_Image::is_amex_enabled( $card_icons ) ) {
			array_push($cardType, " American Express");
		}

		return $cardType;
	}

	/**
	 * Register all required javascripts
	 */
	public function netpay_scripts_card() {
		if ( is_checkout() && $this->is_available() ) {
			wp_enqueue_script( 'netpay-payment-form-handler', plugins_url( '../../assets/javascripts/netpay-payment-form-handler.js', __FILE__ ), array( ), NETPAY_WOOCOMMERCE_PLUGIN_VERSION, true );
			wp_enqueue_script( 'cleave', plugins_url( '../../assets/javascripts/cleave.js', __FILE__ ), array(  ), NETPAY_WOOCOMMERCE_PLUGIN_VERSION, true );
			wp_enqueue_script( 'netpay_devicefingerprint', plugins_url( '../../assets/javascripts/netpay_devicefingerprint.js', __FILE__ ), array(  ), NETPAY_WOOCOMMERCE_PLUGIN_VERSION, true );
			wp_enqueue_script( 'netpay_bin_lookup', plugins_url( '../../assets/javascripts/netpay_bin_lookup.js', __FILE__ ), array(  ), NETPAY_WOOCOMMERCE_PLUGIN_VERSION, true );

			$netpay_params_card = array(
				'test_mode'						 => $this->is_test(),
				'org_id'						 => ($this->is_test()) ? '45ssiuz3' : '9ozphlqx',
				'key'                            => $this->public_key(),
				'accept_visa'                    => $this->get_option( 'accept_visa' ),
				'accept_mastercard'              => $this->get_option( 'accept_mastercard' ),
				'accept_amex'                    => $this->get_option( 'accept_amex' ),
				'card_types'                     => $this->getCardTypes(),
				'card_types_title'                     => $this->getCardTypesTitle(),
				'required_card_name'             => __( 'Cardholder\'s name is a required field', 'netpay' ),
				'required_card_number'           => __( 'Card number is a required field', 'netpay' ),
				'required_card_expiration_month' => __( 'Card expiry month is a required field', 'netpay' ),
				'required_card_expiration_year'  => __( 'Card expiry year is a required field', 'netpay' ),
				'required_card_security_code'    => __( 'Card security code is a required field', 'netpay' ),
				'invalid_card'                   => __( 'Invalid card.', 'netpay' ),
				'no_card_selected'               => __( 'Please select a card or enter a new one.', 'netpay' ),
				'cannot_create_token'            => __( 'Unable to proceed to the payment.', 'netpay' ),
				'cannot_connect_api'             => __( 'Currently, the payment provider server is undergoing maintenance.', 'netpay' ),
				'retry_checkout'                 => __( 'Please place your order again in a couple of seconds.', 'netpay' ),
				'cannot_load_netpayjs'            => __( 'Cannot connect to the payment provider.', 'netpay' ),
				'check_internet_connection'      => __( 'Please make sure that your internet connection is stable.', 'netpay' ),
			);

			wp_localize_script( 'netpay-payment-form-handler', 'netpay_params_card', $netpay_params_card );
		}
	}
}
