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

namespace NetPay\Api;

use \NetPay\NetPayConfig;

class NetPayCurl
{
    /**
     * Execute a post request with the necessary configuration using curl.
     */
    public static function post($url, $fields_string, $privateKey = null)
    {
        //open connection
        $ch = curl_init();

        $http_header = array(
            'Content-Type: application/json',
            'Connection: Keep-Alive',
            'Cache-Control: no-cache',
            'Expect:',
        );

        if (isset($privateKey)) {
            $http_header[] = 'Authorization: '.$privateKey;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, strlen($fields_string));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip, deflate");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, NetPayConfig::$CURLOPT_TIMEOUT);
        curl_setopt($ch, CURLOPT_TIMEOUT, NetPayConfig::$CURLOPT_TIMEOUT);

        if (!is_null(NetPayConfig::$URL_PORT)) {
            curl_setopt($ch, CURLOPT_PORT, NetPayConfig::$URL_PORT);
        }

        //execute post
        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        //close connection
        curl_close($ch);

        return compact('code', 'result');
    }

    /**
     * Execute a get request with the necessary configuration using curl.
     */
    public static function get($url, $privateKey = null)
    {
        //open connection
        $ch = curl_init();

        $http_header = array(
            'Content-Type: application/json',
            'Connection: Keep-Alive',
            'Cache-Control: no-cache',
            'Expect:',
        );

        if (isset($privateKey)) {
            $http_header[] = 'Authorization: '.$privateKey;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING , "gzip, deflate");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, NetPayConfig::$CURLOPT_TIMEOUT);
        curl_setopt($ch, CURLOPT_TIMEOUT, NetPayConfig::$CURLOPT_TIMEOUT);

        if (!is_null(NetPayConfig::$URL_PORT)) {
            curl_setopt($ch, CURLOPT_PORT, NetPayConfig::$URL_PORT);
        }

        //execute post
        $result = curl_exec($ch);

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        //close connection
        curl_close($ch);

        return compact('code', 'result');
    }

    /**
     * Execute a put request with the necessary configuration using curl.
     */
    public static function put($url, $fields_string, $privateKey = null)
    {
        //open connection
        $ch = curl_init();

        $http_header = array(
            'Content-Type: application/json',
            'Connection: Keep-Alive',
            'Cache-Control: no-cache',
            'Expect:',
        );

        if (isset($privateKey)) {
            $http_header[] = 'Authorization: '.$privateKey;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip, deflate");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, NetPayConfig::$CURLOPT_TIMEOUT);
        curl_setopt($ch, CURLOPT_TIMEOUT, NetPayConfig::$CURLOPT_TIMEOUT);

        if (!is_null(NetPayConfig::$URL_PORT)) {
            curl_setopt($ch, CURLOPT_PORT, NetPayConfig::$URL_PORT);
        }

        //execute post
        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        //close connection
        curl_close($ch);

        return compact('code', 'result');
    }

    /**
     * Execute a delete request with the necessary configuration using curl.
     */
    public static function delete($url, $fields_string, $privateKey = null)
    {
        //open connection
        $ch = curl_init();

        $http_header = array(
            'Content-Type: application/json',
            'Connection: Keep-Alive',
            'Cache-Control: no-cache',
            'Expect:',
        );

        if (isset($privateKey)) {
            $http_header[] = 'Authorization: '.$privateKey;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip, deflate");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, NetPayConfig::$CURLOPT_TIMEOUT);
        curl_setopt($ch, CURLOPT_TIMEOUT, NetPayConfig::$CURLOPT_TIMEOUT);

        if (!is_null(NetPayConfig::$URL_PORT)) {
            curl_setopt($ch, CURLOPT_PORT, NetPayConfig::$URL_PORT);
        }

        //execute post
        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        //close connection
        curl_close($ch);

        return compact('code', 'result');
    }

}