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
 * Copyright 2018 NetPay. All rights reserved.
 */

	return array(
        'admin_title' => 'Make a payment using a credit card',
        'thank_you_default' => 'Thank you for making business with us.',
        'receipt_page_title' => 'Please enter all required fields.',
        'method_description' => 'Payment can be made using VISA, MasterCard and American Express.',
        'description_payment' => 'Click to Place Order.',
        'form_fields' => array(
            'enabled' => array(
                'title' => 'Enable/disable',
                'label' => 'Enable NetPay',
            ),
            'title' => array(
                'title' => 'Title',
                'description' => 'This controls the title which the user sees during checkout.',
                'default' => 'Make a payment using a credit card'
            ),
            'trans_type' => array(
                'title' => 'Transaction type',
                'description' => 'Select the transaction type.'
            ),
            'mid' => array(
                'title' => 'MID',
                'description' => 'Select the MID.',
                'agencias' => 'Travel agency',
                'donaciones' => 'Donations',
                'escuelas' => 'Schools',
                'tickets' => 'Events',
                'restaurant' => 'Restaurant',
                'retail' => 'Retail',
                'generales' => 'General services',
                'profesionales' => 'Professional services',
            ),
            'method_of_delivery' => array(
                'title' => 'Method of delivery',
                'description' => 'MID = Retail',
            ),
            'category' => array(
                'title' => 'Category',
                'description' => 'MID = Retail, Restaurant, Schools, Travel agency, Professional services y Events',
            ),
            'store_city' => array(
                'title' => 'Store City',
                'description' => 'MID = Restaurant',
            ),
            'store_postcode' => array(
                'title' => 'Store Zipcode',
                'description' => 'MID = Restaurant',
            ),
            'store_primary_type_food' => array(
                'title' => 'Type of food (primary)',
                'description' => 'MID = Restaurant',
            ),
            'store_secundary_type_food' => array(
                'title' => 'Type of food (secundary)',
                'description' => 'MID = Restaurant',
            ),
            'store_level' => array(
                'title' => 'School level',
                'description' => 'MID = Schools',
            ),
            'store_service_type' => array(
                'title' => 'Type of service',
                'description' => 'MID = General services',
            ),
            'store_user' => array(
                'title' => 'User',
            ),
            'store_password' => array(
                'title' => 'Password',
            ),
            'store_customer' => array(
                'title' => 'Merchant Id',
            ),
            'promotion' => array(
                'title' => 'Months without interest',
                'label' => 'Enable months without interest',
                'number_months' => 'Number of months',
                'months_without_interest_0' => 'Only one payment',
                'months_without_interest_3' => '3 months without interest',
                'months_without_interest_6' => '6 months without interest',
                'months_without_interest_9' => '9 months without interest',
                'months_without_interest_12' => '12 months without interest',
                'months_without_interest_18' => '18 months without interest',
                'months_without_interest' => ' months without interest',
            ),
        ),
        'http_error' => 'Internal Server Error',
        'http_codes' => array(
            0   => 'Incorrect URL.',
            400 => 'Bad Request.',
            401 => 'Unauthorized.',
            403 => 'Forbidden.',
            404 => 'Not found.',
            500 => 'Internal Server Error.',
        ),
        'bank_error' => 'Bank Error',
        'bank_codes' => array(
            '00' => 'Approved.',
            '01' => 'Call transmitter.',
            '02' => 'Call transmitter.',
            '03' => 'Invalid merchant.',
            '04' => 'Retain card.',
            '05' => 'Invalid transaction.',
            '06' => 'Try again.',
            '07' => 'Retain card.',
            '12' => 'Transaction not permitted.',
            '13' => 'Transaction not permitted.',
            '14' => 'Invalid card.',
            '15' => 'Transmitter not exists.',
            '30' => 'Format error.',
            '31' => 'Transaction not permitted.',
            '33' => 'Expired card.',
            '34' => 'Fraud.',
            '36' => 'Retain card.',
            '41' => 'Retain card.',
            '43' => 'Retain card.',
            '51' => 'Insufficient funds.',
            '54' => 'Expired card.',
            '55' => 'Pin not vali.',
            '56' => 'Invalid card.',
            '57' => 'Invalid transaction.',
            '58' => 'Invalid transaction.',
            '61' => 'Insufficient funds.',
            '62' => 'Restricted card.',
            '63' => 'Insufficient permissions.',
            '68' => 'Try again.',
            '75' => 'Invalid pin.',
            '82' => 'Invalid card.',
            '83' => 'Invalid card.',
            '87' => 'Invalid card.',
            '91' => 'Processor not available.',
        ),
        'checkout' => array(
            'error' => 'Error in the shopping cart.',
        ),
        'transaction' => array(
            'complete' => 'The transaction is complete.',
            'error' => array(
                'callback' => 'Error de callback',
                'empty_order' => 'The transaction does not match with any purchase order.',
                'error_bank' => 'Transaction error.',
                'is_complete' => 'The order %s has been completed.'
            ),
            'payment_complete' => 'The payment was completed [Orderid: %s, Orderid NetPay - %s, Card - **** **** **** %s], Bank - %s, Brand card - %s, Months without interest: %s',
        ),
        'cancelled' => array(
            'complete' => 'The order #%s was cancelled successfully',
            'error_bank' => 'Error to make cancel.',
            'stock_restored' => 'The product #%s increase from %s to %s.',
            'cannot_cancelled' => 'The day paid of the order is different from current day.',
        ),
        'client_form_fields' => array(
            'regimen_fical' => array(
                'label' => 'Taxpayer',
                'placeholder' => 'Taxpayer',
                'option1' => 'Citizen',
                'option2' => 'Domestic partnership',
                'option3' => 'Domestic corporation',
            ),
            'legend' => array(
                'label' => 'My travel',
            ),
            'type_travel' => array(
                'label' => 'Travel type',
                'option1' => 'One way',
                'option2' => 'Round Trip',
            ),
            'depart' => array(
                'label' => 'Depart',
                'placeholder' => 'Depart',
            ),
            'return' => array(
                'label' => 'Return (Optional)',
                'placeholder' => 'Return',
            ),
            'passegers_number' => array(
                'label' => 'Number of passengers',
            ),
            'frequency_number' => array(
                'label' => 'Frequency number',
            ),
            'name_passenger1' => array(
                'label' => 'Passenger 1',
            ),
            'phone_passenger1' => array(
                'label' => 'Phone number 1',
            ),
            'name_passenger2' => array(
                'label' => 'Passenger 2',
            ),
            'phone_passenger2' => array(
                'label' => 'Phone number 2',
            ),
            'name_passenger3' => array(
                'label' => 'Passenger 3',
            ),
            'phone_passenger3' => array(
                'label' => 'Phone number 3',
            ),
            'name_passenger4' => array(
                'label' => 'Passenger 4',
            ),
            'phone_passenger4' => array(
                'label' => 'Phone number 4',
            ),
        ),
        'charge' => array(
            'error' => 'Error make the payment.',
            'double_charge' => 'The payment already done.',
            'type_error' => 'Type of transaction must be Pre/PostAuth.',
        ),
        'change_status' => array(
            'to_postauth' => 'Change status to PostAuth',
        ),
    );
