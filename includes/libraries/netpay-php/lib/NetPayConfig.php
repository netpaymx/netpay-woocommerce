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

class NetPayConfig
{
    //-- Account settings
    public static $PRIVATE_KEY;
    public static $PUBLIC_KEY;

    //-- General settings
    public static $CURLOPT_TIMEOUT; //Timeout in seconds

    public static $API_URL;
    public static $API_URL_LIVE;
    public static $API_URL_SANDBOX;
    public static $TRANSACTION_URL;
    public static $CHECKOUT_URL;
    public static $CASH_URL;
    public static $WEBHOOK_URL;
    public static $CASH_ENABLE_URL;
    public static $CONFIRM_URL;
    public static $TOKEN_URL;
    
    public static $URL_PORT = null;
    public static $CARD_TYPES = [];

    public static function init($testMode) {
        self::$API_URL_LIVE = "https://suite.netpay.com.mx/gateway-ecommerce";
        self::$API_URL_SANDBOX = "https://gateway-154.netpaydev.com/gateway-ecommerce";

        //-- General settings
        self::$CURLOPT_TIMEOUT = 40; //Timeout in seconds

        self::$API_URL = ($testMode) ? self::$API_URL_SANDBOX : self::$API_URL_LIVE ;
    
        self::$TOKEN_URL = self::$API_URL."/v3/token";
        self::$CHECKOUT_URL = self::$API_URL."/v3/charges";
        self::$CONFIRM_URL = self::$API_URL."/v3/charges/%s/confirm";
        self::$TRANSACTION_URL = self::$API_URL."/v3/transactions/%s";
        self::$CASH_URL = self::$API_URL."/v3/charges";
        self::$CASH_ENABLE_URL = self::$API_URL."/v3/stores";
        self::$WEBHOOK_URL = self::$API_URL."/v3/webhooks/";
        
        self::$URL_PORT = null;
        self::$CARD_TYPES = array(
            '001' => 'Visa',
            '002' => 'MasterCard',
            '003' => 'American Express',
        );
    }

}
