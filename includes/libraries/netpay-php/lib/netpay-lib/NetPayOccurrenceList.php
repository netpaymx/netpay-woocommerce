<?php

class NetPayOccurrenceList extends NetPayApiResource
{
    /**
     * @param  string $id
     *
     * @return NetPayOccurrence
     */
    public function retrieve($id)
    {
        return NetPayOccurrence::retrieve($id, $this->_publickey, $this->_secretkey);
    }
}
