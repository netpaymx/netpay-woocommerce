<?php

class NetPayCharge extends NetPayApiResource
{
    const ENDPOINT = 'charges';

    /**
     * Retrieves a charge.
     *
     * @param  string $id
     * @param  string $publickey
     * @param  string $secretkey
     *
     * @return NetPayCharge
     */
    public static function retrieve($id = '', $publickey = null, $secretkey = null)
    {
        return parent::g_retrieve(get_class(), self::getUrl($id), $publickey, $secretkey);
    }

    /**
     * Search for charges.
     *
     * @param  string $query
     * @param  string $publickey
     * @param  string $secretkey
     *
     * @return NetPaySearch
     */
    public static function search($query = '', $publickey = null, $secretkey = null)
    {
        return NetPaySearch::scope('charge', $publickey, $secretkey)->query($query);
    }

    /**
     * (non-PHPdoc)
     *
     * @see NetPayApiResource::g_reload()
     */
    public function reload()
    {
        if ($this['object'] === 'charge') {
            parent::g_reload(self::getUrl($this['id']));
        } else {
            parent::g_reload(self::getUrl());
        }
    }

    /**
     * Schedule a charge.
     *
     * @param  string $params
     * @param  string $publickey
     * @param  string $secretkey
     *
     * @return NetPayScheduler
     */
    public static function schedule($params, $publickey = null, $secretkey = null)
    {
        return new NetPayScheduler('charge', $params, $publickey, $secretkey);
    }

    /**
     * Creates a new charge.
     *
     * @param  array  $params
     * @param  string $publickey
     * @param  string $secretkey
     *
     * @return NetPayCharge
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
     * Captures a charge.
     *
     * @return NetPayCharge
     */
    public function capture()
    {
        $result = parent::execute(self::getUrl($this['id']).'/capture', parent::REQUEST_POST, parent::getResourceKey());
        $this->refresh($result);

        return $this;
    }

    /**
     * Refund a charge.
     *
     * @return NetPayRefund
     */
    public function refund($params)
    {
        $result = parent::execute(self::getUrl($this['id']) . '/refunds', parent::REQUEST_POST, parent::getResourceKey(), $params);
        return new NetPayRefund($result, $this->_publickey, $this->_secretkey);
    }

    /**
     * Reverses a charge.
     *
     * @return NetPayCharge
     */
    public function reverse()
    {
        $result = parent::execute(self::getUrl($this['id']).'/reverse', parent::REQUEST_POST, parent::getResourceKey());
        $this->refresh($result);

        return $this;
    }

    /**
     * list refunds
     *
     * @return NetPayRefundList
     */
    public function refunds($options = array())
    {
        if (is_array($options) && ! empty($options)) {
            $refunds = parent::execute(self::getUrl($this['id']) . '/refunds?' . http_build_query($options), parent::REQUEST_GET, parent::getResourceKey());
        } else {
            $refunds = $this['refunds'];
        }

        return new NetPayRefundList($refunds, $this['id'], $this->_publickey, $this->_secretkey);
    }

    /**
     * Gets a list of charge schedules.
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
     * @param  string $id
     *
     * @return string
     */
    private static function getUrl($id = '')
    {
        return NETPAY_API_URL.self::ENDPOINT.'/'.$id;
    }
}
