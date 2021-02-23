<?php

class NetPayScheduleList extends NetPayApiResource
{
    /**
     * @param  string $id
     *
     * @return NetPayOccurrence
     */
    public function retrieve($id)
    {
        return NetPaySchedule::retrieve($id, $this->_publickey, $this->_secretkey);
    }
}
