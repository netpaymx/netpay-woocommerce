<?php

class NetPaySchedule extends NetPayApiResource
{
    const ENDPOINT = 'schedules';

    /**
     * Retrieves a schedule.
     *
     * @param  string $id
     * @param  string $publickey
     * @param  string $secretkey
     *
     * @return NetPaySchedule
     */
    public static function retrieve($id = '', $publickey = null, $secretkey = null)
    {
        return parent::g_retrieve(get_class(), self::getUrl($id), $publickey, $secretkey);
    }

    /**
     * @return void
     */
    public function reload()
    {
        if ($this['object'] === 'schedule') {
            parent::g_reload(self::getUrl($this['id']));
        } else {
            parent::g_reload(self::getUrl());
        }
    }

    /**
     * Creates a new schedule.
     *
     * @param  array  $params
     * @param  string $publickey
     * @param  string $secretkey
     *
     * @return NetPaySchedule
     */
    public static function create($params, $publickey = null, $secretkey = null)
    {
        return parent::g_create(get_class(), self::getUrl(), $params, $publickey, $secretkey);
    }

    /**
     * @param  array|string $options
     *
     * @return NetPayOccurrenceList|null
     */
    public function occurrences($options = array())
    {
        if ($this['object'] === 'schedule') {
            if (is_array($options)) {
                $options = '?' . http_build_query($options);
            }

            return parent::g_retrieve('NetPayOccurrenceList', self::getUrl($this['id'] . '/occurrences' . $options), $this->_publickey, $this->_secretkey);
        }
    }

    /**
     * @return void
     */
    public function destroy()
    {
        parent::g_destroy(self::getUrl($this['id']));
    }

    /**
     * @return bool
     *
     * @see    NetPayApiResource::isDestroyed()
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
        return NETPAY_API_URL.self::ENDPOINT . '/' . $id;
    }
}
