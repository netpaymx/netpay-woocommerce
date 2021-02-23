<?php

/**
 * This class is not intended to be used directly from client code.
 *
 * Use NetPay resource classes to begin searching:
 *
 *     $search = NetPayCharge::search('query');
 *
 *     foreach ($search['data'] as $result) {
 *         echo $result['object'];
 *     }
 *
 * @see NetPayCharge::search()
 * @see NetPayCustomer::search()
 * @see NetPayDispute::search()
 * @see NetPayLink::search()
 * @see NetPayRecipient::search()
 * @see NetPayRefund::search()
 * @see NetPayTransfer::search()
 */
class NetPaySearch extends NetPayApiResource
{
    const ENDPOINT = 'search';

    private $dirty = true;
    private $attributes = array();

    /**
     * Create an instance of `NetPaySearch` with the given scope.
     *
     * @param  string $scope  See supported scope at [Search API](https://www.netpay.co/search-api) page.
     * @param  string $publickey
     * @param  string $secretkey
     *
     * @return NetPaySearch  The created instance.
     */
    public static function scope($scope, $publickey = null, $secretkey = null)
    {
        return new NetPaySearch($scope, $publickey, $secretkey);
    }

    /**
     * Create an instance of `NetPaySearch` with the given scope.
     *
     * This constructor is `protected` thus not intended to be used directly.
     *
     * @param string $scope  See supported scope at [Search API](https://www.netpay.co/search-api) page.
     * @param string $publickey
     * @param string $secretkey
     */
    protected function __construct($scope, $publickey, $secretkey)
    {
        parent::__construct($publickey, $secretkey);
        $this->mergeAttributes('scope', $scope);
    }

    /**
     * Update `query` parameter.
     *
     * @param  string $query  Searching text within the scope.
     *
     * @return NetPaySearch  This instance.
     */
    public function query($query)
    {
        return $this->mergeAttributes('query', $query);
    }

    /**
     * Update `filters` parameter.
     *
     * @param  string $filters  Searching text with specific key within the scope.
     *
     * @return NetPaySearch  This instance.
     */
    public function filter(array $filters = array())
    {
        foreach ($filters as $k => $v) {
            if (is_bool($v)) {
                $filters[$k] = $v ? 'true' : 'false';
            }
        }
        return $this->mergeAttributes('filters', $filters);
    }

    /**
     * Update `page` parameter.
     *
     * @param  int $page  Specific number of searching page.
     *
     * @return NetPaySearch  This instance.
     */
    public function page($page)
    {
        return $this->mergeAttributes('page', $page);
    }

    /**
     * Update `per_page` parameter.
     *
     * @param  int $limit   Number of items that will be shown per page.
     *
     * @return NetPaySearch  This instance.
     */
    public function per_page($limit)
    {
        return $this->mergeAttributes('per_page', $limit);
    }

    /**
     * Update `order` parameter.
     *
     * @param  string $order  The order of the list returned.
     *
     * @see    https://www.netpay.co/search-api
     *
     * @return NetPaySearch  This instance.
     */
    public function order($order)
    {
        return $this->mergeAttributes('order', $order);
    }

    /**
     * Check whether this search instance is dirty or not.
     *
     * Dirty search instance needs to be reloaded before further usage. The
     * dirty search instance automatically reloads its data when client code
     * tries to access its array element.
     *
     *     $search = NetPayCharge::query('demo'); // the instance is dirty
     *     echo $search['object'];               // this will automatically retrive remote value
     *
     * @return bool  true if the instance is dirty and needs to be reloaded
     */
    public function isDirty()
    {
        return $this->dirty;
    }

    /**
     * Reload search data from NetPay server.
     *
     * This method does not consider the dirty status of the instance and will
     * always call backend server and reset dirty flag.
     */
    public function reload()
    {
        $this->dirty = false;
        $this->g_reload($this->getUrl());
    }

    /**
     * Reload search data from NetPay server if this instance is in dirty state.
     */
    private function reloadIfDirty()
    {
        if ($this->isDirty()) {
            $this->reload();
        }
    }

    /**
     * Merge the given key and value to search attributes, and set instance state
     * as dirty.
     *
     * @param  string $key    Search attribute key.
     * @param  mixed  $value  Search attribute value.
     *
     * @return NetPaySearch  This instance.
     */
    private function mergeAttributes($key, $value)
    {
        $this->dirty = true;
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * Generate request url.
     *
     * @return string
     */
    private function getUrl()
    {
        $querystring = http_build_query($this->attributes);
        return NETPAY_API_URL.self::ENDPOINT.'/?'.$querystring;
    }

    // Override methods of ArrayAccess

    /*
     * (non-PHPdoc)
     *
     * @see NetPayObject::offsetSet()
     */
    public function offsetSet($key, $value)
    {
        $this->reloadIfDirty();
        return parent::offsetSet($key, $value);
    }

    /*
     * (non-PHPdoc)
     *
     * @see NetPayObject::offsetExists()
     */
    public function offsetExists($key)
    {
        $this->reloadIfDirty();
        return parent::offsetExists($key);
    }

    /*
     * (non-PHPdoc)
     *
     * @see NetPayObject::offsetUnset()
     */
    public function offsetUnset($key)
    {
        $this->reloadIfDirty();
        return parent::offsetUnset($key);
    }

    /*
     * (non-PHPdoc)
     *
     * @see NetPayObject::offsetGet()
     */
    public function offsetGet($key)
    {
        $this->reloadIfDirty();
        return parent::offsetGet($key);
    }
}
