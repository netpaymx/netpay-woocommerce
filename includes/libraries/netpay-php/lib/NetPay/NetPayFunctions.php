<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright 2020 NetPay. All rights reserved.
 */

namespace NetPay;

use \NetPay\NetPayConfig;

class NetPayFunctions
{
    /**
     * Return the lang options that need the plugin base in the locale.
     */
    public static function get_lang_options()
    {
        $lang = 'es';
        $filename = dirname(__FILE__).'/lang/' . $lang . ".php";
        if (!file_exists($filename)) {
            $filename = "../lang/en.php";
        }

        return require($filename);
    }

    /**
     * Get the path of the plugin.
     */
    public static function get_plugin_directory()
    {
        $explode_string = explode("/",plugin_basename(__FILE__));

        return WP_PLUGIN_DIR.'/'.$explode_string[0];
    }

    /**
     * Encode a url to base64.
     */
    public static function base64url_encode($data)
    {
        return rtrim(base64_encode($data), '=');
    }

    public static function urlencode($url) {
        return urlencode($url);
    }

    /**
     * Get the month of the promotion string.
     */
    public static function promotion_month($promotion_string)
    {
        if ($promotion_string != '000000') {
            return intval(substr($promotion_string, -4, 2));
        }

        return 0;
    }

    /**
     * Generate the available months without interest options.
     */
    public static function promotion_options($promotion, $msi3, $msi6, $msi9, $msi12, $msi18)
    {
        $lang_options = self::get_lang_options();

        $promotions = array();
        $msi = array(3, 6, 9, 12, 18);
        $msi_active = array($msi3, $msi6, $msi9, $msi12, $msi18);
        $promotions_default = array();
        foreach ($msi as $key => $value) {
            array_push($promotions_default, 
            array(
                "number" => $value,
                "lang" => $value . $lang_options['form_fields']['promotion']['months_without_interest'],
                "amount" => $value * 100
            ));
        }

        for($i=0 ; $i<count($promotions_default) ; $i++) {
            if ($msi_active[$i] == 'yes') {
                array_push($promotions, $promotions_default[$i]);
            }
        }

        if (empty($promotions) || $promotion == 'no') {
            return array();
        }

        return $promotions;
    }

    /**
     * Return the card type name base in a card type code.
     */
    public static function card_type_name($type)
    {
        $card_types = Config::CARD_TYPES;

        if (isset($card_types[$type])) {
           return $card_types[$type];
        }

        return '';
    }

    /**
     * Return the http error message base in a code and the lang.
     */
    public static function http_code_message($code)
    {
        $lang_options = self::get_lang_options();

        $http_codes = $lang_options['http_codes'];

        $message = $lang_options['http_error'];

        if (isset($http_codes[$code])) {
            $message = $http_codes[$code];
        }

        return $message;
    }

    /**
     * Return the bank error message base in a code and the lang.
     */
    public static function bank_code_message($code)
    {
        $lang_options = self::get_lang_options();
        $bank_codes = $lang_options['bank_codes'];
        $message = $lang_options['bank_error'];
        if (isset($bank_codes[$code])) {
            $message = $bank_codes[$code];
        }
        return $message;
    }

    public static function replace_caracters($input) {
        $replaced = strtr(utf8_decode($input), utf8_decode('ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ'), 'SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy');
        $replaced  = str_replace("#", " ", $replaced);
        return preg_replace('/[^A-Za-z0-9@.-_ \-]/', '', $replaced);
    }
    
    public static function replace_only_numbers($input) {
        return preg_replace('/[^0-9]/', '', $input);
    }

    public static function replace_country_code($input) {
        $output = str_replace("+52", "", $input);
        return str_replace("+1", "", $output);
    }

    public static function replace_validate_email($input) {
        return preg_replace("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i", '', $input);
    }

    public static function sign($message, $key) {
		return hash_hmac('sha256', $message, $key) . $message;
	}
	
	public static function verify($bundle, $key) {
		return hash_equals(
		  hash_hmac('sha256', mb_substr($bundle, 64, null, '8bit'), $key),
		  mb_substr($bundle, 0, 64, '8bit')
		);
	}
	
	public static function getKey($password, $keysize = 16) {
		return hash_pbkdf2('sha256',$password,'some_token',100000,$keysize,true);
	}
	
	public static function encrypt($message, $password) {
		$iv = random_bytes(16);
		$key = self::getKey($password);
		$result = self::sign(openssl_encrypt($message,'aes-256-ctr',$key,OPENSSL_RAW_DATA,$iv), $key);
		return bin2hex($iv).bin2hex($result);
	}
	
	public static function decrypt($hash, $password) {
		$iv = hex2bin(substr($hash, 0, 32));
		$data = hex2bin(substr($hash, 32));
		$key = self::getKey($password);
		if (!self::verify($data, $key)) {
		  return null;
		}
		return openssl_decrypt(mb_substr($data, 64, null, '8bit'),'aes-256-ctr',$key,OPENSSL_RAW_DATA,$iv);
    }
    
    public static function isValidCard($number){
        // Strip any non-digits (useful for credit card numbers with spaces and hyphens)
        $number=preg_replace('/\D/', '', $number);
        // Set the string length and parity
        $number_length=strlen($number);
        $parity=$number_length % 2;

        // Loop through each digit and do the maths
        $total=0;
        for ($i=0; $i<$number_length; $i++) {
            $digit=$number[$i];
            // Multiply alternate digits by two
            if ($i % 2 == $parity) {
                $digit*=2;
                // If the sum is two digits, add them together (in effect)
                if ($digit > 9) {
                    $digit-=9;
                }
            }
            // Total up the digits
            $total+=$digit;
        }

        // If the total mod 10 equals 0, the number is valid
        return ($total % 10 == 0) ? TRUE : FALSE;
    }

    public static function getCardScheme($ccNumber) {
        $ccType = '';
        $ccTypeRegExpList = [
            // Solo only
            'SO' => '/(^(6334)[5-9](\d{11}$|\d{13,14}$))|(^(6767)(\d{12}$|\d{14,15}$))/',
            'SM' => '/(^(5[0678])\d{11,18}$)|(^(6[^05])\d{11,18}$)|(^(601)[^1]\d{9,16}$)|(^(6011)\d{9,11}$)'
                . '|(^(6011)\d{13,16}$)|(^(65)\d{11,13}$)|(^(65)\d{15,18}$)'
                . '|(^(49030)[2-9](\d{10}$|\d{12,13}$))|(^(49033)[5-9](\d{10}$|\d{12,13}$))'
                . '|(^(49110)[1-2](\d{10}$|\d{12,13}$))|(^(49117)[4-9](\d{10}$|\d{12,13}$))'
                . '|(^(49118)[0-2](\d{10}$|\d{12,13}$))|(^(4936)(\d{12}$|\d{14,15}$))/',
            // Visa
            'visa' => '/^4[0-9]{12}([0-9]{3})?$/',
            // Master Card
            'mastercard' => '/^5[1-5][0-9]{14}$/',
            // American Express
            'amex' => '/^3[47][0-9]{13}$/',
            // Discovery
            'DI' => '/^6011[0-9]{12}$/',
            // JCB
            'JCB' => '/^(3[0-9]{15}|(2131|1800)[0-9]{11})$/'
        ];

        foreach ($ccTypeRegExpList as $ccTypeMatch => $ccTypeRegExp) {
            if (preg_match($ccTypeRegExp, $ccNumber)) {
                $ccType = $ccTypeMatch;
                break;
            }
        }
        return $ccType;
    }

    /**
     * Save custom field order meta in the order.
     */
    public static function custom_field_update_order_meta($order_id, $field, $value)
    {
        if (isset($value)) {
            update_post_meta($order_id, $field, sanitize_text_field($value));
        }
    }

    public static function custom_field_get_order_meta($order_id, $field)
    {
        return get_post_meta($order_id, $field, true);
    }

    public static function friendly_response($response) {
        $friendly_response = '';
        switch ($response) {
            case 'Aprobada':
                $friendly_response =  "Transacción Aprobada.";
                break;
            case 'Configuracion Invalida Procesador Tarjetas':
                $friendly_response =  "Error de comunicación, intente de nuevo.";
                break;
            case 'Error':
                $friendly_response =  "Error de comunicación, intente de nuevo.";
                break;
            case 'Error al procesar transaccion':
                $friendly_response =  "Error de comunicación, intente de nuevo.";
                break;
            case 'No response from DM':
                $friendly_response =  "Error de comunicación, intente de nuevo.";
                break;
            case 'Respuesta Tardia':
                $friendly_response =  "Error de comunicación, intente de nuevo.";
                break;
            case '(Null)':
                $friendly_response =  "Error de configuración.";
                break;
            case 'Invalido Identificador de Negocio':
                $friendly_response =  "Error de configuración.";
                break;
            case 'La Tienda Esta Deshabilitada':
                $friendly_response =  "Error de configuración.";
                break;
            case 'No Existe Registro':
                $friendly_response =  "Error de configuración.";
                break;
            case 'Violacion Integridad Base de Datos':
                $friendly_response =  "Error de configuración.";
                break;
            case 'Fondos Insuficientes':
                $friendly_response =  "Transacción no exitosa, tu tarjeta cuenta con fondos insuficientes.";
                break;
            case 'Rechazada Fondos Insuficientes':
                $friendly_response =  "Transacción no exitosa, tu tarjeta cuenta con fondos insuficientes.";
                break;
            case 'ORDER_REJECTED_BY_DM':
                $friendly_response =  "Transacción rechazada. Por favor intenta con otro método de pago.";
                break;
            case 'Reject by DM':
                $friendly_response =  "Transacción rechazada. Por favor intenta con otro método de pago.";
                break;
            case 'Rejected by Decision Manager':
                $friendly_response =  "Transacción rechazada. Por favor intenta con otro método de pago.";
                break;
            case 'Recoger Tarjeta':
                $friendly_response =  "Transacción rechazada.";
                break;
            case 'Tarjeta Perdida':
                $friendly_response =  "Transacción rechazada.";
                break;
            case 'Tarjeta Perdida, Recoger':
                $friendly_response =  "Transacción rechazada.";
                break;
            case 'Error, se recibio un valor nulo':
                $friendly_response =  "Información incorrecta, intente de nuevo.";
                break;
            case 'Formato de fecha exp invalido':
                $friendly_response =  "Información incorrecta, intente de nuevo.";
                break;
            case 'WAIT_AND_RESEND_REQUEST':
                $friendly_response =  "Información incorrecta, intente de nuevo.";
                break;
            case 'Promocion de tarjeta invalida':
                $friendly_response =  "Meses sin intereses no compatible con la tarjeta.";
                break;
            case 'Promocion invalida':
                $friendly_response =  "Meses sin intereses no compatible con tarjeta.";
                break;
            case 'Pago Diferido No Permitido':
                $friendly_response =  "Pago Diferido No Permitido.";
                break;
            case 'Promocion no valida para el tipo de tarjeta':
                $friendly_response =  "Meses sin intereses no compatible con tarjeta.";
                break;
            case 'Consultar con el Emisor de la Tarjeta':
                $friendly_response =  "Rechazada por su banco, llame a su banco.";
                break;
            case 'Declinada General':
                $friendly_response =  "Rechazada por su banco, llame a su banco.";
                break;
            case 'Limite Retiro Frecuencia':
                $friendly_response =  "Transacción rechazada. Favor de comunicarte con tu banco.";
                break;
            case 'Llame al Emisor':
                $friendly_response =  "Transacción rechazada. Favor de comunicarte con tu banco.";
                break;
            case 'Numero de Intentos de PIN excedido':
                $friendly_response =  "Transacción rechazada. Favor de comunicarte con tu banco.";
                break;
            case 'PARes signature digest value mismatch. PARes message has been modified.':
                $friendly_response =  "Transacción rechazada. Favor de comunicarte con tu banco.";
                break;
            case 'Rechazada':
                $friendly_response =  "Transacción rechazada. Favor de comunicarte con tu banco.";
                break;
            case 'Reservado Uso Privado':
                $friendly_response =  "Transacción rechazada. Favor de comunicarte con tu banco.";
                break;
            case 'Tarjeta Invalida':
                $friendly_response =  "Transacción rechazada. Tu tarjeta es inválida.";
                break;
            case 'Tarjeta Restringida':
                $friendly_response =  "Transacción rechazada. Favor de comunicarte con tu banco.";
                break;
            case 'Transaccion Invalida':
                $friendly_response =  "Transacción rechazada. Favor de comunicarte con tu banco.";
                break;
            case 'Transaccion Rechazada':
                $friendly_response =  "Transacción rechazada. Por favor intenta con otro método de pago.";
                break;
            case '3DS is not active for this store':
                $friendly_response =  "Tarjeta no permitida, intente con otra tarjeta.";
                break;
            case 'El Password es incorrecto':
                $friendly_response =  "Fallo en autenticación.";
                break;
            case 'Issuer unable to perform authentication':
                $friendly_response =  "Fallo en autenticación.";
                break;
            case 'ORDER_FOR_REVIEW_BY_DM':
                $friendly_response =  "Error de comunicación.";
                break;
            case 'REQUEST_DIFFERENT_CARD':
                $friendly_response =  "Tarjeta no permitida, intente nuevamente.";
                break;
            case 'RESEND_THE_REQUEST_WITH_COMPLETE_INFORMATION':
                $friendly_response =  "Información incompleta.";
                break;
            case 'RESEND_THE_REQUEST_WITH_CORRECT_INFORMATION':
                $friendly_response =  "Información incompleta.";
                break;
            case 'REVIEW_CUSTOMERS_ORDER':
                $friendly_response =  "Información incompleta.";
                break;
            case 'User failed authentication':
                $friendly_response =  "Fallo en autenticación.";
                break;
            case 'Usuario o Password Invalidos':
                $friendly_response =  "Fallo en autenticación.";
                break;
            case 'Tipo de Tarjeta No Soportada':
                $friendly_response =  "Tarjeta no permitida, intente con otra tarjeta.";
                break;
            case 'Transaccion No Permitida':
                $friendly_response =  "Tarjeta no permitida, intente con otra tarjeta.";
                break;
            case 'Tarjeta invalida':
                $friendly_response =  "Tarjeta invalida, intente con otra tarjeta.";
                break;
            case 'Tarjeta Vencida':
                $friendly_response =  "Transacción rechazada, La tarjeta que estás utilizando está expirada.";
                break;
            case 'Tarjeta Expirada':
                $friendly_response =  "Transacción rechazada, La tarjeta que estás utilizando está expirada.";
                break;
            case 'Procesador No Disponible.':
                $friendly_response =  "Transacción no existosa, No fue posible procesar tu pago, intenta más tarde.";
                break;
            case 'Procesador No Disponible':
                $friendly_response =  "Transacción no existosa, No fue posible procesar tu pago, intenta más tarde.";
                break;
            case 'Retener Tarjeta':
                $friendly_response =  "Transacción rechazada. Por favor intenta con otro método de pago.";
                break;
            case 'Llame Emisor':
                $friendly_response =  "Transacción rechazada. Llame Emisor.";
                break;
            case 'Tarjeta Sin Activar':
                $friendly_response =  "Transacción rechazada. Tarjeta Sin Activar.";
                break;
            case 'Numero de Orden Existente':
                $friendly_response =  "Numero de Orden Existente.";
                break;
            case 'Autorizaciones Excedidas':
                $friendly_response =  "Transacción rechazada, Has alcanzado el límite de transacciones permitidas por día, llama a tu banco.";
                break;
            case 'Limite Excedido':
                $friendly_response =  "Transacción rechazada, Has alcanzado el monto máximo aprobado permitido por día, llama a tu banco.";
                break;
            case 'Reintente':
                $friendly_response =  "Transacción rechazada. Intenta nuevamente.";
                break;
            case 'Comercio Invalido':
                $friendly_response =  "Transacción rechazada. Error de configuración.";
                break;
            case 'Monto Invalido':
                $friendly_response =  "Transacción rechazada. Monto Invalido.";
                break;
            case 'Formato de numero invalido':
                $friendly_response =  "Transacción rechazada. Formato de numero invalido.";
                break;
            case 'PIN Invalido/Excedido':
                $friendly_response =  "Transacción rechazada. PIN Invalido/Excedido.";
                break;
            case 'Invalid Context':
                $friendly_response =  "Transacción rechazada.";
                break;
            default:
                $friendly_response =  "Transacción no exitosa, No fue posible procesar tu pago, intenta más tarde.";
        }
        return $friendly_response;
    }

    public static function get_post_id_by_transaction_id($transaction_id) {
        global $wpdb;
        $result = 0;
        $result_db = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT post_id FROM wp_postmeta WHERE meta_key = '_transaction_token_id' and meta_value = %s order by post_id desc limit 1;",
                $transaction_id
              )
        );
        foreach ( $result_db as $print )   {
            $result = $print->post_id;
        }
        return $result;
    }

}