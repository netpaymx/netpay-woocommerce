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

namespace NetPay\Exceptions;

use \Exception;
use \NetPay\NetPayFunctions;

class NetPayHandlerHTTP extends Exception
{
    /**
     * Initialize the class.
     */
    public function __construct($message = null, $messageToPurchaser = null)
    {
        Exception::__construct($message);
        $this->message = $message;
        $this->messageToPurchaser = $messageToPurchaser;
    }

    /**
     * Build the error message of Curl request.
     */
    private function build($result = null, $http_code)
    {
        if (isset($http_code) != true || $http_code == 0) {
            $http_code = 0;
        }

        $message = NetPayFunctions::http_code_message($http_code);

        return new Exception($message, 1);
    }

    /**
     * Function that is call to format the message when a Curl request has an error.
     */
    public static function errorHandler($result, $http_code)
    {
        throw self::build($result, $http_code);
    }

    public static function getError($response) {
        return new Exception($response['message'], 1);
    }
}
