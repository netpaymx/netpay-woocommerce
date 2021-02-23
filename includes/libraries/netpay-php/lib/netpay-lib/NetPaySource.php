<?php

class NetPaySource extends NetPayApiResource
{
    const ENDPOINT = 'sources';

    /**
     * Creates a new source.
     *
     * @param  array  $params
     * @param  string $publickey
     * @param  string $secretkey
     *
     * @return NetPaySource
     */
    public static function create($params, $publickey = null, $secretkey = null)
    {
        return parent::g_create(get_class(), self::getUrl(), $params, $publickey, $secretkey);
    }

    /**
     * @return string
     */
    private static function getUrl()
    {
        return NETPAY_API_URL.self::ENDPOINT;
    }
}
