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

class NetPayBill {
    /**
     * Prepares the billing information of a order for being send to the checkout.
     */
    public static function format($order)
    {
        return [
            "firstName" => \NetPay\NetPayFunctions::replace_caracters($order['billing_first_name']),
            "lastName" => \NetPay\NetPayFunctions::replace_caracters($order['billing_last_name']),
            "email" => $order['billing_email'],
            "phone" => \NetPay\NetPayFunctions::replace_country_code($order['billing_phone']),
            "address" => [
                "city" => \NetPay\NetPayFunctions::replace_caracters($order['billing_city']),
                "country" => \NetPay\NetPayFunctions::replace_caracters($order['billing_country']),
                "postalCode" => \NetPay\NetPayFunctions::replace_caracters($order['billing_postcode']),
                "state" => \NetPay\NetPayFunctions::replace_caracters($order['billing_state']),
                "street1" => \NetPay\NetPayFunctions::replace_caracters($order['billing_address_1']),
                "street2" => \NetPay\NetPayFunctions::replace_caracters($order['billing_address_2']),
            ],
            "merchantReferenceCode" => \NetPay\NetPayFunctions::replace_caracters($order['reference']),
        ];
    }
}