<?php

class NetPayScheduler extends NetPayApiResource
{
    private $attributes = array();

    /**
     * Create an instance of `NetPayScheduler` with the given type.
     *
     * @param string $type        Either 'charge' or 'transfer'
     * @param string $attributes  See supported attributes at [Charge Schedule API](https://www.netpay.co/charge-schedules-api#charge_schedules-create) page.
     * @param string $publickey
     * @param string $secretkey
     */
    public function __construct($type, $attributes = array(), $publickey = null, $secretkey = null)
    {
        parent::__construct($publickey, $secretkey);

        $this->mergeAttributes($type, $attributes);
    }

    /**
     * @param  int $frequency
     *
     * @return self
     */
    public function every($frequency)
    {
        return $this->mergeAttributes('every', $frequency);
    }

    /**
     * @return self
     */
    public function days()
    {
        return $this->mergeAttributes('period', 'day');
    }

    /**
     * Alias of NetPayScheduler::days()
     *
     * @return self
     *
     * @see    NetPayScheduler::days()
     */
    public function day()
    {
        return $this->days();
    }

    /**
     * @param  string|array $on  An array (or string) of weekday names. ('Monday' ... 'Sunday')
     *
     * @return self
     */
    public function weeks($on)
    {
        return $this->mergeAttributes('period', 'week')
                    ->mergeAttributes('on', array('weekdays' => is_string($on) ? array($on) : $on));
    }

    /**
     * Alias of NetPayScheduler::weeks($on)
     *
     * @return self
     *
     * @see    NetPayScheduler::weeks($on)
     */
    public function week($on)
    {
        return $this->weeks($on);
    }

    /**
     * @param  string|array $on  Be an Array if set to 'days of month'. i.e. [1, 15, 25]
     *                           and a string when set to 'weekday of month'.
     *                           i.e. 'first_monday', 'second_monday', 'third_monday', 'fourth_monday', 'last_monday'
     *
     * @return self
     */
    public function months($on)
    {
        $this->mergeAttributes('period', 'month');

        switch (strtolower(gettype($on))) {
            case 'string':
                return $this->mergeAttributes('on', array('weekday_of_month' => $on));
                break;

            case 'array':
                return $this->mergeAttributes('on', array('days_of_month' => $on));
                break;

            case 'integer':
                return $this->mergeAttributes('on', array('days_of_month' => array($on)));
                break;

            default:
                throw new NetPayBadRequestException("The first argument must be an Array or a String, not " . gettype($on), 1);
                break;
        }
    }

    /**
     * Alias of NetPayScheduler::months($on)
     *
     * @return self
     *
     * @see    NetPayScheduler::months($on)
     */
    public function month($on)
    {
        return $this->months($on);
    }

    /**
     * @param  Date $date  [ISO 8601](https://en.wikipedia.org/wiki/ISO_8601) format (YYYY-MM-DD).
     *
     * @return self
     */
    public function endDate($date)
    {
        return $this->mergeAttributes('end_date', $date);
    }

    /**
     * @param  Date $date  [ISO 8601](https://en.wikipedia.org/wiki/ISO_8601) format (YYYY-MM-DD).
     *
     * @return self
     */
    public function startDate($date)
    {
        return $this->mergeAttributes('start_date', $date);
    }

    /**
     * Start create a schedule
     *
     * @return NetPaySchedule
     */
    public function start()
    {
        return NetPaySchedule::create($this->attributes, $this->_publickey, $this->_secretkey);
    }

    /**
     * Merge the given key and value to attributes.
     *
     * @param  string $key    attribute key.
     * @param  mixed  $value  attribute value.
     *
     * @return self
     */
    private function mergeAttributes($key, $value)
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /** Override methods of ArrayAccess **/

    /**
     * {@inheritDoc}
     *
     * @see NetPayObject::offsetExists()
     */
    public function offsetExists($key)
    {
        if (isset($this->attributes[$key])) {
            return true;
        }

        return parent::offsetExists($key);
    }

    /**
     * {@inheritDoc}
     *
     * @see NetPayObject::offsetGet()
     */
    public function offsetGet($key)
    {
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }

        return parent::offsetGet($key);
    }
}
