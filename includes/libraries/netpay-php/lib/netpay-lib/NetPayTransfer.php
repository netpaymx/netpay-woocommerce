<?php

class NetPayTransfer extends NetPayApiResource
{
    const ENDPOINT = 'transfers';

    /**
     * Retrieves a transfer.
     *
     * @param  string $id
     * @param  string $publickey
     * @param  string $secretkey
     *
     * @return NetPayTransfer
     */
    public static function retrieve($id = '', $publickey = null, $secretkey = null)
    {
        return parent::g_retrieve(get_class(), self::getUrl($id), $publickey, $secretkey);
    }

    /**
     * Search for transfers.
     *
     * @param  string $query
     * @param  string $publickey
     * @param  string $secretkey
     *
     * @return NetPaySearch
     */
    public static function search($query = '', $publickey = null, $secretkey = null)
    {
        return NetPaySearch::scope('transfer', $publickey, $secretkey)->query($query);
    }

    /**
     * Schedule a transfer.
     *
     * @param  string $params
     * @param  string $publickey
     * @param  string $secretkey
     *
     * @return NetPayScheduler
     */
    public static function schedule($params, $publickey = null, $secretkey = null)
    {
        return new NetPayScheduler('transfer', $params, $publickey, $secretkey);
    }

    /**
     * Creates a transfer.
     *
     * @param  mixed  $params
     * @param  string $publickey
     * @param  string $secretkey
     *
     * @return NetPayTransfer
     */
    public static function create($params, $publickey = null, $secretkey = null)
    {
        return parent::g_create(get_class(), self::getUrl(), $params, $publickey, $secretkey);
    }

    /**
     * (non-PHPdoc)
     *
     * @see NetPayApiResource::g_reload()
     */
    public function reload()
    {
        if ($this['object'] === 'transfers') {
            parent::g_reload(self::getUrl($this['id']));
        } else {
            parent::g_reload(self::getUrl());
        }
    }

    /**
     * Updates the transfer amount.
     */
    public function save()
    {
        $this->update(array('amount' => $this['amount']));
    }

    /**
     * (non-PHPdoc)
     *
     * @see NetPayApiResource::g_update()
     */
    protected function update($params)
    {
        parent::g_update(self::getUrl($this['id']), $params);
    }

    /**
     * Gets a list of transfer schedules.
     *
     * @param  array|string $options
     * @param  string       $publickey
     * @param  string       $secretkey
     *
     * @return NetPayScheduleList
     */
    public static function schedules($options = array(), $publickey = null, $secretkey = null)
    {
        if (is_array($options)) {
            $options = '?' . http_build_query($options);
        }

        return parent::g_retrieve('NetPayScheduleList', self::getUrl('schedules' . $options), $publickey, $secretkey);
    }

    /**
     * (non-PHPdoc)
     *
     * @see NetPayApiResource::g_destroy()
     */
    public function destroy()
    {
        parent::g_destroy(self::getUrl($this['id']));
    }

    /**
     * (non-PHPdoc)
     *
     * @see NetPayApiResource::isDestroyed()
     */
    public function isDestroyed()
    {
        return parent::isDestroyed();
    }

    /**
     * @param  string $id
     *
     * @return string
     */
    private static function getUrl($id = '')
    {
        return NETPAY_API_URL.self::ENDPOINT.'/'.$id;
    }
}
