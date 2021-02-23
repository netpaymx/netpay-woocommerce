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

if (!function_exists('curl_init')) {
    throw new Exception('NetPay needs the CURL PHP extension.');
}

if (!function_exists('json_decode')) {
    throw new Exception('NetPay needs the JSON PHP extension.');
}

if (!function_exists('get_called_class')) {
    throw new Exception('NetPay needs to be run on PHP >= 5.3.0.');
}

require_once dirname(__FILE__).'/lib/NetPayConfig.php';

require_once dirname(__FILE__).'/lib/NetPay/Api/NetPayToken.php';
require_once dirname(__FILE__).'/lib/NetPay/Api/NetPayCheckout.php';
require_once dirname(__FILE__).'/lib/NetPay/Api/NetPayCash.php';
require_once dirname(__FILE__).'/lib/NetPay/Api/NetPayWebhook.php';
require_once dirname(__FILE__).'/lib/NetPay/Api/NetPayCashEnable.php';
require_once dirname(__FILE__).'/lib/NetPay/Api/NetPayCurl.php';
require_once dirname(__FILE__).'/lib/NetPay/Api/NetPayTransaction.php';
require_once dirname(__FILE__).'/lib/NetPay/Api/NetPayConfirm.php';

require_once dirname(__FILE__).'/lib/NetPay/Exceptions/NetPayHandlerBank.php';
require_once dirname(__FILE__).'/lib/NetPay/Exceptions/NetPayHandlerHTTP.php';

require_once dirname(__FILE__).'/lib/NetPay/Handlers/NetPayCheckoutDataHandler.php';
require_once dirname(__FILE__).'/lib/NetPay/Handlers/NetPayCashDataHandler.php';
require_once dirname(__FILE__).'/lib/NetPay/Handlers/NetPayWebhookDataHandler.php';
require_once dirname(__FILE__).'/lib/NetPay/Handlers/NetPayTokenDataHandler.php';

require_once dirname(__FILE__).'/lib/NetPay/NetPayBill.php';
require_once dirname(__FILE__).'/lib/NetPay/NetPayFunctions.php';
require_once dirname(__FILE__).'/lib/NetPay/NetPayOrder.php';
require_once dirname(__FILE__).'/lib/NetPay/NetPayShip.php';
require_once dirname(__FILE__).'/lib/NetPay/NetPayInstallments.php';
