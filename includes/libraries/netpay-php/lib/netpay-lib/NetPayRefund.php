<?php

class NetPayRefund extends NetPayApiResource
{
    /**
     * @param array  $refund
     * @param string $publickey
     * @param string $secretkey
     */
    public function __construct($refund, $publickey = null, $secretkey = null)
    {
        parent::__construct($publickey, $secretkey);
        $this->refresh($refund);
    }

    /**
     * Search for refunds.
     *
     * @param  string $query
     * @param  string $publickey
     * @param  string $secretkey
     *
     * @return NetPaySearch
     */
    public static function search($query = '', $publickey = null, $secretkey = null)
    {
        return NetPaySearch::scope('refund', $publickey, $secretkey)->query($query);
    }
}
