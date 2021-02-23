<?php

class NetPayVaultResource extends NetPayApiResource
{
    /**
     * Returns the public key.
     *
     * @return string
     */
    protected function getResourceKey()
    {
        return $this->_publickey;
    }
}
