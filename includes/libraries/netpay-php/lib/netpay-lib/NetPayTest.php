<?php

class NetPayTest extends NetPayApiResource
{
    /**
     * (non-PHPdoc)
     *
     * @see NetPayApiResource::getInstance()
     */
    public static function resource()
    {
        return parent::getInstance(get_class());
    }
}
