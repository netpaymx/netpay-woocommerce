<?php

class NetPayForex extends NetPayApiResource
{
    const ENDPOINT = 'forex';

    /**
     * Retrieves a forex data.
     *
     * @param  string $currency
     * @param  string $publickey
     * @param  string $secretkey
     *
     * @return NetPayForex
     */
    public static function retrieve($currency = '', $publickey = null, $secretkey = null)
    {
        return parent::g_retrieve(get_class(), self::getUrl($currency), $publickey, $secretkey);
    }

    /**
     * @see NetPayApiResource::g_reload()
     */
    public function reload()
    {
        parent::g_reload(self::getUrl($this['from']));
    }

    /**
     * @param  string $currency
     *
     * @return string
     */
    private static function getUrl($currency = '')
    {
        return NETPAY_API_URL . self::ENDPOINT . '/' . $currency;
    }
}
