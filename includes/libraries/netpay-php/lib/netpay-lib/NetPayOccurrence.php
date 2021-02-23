<?php

class NetPayOccurrence extends NetPayApiResource
{
    const ENDPOINT = 'occurrences';

    /**
     * Retrieves an occurence object.
     *
     * @param  string $id
     * @param  string $publickey
     * @param  string $secretkey
     *
     * @return NetPayOccurrence
     */
    public static function retrieve($id, $publickey = null, $secretkey = null)
    {
        return parent::g_retrieve(get_class(), self::getUrl($id), $publickey, $secretkey);
    }

    public function reload()
    {
        parent::g_reload(self::getUrl($this['id']));
    }

    /**
     * @param  string $id
     *
     * @return string
     */
    private static function getUrl($id = '')
    {
        return NETPAY_API_URL.self::ENDPOINT . '/' . $id;
    }
}
