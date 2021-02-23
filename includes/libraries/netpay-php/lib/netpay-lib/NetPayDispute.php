<?php

class NetPayDispute extends NetPayApiResource
{
    const ENDPOINT = 'disputes';

    /**
     * Retrieves a dispute.
     *
     * @param  string $id
     * @param  string $publickey
     * @param  string $secretkey
     *
     * @return NetPayDispute
     */
    public static function retrieve($id = '', $publickey = null, $secretkey = null)
    {
        return parent::g_retrieve(get_class(), self::getUrl($id), $publickey, $secretkey);
    }

    /**
     * Search for disputes.
     *
     * @param  string $query
     * @param  string $publickey
     * @param  string $secretkey
     *
     * @return NetPaySearch
     */
    public static function search($query = '', $publickey = null, $secretkey = null)
    {
        return NetPaySearch::scope('dispute', $publickey, $secretkey)->query($query);
    }

    /**
     * (non-PHPdoc)
     *
     * @see NetPayApiResource::g_reload()
     */
    public function reload()
    {
        if ($this['object'] === 'dispute') {
            parent::g_reload(self::getUrl($this['id']));
        } else {
            parent::g_reload(self::getUrl());
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see NetPayApiResource::g_update()
     */
    public function update($params)
    {
        parent::g_update(self::getUrl($this['id']), $params);
    }

    /**
     * Generate request url.
     *
     * @param  string $id
     *
     * @return string
     */
    private static function getUrl($id = '')
    {
        return NETPAY_API_URL.self::ENDPOINT.'/'.$id;
    }
}
