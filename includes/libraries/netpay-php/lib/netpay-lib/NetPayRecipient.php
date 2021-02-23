<?php

class NetPayRecipient extends NetPayApiResource
{
    const ENDPOINT = 'recipients';

    /**
     * Retrieves recipients.
     *
     * @param  string $id
     * @param  string $publickey
     * @param  string $secretkey
     *
     * @return NetPayRecipient
     */
    public static function retrieve($id = '', $publickey = null, $secretkey = null)
    {
        return parent::g_retrieve(get_class(), self::getUrl($id), $publickey, $secretkey);
    }

    /**
     * Search for recipients.
     *
     * @param  string $query
     * @param  string $publickey
     * @param  string $secretkey
     *
     * @return NetPaySearch
     */
    public static function search($query = '', $publickey = null, $secretkey = null)
    {
        return NetPaySearch::scope('recipient', $publickey, $secretkey)->query($query);
    }

    /**
     * Creates a new recipient.
     *
     * @param  array  $params
     * @param  string $publickey
     * @param  string $secretkey
     *
     * @return NetPayRecipient
     */
    public static function create($params, $publickey = null, $secretkey = null)
    {
        return parent::g_create(get_class(), self::getUrl(), $params, $publickey, $secretkey);
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
     * (non-PHPdoc)
     *
     * @see NetPayApiResource::g_reload()
     */
    public function reload()
    {
        if ($this['object'] === 'recipient') {
            parent::g_reload(self::getUrl($this['id']));
        } else {
            parent::g_reload(self::getUrl());
        }
    }

    /**
     * Gets a list of transfer schedules that belongs to a given recipient.
     *
     * @param  array|string $options
     *
     * @return NetPayScheduleList
     */
    public function schedules($options = array())
    {
        if ($this['object'] === 'recipient') {
            if (is_array($options)) {
                $options = '?' . http_build_query($options);
            }

            return parent::g_retrieve('NetPayScheduleList', self::getUrl($this['id'] . '/schedules' . $options), $this->_publickey, $this->_secretkey);
        }
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
