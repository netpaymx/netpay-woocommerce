(function ( $, undefined ) {
	$netpay_card_panel = $("#netpay_card_panel");
	$form = $("#netpay_cc_form");
	
	function showError(message, target){
		if(target===undefined){
			target = $netpay_card_panel;
		}
		
		target.unblock();
		
		if(!message){
			return;
		}
		$(".woocommerce-error, input.netpay_token").remove();
		
		$ulError = $("<ul>").addClass("woocommerce-error");
		
		if($.isArray(message)){
			$.each(message, function(i,v){
				$ulError.append($("<li>" + v + "</li>"));
			})
		}else{
			$ulError.html("<li>" + message + "</li>");
		}
		
		target.prepend( $ulError );
	}
	
	function hideError(){
		$(".woocommerce-error").remove();
	}
	
	function delete_card(card_id, nonce){
		data = {
				action: "netpay_delete_card", 
				card_id: card_id, 
				netpay_nonce: nonce
				};
		
		$.post(netpay_params.ajax_url, data, 
			function(response){
				if(response.deleted){
					window.location.reload();
				}else{
					showError(response.message);
				}
			}, "json"
		);
		
	}
	
	function create_card(){
		$form.block({
			message: null,
			overlayCSS: {
				background: '#fff url(' + netpay_params.ajax_loader_url + ') no-repeat center',
				backgroundSize: '16px 16px',
				opacity: 0.6
			}
		});

		let errors            = [],
		    netpay_card        = {},
		    netpay_card_fields = {
				'name'             : $( '#netpay_card_name' ),
				'number'           : $( '#netpay_card_number' ),
				'expiration_month' : $( '#netpay_card_expiration_month' ),
				'expiration_year'  : $( '#netpay_card_expiration_year' ),
				'security_code'    : $( '#netpay_card_security_code' )
			};

		$.each( netpay_card_fields, function( index, field ) {
			netpay_card[ index ] = field.val();
			if ( "" === netpay_card[ index ] ) {
				errors.push( netpay_params[ 'required_card_' + index ] );
			}
		} );
		
		if ( errors.length > 0 ) {
			showError(errors, $form);
			return false;
		}else{
			hideError();
			if(NetPay){
				NetPay.setPublicKey(netpay_params.key);
				NetPay.createToken("card", card, function (statusCode, response) {
				    if (statusCode == 200) {
						$.each( netpay_card_fields, function( index, field ) {
							field.val( '' );
						} );

				    	data = {
								action: "netpay_create_card", 
								netpay_token: response.id, 
								netpay_nonce: $("#netpay_add_card_nonce").val() 
							    };
						
						$.post(netpay_params.ajax_url, data, 
							function(wp_response){
								if(wp_response.id){
									window.location.reload();
								}else{
									showError(wp_response.message, $form);
								}
							}, "json"
						);
					} else {
						if(response.message){
							showError( netpay_params.cannot_create_card + "<br/>" + response.message, $form );
						}else if(response.responseJSON && response.responseJSON.message){
							showError( netpay_params.cannot_create_card + "<br/>" + response.responseJSON.message, $form );
						}else if(response.status==0){
							showError( netpay_params.cannot_create_card + "<br/>" + netpay_params.cannot_connect_api, $form );
						}else {
							showError( netpay_params.retry_or_contact_support, $form );
						}
					};
				});
			}else{
				showError( netpay_params.cannot_load_netpayjs + '<br/>' + netpay_params.check_internet_connection, $form );
			}
		}
	}
	
	$(".delete_card").click(function(event){
		if(confirm('Confirm delete card?')){
			var $button = $(this);
			$button.block({
				message: null,
				overlayCSS: {
					background: '#fff url(' + netpay_params.ajax_loader_url + ') no-repeat center',
					backgroundSize: '16px 16px',
					opacity: 0.6
				}
			});
			delete_card($button.data("card-id"), $button.data("delete-card-nonce"));
		}
	});
	
	$("#netpay_add_new_card").click(function(event){
		create_card();
	});
}
)(jQuery);
