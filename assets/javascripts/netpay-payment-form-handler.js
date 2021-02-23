(function ( $, undefined ) {
    var $form = $( 'form.checkout, form#order_review' );
    
    function netpayFormHandler() {
        function showError(message) {
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
            
            $form.prepend( $ulError );
            $("html, body").animate({
                 scrollTop:0
                 },"slow");
        }
        
        function hideError() {
            $(".woocommerce-error").remove();
        }
        
        function validSelection() {
            $card_list = $("input[name='card_id']");
            $selected_card_id = $("input[name='card_id']:checked");
            // there is some existing cards but nothing selected then warning
            if($card_list.length > 0 && $selected_card_id.length === 0){
                return false;
            }
            
            return true;
        }
        
        if ( $( '#payment_method_netpay_card' ).is( ':checked' ) ) {
            if( !validSelection() ) {
                showError( netpay_params_card.no_card_selected );
                return false;
            }
            
            if ( 0 === $( 'input.netpay_token' ).length ) {
                $form.block({
                    message: null,
                    overlayCSS: {
                        background: '#fff url(' + wc_checkout_params.ajax_loader_url + ') no-repeat center',
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
                        errors.push( netpay_params_card[ 'required_card_' + index ] );
                    }
                } );

                if ( errors.length > 0 ) {
                    showError(errors);
                    $form.unblock();
                    return false;
                }

                hideError();

                if(NetPay){
                    NetPay.setPublicKey(netpay_params_card.key);
                    NetPay.createToken("card", netpay_card, function (statusCode, response) {
                        if (statusCode == 200) {
                            $.each( netpay_card_fields, function( index, field ) {
                                field.val( '' );
                            } );
                            $form.append( '<input type="hidden" class="netpay_token" name="netpay_token" value="' + response.id + '"/>' );
                            $form.submit();
                        } else {
                            if ( response.object && 'error' === response.object && 'invalid_card' === response.code ) {
                                showError( netpay_params_card.invalid_card + "<br/>" + response.message );
                            } else if(response.message){
                                showError( netpay_params_card.cannot_create_token + "<br/>" + response.message );
                            }else if(response.responseJSON && response.responseJSON.message){
                                showError( netpay_params_card.cannot_create_token + "<br/>" + response.responseJSON.message );
                            }else if(response.status==0){
                                showError( netpay_params_card.cannot_create_token + "<br/>" + netpay_params_card.cannot_connect_api + netpay_params_card.retry_checkout );
                            }else {
                                showError( netpay_params_card.cannot_create_token + "<br/>" + netpay_params_card.retry_checkout );
                            }
                            $form.unblock();
                        };
                    });
                }else{
                    showError( netpay_params_card.cannot_load_netpayjs + '<br/>' + netpay_params_card.check_internet_connection );
                    $form.unblock();
                }
                
                return false;
            }
            
        }
    }

    function getCardType(cardNumber) {
        const Lookup = new NetPay();
        var binCard = cardNumber.replace(/\s/g, "").substring(0, 6);
        var data = Lookup.lookup(binCard);
        var json = JSON.parse(data);
        if (json.result == 'success') {
            //if (json.data.type == 'credit') {//scheme
            //  return true;
            //}
            return json.data;
        }
        return false;
    }

	var generateDeviceFingerprint = (function() {
		var executed = false;
		return function(org_id) {
			if (!executed) {
                executed = true;
                var session_id = doProfile(org_id); 
                $( "#netpay_card_devicefingerprint" ).val(session_id);
			}
		};
	})();

    function luhnCheck(value) {
        // Accept only digits, dashes or spaces
	    if (/[^0-9-\s]+/.test(value)) return false;

	    // The Luhn Algorithm. It's so pretty.
	    let nCheck = 0, bEven = false;
	    value = value.replace(/\D/g, "");

	    for (var n = value.length - 1; n >= 0; n--) {
		    var cDigit = value.charAt(n),
			      nDigit = parseInt(cDigit, 10);

		    if (bEven && (nDigit *= 2) > 9) nDigit -= 9;

		    nCheck += nDigit;
		    bEven = !bEven;
	    }

	    return (nCheck % 10) == 0;
    }

    function cardTypes() {
        return {
            visa_electron: {
                name: "visa",
                title: "Visa",
                regx: /^(4026|417500|4508|4844|491(3|7))/,
                length: [16],
                accept: true
            },
            visa: {
                name: "visa",
                title: "Visa",
                regx: /^4/,
                length: [16],
                accept: true
            },
            mastercard: {
                name: "mastercard",
                title: "MasterCard",
                regx: /^5[1-5]/,
                length: [16],
                accept: true
            },
            amex: {
                name: "amex",
                title: "American Express",
                regx: /^3[47]/,
                length: [15],
                accept: true
            },
        };
    }

    function getCardScheme (val) {
        var bookIDs;
        bookIDs = cardTypes();
        var bookIdIndex;
        for (bookIdIndex in bookIDs) {
            var _cardObj = bookIDs[bookIdIndex];
            if (_cardObj, val.match(_cardObj.regx)) {
                return _cardObj;
            }
        }
        return false;
    }

    function contains(obj, a) {
        var i = a.length;
        while (i--) {
           if (a[i] === obj) {
               return true;
           }
        }
        return false;
    }

    function isCardAccepted(cardType) {
        return (contains(cardType.name, netpay_params_card.card_types)? true : false);
    }

    $(function() {
        $( 'body' ).on( 'checkout_error', function () {
            $( '.netpay_token' ).remove();
        });
        
        $( 'form.checkout' ).unbind('checkout_place_order_netpay');
        $( 'form.checkout' ).on( 'checkout_place_order_netpay', function () {
            return netpayFormHandler();
        });
        
        /* Pay Page Form */
        $( 'form#order_review' ).on( 'submit', function () {
            return netpayFormHandler();
        });
        
        /* Both Forms */
        $( 'form.checkout, form#order_review' ).on( 'change', '#netpay_cc_form input', function() {
            $( '.netpay_token' ).remove();
        });

        $('body').on('focus', 'input', function() {
            $( "#netpay_card_number" ).keyup(function() {
				var cardNumber = $("#netpay_card_number").val().replace(/ /g, '');
                var creditCardLength = cardNumber.length;
                var isValidCardScheme = false;
                var isValidCard = false;

                if(creditCardLength >=6) {
                    var cardScheme = getCardScheme(cardNumber);
                    if(!isCardAccepted(cardScheme)) {
                        if(!isCardAccepted(cardScheme)) {
                            $('#netpay_card_number').parent().find('#netpay_card_invalid_card_scheme').remove();
                            $("#netpay_card_number" ).parent().append('<span id="netpay_card_invalid_card_scheme" class="netpay-card-woocommerce-error"> - No. tarjeta inválido, sólo se aceptan tarjetas "' + netpay_params_card.card_types_title.toString() + '."<br/></span>');
                        }
                        else {
                            $('#netpay_card_number').parent().find('#netpay_card_invalid_card_scheme').remove();
                        }
                    }
                    else {
                        isValidCardScheme = true;
                    }
				
                    if(cardScheme.name == 'amex') {
                       $("#netpay_card_promotion option[value='18']").hide();
                        $("#netpay_card_promotion").val($("#netpay_card_promotion option:eq(1)").val()); 
                    }
                    else {
                        $("#netpay_card_promotion option[value='18']").show();
                        $("#netpay_card_promotion").val($("#netpay_card_promotion option:eq(0)").val()); 
                    }
                    
                    var cardType = getCardType(cardNumber);
                    if (cardType.type == 'credit' || cardScheme.name == 'amex') {
                        $('div#netpay_promotion_div').show();
                        $( "#netpay_card_promotion_hidden" ).val('1');
                    }
                    else {
                        $('div#netpay_promotion_div').hide();
                        $( "#netpay_card_promotion_hidden" ).val('0');
                    }

                    if((cardScheme.name == 'amex' && creditCardLength == 15) || (cardScheme.name != 'amex' && creditCardLength == 16)) {
                        var validateCard = luhnCheck(cardNumber);
                        if(!validateCard) {
                            $('#netpay_card_number').parent().find('#netpay_card_invalid_card').remove();
                            $("#netpay_card_number" ).parent().append('<span id="netpay_card_invalid_card"  class="netpay-card-woocommerce-error"> - No. tarjeta inválido. </span>');
                        }
                        else {
                            isValidCard = true;
                            $('#netpay_card_number').parent().find('#netpay_card_invalid_card').remove();
                        }
                    }
                    else {
                        $('#netpay_card_number').parent().find('#netpay_card_invalid_card').remove();
                    }

                    if(isValidCardScheme && isValidCard)  {
                        $('#netpay_card_number').parent().find('#netpay_card_invalid_card_length').remove();
                        $('#netpay_card_number').parent().find('#netpay_card_invalid_card_scheme').remove();
                        $("#netpay_card_number").css("border-color", "gray");
                    }
                    else {
                        $("#netpay_card_number").css("border-color", "red");
                    }
				}
				else {
                    $("#netpay_card_number").css("border-color", "red");
                }
                if(creditCardLength < 15) {
                    $('#netpay_card_number').parent().find('#netpay_card_invalid_card_length').remove();
                    $("#netpay_card_number" ).parent().append('<span id="netpay_card_invalid_card_length"  class="netpay-card-woocommerce-error"> - No. tarjeta inválido, deben ser 15-16 dígitos. <br /></span>');
                }
                else {
                    $('#netpay_card_number').parent().find('#netpay_card_invalid_card_length').remove();
                    $("#netpay_card_number").css("border-color", "gray");
                }
            });

            $( "#netpay_card_name" ).keypress(function(event) {
                var regex = new RegExp("^[a-zA-Z ]+$");
                var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                if (!regex.test(key)) {
                   event.preventDefault();
                   return false;
                }
            });

            $('#netpay_card_name').keyup(function() {
                this.value = this.value.toUpperCase();
                
                var netpay_card_name = $('#netpay_card_name').val();
                var fullname = netpay_card_name.split(' ');
                if(fullname.length >= 2 && fullname[1].length > 0) {
                    $('#netpay_card_name').parent().find('#netpay_card_name_validate').remove();
                    $("#netpay_card_name").css("border-color", "gray");
                }
                else {
                    $('#netpay_card_name').parent().find('#netpay_card_name_validate').remove();
                    $("#netpay_card_name" ).parent().append('<span id="netpay_card_name_validate"  class="netpay-card-woocommerce-error"> - Nombre inválido. <br /></span>');
                    $("#netpay_card_name").css("border-color", "red");
                }
            });

            $( "#netpay_card_security_code" ).keypress(function(e) {
                if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                    return false;
                }
            });

            $('#netpay_card_expiration_card').keyup(function() {
                var expiration_card = $('#netpay_card_expiration_card').val();
                if(expiration_card.length == 5) {
                    var today = new Date();
                    var year = today.getFullYear();
                    var month = today.getMonth();
                    
                    var fields = expiration_card.split('/');
                    var expiration_month = fields[0];
                    var expiration_year = fields[1];
                    if(parseInt(expiration_year) < year-2000) {
                        $("#netpay_card_expiration_card").css("border-color", "red");
                        $('#netpay_card_expiration_card').parent().find('#netpay_card_expiry_card').remove();
                        $("#netpay_card_expiration_card" ).parent().append('<span id="netpay_card_expiry_card"  class="netpay-card-woocommerce-error"> - Fecha de vencimiento inválida, debe tener el formato mm/aa y debe ser posterior a la actual. </span>');
                    }  
                    else if(parseInt(expiration_year) == year-2000 && parseInt(expiration_month) < month) {
                        $("#netpay_card_expiration_card").css("border-color", "red");
                        $('#netpay_card_expiration_card').parent().find('#netpay_card_expiry_card').remove();
                        $("#netpay_card_expiration_card" ).parent().append('<span id="netpay_card_expiry_card"  class="netpay-card-woocommerce-error"> - Fecha de vencimiento inválida, debe tener el formato mm/aa y debe ser posterior a la actual. </span>');
                    }
                    else {
                        $('#netpay_card_expiration_card').parent().find('#netpay_card_expiry_card').remove();
                        $("#netpay_card_expiration_card").css("border-color", "gray");
                    }
                }
                else {
                    $("#netpay_card_expiration_card").css("border-color", "red");
                }
            });

            $('#netpay_card_security_code').keyup(function() {
                var cardNumber = $("#netpay_card_number").val().replace(/ /g, '');
                var cardScheme = getCardScheme(cardNumber);

                var security_code = $('#netpay_card_security_code').val();

                if(cardScheme.name == 'amex') {
                    $("#netpay_card_security_code").attr('maxlength','4');
                    if(security_code.length == 4) {
                        $('#netpay_card_security_code').parent().find('#netpay_card_security_code_validation').remove();
                        $("#netpay_card_security_code").css("border-color", "gray");
                    }
                    else {
                        $("#netpay_card_security_code").css("border-color", "red");
                        $('#netpay_card_security_code').parent().find('#netpay_card_security_code_validation').remove();
                        $("#netpay_card_security_code" ).parent().append('<span id="netpay_card_security_code_validation"  class="netpay-card-woocommerce-error"> - Código de seguridad inválido, deben ser 4 dígitos. </span>');
                    }
                }
                else {
                    $("#netpay_card_security_code").attr('maxlength','3');
                    if(security_code.length == 3) {
                        $('#netpay_card_security_code').parent().find('#netpay_card_security_code_validation').remove();
                        $("#netpay_card_security_code").css("border-color", "gray");
                    }
                    else {
                        $("#netpay_card_security_code").css("border-color", "red");
                        $('#netpay_card_security_code').parent().find('#netpay_card_security_code_validation').remove();
                        $("#netpay_card_security_code" ).parent().append('<span id="netpay_card_security_code_validation"  class="netpay-card-woocommerce-error"> - Código de seguridad inválido, deben ser 3 dígitos. </span>');
                    }
                }
            });

            $( "#netpay_card_number" ).focus(function() {
                new Cleave('#netpay_card_number', {
                    creditCard: true
                });
            });

            $( "#netpay_card_expiration_card" ).focus(function() {
                new Cleave('#netpay_card_expiration_card', {
                    date: true,
                    datePattern: ['m', 'y']
                });
            });

            $( "#netpay_card_number" ).on( "click", function() {
                generateDeviceFingerprint(netpay_params_card.org_id);
            });
            
        });

    })
})(jQuery)
